<?php

namespace App\Livewire\Backend\Company\Reports;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Employee;
use App\Models\PaySlip;
use App\Models\PaySlipRequest;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class CompanyPayslip extends BaseComponent
{
    use WithPagination, WithFileUploads;

    public $company_id;

    // Filters
    public $filterUser = '';

    public $sortOrder = 'desc';
    public $perPage = 20;

    // Infinite loading
    public $loaded;
    public $lastId = null;
    public $hasMore = true;

    public $employees;

    // Upload fields  
    public $period;
    public $file;
    public $user_id;
    public $payslip_id;


    public $requests;

    public $month;
    public $year;

    public $filterMonth = '';
    public $filterYear = '';

    protected $listeners = [
        'deletePayslip' => 'deletePayslip',
        'deleteRequest' => 'deleteRequest',
    ];

    public function mount()
    {
        $this->company_id = auth()->user()->company->id;

        $this->employees = Employee::where('company_id', $this->company_id)
            ->whereNotNull('user_id')
            ->orderBy('f_name')
            ->get();

        $this->requests = PaySlipRequest::where('company_id', $this->company_id)
            ->where('status', 'pending')
            ->get();

        $this->loaded = collect();
        $this->loadMore();
    }

    public function render()
    {
        return view('livewire.backend.company.reports.company-payslip', [
            'infos' => $this->loaded,
            'employees' => $this->employees,
            'requests' => $this->requests,
        ]);
    }

    // ─── LOAD DATA ───────────────────────────────────────────────────

    public function loadMore()
    {
        if (!$this->hasMore) return;

        $query = PaySlip::where('company_id', $this->company_id);

        if ($this->filterUser) {
            $query->where('user_id', $this->filterUser);
        }


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

    public function handleUserFilter($id)
    {
        $this->filterUser = $id;
        $this->resetLoaded();
    }


    public function handleSort($value)
    {
        $this->sortOrder = $value;
        $this->resetLoaded();
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


    // ─── UPLOAD PAYSLIP ─────────────────────────────────────────────

    public function savePayslip()
    {
        $this->validate([
            'file' => 'required|file|mimes:pdf|max:2048',
            'user_id' => 'required|exists:users,id',
            'month' => 'required|string',
            'year' => 'required|numeric',
        ]);

        $fileName = 'payslip_' . rand(100000, 999999) . '_' . now()->format('YmdHis') . '.pdf';
        $path = $this->file->storeAs('company/payslips', $fileName, 'public');

        PaySlip::create([
            'company_id' => $this->company_id,
            'user_id' => $this->user_id,
            'period' => $this->month . ' ' . $this->year,
            'file_path' => $path,
        ]);


        $this->toast('Payslip uploaded successfully!', 'success');
        $this->dispatch('closemodal');

        $this->resetFields();
        $this->resetLoaded();
    }



    public function editPayslip($id)
    {
        $payslip = PaySlip::find($id);

        if ($payslip) {

            $this->payslip_id = $payslip->id;
            $this->user_id    = $payslip->user_id;
            if ($payslip->period) {
                [$this->month, $this->year] = explode(' ', $payslip->period);
            }


            $this->file = $payslip->file_path;
        }
    }




    public function updatePayslip()
    {
        $payslip = PaySlip::findOrFail($this->payslip_id);


        $isNewUpload = is_object($this->file);

        $this->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|string',
            'year' => 'required|numeric',
            'file'    => $isNewUpload ? 'required|file|mimes:pdf|max:2048' : 'nullable',
        ]);

        $newFilePath = $payslip->file_path;

        if ($isNewUpload) {
            // Upload new file
            $extension = $this->file->getClientOriginalExtension();
            $randomName = rand(10000000, 99999999) . now()->format('is') . '.' . $extension;
            $newFilePath = $this->file->storeAs('company/payslips', $randomName, 'public');

            // Delete old file
            if ($payslip->file_path && file_exists(storage_path('app/public/' . $payslip->file_path))) {
                unlink(storage_path('app/public/' . $payslip->file_path));
            }
        }

        // Update database
        $payslip->update([
            'user_id'   => $this->user_id,
            'period'    => $this->month . ' ' . $this->year,
            'file_path' => $newFilePath,
        ]);

        $this->toast('Payslip updated successfully!', 'success');

        $this->dispatch('closemodal');
        $this->resetFields();
        $this->resetLoaded();
    }


    public function loadRequests()
    {
        $this->requests = PaySlipRequest::where('company_id', $this->company_id)
            ->where('status', 'pending')
            ->get();
    }



    public function uploadRequestFile($requestId, $files)
    {
        $request = PaySlipRequest::find($requestId);

        if (!$request) return;


        $file = is_array($files) ? reset($files) : $files;

        $this->validate([
            'file' => 'file|mimes:pdf|max:2048'
        ]);

        // Generate file name
        $fileName = 'payslip_' . rand(100000, 999999) . '_' . now()->format('YmdHis') . '.pdf';
        $path = $file->storeAs('company/payslips', $fileName, 'public');

        // If a payslip already exists, delete old file
        if ($request->payslip && $request->payslip->file_path && file_exists(storage_path('app/public/' . $request->payslip->file_path))) {
            unlink(storage_path('app/public/' . $request->payslip->file_path));
        }

        // Create or update the payslip
        $payslip = $request->payslip ?? new PaySlip();
        $payslip->company_id = $this->company_id;
        $payslip->user_id = $request->user_id;
        $payslip->period = $request->period;
        $payslip->file_path = $path;
        $payslip->save();


        $request->update([
            'status' => 'uploaded',
            'payslip_id' => $payslip->id,
        ]);

        $this->toast('Payslip uploaded successfully!', 'success');

        // Reload requests to update table
        $this->loadRequests();
    }




    public function resetFields()
    {
        $this->period = '';
        $this->file = null;
        $this->user_id = '';
        $this->year = '';
        $this->month = '';
    }

    // ─── DELETE ─────────────────────────────────────────────────────

    public function deletePayslip($id)
    {
        $ps = PaySlip::find($id);

        if ($ps) {
            if (file_exists(storage_path('app/public/' . $ps->file_path))) {
                unlink(storage_path('app/public/' . $ps->file_path));
            }

            $ps->delete();
            $this->toast('Payslip deleted successfully!', 'success');
        }

        $this->resetLoaded();
    }


    public function deleteRequest($id)
    {
        $request = PaySlipRequest::find($id);
        if ($request) {
            if ($request->payslip && $request->payslip->file_path && file_exists(storage_path('app/public/' . $request->payslip->file_path))) {
                unlink(storage_path('app/public/' . $request->payslip->file_path));
            }


            if ($request->payslip) {
                $request->payslip->delete();
            }


            $request->delete();

            $this->toast('Requested payslip deleted successfully!', 'success');
            $this->loadRequests();
        }
    }
}
