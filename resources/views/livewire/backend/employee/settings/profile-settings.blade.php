<div>
    <div class="row align-items-center justify-content-between mb-4">
        <div class="col">
            <h5 class="fw-500 text-dark">Employee Profile</h5>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-lg-5 p-4 shadow-sm border rounded-3 bg-white">
                    <form class="row g-4 align-items-center" wire:submit.prevent="save">

                        <!-- PERSONAL INFORMATION -->
                        <h5 class="fw-bold mb-0">Personal Information</h5>
                        <hr class="mt-2">

                        <!-- First Name -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control shadow-sm" wire:model="f_name"
                                placeholder="Enter First Name">
                            @error('f_name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control shadow-sm" wire:model="l_name"
                                placeholder="Enter Last Name">
                            @error('l_name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Avatar -->
                        {{-- <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Avatar</label>
                            <input type="file" class="form-control shadow-sm" wire:model="avatar" accept="image/*">
                            @error('avatar')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror

                            @if ($avatar)
                                <img src="{{ $avatar->temporaryUrl() }}" class="img-thumbnail mt-2 shadow-sm rounded"
                                    width="90">
                                <div wire:loading wire:target="avatar">
                                    <span class="text-muted small">Uploading...</span>
                                </div>
                            @elseif ($old_avatar)
                                <img src="{{ $old_avatar }}" class="img-thumbnail mt-2 shadow-sm rounded"
                                    width="90">
                            @endif
                        </div> --}}

                        <!-- JOB & DEPARTMENT INFO -->
                        <hr class="mt-4">
                        <h5 class="fw-bold mb-0">Job & Department Info</h5>
                        <hr class="mt-2">

                        <div class="col-md-4">
                            <p><strong>Job Title:</strong> {{ $job_title }}</p>
                        </div>

                        <div class="col-md-4">
                            <p><strong>Department:</strong> {{ $departments[$department_id] ?? 'N/A' }}</p>
                        </div>

                        <div class="col-md-4">
                            <p><strong>Team:</strong> {{ $teams[$team_id] ?? 'N/A' }}</p>
                        </div>

                        <!-- CONTRACT & SALARY -->
                        <hr class="mt-4">
                        <h5 class="fw-bold mb-0">Contract & Salary</h5>
                        <hr class="mt-2">

                        <div class="col-md-4">
                            <p><strong>Salary Type:</strong> {{ ucfirst($salary_type) }}</p>
                        </div>

                        <div class="col-md-4">
                            <p><strong>Employment Type:</strong>
                                {{ $salary_type === 'monthly' ? 'Full-time' : 'Part-time' }}</p>
                        </div>

                        @if ($salary_type === 'hourly')
                            <div class="col-md-4">
                                <p><strong>Contract Hours:</strong> {{ $contract_hours }}</p>
                            </div>
                        @endif

                        <div class="col-md-4">
                            <p><strong>Start Date:</strong> {{ $start_date ?? 'N/A' }}</p>
                        </div>

                        <div class="col-md-4">
                            <p><strong>End Date:</strong> {{ $end_date ?? 'N/A' }}</p>
                        </div>

                        <!-- PROFILE INFORMATION -->
                        <hr class="my-3">
                        <h6 class="fw-bold">Profile Information</h6>

                        <!-- Date of Birth -->
                        <div class="col-md-6">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" wire:model="date_of_birth">
                            @error('date_of_birth')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Gender -->
                        <div class="col-md-6" id="genderDropdownContainer">
                            <label class="form-label">Gender</label>

                            <div style="position:relative;">
                                <!-- Button -->
                                <button class="btn btn-sm w-100 text-start" type="button" id="genderDropdownButton"
                                    style="border:1px solid #ccc; background:#fff;">
                                    {{ $gender ? ucfirst($gender) : 'Select Gender' }}
                                </button>

                                <!-- Dropdown -->
                                <div id="genderDropdownMenu"
                                    style="display:none; position:absolute; z-index:1000; width:100%; max-height:200px; overflow-y:auto; background:#fff; border:1px solid #ccc; border-radius:4px;">


                                    @foreach ($genderOptions as $option)
                                        @if (str_contains(strtolower($option), strtolower($genderSearch ?? '')))
                                            <a href="#" class="dropdown-item"
                                                wire:click.prevent="$set('gender', '{{ $option }}'); closeDropdown('gender')">
                                                {{ ucfirst($option) }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            @error('gender')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <!-- Marital Status -->
                        <div class="col-md-6" id="maritalDropdownContainer">
                            <label class="form-label">Marital Status</label>

                            <div style="position:relative;">
                                <!-- Button -->
                                <button class="btn btn-sm w-100 text-start" type="button" id="maritalDropdownButton"
                                    style="border:1px solid #ccc; background:#fff;">
                                    {{ $marital_status ? ucfirst($marital_status) : 'Select Status' }}
                                </button>

                                <!-- Dropdown -->
                                <div id="maritalDropdownMenu"
                                    style="display:none; position:absolute; z-index:1000; width:100%; max-height:200px; overflow-y:auto; background:#fff; border:1px solid #ccc; border-radius:4px;">


                                    @foreach ($maritalOptions as $option)
                                        @if (str_contains(strtolower($option), strtolower($maritalSearch ?? '')))
                                            <a href="#" class="dropdown-item"
                                                wire:click.prevent="$set('marital_status', '{{ $option }}'); closeDropdown('marital')">
                                                {{ ucfirst($option) }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            @error('marital_status')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <!-- Address -->
                        <div class="col-md-6">
                            <label class="form-label">Street 1</label>
                            <input type="text" class="form-control" wire:model="street_1">
                            @error('street_1')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Street 2</label>
                            <input type="text" class="form-control" wire:model="street_2">
                            @error('street_2')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- State Dropdown -->
                        <div class="col-md-6" id="stateDropdownContainer">
                            <label class="form-label">State</label>
                            <div style="position:relative;">
                                <!-- Button -->
                                <button class="btn btn-sm w-100 text-start" type="button" id="stateDropdownButton"
                                    style="border:1px solid #ccc; background:#fff;">
                                    {{ $state ?? 'Select State' }}
                                </button>

                                <!-- Dropdown -->
                                <div id="stateDropdownMenu"
                                    style="display:none; position:absolute; z-index:1000; width:100%; max-height:200px; overflow-y:auto; background:#fff; border:1px solid #ccc; border-radius:4px;">


                                    @foreach ($locations as $loc)
                                        @if (str_contains(strtolower($loc['state']), strtolower($stateSearch ?? '')))
                                            <a href="#" class="dropdown-item d-flex align-items-center"
                                                wire:click.prevent="$set('state', '{{ $loc['state'] }}'); selectState('{{ $loc['state'] }}'); closeDropdown('state')">
                                                {{ $loc['state'] }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            @error('state')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- City Dropdown -->
                        <div class="col-md-6" id="cityDropdownContainer">
                            <label class="form-label">City</label>
                            <div style="position:relative;">
                                <!-- Button -->
                                <button class="btn btn-sm w-100 text-start" type="button" id="cityDropdownButton"
                                    style="border:1px solid #ccc; background:#fff;"
                                    @if (!$cities) disabled @endif>
                                    {{ $city ?? 'Select City' }}
                                </button>

                                <!-- Dropdown -->
                                <div id="cityDropdownMenu"
                                    style="display:none; position:absolute; z-index:1000; width:100%; max-height:200px; overflow-y:auto; background:#fff; border:1px solid #ccc; border-radius:4px;">


                                    @foreach ($cities as $c)
                                        @if (str_contains(strtolower($c), strtolower($citySearch ?? '')))
                                            <a href="#" class="dropdown-item d-flex align-items-center"
                                                wire:click.prevent="$set('city', '{{ $c }}'); closeDropdown('city')">
                                                {{ $c }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            @error('city')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <div class="col-md-6">
                            <label class="form-label">Postcode</label>
                            <input type="text" class="form-control" wire:model="postcode"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('postcode')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6" id="countryDropdownContainer">
                            <label class="form-label">Country</label>

                            <div style="position:relative;">
                                <!-- Button -->
                                <button class="btn btn-sm w-100 text-start" type="button" id="countryDropdownButton"
                                    style="border:1px solid #ccc; background:#fff;">
                                    {{ $country ?? 'Select Country' }}
                                </button>

                                <!-- Dropdown -->
                                <div id="countryDropdownMenu" wire:ignore.self
                                    style="display:none; position:absolute; z-index:1000; width:100%; max-height:200px; overflow-y:auto; background:#fff; border:1px solid #ccc; border-radius:4px;">
                                    <input type="text" class="form-control mb-2" placeholder="Search country..."
                                        wire:model.live="countrySearch">

                                    @foreach ($filteredCountries as $c)
                                        <a href="#" class="dropdown-item d-flex align-items-center"
                                            wire:click.prevent="$set('country', '{{ $c['name'] }}'); closeDropdown()">

                                            <!-- Flag Image -->
                                            <img src="{{ $c['image'] }}" alt="{{ $c['name'] }}"
                                                style="width:20px; height:15px; margin-right:8px;">

                                            {{ $c['name'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            @error('country')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>



                        <!-- Nationality -->
                        <div class="col-md-6">
                            <label class="form-label">Nationality</label>
                            <input type="text" class="form-control" wire:model="nationality">
                            @error('nationality')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Phones & Email -->
                        <div class="col-md-6">
                            <label class="form-label">Home Phone</label>
                            <input type="text" class="form-control" wire:model="home_phone"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('home_phone')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Personal Email</label>
                            <input type="email" class="form-control" wire:model="personal_email">
                            @error('personal_email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Tax, Immigration, BRP & Passport -->
                        <div class="col-md-6">
                            <label class="form-label">Tax Reference Number</label>
                            <input type="text" class="form-control" wire:model="tax_reference_number"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('tax_reference_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6" id="immigrationDropdownContainer">
                            <label class="form-label">Immigration Status / Visa Type</label>

                            <div style="position:relative;">
                                <!-- Button -->
                                <button class="btn btn-sm w-100 text-start" type="button"
                                    id="immigrationDropdownButton" style="border:1px solid #ccc; background:#fff;">
                                    {{ $immigration_status ?? 'Select Immigration Status / Visa Type' }}
                                </button>

                                <!-- Dropdown -->
                                <div id="immigrationDropdownMenu"
                                    style="display:none; position:absolute; z-index:1000; width:100%; max-height:200px; overflow-y:auto; background:#fff; border:1px solid #ccc; border-radius:4px;">


                                    @foreach ($immigrationOptions as $option)
                                        @if (str_contains(strtolower($option), strtolower($immigrationSearch ?? '')))
                                            <a href="#" class="dropdown-item"
                                                wire:click.prevent="$set('immigration_status', '{{ $option }}'); closeDropdown('immigration')">
                                                {{ $option }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            @error('immigration_status')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">BRP Number</label>
                            <input type="text" class="form-control" wire:model="brp_number"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('brp_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">BRP Expiry Date</label>
                            <input type="date" class="form-control" wire:model="brp_expiry_date">
                            @error('brp_expiry_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Right to Work Expiry Date</label>
                            <input type="date" class="form-control" wire:model="right_to_work_expiry_date">
                            @error('right_to_work_expiry_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Passport Number</label>
                            <input type="text" class="form-control" wire:model="passport_number"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('passport_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Passport Expiry Date</label>
                            <input type="date" class="form-control" wire:model="passport_expiry_date">
                            @error('passport_expiry_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>





                        @if (!empty($customFields) && $customFields->count())
                            <hr>
                            @foreach ($customFields as $field)
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">
                                        {{ $field->name }}
                                        @if ($field->required)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>

                                    @if ($field->type === 'text')
                                        <input type="text" class="form-control"
                                            placeholder="Enter {{ $field->name }}"
                                            wire:model.defer="customValues.{{ $field->id }}">
                                    @elseif($field->type === 'date')
                                        <input type="date" class="form-control" placeholder="{{ $field->name }}"
                                            wire:model.defer="customValues.{{ $field->id }}">
                                    @elseif($field->type === 'textarea')
                                        <textarea class="form-control" placeholder="Enter {{ $field->name }}"
                                            wire:model.defer="customValues.{{ $field->id }}"></textarea>
                                    @elseif($field->type === 'select')
                                        <select class="form-select"
                                            wire:model.defer="customValues.{{ $field->id }}">
                                            <option value="">{{ $field->name }}</option>
                                            @foreach ($field->options ?? [] as $opt)
                                                <option value="{{ $opt }}">{{ $opt }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            @endforeach
                        @endif



                        <!-- SUBMIT -->
                        <hr class="mt-4">
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success px-4 py-2 shadow-sm"
                                wire:loading.attr="disabled" wire:target="save">
                                <span wire:loading wire:target="save">
                                    <i class="fas fa-spinner fa-spin me-2"></i> Saving...
                                </span>
                                <span wire:loading.remove wire:target="save">Save</span>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>




<script>
    document.addEventListener('click', function(e) {
        ['country', 'state', 'city', 'immigration', 'gender', 'marital'].forEach(type => {
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
