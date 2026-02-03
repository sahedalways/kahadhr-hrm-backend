<?php

namespace App\Livewire\Backend\Components;

use App\Jobs\SendSupportRequestMail;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class SupportFormModal extends BaseComponent
{
    use WithFileUploads;

    public $company_name;
    public $house_number;
    public $email;
    public $mobile;
    public $subject;
    public $description;
    public $attachment;

    protected $rules = [
        'company_name' => 'required|string|max:255',
        'house_number' => 'required|string|max:255',
        'email'        => 'required|email|max:255',
        'mobile'       => 'required|string|max:20',
        'subject'      => 'required|string|max:255',
        'description'  => 'required|string',
        'attachment'   => 'nullable|file|max:5120|mimes:jpg,png,pdf',
    ];



    public function submitSupportRequest()
    {
        $this->validate();


        if (!Storage::disk('public')->exists('support')) {
            Storage::disk('public')->makeDirectory('support');
        }


        $fileName = time() . '_' . $this->attachment->getClientOriginalName();
        $this->attachment->storeAs('support', $fileName, 'public');
        $attachmentUrl = asset('storage/support/' . $fileName);

        $contact = [
            'company_name'   => $this->company_name,
            'house_number'   => $this->house_number,
            'email'          => $this->email,
            'mobile'         => $this->mobile,
            'subject'        => $this->subject,
            'description'    => $this->description,
            'attachment_path' => $attachmentUrl ?? null,
        ];

        // Dispatch job to send email
        SendSupportRequestMail::dispatch($contact);


        $this->toast('Support request submitted successfully!', 'success');

        $this->resetForm();
        $this->dispatch('closemodal');
    }


    public function resetForm()
    {
        $this->reset([
            'company_name',
            'house_number',
            'email',
            'mobile',
            'subject',
            'description',
            'attachment'
        ]);
    }



    public function render()
    {
        return view('livewire.backend.components.support-form-modal');
    }
}
