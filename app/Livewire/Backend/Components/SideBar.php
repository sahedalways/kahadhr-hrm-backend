<?php

namespace App\Livewire\Backend\Components;


use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SideBar extends Component
{

    public function render()
    {
        return view('livewire.backend.components.side-bar');
    }

    public function logout()
    {
        Auth::logout();
        Session::flush();
        return redirect('/');
    }
}