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
    }

    public function render()
    {
        return view('livewire.backend.admin.dashboard');
    }
}
