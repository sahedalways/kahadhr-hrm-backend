<aside
    class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 fixed-end me-4 rotate-caret fixed-start ps ps--active-y"
    id="sidenav-main" data-color="primary">

    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>

        @php
            $authUser = app('authUser');

            $logoHref = match ($authUser->user_type) {
                'company' => route('company.dashboard.index', ['company' => $authUser->company->sub_domain]),
                'employee' => route('employee.dashboard.index', [
                    'company' => $authUser->employee->company->sub_domain,
                ]),
                default => route('super-admin.home'),
            };

            $logoUrl = match ($authUser->user_type) {
                'company' => getCompanyLogoUrl(),
                'employee' => $authUser->employee->company ? getCompanyLogoUrl() : asset(siteSetting()->logo_url),
                default => asset(siteSetting()->logo_url),
            };
        @endphp


        <a class="navbar-brand m-0 flex-column d-flex gap-2" href="{{ $logoHref }}">
            <img src="{{ $logoUrl }}" class="navbar-brand-img h-100 scale-200" alt="main_logo">
            <span class="ms-2 h6 font-weight-bold ">{{ siteSetting()->site_title }} </span>
        </a>
    </div>


    <hr class="horizontal mt-0">
    <div class="collapse navbar-collapse w-auto h-auto h-100" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            @if (app('authUser')->user_type == 'superAdmin')
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}"
                        href="{{ route('super-admin.home') }}">
                        <i class="fa-solid fa-gauge text-info text-sm opacity-10"></i>
                        <span class="nav-link-text ms-1">Dashboard</span>
                    </a>
                </li>




                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#companiesMenu"
                        class="nav-link {{ Request::is('dashboard/companies*') ? 'active' : '' }}"
                        aria-controls="companiesMenu" role="button"
                        aria-expanded="{{ Request::is('dashboard/companies*') ? 'true' : 'false' }}">

                        <i class="fas fa-building text-primary text-sm opacity-10"></i>
                        <span class="nav-link-text ms-1">Manage Companies</span>

                    </a>

                    <div class="collapse {{ Request::is('dashboard/companies*') ? 'show' : '' }}" id="companiesMenu">
                        <ul class="nav ms-4">

                            <!-- Companies -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/companies') ? 'active' : '' }}"
                                    href="{{ route('super-admin.companies') }}">
                                    <i class="fas fa-building sidenav-mini-icon side-bar-inner"></i>
                                    <span class="sidenav-normal side-bar-inner"> Companies </span>
                                </a>
                            </li>

                            <!-- Employees -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/companies/employees') ? 'active' : '' }}"
                                    href="{{ route('super-admin.employees') }}">
                                    <i class="fas fa-user-friends sidenav-mini-icon side-bar-inner"></i>
                                    <span class="sidenav-normal side-bar-inner"> Employees </span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>



                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/billing*') ? 'active' : '' }}"
                        href="{{ route('super-admin.billing') }}">
                        <i class="fas fa-credit-card text-success text-sm opacity-10"></i>
                        <span class="nav-link-text ms-1">Billing & Payments</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/reports*') ? 'active' : '' }}"
                        href="{{ route('super-admin.reports') }}">
                        <i class="fas fa-chart-line text-warning text-sm opacity-10"></i>
                        <span class="nav-link-text ms-1">Reports</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/support*') ? 'active' : '' }}"
                        href="{{ route('super-admin.support') }}">
                        <i class="fas fa-life-ring text-danger text-sm opacity-10"></i>
                        <span class="nav-link-text ms-1">Support Tickets</span>
                    </a>
                </li>




                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/contact-info*') ? 'active' : '' }}"
                        href="{{ route('super-admin.contact-info.index') }}">

                        <i class="fa-solid fa-envelope text-primary text-sm opacity-10"></i>

                        <span class="nav-link-text ms-1">Contact Inquiries</span>

                        @if (isset($unreadContacts) && $unreadContacts > 0)
                            <span class="badge bg-danger ms-auto">{{ $unreadContacts }}</span>
                        @endif
                    </a>
                </li>




                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#settings"
                        class="nav-link {{ Request::is('dashboard/settings*') ? 'active' : '' }}"
                        aria-controls="settings" role="button" aria-expanded="false">
                        <i class="ni ni-single-copy-04 text-danger text-sm opacity-10"></i>
                        <span class="nav-link-text ms-1">System Settings</span>
                    </a>
                    <div class="collapse {{ Request::is('dashboard/settings*') ? 'show' : '' }}" id="settings">
                        <ul class="nav ms-4">

                            <!-- Site Settings -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/settings/site') ? 'active' : '' }}"
                                    href="{{ route('super-admin.settings.site') }}">
                                    <i class="fas fa-cog sidenav-mini-icon side-bar-inner"></i>
                                    <span class="sidenav-normal side-bar-inner"> Site Settings </span>
                                </a>
                            </li>

                            <!-- Mail Settings -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/settings/mail') ? 'active' : '' }}"
                                    href="{{ route('super-admin.settings.mail') }}">
                                    <i class="fas fa-envelope sidenav-mini-icon side-bar-inner"></i>
                                    <span class="sidenav-normal side-bar-inner"> Mail Settings </span>
                                </a>
                            </li>



                            <!-- SMS Settings -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/settings/sms') ? 'active' : '' }}"
                                    href="{{ route('super-admin.settings.sms') }}">
                                    <i class="fas fa-comment-alt sidenav-mini-icon side-bar-inner"></i>
                                    <span class="sidenav-normal side-bar-inner"> SMS Settings </span>
                                </a>
                            </li>




                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/settings/social') ? 'active' : '' }}"
                                    href="{{ route('super-admin.settings.social') }}">
                                    <i class="fab fa-facebook-f sidenav-mini-icon side-bar-inner"></i>

                                    <span class="sidenav-normal side-bar-inner"> Social Settings </span>
                                </a>
                            </li>



                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/settings/charge') ? 'active' : '' }}"
                                    href="{{ route('super-admin.settings.charge') }}">
                                    <i class="fas fa-pound-sign sidenav-mini-icon side-bar-inner"></i>
                                    <span class="sidenav-normal side-bar-inner">Charge Settings</span>
                                </a>
                            </li>




                            <!-- Password Settings -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/settings/password') ? 'active' : '' }}"
                                    href="{{ route('super-admin.settings.password') }}">
                                    <i class="fas fa-lock sidenav-mini-icon side-bar-inner"></i>
                                    <span class="sidenav-normal side-bar-inner"> Password Settings </span>
                                </a>
                            </li>
                        </ul>
                    </div>

                </li>





                <li class="nav-item">
                    <a class="nav-link" wire:click.prevent="logout" href="#">
                        <i class="fa-solid fa-right-from-bracket text-secondary text-sm opacity-10"></i>
                        <span class="nav-link-text ms-1">Logout</span>
                    </a>
                </li>
            @endif


            @if (app('authUser')->user_type == 'company')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('company.dashboard.index') ? 'active' : '' }}"
                        href="{{ route('company.dashboard.index', ['company' => app('authUser')->company->sub_domain]) }}">
                        <i class="fa-solid fa-gauge text-info text-sm opacity-10"></i>
                        <span class="nav-link-text ms-1">Dashboard</span>
                    </a>
                </li>


                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/employees*') ? 'active' : '' }}"
                        href="{{ route('company.dashboard.employees.index', ['company' => app('authUser')->company->sub_domain]) }}">
                        <i class="fas fa-users"></i>
                        <span class="nav-link-text ms-1">Employees</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/departments*') ? 'active' : '' }}"
                        href="{{ route('company.dashboard.departments.index', ['company' => app('authUser')->company->sub_domain]) }}">
                        <i class="fas fa-building"></i>
                        <span class="nav-link-text ms-1">Departments</span>
                    </a>
                </li>


                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/teams*') ? 'active' : '' }}"
                        href="{{ route('company.dashboard.teams.index', ['company' => app('authUser')->company->sub_domain]) }}">
                        <i class="fas fa-users"></i>
                        <span class="nav-link-text ms-1">Teams</span>
                    </a>
                </li>


                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/chat*') ? 'active' : '' }}"
                        href="{{ route('company.dashboard.chat.index', ['company' => app('authUser')->company->sub_domain]) }}">
                        <i class="fas fa-comments"></i>
                        <span class="nav-link-text ms-1">Group / Chat</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/timesheet*') ? 'active' : '' }}"
                        href="{{ route('company.dashboard.timesheet.index', ['company' => app('authUser')->company->sub_domain]) }}">
                        <i class="fas fa-clock"></i>
                        <span class="nav-link-text ms-1">Timesheet</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/schedule*') ? 'active' : '' }}"
                        href="{{ route('company.dashboard.schedule.index', ['company' => app('authUser')->company->sub_domain]) }}">
                        <i class="fas fa-calendar-alt"></i>
                        <span class="nav-link-text ms-1">Schedule</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#leaves"
                        class="nav-link {{ Request::is('dashboard/leaves*') || Request::is('dashboard/leaves-settings*') ? 'active' : '' }}"
                        aria-controls="leaves" role="button"
                        aria-expanded="{{ Request::is('dashboard/leaves*') || Request::is('dashboard/leaves-settings*') ? 'true' : 'false' }}">
                        <i class="fas fa-plane-departure text-primary text-sm opacity-10"></i>
                        <span class="nav-link-text ms-1">Leaves</span>
                    </a>

                    <div class="collapse {{ Request::is('dashboard/leaves*') || Request::is('dashboard/leaves-settings*') ? 'show' : '' }}"
                        id="leaves">
                        <ul class="nav ms-4">

                            <!-- Leave Settings -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/leaves/settings*') ? 'active' : '' }}"
                                    href="{{ route('company.dashboard.leaves.settings', ['company' => app('authUser')->company->sub_domain]) }}">
                                    <i class="fas fa-cogs sidenav-mini-icon"></i>
                                    <span class="sidenav-normal"> Leave Settings </span>
                                </a>
                            </li>

                            <!-- Leave Management -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/leaves/manage') ? 'active' : '' }}"
                                    href="{{ route('company.dashboard.leaves.index', ['company' => app('authUser')->company->sub_domain]) }}">
                                    <i class="fas fa-tasks sidenav-mini-icon"></i>
                                    <span class="sidenav-normal"> Leave Management </span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>


                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#documents"
                        class="nav-link {{ Request::is('dashboard/document-types*') || Request::is('dashboard/document-manage*') ? 'active' : '' }}"
                        aria-controls="documents" role="button"
                        aria-expanded="{{ Request::is('dashboard/document-types*') || Request::is('dashboard/document-manage*') ? 'true' : 'false' }}">
                        <i class="fas fa-file-alt text-primary text-sm opacity-10"></i>
                        <span class="nav-link-text ms-1">Documents</span>
                    </a>

                    <div class="collapse {{ Request::is('dashboard/document-types*') || Request::is('dashboard/document-manage*') ? 'show' : '' }}"
                        id="documents">
                        <ul class="nav ms-4">

                            <!-- Document Types -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/document-types*') ? 'active' : '' }}"
                                    href="{{ route('company.dashboard.document-types.index', ['company' => app('authUser')->company->sub_domain]) }}">
                                    <i class="fas fa-th-list sidenav-mini-icon"></i>
                                    <span class="sidenav-normal"> Document Types </span>
                                </a>
                            </li>

                            <!-- Manage Documents -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/document-manage*') ? 'active' : '' }}"
                                    href="{{ route('company.dashboard.document-manage.index', ['company' => app('authUser')->company->sub_domain]) }}">
                                    <i class="fas fa-folder-open sidenav-mini-icon"></i>
                                    <span class="sidenav-normal"> Manage Documents </span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>



                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/training*') ? 'active' : '' }}"
                        href="{{ route('company.dashboard.training.index', ['company' => app('authUser')->company->sub_domain]) }}">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span class="nav-link-text ms-1">Training</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/onboarding*') ? 'active' : '' }}"
                        href="{{ route('company.dashboard.onboarding.index', ['company' => app('authUser')->company->sub_domain]) }}">
                        <i class="fas fa-user-plus"></i>
                        <span class="nav-link-text ms-1">Onboarding</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#reports"
                        class="nav-link {{ Request::is('dashboard/reports*') ? 'active' : '' }}"
                        aria-controls="reports" role="button" aria-expanded="false">

                        <i class="fas fa-chart-line"></i>
                        <span class="nav-link-text ms-1">Reports</span>
                    </a>

                    <div class="collapse {{ Request::is('dashboard/reports*') ? 'show' : '' }}" id="reports">
                        <ul class="nav ms-4">

                            <!-- Expenses -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/reports/expenses') ? 'active' : '' }}"
                                    href="{{ route('company.dashboard.reports.expenses', ['company' => app('authUser')->company->sub_domain]) }}">
                                    <i class="fas fa-money-bill-wave sidenav-mini-icon"></i>
                                    <span class="sidenav-normal"> Expenses </span>
                                </a>
                            </li>


                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/reports/pay-slips') ? 'active' : '' }}"
                                    href="{{ route('company.dashboard.reports.payslips', ['company' => app('authUser')->company->sub_domain]) }}">
                                    <i class="fas fa-file-invoice-dollar sidenav-mini-icon"></i>
                                    <span class="sidenav-normal"> Pay Slips </span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>





                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#settings"
                        class="nav-link {{ Request::is('dashboard/settings*') ? 'active' : '' }}"
                        aria-controls="settings" role="button" aria-expanded="false">
                        <i class="ni ni-single-copy-04 text-danger text-sm opacity-10"></i>
                        <span class="nav-link-text ms-1">Settings</span>
                    </a>




                    <div class="collapse {{ Request::is('dashboard/settings*') ? 'show' : '' }}" id="settings">
                        <ul class="nav ms-4">


                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/settings/profile') ? 'active' : '' }}"
                                    href="{{ route('company.dashboard.settings.profile', ['company' => app('authUser')->company->sub_domain]) }}">
                                    <i class="fas fa-user sidenav-mini-icon"></i>
                                    <span class="sidenav-normal"> Profile Settings </span>
                                </a>
                            </li>

                            <!-- Bank Info Settings -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/settings/bank-info') ? 'active' : '' }}"
                                    href="{{ route('company.dashboard.settings.bank-info', ['company' => app('authUser')->company->sub_domain]) }}">
                                    <i class="fas fa-university sidenav-mini-icon"></i>
                                    <span class="sidenav-normal"> Bank Info Settings </span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/settings/verification-center') ? 'active' : '' }}"
                                    href="{{ route('company.dashboard.settings.verification-center', ['company' => app('authUser')->company->sub_domain]) }}">
                                    <i class="fas fa-shield-alt sidenav-mini-icon"></i>
                                    <span class="sidenav-normal"> Verification Center </span>
                                </a>
                            </li>


                            {{-- <!-- Mail Settings -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/settings/mail') ? 'active' : '' }}"
                                    href="{{ route('company.dashboard.settings.mail', ['company' => app('authUser')->company->sub_domain]) }}">
                                    <i class="fas fa-envelope sidenav-mini-icon side-bar-inner"></i>
                                    <span class="sidenav-normal side-bar-inner"> Mail Settings </span>
                                </a>
                            </li>
 --}}


                            {{-- <!-- SMS Settings -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/settings/sms') ? 'active' : '' }}"
                                    href="{{ route('company.dashboard.settings.sms', ['company' => app('authUser')->company->sub_domain]) }}">
                                    <i class="fas fa-comment-alt sidenav-mini-icon side-bar-inner"></i>
                                    <span class="sidenav-normal side-bar-inner"> SMS Settings </span>
                                </a>
                            </li> --}}


                            <!-- Calendar Year Settings -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/settings/calendar-year') ? 'active' : '' }}"
                                    href="{{ route('company.dashboard.settings.calendar-year', ['company' => app('authUser')->company->sub_domain]) }}">
                                    <i class="fas fa-calendar-alt sidenav-mini-icon side-bar-inner"></i>
                                    <span class="sidenav-normal side-bar-inner"> Calendar Year Settings </span>
                                </a>
                            </li>




                            <!-- Password Settings -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/settings/password') ? 'active' : '' }}"
                                    href="{{ route('company.dashboard.settings.password', ['company' => app('authUser')->company->sub_domain]) }}">
                                    <i class="fas fa-lock sidenav-mini-icon side-bar-inner"></i>
                                    <span class="sidenav-normal side-bar-inner"> Password Settings </span>
                                </a>
                            </li>



                        </ul>
                    </div>

                </li>





                <li class="nav-item">
                    <a class="nav-link" wire:click.prevent="logout" href="#">
                        <i class="fa-solid fa-right-from-bracket text-secondary text-sm opacity-10"></i>
                        <span class="nav-link-text ms-1">Logout</span>
                    </a>
                </li>
            @endif



            @if (app('authUser')->user_type == 'employee')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('employee.dashboard.index') ? 'active' : '' }}"
                        href="{{ route('employee.dashboard.index', ['company' => app('authUser')->employee->company->sub_domain]) }}">
                        <i class="fa-solid fa-gauge"></i>
                        <span class="nav-link-text ms-1">Dashboard</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('employee/dashboard/profile*') ? 'active' : '' }}"
                        href="{{ route('employee.dashboard.profile.index', ['company' => app('authUser')->employee->company->sub_domain]) }}">
                        <i class="fas fa-user"></i>
                        <span class="nav-link-text ms-1">Profile</span>
                    </a>
                </li>


                <li class="nav-item">
                    <a class="nav-link {{ Request::is('employee/dashboard/chat*') ? 'active' : '' }}"
                        href="{{ route('employee.dashboard.chat.index', ['company' => app('authUser')->employee->company->sub_domain]) }}">
                        <i class="fas fa-comments"></i>
                        <span class="nav-link-text ms-1">Group / Chat</span>
                    </a>
                </li>





                {{-- Clock In --}}
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('employee/dashboard/clock-in*') ? 'active' : '' }}"
                        href="{{ route('employee.dashboard.clockin.index', ['company' => app('authUser')->employee->company->sub_domain]) }}">
                        <i class="fa-regular fa-clock"></i>
                        <span class="nav-link-text ms-1">Clock In</span>
                    </a>
                </li>

                {{-- Schedule --}}
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('employee/dashboard/schedule*') ? 'active' : '' }}"
                        href="{{ route('employee.dashboard.schedule.index', ['company' => app('authUser')->employee->company->sub_domain]) }}">
                        <i class="fa-solid fa-calendar-days"></i>
                        <span class="nav-link-text ms-1">Schedule</span>
                    </a>
                </li>

                {{-- Leaves --}}
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('employee/dashboard/leaves*') ? 'active' : '' }}"
                        href="{{ route('employee.dashboard.leaves.index', ['company' => app('authUser')->employee->company->sub_domain]) }}">
                        <i class="fa-solid fa-umbrella-beach"></i>
                        <span class="nav-link-text ms-1">Leaves</span>
                    </a>
                </li>



                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#documents"
                        class="nav-link {{ Request::is('employee/dashboard/documents*') ? 'active' : '' }}"
                        aria-controls="documents" role="button" aria-expanded="false">
                        <i class="ni ni-folder-17 text-danger text-sm opacity-10"></i>
                        <span class="nav-link-text ms-1">Documents</span>
                    </a>





                    <div class="collapse {{ Request::is('employee/dashboard/documents*') ? 'show' : '' }}"
                        id="documents">
                        <ul class="nav ms-4">

                            {{-- Assigned Documents --}}
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('employee/dashboard/documents/assigned') ? 'active' : '' }}"
                                    href="{{ route('employee.dashboard.documents.assigned', ['company' => app('authUser')->employee->company->sub_domain]) }}">
                                    <i class="fas fa-file-alt sidenav-mini-icon"></i>
                                    <span class="sidenav-normal"> Assigned Documents </span>
                                </a>
                            </li>

                            {{-- Manage Documents --}}
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('employee/dashboard/documents/manage') ? 'active' : '' }}"
                                    href="{{ route('employee.dashboard.documents.manage', ['company' => app('authUser')->employee->company->sub_domain]) }}">
                                    <i class="fas fa-folder-open sidenav-mini-icon"></i>
                                    <span class="sidenav-normal"> Manage Documents </span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>




                {{-- Training --}}
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('employee/dashboard/training*') ? 'active' : '' }}"
                        href="{{ route('employee.dashboard.training.index', ['company' => app('authUser')->employee->company->sub_domain]) }}">
                        <i class="fa-solid fa-chalkboard-teacher"></i>
                        <span class="nav-link-text ms-1">Training</span>
                    </a>
                </li>

                {{-- Onboarding --}}
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('employee/dashboard/onboarding*') ? 'active' : '' }}"
                        href="{{ route('employee.dashboard.onboarding.index', ['company' => app('authUser')->employee->company->sub_domain]) }}">
                        <i class="fa-solid fa-user-check"></i>
                        <span class="nav-link-text ms-1">Onboarding</span>
                    </a>
                </li>



                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#employeeReports"
                        class="nav-link {{ Request::is('employee/dashboard/reports*') ? 'active' : '' }}"
                        aria-controls="employeeReports" role="button"
                        aria-expanded="{{ Request::is('employee/dashboard/reports*') ? 'true' : 'false' }}">
                        <i class="fa-solid fa-chart-line"></i>
                        <span class="nav-link-text ms-1">Reports</span>
                    </a>

                    <div class="collapse {{ Request::is('employee/dashboard/reports*') ? 'show' : '' }}"
                        id="employeeReports">
                        <ul class="nav ms-4">

                            <!-- Expenses -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('employee/dashboard/reports/expenses*') ? 'active' : '' }}"
                                    href="{{ route('employee.dashboard.reports.expenses', ['company' => app('authUser')->employee->company->sub_domain]) }}">
                                    <i class="fa-solid fa-wallet sidenav-mini-icon"></i>
                                    <span class="sidenav-normal"> Expenses </span>
                                </a>
                            </li>

                            <!-- Payslips -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('employee/dashboard/reports/pay-slips*') ? 'active' : '' }}"
                                    href="{{ route('employee.dashboard.reports.payslips', ['company' => app('authUser')->employee->company->sub_domain]) }}">
                                    <i class="fa-solid fa-file-invoice-dollar sidenav-mini-icon"></i>
                                    <span class="sidenav-normal"> Pay Slips </span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>




                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#settings"
                        class="nav-link {{ Request::is('dashboard/settings*') ? 'active' : '' }}"
                        aria-controls="settings" role="button" aria-expanded="false">
                        <i class="ni ni-single-copy-04 text-danger text-sm opacity-10"></i>
                        <span class="nav-link-text ms-1">Settings</span>
                    </a>




                    <div class="collapse {{ Request::is('employee/dashboard/settings*') ? 'show' : '' }}"
                        id="settings">
                        <ul class="nav ms-4">



                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('employee/dashboard/settings/verification-center') ? 'active' : '' }}"
                                    href="{{ route('employee.dashboard.settings.verification-center', ['company' => app('authUser')->employee->company->sub_domain]) }}">
                                    <i class="fas fa-shield-alt sidenav-mini-icon"></i>
                                    <span class="sidenav-normal"> Verification Center </span>
                                </a>
                            </li>



                            <!-- Password Settings -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('employee/dashboard/settings/password') ? 'active' : '' }}"
                                    href="{{ route('employee.dashboard.settings.password', ['company' => app('authUser')->employee->company->sub_domain]) }}">
                                    <i class="fas fa-lock sidenav-mini-icon side-bar-inner"></i>
                                    <span class="sidenav-normal side-bar-inner"> Password Settings </span>
                                </a>
                            </li>



                        </ul>
                    </div>

                </li>




                <li class="nav-item">
                    <a class="nav-link" wire:click.prevent="logout" href="#">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span class="nav-link-text ms-1">Logout</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
    <hr class="horizontal dark mt-2">
</aside>
