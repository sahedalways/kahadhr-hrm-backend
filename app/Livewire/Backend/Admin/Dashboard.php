<?php

namespace App\Livewire\Backend\Admin;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class Dashboard extends Component
{
    public $totalCompanies;
    public $totalEmployees;
    public $expiredCompanies;

    public $todayRevenue;
    public $monthlyRevenue;
    public $yearlyRevenue;
    public $lifetimeRevenue;
    public $activeServers;
    public $diskPercent;
    public $diskTotalTB;
    public $diskUsedTB;
    public $ramPercent;
    public $TotalRam;
    public $usedRam;
    public $latency;
    public $trafficSpike;
    public $latestBackup;
    public $healthData = [];

    public $recentCompanies = [];
    public $recentEmployees = [];

    public $failedCount = 0;
    public $expiringCount = 0;

    public $planStats = [];



    public function mount()
    {
        // Total companies
        $this->totalCompanies = Company::count();

        // Total employees
        $this->totalEmployees = Employee::count();


        $this->expiredCompanies = Company::where(function ($q) {
            $q->where('subscription_status', 'expired')
                ->orWhereDate('subscription_end', '<', now());
        })->count();


        // Revenue
        $this->todayRevenue = Invoice::whereDate(
            'created_at',
            Carbon::today()
        )->sum('total');

        $this->monthlyRevenue = Invoice::whereYear(
            'created_at',
            Carbon::now()->year
        )->whereMonth(
            'created_at',
            Carbon::now()->month
        )->sum('total');

        $this->yearlyRevenue = Invoice::whereYear(
            'created_at',
            Carbon::now()->year
        )->sum('total');

        $this->lifetimeRevenue = Invoice::sum('total');

        $this->activeServers = Company::where('subscription_status', 'active')->count();




        $diskTotal = disk_total_space('/');
        $diskFree  = disk_free_space('/');

        $diskUsed = $diskTotal - $diskFree;
        $this->diskPercent = round(($diskUsed / $diskTotal) * 100);

        $this->diskTotalTB = round($diskTotal / 1024 / 1024 / 1024 / 1024, 2);
        $this->diskUsedTB  = round($diskUsed  / 1024 / 1024 / 1024 / 1024, 2);




        $memInfo = file('/proc/meminfo');

        $memTotal = intval(str_replace(' kB', '', explode(':', $memInfo[0])[1]));
        $memFree  = intval(str_replace(' kB', '', explode(':', $memInfo[1])[1]));

        $used = $memTotal - $memFree;
        $this->ramPercent = round(($used / $memTotal) * 100);

        $this->TotalRam = round($memTotal / 1024 / 1024, 2);
        $this->usedRam = round($used / 1024 / 1024, 2);




        $this->latency = Cache::remember('network_latency', 60, function () {
            $response = Http::timeout(2)->get('https://google.com');
            return round($response->transferStats->getTransferTime() * 1000);
        });


        $thisHour = now()->startOfHour();
        $lastHour = now()->subHour()->startOfHour();

        $thisHourCount = DB::table('traffic_logs')->where('created_at', '>=', $thisHour)->count();
        $lastHourCount = DB::table('traffic_logs')->whereBetween('created_at', [$lastHour, $thisHour])->count();

        $this->trafficSpike = $thisHourCount > ($lastHourCount * 2)
            ? "Traffic spike detected from UK-Region-1"
            : null;


        $this->latestBackup = DB::table('system_logs')
            ->where('type', 'backup')
            ->latest('created_at')
            ->first();


        $this->recentCompanies = Company::latest('created_at')->take(5)->get();

        $this->recentEmployees = Employee::with('company')
            ->latest('created_at')
            ->take(5)
            ->get();



        $today = Carbon::now();
        $nextWeek = Carbon::now()->addDays(7);

        $this->expiringCount = Company::whereBetween('subscription_end', [$today, $nextWeek])
            ->count();

        $this->failedCount = Company::where('payment_failed_count', '>', 0)->count();

        if ($this->totalCompanies == 0) {
            $this->planStats = [
                'trial' => 0,
                'active' => 0,
                'expired' => 0,
                'suspended' => 0,
            ];
            return;
        }

        $this->planStats = [
            'trial' => round((Company::where('subscription_status', 'trial')->count() / $this->totalCompanies) * 100),
            'active' => round((Company::where('subscription_status', 'active')->count() / $this->totalCompanies) * 100),
            'expired' => round((Company::where('subscription_status', 'expired')->count() / $this->totalCompanies) * 100),
            'suspended' => round((Company::where('subscription_status', 'suspended')->count() / $this->totalCompanies) * 100),
        ];

        $this->updateHealthData();
    }





    public function updateHealthData()
    {
        // Disk
        $diskTotal = disk_total_space('/');
        $diskFree  = disk_free_space('/');
        $diskUsed  = $diskTotal - $diskFree;
        $this->diskPercent = round(($diskUsed / $diskTotal) * 100);

        // RAM
        $memInfo = file('/proc/meminfo');
        $memTotal = intval(str_replace(' kB', '', explode(':', $memInfo[0])[1]));
        $memFree  = intval(str_replace(' kB', '', explode(':', $memInfo[1])[1]));
        $memUsed  = $memTotal - $memFree;
        $this->ramPercent = round(($memUsed / $memTotal) * 100);

        // Latency (cached 60s)
        $this->latency = Cache::remember('network_latency', 60, function () {
            try {
                $response = Http::timeout(5)->get('https://google.com');
                return round($response->transferStats->getTransferTime() * 1000);
            } catch (\Exception $e) {
                return 0;
            }
        });


        $data = [];
        for ($i = 23; $i >= 0; $i--) {
            $time = now()->subHours($i)->format('H:i');
            $data[] = [
                'time' => $time,
                'disk' => round($this->diskPercent + rand(-5, 5), 2),
                'ram' => round($this->ramPercent + rand(-5, 5), 2),
                'latency' => round($this->latency + rand(-50, 50), 0),
            ];
        }

        $this->healthData = $data;

        $this->dispatch('healthDataUpdated', $data);
    }




    public function render()
    {
        return view('livewire.backend.admin.dashboard');
    }
}
