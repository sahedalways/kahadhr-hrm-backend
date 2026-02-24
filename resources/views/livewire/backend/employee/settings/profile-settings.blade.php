<div>
    <div class="container-fluid py-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold text-dark mb-1"> My Profile</h4>

            </div>
        </div>

        <form wire:submit.prevent="save">
            <div class="row g-4">
                <div class="col-lg-8">

                    <div class="d-block d-md-none">

                        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-light bg-opacity-25">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-dark text-uppercase small mb-3 border-bottom pb-2 text-center">
                                    Job Assignment
                                </h6>

                                <div class="mb-3 pb-3 border-bottom">
                                    <label class="d-block text-muted x-small text-uppercase fw-bold mb-1">
                                        Job Title
                                    </label>
                                    <span class="{{ $job_title ? 'fw-bold' : 'fst-italic' }}">
                                        {{ $job_title ?? 'N/A' }}
                                    </span>

                                </div>

                                <div class="row g-3">

                                    <div class="col-6">
                                        <div class="p-3 rounded-3 border border-1 border-light bg-white shadow-sm">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <label
                                                           class="d-block text-muted x-small text-uppercase fw-bold mb-1">
                                                        Employment Status
                                                    </label>
                                                    <span class="badge bg-soft-primary text-primary">
                                                        {{ ucfirst($employment_status ?? 'N/A') }}
                                                    </span>
                                                </div>
                                                <i class="fas fa-briefcase text-muted"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="p-3 rounded-3 border border-1 border-light bg-white shadow-sm">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <label
                                                           class="d-block text-muted x-small text-uppercase fw-bold mb-1">
                                                        Hours/Week
                                                    </label>
                                                    <span class="fw-semibold">{{ $contract_hours ?? '0' }} hrs</span>
                                                </div>
                                                <i class="fas fa-clock text-muted"></i>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Right to Work --}}
                                    <div class="col-12">
                                        @include('livewire.backend.employee.settings.partials.right-to-work-status')
                                    </div>

                                    {{-- Departments --}}
                                    <div class="col-12">
                                        <div class="p-3 rounded-3 border border-1 border-light bg-white shadow-sm">
                                            <label class="d-block text-muted x-small text-uppercase fw-bold mb-1">
                                                Departments
                                            </label>

                                            @php $max = 5; @endphp

                                            @if ($departments->isEmpty())
                                                <span class="text-muted fst-italic">N/A</span>
                                            @else
                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach ($departments->take($showAllDepartments ? $departments->count() : $max) as $department)
                                                        <span
                                                              class="badge rounded-pill bg-light text-dark border px-3 py-2">
                                                            {{ $department->name }}
                                                        </span>
                                                    @endforeach

                                                    @if ($departments->count() > $max)
                                                        <button wire:click="toggleDepartments"
                                                                class="btn btn-sm btn-link text-decoration-none fw-semibold ms-1">
                                                            {{ $showAllDepartments ? 'See less' : 'View more' }}
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Teams --}}
                                    <div class="col-12">
                                        <div class="p-3 rounded-3 border border-1 border-light bg-white shadow-sm">
                                            <label class="d-block text-muted x-small text-uppercase fw-bold mb-1">
                                                Teams
                                            </label>

                                            @php
                                                $assignedTeams = $employee->user ? $employee->user->teams : collect();
                                                $max = 5;
                                            @endphp

                                            @if ($assignedTeams->isEmpty())
                                                <span class="text-muted fst-italic">N/A</span>
                                            @else
                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach ($assignedTeams->take($showAllTeams ? $assignedTeams->count() : $max) as $team)
                                                        @php
                                                            $isLead = $team->team_lead_id === $employee->user_id;
                                                        @endphp
                                                        <span
                                                              class="badge rounded-pill px-3 py-2
    {{ $isLead ? 'bg-soft-primary text-primary' : 'bg-light text-dark border' }}">
                                                            {{ $team->name }}

                                                            @if ($isLead)
                                                                <span class="ms-1 fw-semibold text-dark">★ Leader</span>
                                                            @endif
                                                        </span>
                                                    @endforeach

                                                    @if ($assignedTeams->count() > $max)
                                                        <button wire:click="toggleTeams"
                                                                class="btn btn-sm btn-link text-decoration-none fw-semibold ms-1">
                                                            {{ $showAllTeams ? 'See less' : 'View more' }}
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>





                        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-light bg-opacity-25">
                            <div class="card-body p-4">
                                @if ($employee->profile?->addressHistory)
                                    @php
                                        $addr = $employee->profile->addressHistory;
                                    @endphp

                                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                                        <div class="card-body p-3">
                                            <h6 class="fw-semibold text-muted small mb-2">Previous Address</h6>
                                            <div class="text-dark small"
                                                 style="line-height:1.5;">
                                                {{ $addr->house_no }}, {{ $addr->street }}<br>
                                                {{ $addr->city }}, {{ $addr->state }}, {{ $addr->postcode }}<br>
                                                <span class="text-muted">{{ $addr->country }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif


                                <div class="card border-0 shadow-sm rounded-4 mb-4">
                                    <div class="card-body p-4">
                                        <div
                                             class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                            <h6 class="fw-bold text-dark text-uppercase small mb-0">Emergency Contacts
                                            </h6>

                                            @if ($employee->emergencyContacts->count() < 2)
                                                <button type="button"
                                                        class="btn btn-sm btn-primary d-flex align-items-center px-3 py-1"
                                                        wire:click="openEmergencyContactModal">
                                                    <i class="fas fa-user-plus me-1"></i> Add Contact
                                                </button>
                                            @else
                                                <span class="text-warning small fw-semibold">
                                                    Limit Reached: Max 2 contacts
                                                </span>
                                            @endif
                                        </div>


                                        <div class="table-responsive bg-white shadow-sm border rounded-3">
                                            <table class="table table-hover mb-0 align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="ps-3 py-2 text-muted fw-semibold">Name</th>
                                                        <th class="py-2 text-muted fw-semibold">Relationship</th>
                                                        <th class="py-2 text-muted fw-semibold">Mobile</th>
                                                        <th class="py-2 text-muted fw-semibold">Email</th>
                                                        <th class="py-2 text-muted fw-semibold">Address</th>
                                                        <th class="py-2 text-muted fw-semibold text-center">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($employee->emergencyContacts as $contact)
                                                        <tr class="align-middle">
                                                            <td class="ps-3 fw-semibold">{{ $contact->name }}</td>
                                                            <td>{{ $contact->relationship }}</td>
                                                            <td>{{ $contact->mobile }}</td>
                                                            <td>{{ $contact->email ?? '-' }}</td>
                                                            <td style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                                                title="{{ $contact->address }}">
                                                                {{ $contact->address }}
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="d-flex justify-content-center gap-1">
                                                                    <button class="btn btn-outline-secondary btn-sm p-1"
                                                                            style="width:28px; height:28px; display:flex; align-items:center; justify-content:center;"
                                                                            wire:click.prevent="openEditEmergencyContactModal({{ $contact->id }})">
                                                                        <i class="fas fa-pencil-alt"
                                                                           style="font-size:0.75rem;"></i>
                                                                    </button>

                                                                    <button class="btn btn-outline-danger btn-sm p-1"
                                                                            style="width:28px; height:28px; display:flex; align-items:center; justify-content:center;"
                                                                            onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                                                            wire:click="deleteEmergencyContact({{ $contact->id }})">
                                                                        <i class="fas fa-trash-alt"
                                                                           style="font-size:0.75rem;"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach

                                                    @if ($employee->emergencyContacts->isEmpty())
                                                        <tr>
                                                            <td colspan="6"
                                                                class="text-center py-4 text-muted">No emergency
                                                                contacts
                                                                found.</td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>



                                    <style>
                                        .py-2-5 {
                                            padding-top: 0.65rem;
                                            padding-bottom: 0.65rem;
                                        }

                                        .transition-all {
                                            transition: all 0.3s ease;
                                        }

                                        .btn-primary:hover {
                                            transform: translateY(-2px);
                                            shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
                                        }

                                        #countryDropdownButton,
                                        #stateDropdownButton,
                                        #cityDropdownButton,
                                        #countryDropdownMenu,
                                        #stateDropdownMenu,
                                        #cityDropdownMenu {
                                            box-shadow: none !important;
                                        }
                                    </style>


                                </div>
                            </div>
                        </div>


                        <style>
                            .py-2-5 {
                                padding-top: 0.65rem;
                                padding-bottom: 0.65rem;
                            }

                            .transition-all {
                                transition: all 0.3s ease;
                            }

                            .btn-primary:hover {
                                transform: translateY(-2px);
                                shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
                            }

                            #countryDropdownButton,
                            #stateDropdownButton,
                            #cityDropdownButton,
                            #countryDropdownMenu,
                            #stateDropdownMenu,
                            #cityDropdownMenu {
                                box-shadow: none !important;
                            }
                        </style>


                    </div>



                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-transparent border-bottom-0 pt-4 px-4">
                            <h6 class="fw-bold text-primary text-uppercase mb-0 ls-1"> Personal Information</h6>
                        </div>
                        <div class="card-body p-4 pt-2">
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <label class="form-label small fw-bold text-secondary">Title <span
                                              class="text-danger">*</span></label>
                                    <select class="form-select border-light-subtle shadow-none bg-light bg-opacity-50"
                                            wire:model="title">
                                        <option value="">Select</option>
                                        <option value="Mr">Mr</option>
                                        <option value="Mrs">Mrs</option>
                                        <option value="Ms">Ms</option>
                                    </select>
                                    @error('title')
                                        <span class="text-danger x-small mt-1 d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label small fw-bold text-secondary">First Name <span
                                              class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control border-light-subtle shadow-none bg-light bg-opacity-50"
                                           wire:model="f_name"
                                           placeholder="John">
                                    @error('f_name')
                                        <span class="text-danger x-small mt-1 d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label small fw-bold text-secondary">Last Name <span
                                              class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control border-light-subtle shadow-none bg-light bg-opacity-50"
                                           wire:model="l_name"
                                           placeholder="Doe">
                                    @error('l_name')
                                        <span class="text-danger x-small mt-1 d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- 5. Personal Email -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Personal Email</label>
                                    <input type="email"
                                           class="form-control border-light-subtle shadow-none"
                                           wire:model="personal_email"
                                           placeholder="personal@example.com">
                                </div>



                                <!-- 7. Personal Mobile -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Personal Mobile</label>
                                    <input type="text"
                                           class="form-control border-light-subtle shadow-none"
                                           wire:model="home_phone"
                                           placeholder="+44 ..."
                                           pattern="[0-9]+"
                                           inputmode="numeric"
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    @error('home_phone')
                                        <span class="text-danger x-small">{{ $message }}</span>
                                    @enderror
                                </div>


                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-secondary">Date of Birth <span
                                              class="text-danger">*</span></label>
                                    <input type="date"
                                           class="form-control border-light-subtle shadow-none"
                                           wire:model="date_of_birth">
                                    @error('date_of_birth')
                                        <span class="text-danger x-small mt-1 d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-secondary">Gender</label>
                                    <select class="form-select border-light-subtle shadow-none"
                                            wire:model="gender">
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                    @error('gender')
                                        <span class="text-danger x-small mt-1 d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-secondary">Marital Status</label>
                                    <select class="form-select border-light-subtle shadow-none"
                                            wire:model="marital_status">
                                        <option value="">Select Status</option>
                                        <option value="single">Single</option>
                                        <option value="married">Married</option>
                                    </select>
                                    @error('marital_status')
                                        <span class="text-danger x-small mt-1 d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>


                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-transparent border-bottom-0 pt-4 px-4">
                            <h6 class="fw-bold text-primary text-uppercase mb-0 ls-1"> Current Address Details</h6>
                        </div>
                        <div class="card-body p-4 pt-2">
                            <div class="row g-3"
                                 x-data="addressAutocomplete()">


                                <div class="col-md-6"
                                     id="countryDropdownContainer">
                                    <label class="form-label">Country <span class="text-danger">*</span></label>
                                    <div style="position:relative;">
                                        <button class="btn btn-sm w-100 text-start"
                                                type="button"
                                                id="countryDropdownButton"
                                                style="border:1px solid #ccc; background:#fff;">
                                            {{ $country ?? 'Select Country' }}
                                        </button>

                                        <div id="countryDropdownMenu"
                                             wire:ignore.self
                                             style="display:none; position:absolute; z-index:1000; width:100%; max-height:200px; overflow-y:auto; background:#fff; border:1px solid #ccc; border-radius:4px; padding: 8px;">
                                            <input type="text"
                                                   class="form-control mb-2"
                                                   placeholder="Search country..."
                                                   wire:model.live="countrySearch">

                                            @foreach ($filteredCountries as $c)
                                                <a href="#"
                                                   class="dropdown-item d-flex align-items-center"
                                                   wire:click.prevent="$set('country', '{{ $c['name'] }}'); closeDropdown()">
                                                    <img src="{{ $c['flag'] }}"
                                                         alt="{{ $c['name'] }}"
                                                         style="width:20px; height:15px; margin-right:8px;">
                                                    {{ $c['name'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                    @error('country')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6"
                                     id="stateDropdownContainer">
                                    <label class="form-label">State </label>
                                    <div style="position:relative;">
                                        <button class="btn btn-sm w-100 text-start"
                                                type="button"
                                                id="stateDropdownButton"
                                                style="border:1px solid #ccc; background:#fff;"
                                                @if (empty($states)) disabled @endif>
                                            {{ $state ?? 'Select State' }}
                                        </button>

                                        <div id="stateDropdownMenu"
                                             wire:ignore.self
                                             style="display:none; position:absolute; z-index:1000; width:100%; max-height:200px; overflow-y:auto; background:#fff; border:1px solid #ccc; border-radius:4px; padding: 8px;">
                                            <input type="text"
                                                   class="form-control mb-2"
                                                   placeholder="Search state..."
                                                   wire:model.live="stateSearch">

                                            @forelse ($states as $s)
                                                <a href="#"
                                                   class="dropdown-item"
                                                   wire:click.prevent="$set('state', '{{ $s['name'] }}'); $set('city', null); closeDropdown()">
                                                    {{ $s['name'] }}
                                                </a>
                                            @empty
                                                <span class="dropdown-item text-muted small">Select country
                                                    first</span>
                                            @endforelse
                                        </div>
                                    </div>
                                    @error('state')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6"
                                     id="cityDropdownContainer">
                                    <label class="form-label">City </label>
                                    <div style="position:relative;">
                                        <button class="btn btn-sm w-100 text-start"
                                                type="button"
                                                id="cityDropdownButton"
                                                style="border:1px solid #ccc; background:#fff;"
                                                @if (empty($cities)) disabled @endif>
                                            {{ $city ?? 'Select City' }}
                                        </button>

                                        <div id="cityDropdownMenu"
                                             wire:ignore.self
                                             style="display:none; position:absolute; z-index:1000; width:100%; max-height:200px; overflow-y:auto; background:#fff; border:1px solid #ccc; border-radius:4px; padding: 8px;">
                                            <input type="text"
                                                   class="form-control mb-2"
                                                   placeholder="Search city..."
                                                   wire:model.live="citySearch">

                                            @forelse ($cities as $c)
                                                <a href="#"
                                                   class="dropdown-item"
                                                   wire:click.prevent="$set('city', '{{ $c }}'); closeDropdown()">
                                                    {{ $c }}
                                                </a>
                                            @empty
                                                <span class="dropdown-item text-muted small">Select state
                                                    first</span>
                                            @endforelse
                                        </div>
                                    </div>
                                    @error('city')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>



                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-secondary">Post Code <span
                                              class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control border-light-subtle shadow-none"
                                           wire:model="postcode"
                                           placeholder="1234">
                                    @error('postcode')
                                        <span class="text-danger x-small mt-1 d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row g-3 mt-1">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">House Number
                                        <span class="text-danger">*</span></label>


                                    <input type="text"
                                           class="form-control border-light-subtle shadow-none"
                                           wire:model="house_no"
                                           placeholder="Enter House Number">

                                    @error('house_no')
                                        <span class="text-danger x-small">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary">Street <span
                                              class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control border-light-subtle shadow-none"
                                           wire:model="street"
                                           placeholder="Enter House Number">

                                    @error('street')
                                        <span class="text-danger x-small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>


                    </div>




                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-transparent border-bottom-0 pt-4 px-4">
                            <h6 class="fw-bold text-primary text-uppercase mb-0 ls-1"> Compliance & ID</h6>
                        </div>
                        <div class="card-body p-4 pt-2">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-secondary">Nationality <span
                                              class="text-danger">*</span></label>
                                    <select class="form-select border-light-subtle shadow-none"
                                            wire:model.live="nationality">
                                        @foreach ($nationalities as $nation)
                                            <option value="{{ $nation }}">{{ $nation }}</option>
                                        @endforeach
                                    </select>
                                    @error('nationality')
                                        <span class="text-danger x-small mt-1 d-block">{{ $message }}</span>
                                    @enderror
                                </div>


                                @if ($nationality && $nationality !== 'British')
                                    <div class="col-md-12 ">
                                        <label class="form-label">
                                            Share Code
                                        </label>
                                        <input type="text"
                                               class="form-control"
                                               wire:model.live="share_code"
                                               placeholder="Example: WLE JFZ 6FT">

                                        @error('share_code')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif


                                <div class="col-12">
                                    <label class="form-label small fw-bold text-secondary">National Insurance / Tax Ref
                                        <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control border-light-subtle shadow-none "
                                           wire:model="tax_reference_number"
                                           placeholder="Enter Tax Ref Number">
                                    @error('tax_reference_number')
                                        <span class="text-danger x-small mt-1 d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12 border-top pt-3 mt-3">
                                    <label class="form-label small fw-bold text-secondary">Passport Number <span
                                              class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control border-light-subtle shadow-none"
                                           wire:model="passport_number"
                                           placeholder="Enter Passport Number">
                                    @error('passport_number')
                                        <span class="text-danger x-small mt-1 d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label small fw-bold text-secondary">
                                        Passport Expiry <span class="text-danger">*</span>
                                    </label>

                                    <input type="date"
                                           class="form-control border-light-subtle shadow-none"
                                           wire:model="passport_expiry_date"
                                           min="{{ now()->addDay()->format('Y-m-d') }}">

                                    @error('passport_expiry_date')
                                        <span class="text-danger x-small mt-1 d-block">{{ $message }}</span>
                                    @enderror
                                </div>



                            </div>
                        </div>
                    </div>



                    @if (!empty($customFields) && $customFields->count())
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-transparent border-bottom-0 pt-4 px-4">
                                <h6 class="fw-bold text-primary text-uppercase mb-0 ls-1">More Information</h6>
                            </div>
                            <div class="card-body p-4 pt-2">
                                <div class="row g-3">
                                    @foreach ($customFields as $field)
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">
                                                {{ $field->name }}
                                                @if ($field->required)
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>

                                            @if ($field->type === 'text')
                                                <input type="text"
                                                       class="form-control"
                                                       placeholder="Enter {{ $field->name }}"
                                                       wire:model.defer="customValues.{{ $field->id }}">
                                            @elseif($field->type === 'date')
                                                <input type="date"
                                                       class="form-control"
                                                       placeholder="{{ $field->name }}"
                                                       wire:model.defer="customValues.{{ $field->id }}">
                                            @elseif($field->type === 'textarea')
                                                <textarea class="form-control"
                                                          placeholder="Enter {{ $field->name }}"
                                                          wire:model.defer="customValues.{{ $field->id }}"></textarea>
                                            @elseif($field->type === 'select')
                                                <select class="form-select"
                                                        wire:model.defer="customValues.{{ $field->id }}">
                                                    <option value="">{{ $field->name }}</option>
                                                    @foreach ($field->options ?? [] as $opt)
                                                        <option value="{{ $opt }}">{{ $opt }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif

                                            @error('customValues.' . $field->id)
                                                <span class="text-danger x-small mt-1 d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif


                    <div class="mt-5 pt-4 border-top">
                        <div class="d-flex justify-content-center align-items-center gap-3">

                            <button type="submit"
                                    class="btn btn-primary px-5 py-2-5 rounded-pill shadow-sm d-flex align-items-center fw-bold transition-all"
                                    wire:loading.attr="disabled"
                                    wire:target="save">

                                <span wire:loading.remove
                                      wire:target="save">
                                    <i class="fas fa-check-circle me-2"></i> Save Changes
                                </span>

                                <span wire:loading
                                      wire:target="save">
                                    <span class="spinner-border spinner-border-sm me-2"
                                          role="status"
                                          aria-hidden="true"></span>
                                    Processing...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 d-none d-md-block">

                    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-light bg-opacity-25">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-dark text-uppercase small mb-3 border-bottom pb-2 text-center">
                                Job Assignment
                            </h6>

                            <div class="mb-3 pb-3 border-bottom">
                                <label class="d-block text-muted x-small text-uppercase fw-bold mb-1">
                                    Job Title
                                </label>
                                <span class="{{ $job_title ? 'fw-bold' : 'fst-italic' }}">
                                    {{ $job_title ?? 'N/A' }}
                                </span>

                            </div>

                            <div class="row g-3">

                                <div class="col-6">
                                    <div class="p-3 rounded-3 border border-1 border-light bg-white shadow-sm">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <label class="d-block text-muted x-small text-uppercase fw-bold mb-1">
                                                    Employment Status
                                                </label>
                                                <span class="badge bg-soft-primary text-primary">
                                                    {{ ucfirst($employment_status ?? 'N/A') }}
                                                </span>
                                            </div>
                                            <i class="fas fa-briefcase text-muted"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="p-3 rounded-3 border border-1 border-light bg-white shadow-sm">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <label class="d-block text-muted x-small text-uppercase fw-bold mb-1">
                                                    Hours/Week
                                                </label>
                                                <span class="fw-semibold">{{ $contract_hours ?? '0' }} hrs</span>
                                            </div>
                                            <i class="fas fa-clock text-muted"></i>
                                        </div>
                                    </div>
                                </div>

                                {{-- Right to Work --}}
                                <div class="col-12">
                                    @include('livewire.backend.employee.settings.partials.right-to-work-status')
                                </div>

                                {{-- Departments --}}
                                <div class="col-12">
                                    <div class="p-3 rounded-3 border border-1 border-light bg-white shadow-sm">
                                        <label class="d-block text-muted x-small text-uppercase fw-bold mb-1">
                                            Departments
                                        </label>

                                        @php $max = 5; @endphp

                                        @if ($departments->isEmpty())
                                            <span class="text-muted fst-italic">N/A</span>
                                        @else
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach ($departments->take($showAllDepartments ? $departments->count() : $max) as $department)
                                                    <span
                                                          class="badge rounded-pill bg-light text-dark border px-3 py-2">
                                                        {{ $department->name }}
                                                    </span>
                                                @endforeach

                                                @if ($departments->count() > $max)
                                                    <button wire:click="toggleDepartments"
                                                            class="btn btn-sm btn-link text-decoration-none fw-semibold ms-1">
                                                        {{ $showAllDepartments ? 'See less' : 'View more' }}
                                                    </button>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Teams --}}
                                <div class="col-12">
                                    <div class="p-3 rounded-3 border border-1 border-light bg-white shadow-sm">
                                        <label class="d-block text-muted x-small text-uppercase fw-bold mb-1">
                                            Teams
                                        </label>

                                        @php
                                            $assignedTeams = $employee->user ? $employee->user->teams : collect();
                                            $max = 5;
                                        @endphp

                                        @if ($assignedTeams->isEmpty())
                                            <span class="text-muted fst-italic">N/A</span>
                                        @else
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach ($assignedTeams->take($showAllTeams ? $assignedTeams->count() : $max) as $team)
                                                    @php
                                                        $isLead = $team->team_lead_id === $employee->user_id;
                                                    @endphp
                                                    <span
                                                          class="badge rounded-pill px-3 py-2
    {{ $isLead ? 'bg-soft-primary text-primary' : 'bg-light text-dark border' }}">
                                                        {{ $team->name }}

                                                        @if ($isLead)
                                                            <span class="ms-1 fw-semibold text-dark">★ Leader</span>
                                                        @endif
                                                    </span>
                                                @endforeach

                                                @if ($assignedTeams->count() > $max)
                                                    <button wire:click="toggleTeams"
                                                            class="btn btn-sm btn-link text-decoration-none fw-semibold ms-1">
                                                        {{ $showAllTeams ? 'See less' : 'View more' }}
                                                    </button>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>


                    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-light bg-opacity-25">
                        <div class="card-body p-4">
                            @if ($employee->profile?->addressHistory)
                                @php
                                    $addr = $employee->profile->addressHistory;
                                @endphp

                                <div class="card border-0 shadow-sm rounded-3 mb-4">
                                    <div class="card-body p-3">
                                        <h6 class="fw-semibold text-muted small mb-2">Previous Address</h6>
                                        <div class="text-dark small"
                                             style="line-height:1.5;">
                                            {{ $addr->house_no }}, {{ $addr->street }}<br>
                                            {{ $addr->city }}, {{ $addr->state }}, {{ $addr->postcode }}<br>
                                            <span class="text-muted">{{ $addr->country }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif


                            <div class="card border-0 shadow-sm rounded-4 mb-4">
                                <div class="card-body p-4">
                                    <div
                                         class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                        <h6 class="fw-bold text-dark text-uppercase small mb-0">Emergency Contacts</h6>

                                        @if ($employee->emergencyContacts->count() < 2)
                                            <button type="button"
                                                    class="btn btn-sm btn-primary d-flex align-items-center px-3 py-1"
                                                    wire:click="openEmergencyContactModal">
                                                <i class="fas fa-user-plus me-1"></i> Add Contact
                                            </button>
                                        @else
                                            <span class="text-warning small fw-semibold">
                                                Limit Reached: Max 2 contacts
                                            </span>
                                        @endif
                                    </div>


                                    <div class="table-responsive bg-white shadow-sm border rounded-3">
                                        <table class="table table-hover mb-0 align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="ps-3 py-2 text-muted fw-semibold">Name</th>
                                                    <th class="py-2 text-muted fw-semibold">Relationship</th>
                                                    <th class="py-2 text-muted fw-semibold">Mobile</th>
                                                    <th class="py-2 text-muted fw-semibold">Email</th>
                                                    <th class="py-2 text-muted fw-semibold">Address</th>
                                                    <th class="py-2 text-muted fw-semibold text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($employee->emergencyContacts as $contact)
                                                    <tr class="align-middle">
                                                        <td class="ps-3 fw-semibold">{{ $contact->name }}</td>
                                                        <td>{{ $contact->relationship }}</td>
                                                        <td>{{ $contact->mobile }}</td>
                                                        <td>{{ $contact->email ?? '-' }}</td>
                                                        <td style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                                            title="{{ $contact->address }}">
                                                            {{ $contact->address }}
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="d-flex justify-content-center gap-1">
                                                                <button class="btn btn-outline-secondary btn-sm p-1"
                                                                        style="width:28px; height:28px; display:flex; align-items:center; justify-content:center;"
                                                                        wire:click.prevent="openEditEmergencyContactModal({{ $contact->id }})">
                                                                    <i class="fas fa-pencil-alt"
                                                                       style="font-size:0.75rem;"></i>
                                                                </button>

                                                                <button class="btn btn-outline-danger btn-sm p-1"
                                                                        style="width:28px; height:28px; display:flex; align-items:center; justify-content:center;"
                                                                        onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                                                        wire:click="deleteEmergencyContact({{ $contact->id }})">
                                                                    <i class="fas fa-trash-alt"
                                                                       style="font-size:0.75rem;"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                                @if ($employee->emergencyContacts->isEmpty())
                                                    <tr>
                                                        <td colspan="6"
                                                            class="text-center py-4 text-muted">No emergency contacts
                                                            found.</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>



                                <style>
                                    .py-2-5 {
                                        padding-top: 0.65rem;
                                        padding-bottom: 0.65rem;
                                    }

                                    .transition-all {
                                        transition: all 0.3s ease;
                                    }

                                    .btn-primary:hover {
                                        transform: translateY(-2px);
                                        shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
                                    }

                                    #countryDropdownButton,
                                    #stateDropdownButton,
                                    #cityDropdownButton,
                                    #countryDropdownMenu,
                                    #stateDropdownMenu,
                                    #cityDropdownMenu {
                                        box-shadow: none !important;
                                    }
                                </style>


                            </div>
                        </div>
                    </div>
        </form>
    </div>

    <style>
        .ls-1 {
            letter-spacing: 0.5px;
        }

        .x-small {
            font-size: 0.75rem;
        }

        .bg-soft-primary {
            background-color: rgba(13, 110, 253, 0.1);
        }

        .btn-white {
            background: #fff;
            color: #0d6efd;
            border: none;
        }

        .btn-white:hover {
            background: #f8f9fa;
            color: #0a58ca;
        }

        input::placeholder {
            font-size: 0.85rem;
            color: #adb5bd;
        }
    </style>



    <div wire:ignore.self
         class="modal fade"
         id="addEmergencyContact"
         data-bs-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h6 class="modal-title fw-600">
                        {{ $mode == 'edit' ? 'Edit Emergency Contact' : 'Add Emergency Contact' }}
                    </h6>

                    <button type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="saveContact">
                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   wire:model="name"
                                   placeholder="Enter name">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mobile <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   wire:model="mobile"
                                   placeholder="Enter mobile number"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('mobile')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email"
                                   class="form-control"
                                   wire:model="email"
                                   placeholder="Enter email (optional)">
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address <span class="text-danger">*</span></label>
                            <textarea class="form-control"
                                      wire:model="address"
                                      placeholder="Enter address"></textarea>
                            @error('address')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Relationship <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   wire:model="relationship"
                                   placeholder="Enter relationship">
                            @error('relationship')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">Cancel</button>

                        <button type="submit"
                                class="btn btn-success"
                                wire:loading.attr="disabled"
                                wire:target="saveContact">
                            <span wire:loading
                                  wire:target="saveContact">
                                <i class="fas fa-spinner fa-spin me-2"></i>
                                Saving...
                            </span>
                            <span wire:loading.remove
                                  wire:target="saveContact">
                                {{ $mode == 'edit' ? 'Update' : 'Save' }}
                            </span>
                        </button>

                    </div>
                </form>


            </div>
        </div>
    </div>

</div>





<script>
    document.addEventListener('click', function(e) {
        ['country', 'state', 'city', 'immigration'].forEach(type => {
            const btn = document.getElementById(type + 'DropdownButton');
            const menu = document.getElementById(type + 'DropdownMenu');
            if (btn && menu) {
                if (btn.contains(e.target)) {
                    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
                } else if (!menu.contains(e.target)) {
                    menu.style.display = 'none';
                }
            }
        });
    });
</script>


<script>
    window.addEventListener('show-emergency-modal', () => {
        new bootstrap.Modal(document.getElementById('addEmergencyContact')).show();
    });

    window.addEventListener('hide-emergency-modal', () => {
        bootstrap.Modal.getInstance(document.getElementById('addEmergencyContact')).hide();
    });
</script>
