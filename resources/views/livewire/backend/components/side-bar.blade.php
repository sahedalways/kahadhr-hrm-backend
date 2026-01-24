<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 fixed-end me-4 rotate-caret fixed-start"
       id="sidenav-main"
       data-color="primary">

    <div class="sidenav-header">
        <div class=" sidenav-toggler-inner-wrapper w-100">
            <a href="javascript:;"
               class="nav-link p-0 w-fitcontent sidenav-toggler">
                {{-- <div class="sidenav-toggler-inner">
                        <i class="sidenav-toggler-line bg-dark"></i>
                        <i class="sidenav-toggler-line bg-dark"></i>
                        <i class="sidenav-toggler-line bg-dark"></i>
                    </div> --}}
                <i class="fa-solid fa-angle-left sidebar-icon"></i>
            </a>
        </div>

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


        <a class="navbar-brand m-0 flex-column d-flex gap-2 text-center p-3"
           href="{{ $logoHref }}">
            <img src="{{ $logoUrl }}"
                 width="100px"
                 class="navbar-brand-img h-100 scale-200 mx-auto"
                 alt="main_logo">
            <span class="mb-0 h6 font-weight-bold ">{{ siteSetting()->site_title }} </span>
        </a>
    </div>



    <div class="collapse navbar-collapse w-auto h-auto"
         id="sidenav-collapse-main">
        <ul class="navbar-nav">
            @if (app('authUser')->user_type == 'superAdmin')
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}"
                       data-bs-toggle="tooltip"
                       data-bs-placement="right"
                       title="Dashboard"
                       data-bs-trigger="manual"
                       href="{{ route('super-admin.home') }}">
                        <i class="fa-solid fa-gauge text-sm opacity-10"></i>
                        <span class="nav-link-text ms-1">Dashboard</span>
                    </a>
                </li>




                <li class="nav-item">
                    <a data-bs-toggle="collapse"
                       href="#companiesMenu"
                       data-bs-toggle="tooltip"
                       data-bs-placement="right"
                       title="Manage Companies"
                       data-bs-trigger="manual"
                       class="nav-link {{ Request::is('dashboard/companies*') ? 'active' : '' }}"
                       aria-controls="companiesMenu"
                       role="button"
                       aria-expanded="{{ Request::is('dashboard/companies*') ? 'true' : 'false' }}">

                        <i class="fa-solid fa-industry text-sm opacity-10"></i>

                        <span class="nav-link-text ms-1">Manage Companies</span>

                    </a>

                    <div class="collapse {{ Request::is('dashboard/companies*') ? 'show' : '' }}"
                         id="companiesMenu">
                        <ul class="nav ms-4">

                            <!-- Companies -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/companies') ? 'active' : '' }}"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="right"
                                   title="Companies"
                                   data-bs-trigger="manual"
                                   href="{{ route('super-admin.companies') }}">
                                    <i class="fas fa-building sidenav-mini-icon side-bar-inner"></i>
                                    <span class="sidenav-normal side-bar-inner"> Companies </span>
                                </a>
                            </li>

                            <!-- Employees -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/companies/employees') ? 'active' : '' }}"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="right"
                                   title="Employees"
                                   data-bs-trigger="manual"
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
                       data-bs-toggle="tooltip"
                       data-bs-placement="right"
                       title="Billing & Payments"
                       data-bs-trigger="manual"
                       href="{{ route('super-admin.billing') }}">
                        <i class="fas fa-credit-card text-sm opacity-10"></i>
                        <span class="nav-link-text ms-1">Billing & Payments</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/reports*') ? 'active' : '' }}"
                       data-bs-toggle="tooltip"
                       data-bs-placement="right"
                       title="Reports"
                       data-bs-trigger="manual"
                       href="{{ route('super-admin.reports') }}">
                        <i class="fas fa-chart-line text-sm opacity-10"></i>
                        <span class="nav-link-text ms-1">Reports</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/support*') ? 'active' : '' }}"
                       data-bs-toggle="tooltip"
                       data-bs-placement="right"
                       title="Support Tickets"
                       data-bs-trigger="manual"
                       href="{{ route('super-admin.support') }}">
                        <i class="fas fa-life-ring text-sm opacity-10"></i>
                        <span class="nav-link-text ms-1">Support Tickets</span>
                    </a>
                </li>




                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/contact-info*') ? 'active' : '' }}"
                       data-bs-toggle="tooltip"
                       data-bs-placement="right"
                       title="Contact Inquiries"
                       data-bs-trigger="manual"
                       href="{{ route('super-admin.contact-info.index') }}">

                        <i class="fa-solid fa-envelope text-sm opacity-10"></i>

                        <span class="nav-link-text ms-1">Contact Inquiries</span>

                        @if (isset($unreadContacts) && $unreadContacts > 0)
                            <span class="badge bg-danger ms-auto">{{ $unreadContacts }}</span>
                        @endif
                    </a>
                </li>




                <li class="nav-item">
                    <a data-bs-toggle="collapse"
                       href="#settings"
                       class="nav-link {{ Request::is('dashboard/settings*') ? 'active' : '' }}"
                       data-bs-toggle="tooltip"
                       data-bs-placement="right"
                       title="System Settings"
                       data-bs-trigger="manual"
                       aria-controls="settings"
                       role="button"
                       aria-expanded="false">
                        <i class="ni ni-single-copy-04 text-sm opacity-10"></i>
                        <span class="nav-link-text ms-1">System Settings</span>
                    </a>
                    <div class="collapse {{ Request::is('dashboard/settings*') ? 'show' : '' }}"
                         id="settings">
                        <ul class="nav ms-4">

                            <!-- Site Settings -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/settings/site') ? 'active' : '' }}"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="right"
                                   title="Site Settings"
                                   data-bs-trigger="manual"
                                   href="{{ route('super-admin.settings.site') }}">
                                    <i class="fas fa-cog sidenav-mini-icon side-bar-inner"></i>
                                    <span class="sidenav-normal side-bar-inner"> Site Settings </span>
                                </a>
                            </li>

                            <!-- Mail Settings -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/settings/mail') ? 'active' : '' }}"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="right"
                                   title="Mail Settings"
                                   data-bs-trigger="manual"
                                   href="{{ route('super-admin.settings.mail') }}">
                                    <i class="fas fa-envelope sidenav-mini-icon side-bar-inner"></i>
                                    <span class="sidenav-normal side-bar-inner"> Mail Settings </span>
                                </a>
                            </li>



                            <!-- SMS Settings -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/settings/sms') ? 'active' : '' }}"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="right"
                                   title="SMS Settings"
                                   data-bs-trigger="manual"
                                   href="{{ route('super-admin.settings.sms') }}">
                                    <i class="fas fa-comment-alt sidenav-mini-icon side-bar-inner"></i>
                                    <span class="sidenav-normal side-bar-inner"> SMS Settings </span>
                                </a>
                            </li>




                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/settings/social') ? 'active' : '' }}"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="right"
                                   title="Social Settings"
                                   data-bs-trigger="manual"
                                   href="{{ route('super-admin.settings.social') }}">
                                    <i class="fab fa-facebook-f sidenav-mini-icon side-bar-inner"></i>

                                    <span class="sidenav-normal side-bar-inner"> Social Settings </span>
                                </a>
                            </li>



                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/settings/charge') ? 'active' : '' }}"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="right"
                                   title="Charge Settings"
                                   data-bs-trigger="manual"
                                   href="{{ route('super-admin.settings.charge') }}">
                                    <i class="fas fa-pound-sign sidenav-mini-icon side-bar-inner"></i>
                                    <span class="sidenav-normal side-bar-inner">Charge Settings</span>
                                </a>
                            </li>




                            <!-- Password Settings -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/settings/password') ? 'active' : '' }}"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="right"
                                   title="Password Settings"
                                   data-bs-trigger="manual"
                                   href="{{ route('super-admin.settings.password') }}">
                                    <i class="fas fa-lock sidenav-mini-icon side-bar-inner"></i>
                                    <span class="sidenav-normal side-bar-inner"> Password Settings </span>
                                </a>
                            </li>
                        </ul>
                    </div>

                </li>





                <li class="nav-item">
                    <a class="nav-link"
                       wire:click.prevent="logout"
                       href="#"
                       data-bs-toggle="tooltip"
                       data-bs-placement="right"
                       title="Logout"
                       data-bs-trigger="manual">
                        <i class="fa-solid fa-right-from-bracket text-sm opacity-10"></i>
                        <span class="nav-link-text ms-1">Logout</span>
                    </a>
                </li>
            @endif


            @if (app('authUser')->user_type == 'company')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('company.dashboard.index') ? 'active' : '' }}"
                       href="{{ route('company.dashboard.index', ['company' => app('authUser')->company->sub_domain]) }}"
                       data-bs-toggle="tooltip"
                       data-bs-placement="right"
                       title="Dashboard"
                       data-bs-trigger="manual">

                        <i class="fa-solid fa-gauge text-info text-sm opacity-10"></i>
                        <span class="nav-link-text ms-1">Dashboard</span>
                    </a>


                </li>


                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/employees*') ? 'active' : '' }}"
                       href="{{ route('company.dashboard.employees.index', ['company' => app('authUser')->company->sub_domain]) }}"
                       data-bs-toggle="tooltip"
                       data-bs-placement="right"
                       title="Employees"
                       data-bs-trigger="manual">
                        <i class="fas fa-users"></i>
                        <span class="nav-link-text ms-1">Employees</span>
                    </a>
                </li>



                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/teams-departments*') ? 'active' : '' }}"
                       href="{{ route('company.dashboard.teams-departments.index', ['company' => app('authUser')->company->sub_domain]) }}"
                       data-bs-toggle="tooltip"
                       data-bs-placement="right"
                       title="Teams & Departments"
                       data-bs-trigger="manual">
                        <i class="fas fa-sitemap"></i>
                        <span class="nav-link-text ms-1">Teams & Departments</span>
                    </a>
                </li>



                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/chat*') ? 'active' : '' }}"
                       href="{{ route('company.dashboard.chat.index', ['company' => app('authUser')->company->sub_domain]) }}"
                       data-bs-toggle="tooltip"
                       data-bs-placement="right"
                       title="Team Chat"
                       data-bs-trigger="manual">
                        <i class="fas fa-comments"></i>
                        <span class="nav-link-text ms-1">Team Chat</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/timesheet*') ? 'active' : '' }}"
                       href="{{ route('company.dashboard.timesheet.index', ['company' => app('authUser')->company->sub_domain]) }}"
                       data-bs-toggle="tooltip"
                       data-bs-placement="right"
                       title="Timesheet"
                       data-bs-trigger="manual">
                        <i class="fas fa-clock"></i>
                        <span class="nav-link-text ms-1">Timesheet</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/schedule*') ? 'active' : '' }}"
                       href="{{ route('company.dashboard.schedule.index', ['company' => app('authUser')->company->sub_domain]) }}"
                       data-bs-toggle="tooltip"
                       data-bs-placement="right"
                       title="Schedule"
                       data-bs-trigger="manual">
                        <i class="fas fa-calendar-alt"></i>
                        <span class="nav-link-text ms-1">Schedule</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#leaves"
                       class="nav-link {{ Request::is('dashboard/leaves*') || Request::is('dashboard/leaves-settings*') ? 'active' : '' }}"
                       data-bs-toggle="collapse"
                       aria-controls="leaves"
                       role="button"
                       aria-expanded="{{ Request::is('dashboard/leaves*') || Request::is('dashboard/leaves-settings*') ? 'true' : 'false' }}">

                        <!-- Tooltip wrapper -->
                        <span class="tooltip-wrapper"
                              title="Leaves">
                            <i class="fas fa-plane-departure text-sm opacity-10"></i>
                        </span>

                        <span class="nav-link-text ms-1">Leaves</span>
                    </a>

                    <div class="collapse {{ Request::is('dashboard/leaves*') || Request::is('dashboard/leaves-settings*') ? 'show' : '' }}"
                         id="leaves">
                        <ul class="nav ms-4">

                            <!-- Leave Settings -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/leaves/settings*') ? 'active' : '' }}"
                                   href="{{ route('company.dashboard.leaves.settings', ['company' => app('authUser')->company->sub_domain]) }}">
                                    <span class="tooltip-wrapper"
                                          title="Leave Settings">
                                        <i class="fas fa-cogs sidenav-mini-icon"></i>
                                    </span>
                                    <span class="sidenav-normal">Leave Settings</span>
                                </a>
                            </li>

                            <!-- Leave Management -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/leaves/manage') ? 'active' : '' }}"
                                   href="{{ route('company.dashboard.leaves.index', ['company' => app('authUser')->company->sub_domain]) }}">
                                    <span class="tooltip-wrapper"
                                          title="Leave Management">
                                        <i class="fas fa-tasks sidenav-mini-icon"></i>
                                    </span>
                                    <span class="sidenav-normal">Leave Management</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>




                <li class="nav-item">
                    <a data-bs-toggle="collapse"
                       href="#documents"
                       class="nav-link {{ Request::is('dashboard/document-types*') || Request::is('dashboard/document-manage*') ? 'active' : '' }}"
                       aria-controls="documents"
                       role="button"
                       aria-expanded="{{ Request::is('dashboard/document-types*') || Request::is('dashboard/document-manage*') ? 'true' : 'false' }}"
                       data-bs-toggle="tooltip"
                       data-bs-placement="right"
                       title="Documents"
                       data-bs-trigger="manual">
                        <i class="fas fa-file-alt text-sm opacity-10"></i>
                        <span class="nav-link-text ms-1">Documents</span>
                    </a>

                    <div class="collapse {{ Request::is('dashboard/document-types*') || Request::is('dashboard/document-manage*') ? 'show' : '' }}"
                         id="documents">
                        <ul class="nav ms-4">

                            <!-- Document Types -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/document-types*') ? 'active' : '' }}"
                                   href="{{ route('company.dashboard.document-types.index', ['company' => app('authUser')->company->sub_domain]) }}"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="right"
                                   title="Document Types"
                                   data-bs-trigger="manual">
                                    <i class="fas fa-th-list sidenav-mini-icon"></i>
                                    <span class="sidenav-normal"> Document Types </span>
                                </a>
                            </li>

                            <!-- Manage Documents -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/document-manage/assigned*') ? 'active' : '' }}"
                                   href="{{ route('company.dashboard.document-manage.index', ['company' => app('authUser')->company->sub_domain]) }}"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="right"
                                   title="Documents By Assiged"
                                   data-bs-trigger="manual">
                                    <i class="fas fa-folder-open sidenav-mini-icon"></i>
                                    <span class="sidenav-normal"> Documents By Assiged</span>
                                </a>
                            </li>


                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/document-manage/types*') ? 'active' : '' }}"
                                   href="{{ route('company.dashboard.document-manage.types.index', ['company' => app('authUser')->company->sub_domain]) }}"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="right"
                                   title="Documents By Type"
                                   data-bs-trigger="manual">
                                    <i class="fas fa-file-alt sidenav-mini-icon"></i>
                                    <span class="sidenav-normal"> Documents By Type</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>



                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/training*') ? 'active' : '' }}"
                       href="{{ route('company.dashboard.training.index', ['company' => app('authUser')->company->sub_domain]) }}"
                       data-bs-toggle="tooltip"
                       data-bs-placement="right"
                       title="Training"
                       data-bs-trigger="manual">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span class="nav-link-text ms-1">Training</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('dashboard/onboarding*') ? 'active' : '' }}"
                       href="{{ route('company.dashboard.onboarding.index', ['company' => app('authUser')->company->sub_domain]) }}"
                       data-bs-toggle="tooltip"
                       data-bs-placement="right"
                       title="Onboarding"
                       data-bs-trigger="manual">
                        <i class="fas fa-user-plus"></i>
                        <span class="nav-link-text ms-1">Onboarding</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a data-bs-toggle="collapse"
                       href="#reports"
                       class="nav-link {{ Request::is('dashboard/reports*') ? 'active' : '' }}"
                       aria-controls="reports"
                       role="button"
                       aria-expanded="{{ Request::is('dashboard/reports*') ? 'true' : 'false' }}"
                       data-bs-placement="right"
                       title="Reports"
                       data-bs-trigger="manual">
                        <i class="fas fa-chart-line"></i>
                        <span class="nav-link-text ms-1">Reports</span>
                    </a>
                    <div class="collapse {{ Request::is('dashboard/reports*') ? 'show' : '' }}"
                         id="reports">
                        <ul class="nav ms-4">

                            <!-- Employee Profile -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/reports/employee-profile*') ? 'active' : '' }}"
                                   href="{{ route('company.dashboard.reports.employee-profile', ['company' => app('authUser')->company->sub_domain]) }}"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="right"
                                   title="Employee Profile"
                                   data-bs-trigger="manual">
                                    <i class="fas fa-id-card sidenav-mini-icon"></i>
                                    <span class="sidenav-normal"> Employee Profile </span>
                                </a>
                            </li>

                            <!-- Timesheet -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/reports/timesheet*') ? 'active' : '' }}"
                                   href="{{ route('company.dashboard.reports.timesheet', ['company' => app('authUser')->company->sub_domain]) }}"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="right"
                                   title="Timesheet"
                                   data-bs-trigger="manual">
                                    <i class="fas fa-clock sidenav-mini-icon"></i>
                                    <span class="sidenav-normal"> Timesheet </span>
                                </a>
                            </li>

                            <!-- Leaves -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/reports/leaves*') ? 'active' : '' }}"
                                   href="{{ route('company.dashboard.reports.leaves', ['company' => app('authUser')->company->sub_domain]) }}"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="right"
                                   title="Leaves"
                                   data-bs-trigger="manual">
                                    <i class="fas fa-calendar-check sidenav-mini-icon"></i>
                                    <span class="sidenav-normal"> Leaves </span>
                                </a>
                            </li>

                            <!-- Expenses -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/reports/expenses') ? 'active' : '' }}"
                                   href="{{ route('company.dashboard.reports.expenses', ['company' => app('authUser')->company->sub_domain]) }}"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="right"
                                   title="Expenses"
                                   data-bs-trigger="manual">
                                    <i class="fas fa-money-bill-wave sidenav-mini-icon"></i>
                                    <span class="sidenav-normal"> Expenses </span>
                                </a>
                            </li>

                            <!-- Payslips -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('dashboard/reports/pay-slips') ? 'active' : '' }}"
                                   href="{{ route('company.dashboard.reports.payslips', ['company' => app('authUser')->company->sub_domain]) }}"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="right"
                                   title="PaySlips"
                                   data-bs-trigger="manual">
                                    <i class="fas fa-file-invoice-dollar sidenav-mini-icon"></i>
                                    <span class="sidenav-normal"> PaySlips </span>
                                </a>
                            </li>

                        </ul>
                    </div>

                </li>
            @endif



            @if (app('authUser')->user_type == 'employee')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('employee.dashboard.index') ? 'active' : '' }} data-bs-toggle="tooltip"
                       data-bs-placement="right"
                       title="Dashboard"
                       data-bs-trigger="manual""
                       href="{{ route('employee.dashboard.index', ['company' => app('authUser')->employee->company->sub_domain]) }}">
                        <i class="fa-solid fa-gauge"></i>
                        <span class="nav-link-text ms-1">Dashboard</span>
                    </a>
                </li>



                <li class="nav-item">
                    <a class="nav-link {{ Request::is('employee/dashboard/chat*') ? 'active' : '' }}"
                       href="{{ route('employee.dashboard.chat.index', ['company' => app('authUser')->employee->company->sub_domain]) }}"
                       data-bs-toggle="tooltip"
                       data-bs-placement="right"
                       title="Team Chat"
                       data-bs-trigger="manual">
                        <i class="fas fa-comments"></i>
                        <span class="nav-link-text ms-1">Team Chat</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('employee/dashboard/clock-in-out-history*') ? 'active' : '' }}"
                       href="{{ route('employee.dashboard.clock.index', ['company' => app('authUser')->employee->company->sub_domain]) }}"
                       data-bs-toggle="tooltip"
                       data-bs-placement="right"
                       title="Clock In/Out"
                       data-bs-trigger="manual">
                        <i class="fa-solid fa-clock"></i>
                        <span class="nav-link-text ms-1">Clock In/Out</span>
                    </a>
                </li>



                {{-- Schedule --}}
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('employee/dashboard/schedule*') ? 'active' : '' }}"
                       href="{{ route('employee.dashboard.schedule.index', ['company' => app('authUser')->employee->company->sub_domain]) }}"
                       data-bs-toggle="tooltip"
                       data-bs-placement="right"
                       title="Schedule"
                       data-bs-trigger="manual">
                        <i class="fa-solid fa-calendar-days"></i>
                        <span class="nav-link-text ms-1">Schedule</span>
                    </a>
                </li>

                {{-- Leaves --}}
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('employee/dashboard/leaves*') ? 'active' : '' }}"
                       href="{{ route('employee.dashboard.leaves.index', ['company' => app('authUser')->employee->company->sub_domain]) }}"
                       data-bs-toggle="tooltip"
                       data-bs-placement="right"
                       title="Leaves"
                       data-bs-trigger="manual">
                        <i class="fa-solid fa-umbrella-beach"></i>
                        <span class="nav-link-text ms-1">Leaves</span>
                    </a>
                </li>



                <li class="nav-item">
                    <a data-bs-toggle="collapse"
                       href="#documents"
                       class="nav-link {{ Request::is('employee/dashboard/documents*') ? 'active' : '' }}"
                       data-bs-toggle="tooltip"
                       data-bs-placement="right"
                       title="Documents"
                       data-bs-trigger="manual"
                       aria-controls="documents"
                       role="button"
                       aria-expanded="false">
                        <i class="ni ni-folder-17 text-danger text-sm opacity-10"></i>

                        <span class="nav-link-text ms-1">Documents</span>
                    </a>





                    <div class="collapse {{ Request::is('employee/dashboard/documents*') ? 'show' : '' }}"
                         id="documents">
                        <ul class="nav ms-4">

                            {{-- Assigned Documents --}}
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('employee/dashboard/documents/assigned') ? 'active' : '' }}"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="right"
                                   title="Assigned Documents"
                                   data-bs-trigger="manual"
                                   href="{{ route('employee.dashboard.documents.assigned', ['company' => app('authUser')->employee->company->sub_domain]) }}">
                                    <i class="fas fa-file-alt sidenav-mini-icon"></i>
                                    <span class="sidenav-normal"> Assigned Documents </span>
                                </a>
                            </li>

                            {{-- Manage Documents --}}
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('employee/dashboard/documents/manage') ? 'active' : '' }}"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="right"
                                   title="Manage Documents"
                                   data-bs-trigger="manual"
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
                       data-bs-toggle="tooltip"
                       data-bs-placement="right"
                       title="Training"
                       data-bs-trigger="manual"
                       href="{{ route('employee.dashboard.training.index', ['company' => app('authUser')->employee->company->sub_domain]) }}">
                        <i class="fa-solid fa-chalkboard-teacher"></i>
                        <span class="nav-link-text ms-1">Training</span>
                    </a>
                </li>

                {{-- Onboarding --}}
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('employee/dashboard/onboarding*') ? 'active' : '' }}"
                       data-bs-toggle="tooltip"
                       data-bs-placement="right"
                       title="Onboarding"
                       data-bs-trigger="manual"
                       href="{{ route('employee.dashboard.onboarding.index', ['company' => app('authUser')->employee->company->sub_domain]) }}">
                        <i class="fa-solid fa-user-check"></i>
                        <span class="nav-link-text ms-1">Onboarding</span>
                    </a>
                </li>



                <li class="nav-item">
                    <a data-bs-toggle="collapse"
                       href="#employeeReports"
                       data-bs-toggle="tooltip"
                       data-bs-placement="right"
                       title="Reports"
                       data-bs-trigger="manual"
                       class="nav-link {{ Request::is('employee/dashboard/reports*') ? 'active' : '' }}"
                       aria-controls="employeeReports"
                       role="button"
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
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="right"
                                   title="Expenses"
                                   data-bs-trigger="manual"
                                   href="{{ route('employee.dashboard.reports.expenses', ['company' => app('authUser')->employee->company->sub_domain]) }}">
                                    <i class="fa-solid fa-wallet sidenav-mini-icon"></i>
                                    <span class="sidenav-normal"> Expenses </span>
                                </a>
                            </li>

                            <!-- Payslips -->
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('employee/dashboard/reports/pay-slips*') ? 'active' : '' }}"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="right"
                                   title="PaySlips"
                                   data-bs-trigger="manual"
                                   href="{{ route('employee.dashboard.reports.payslips', ['company' => app('authUser')->employee->company->sub_domain]) }}">
                                    <i class="fa-solid fa-file-invoice-dollar sidenav-mini-icon"></i>
                                    <span class="sidenav-normal"> PaySlips </span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>
            @endif
        </ul>
    </div>
    <hr class="horizontal dark mt-2">

    <script>
        if (document.querySelector('.navbar-vertical')) {
            document.querySelector('.navbar-vertical').classList.remove('ps');
        }
    </script>

    <style>
        .navbar-vertical {
            overflow-y: auto !important;
            overflow-x: hidden !important;
        }

        .ps__rail-x,
        .ps__rail-y {
            display: none !important;
        }

        /* Normal expanded sidebar */
        .navbar-vertical {
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Collapsed sidebar */
        .g-sidenav-show.g-sidenav-pinned .navbar-vertical {
            max-width: 4rem !important;
            overflow: hidden !important;
        }

        /* Prevent children from overflowing */
        .g-sidenav-show.g-sidenav-pinned .navbar-vertical *,
        .g-sidenav-show.g-sidenav-pinned .navbar-vertical .navbar-collapse,
        .g-sidenav-show.g-sidenav-pinned .navbar-vertical .navbar-nav {
            overflow: hidden !important;
            white-space: nowrap;
        }
    </style>
</aside>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const body = document.body;

        // Select all elements with tooltip: either title directly, or inside .tooltip-wrapper
        const tooltipElements = document.querySelectorAll('[title], .tooltip-wrapper');

        function updateTooltips() {
            tooltipElements.forEach(el => {
                if (body.classList.contains('g-sidenav-pinned')) {
                    if (!el._tooltip) {
                        el._tooltip = new bootstrap.Tooltip(el, {
                            trigger: 'hover',
                            placement: 'right',
                            delay: {
                                show: 150,
                                hide: 100
                            }
                        });
                    }
                } else {
                    if (el._tooltip) {
                        el._tooltip.dispose();
                        el._tooltip = null;
                    }
                }
            });
        }

        // Initial
        updateTooltips();

        // Watch sidebar toggle
        new MutationObserver(updateTooltips).observe(body, {
            attributes: true,
            attributeFilter: ['class']
        });
    });
</script>
