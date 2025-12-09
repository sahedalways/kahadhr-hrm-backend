<?php

namespace App\Livewire\Backend\Employee\Auth;

use App\Livewire\Backend\Components\BaseComponent;
use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class EmployeeLogin extends BaseComponent
{
    public $email = "emp@xyz.com";
    public $password = "12345678";
    public $company;
    public $rememberMe = false;

    protected $rules = [
        'email'    => 'required|email',
        'password' => 'required',
    ];

    public function mount(Request $request)
    {
        $this->company = $request->route('company') ?? null;


        // Redirect already logged-in employees
        $authUser = app('authUser');
        if ($authUser?->user_type === 'employee' && $authUser->employee?->company?->sub_domain) {
            return redirect()->route(
                'employee.dashboard.index',
                ['company' => $authUser->employee->company->sub_domain]
            );
        }
    }

    public function render()
    {
        return view('livewire.backend.employee.auth.employee-login', ['company' => $this->company])
            ->extends('components.layouts.login_layout')
            ->section('content');
    }

    public function login(AuthRepository $authRepository)
    {
        $this->validate();

        $user = $authRepository->loginEmployee($this->email, $this->password);

        if (!$user) {
            return $this->toast('Invalid Email or Password', 'error');
        }

        // Check company subdomain
        $currentSubdomain = explode('.', request()->getHost())[0];
        $employee = $user->employee()->withoutGlobalScopes()->first();

        if (!$employee || $employee->company?->sub_domain !== $currentSubdomain) {
            return $this->toast('Invalid subdomain for this account.', 'error');
        }



        Auth::loginUsingId($user->id, $this->rememberMe);

        return redirect()->intended(route('employee.dashboard.index', ['company' => $employee->company->sub_domain]));
    }
}
