<?php

namespace App\Livewire\Backend\Employee\Reports;

use App\Events\NotificationEvent;
use App\Jobs\PayslipRequestNotificationJob;
use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Notification;
use Livewire\WithPagination;
use App\Models\PaySlip;
use App\Models\PaySlipRequest;
use Illuminate\Support\Facades\Auth;

class PayslipIndex extends BaseComponent
{
    use WithPagination;

    public $filterMonth = '';

    public $filterYear = '';
    public $sortOrder = 'desc';
    public $perPage = 20;

    public $loaded;
    public $lastId = null;
    public $hasMore = true;

    public $requests;

    public $month;
    public $year;

    public $request_id;

    public function mount()
    {

        $this->loaded = collect();
        $this->loadMore();
    }

    public function render()
    {
        return view('livewire.backend.employee.reports.payslip-index', [
            'infos' => $this->loaded,
        ]);
    }

    public function loadMore()
    {
        if (!$this->hasMore) return;

        $query = PaySlip::where('user_id', auth()->id());

        if ($this->filterMonth) {
            $query->where('period', 'like', $this->filterMonth . '%');
        }

        if ($this->filterYear) {
            $query->where('period', 'like', '%' . $this->filterYear);
        }

        if ($this->lastId) {
            $query->where('id', $this->sortOrder === 'desc' ? '<' : '>', $this->lastId);
        }

        $items = $query->orderBy('id', $this->sortOrder)
            ->limit($this->perPage)
            ->get();

        if ($items->isEmpty()) {
            $this->hasMore = false;
            return;
        }

        $this->lastId = $this->sortOrder === 'desc'
            ? $items->last()->id
            : $items->first()->id;

        $this->loaded = $this->loaded->merge($items);

        if ($items->count() < $this->perPage) {
            $this->hasMore = false;
        }
    }

    public function resetLoaded()
    {
        $this->loaded = collect();
        $this->lastId = null;
        $this->hasMore = true;
        $this->loadMore();
    }

    public function handleMonthFilter($month)
    {
        $this->filterMonth = $month;
        $this->resetLoaded();
    }

    public function handleYearFilter($year)
    {
        $this->filterYear = $year;
        $this->resetLoaded();
    }

    public function handleSort($value)
    {
        $this->sortOrder = $value;
        $this->resetLoaded();
    }


    public function send()
    {
        $this->validate([
            'month' => 'required|string',
            'year' => 'required|numeric',
        ]);

        $period = $this->month . ' ' . $this->year;
        $companyId = auth()->user()->employee->company_id;
        $userId = Auth::id();


        $exists = PaySlipRequest::where('user_id', $userId)
            ->where('company_id', $companyId)
            ->where('period', $period)
            ->exists();

        if ($exists) {
            $this->toast('You have already requested a payslip for this period!', 'info');
            return;
        }

        // Create new request
        $request = PaySlipRequest::create([
            'user_id' => $userId,
            'company_id' => $companyId,
            'period' => $period,
            'status' => 'pending',
        ]);

        PayslipRequestNotificationJob::dispatch($request->id);


        $submitterName = auth()->user()->full_name;
        $message = "Employee '{$submitterName}' has requested a payslip for {$this->month} {$this->year}.";

        $notification = Notification::create([
            'company_id' => auth()->user()->employee->company_id,
            'user_id' => null,
            'notifiable_id' => $request->id,
            'type' => 'requested_payslip',

            'data' => [
                'message' => $message

            ],
        ]);


        event(new NotificationEvent($notification));

        $this->dispatch('closemodal');
        $this->resetInput();
        $this->resetLoaded();

        $this->toast('Request submitted successfully!', 'success');
    }





    public function editRequest($id)
    {
        $request = PaySlipRequest::find($id);
        if ($request && $request->user_id == Auth::id()) {
            [$this->month, $this->year] = explode(' ', $request->period);
            $this->request_id = $id;
        }
    }

    public function deleteRequest($id)
    {
        $request = PaySlipRequest::find($id);
        if ($request && $request->user_id == Auth::id()) {
            $request->delete();


            $this->toast('Request deleted successfully!', 'success');

            $this->dispatch('closemodal');
            $this->resetInput();
            $this->resetLoaded();
        }
    }

    private function resetInput()
    {
        $this->month = '';
        $this->year = '';
        $this->request_id = null;
    }
}
