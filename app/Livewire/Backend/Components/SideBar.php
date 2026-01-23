<?php

namespace App\Livewire\Backend\Components;

use App\Models\Contact;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SideBar extends Component
{
    public $unreadContacts = 0;



    public function mount()
    {
        // Count unread contacts
        $this->unreadContacts = Contact::where('is_read', false)->count();
    }



    public function render()
    {
        return view('livewire.backend.components.side-bar', [
            'unreadContacts' => $this->unreadContacts
        ]);
    }

    public function logout()
    {
        $user = auth()->user();

        if ($user && $user->user_type == 'employee' && $user->employee) {
            $sub = $user->employee->company->sub_domain;
            auth()->logout();
            session()->invalidate();
            session()->regenerateToken();

            return redirect()->route('employee.auth.empLogin', [
                'company' => $sub
            ]);
        }


        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();


        return redirect('/');
    }
}
