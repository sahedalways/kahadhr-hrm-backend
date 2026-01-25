<div>
    <div class="row g-3 align-items-center justify-content-between mb-4">
        <div class="col">
            <h5 class="fw-500 text-dark">Employee Profile</h5>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-lg-5 p-4 shadow-sm border rounded-3 bg-white">
                    <form class="row g-4 align-items-center"
                          wire:submit.prevent="save">

                        <!-- PERSONAL INFORMATION -->
                        <h5 class="fw-bold mb-0">Personal Information</h5>
                        <hr class="mt-2">
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold text-secondary">Title <span
                                      class="text-danger">*</span></label>
                            <select class="form-select border-light-subtle shadow-none"
                                    wire:model="title">
                                <option value="">Select</option>
                                <option value="Mr">Mr</option>
                                <option value="Mrs">Mrs</option>
                                <option value="Ms">Ms</option>
                            </select>
                            @error('title')
                                <span class="text-danger x-small">{{ $message }}</span>
                            @enderror
                        </div>
                        <!-- First Name -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control shadow-sm"
                                   wire:model="f_name"
                                   placeholder="Enter First Name">
                            @error('f_name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control shadow-sm"
                                   wire:model="l_name"
                                   placeholder="Enter Last Name">
                            @error('l_name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- JOB & DEPARTMENT INFO -->
                        <hr class="mt-4">
                        <h5 class="fw-bold mb-0">Job & Department Info</h5>
                        <hr class="mt-2">

                        <div class="col-md-4">
                            <p><strong>Job Title:</strong> {{ $job_title ?? 'N/A' }}</p>
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
                            <p><strong>Employment Status:</strong>
                                {{ $employment_status === 'full-time' ? 'Full Time' : 'Part Time' }}</p>
                        </div>

                        @if ($salary_type === 'hourly')
                            <div class="col-md-4">
                                <p><strong>Contract Hours:</strong> {{ $contract_hours }}</p>
                            </div>
                        @endif

                        <div class="col-md-4">
                            <p><strong>Start Date:</strong> {{ $start_date ?? 'N/A' }}</p>
                        </div>



                        <!-- PROFILE INFORMATION -->
                        <hr class="my-3">
                        <h6 class="fw-bold">Profile Information</h6>

                        <!-- Date of Birth -->
                        <div class="col-md-6">
                            <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date"
                                   class="form-control"
                                   wire:model="date_of_birth">
                            @error('date_of_birth')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Gender -->
                        <div class="col-md-6"
                             id="genderDropdownContainer">
                            <label class="form-label">Gender</label>

                            <div style="position:relative;">
                                <!-- Button -->
                                <button class="btn btn-sm w-100 text-start"
                                        type="button"
                                        id="genderDropdownButton"
                                        style="border:1px solid #ccc; background:#fff;">
                                    {{ $gender ? ucfirst($gender) : 'Select Gender' }}
                                </button>

                                <!-- Dropdown -->
                                <div id="genderDropdownMenu"
                                     style="display:none; position:absolute; z-index:1000; width:100%; max-height:200px; overflow-y:auto; background:#fff; border:1px solid #ccc; border-radius:4px;">


                                    @foreach ($genderOptions as $option)
                                        @if (str_contains(strtolower($option), strtolower($genderSearch ?? '')))
                                            <a href="#"
                                               class="dropdown-item"
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
                        <div class="col-md-6"
                             id="maritalDropdownContainer">
                            <label class="form-label">Marital Status</label>

                            <div style="position:relative;">
                                <!-- Button -->
                                <button class="btn btn-sm w-100 text-start"
                                        type="button"
                                        id="maritalDropdownButton"
                                        style="border:1px solid #ccc; background:#fff;">
                                    {{ $marital_status ? ucfirst($marital_status) : 'Select Status' }}
                                </button>

                                <!-- Dropdown -->
                                <div id="maritalDropdownMenu"
                                     style="display:none; position:absolute; z-index:1000; width:100%; max-height:200px; overflow-y:auto; background:#fff; border:1px solid #ccc; border-radius:4px;">


                                    @foreach ($maritalOptions as $option)
                                        @if (str_contains(strtolower($option), strtolower($maritalSearch ?? '')))
                                            <a href="#"
                                               class="dropdown-item"
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




                        <div class="col-12 mt-4">
                            <div class="p-4 rounded-4  border border-light-subtle shadow-sm">

                                <div class="d-flex align-items-center mb-4">

                                    <div>
                                        <h6 class="fw-bold text-dark mb-0">Current Address Details</h5>

                                    </div>
                                </div>

                                <div class="row g-3">

                                    <div class="col-12 mb-2"
                                         x-data="addressAutocomplete()">
                                        <label class="form-label small fw-bold text-secondary">Search Full Address
                                            <span class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <input type="text"
                                                   class="form-control border-light-subtle py-2 shadow-none"
                                                   wire:model.lazy="address"
                                                   x-model="query"
                                                   @input.debounce.500ms="fetchSuggestions"
                                                   @click.away="showSuggestions = false"
                                                   placeholder="Type to search your address..."
                                                   autocomplete="off">

                                            <div x-show="showSuggestions && suggestions.length > 0"
                                                 class="list-group position-absolute w-100 shadow-lg mt-1"
                                                 style="z-index: 1050; max-height: 200px; overflow-y: auto;">
                                                <template x-for="(item, index) in suggestions"
                                                          :key="index">
                                                    <button type="button"
                                                            class="list-group-item list-group-item-action small py-2 border-0"
                                                            @click="selectSuggestion(item)">
                                                        <i class="fas fa-map-pin text-muted me-2"></i>
                                                        <span x-text="item.display_name"></span>
                                                    </button>
                                                </template>
                                            </div>

                                            <div x-show="loading"
                                                 class="position-absolute end-0 top-50 translate-middle-y me-3">
                                                <div class="spinner-border spinner-border-sm text-primary"
                                                     role="status"></div>
                                            </div>
                                        </div>
                                        @error('address')
                                            <span class="text-danger x-small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <hr class="my-3 opacity-25">

                                    <div class="col-md-6"
                                         id="countryDropdownContainer">
                                        <label class="form-label small fw-bold text-secondary">Country <span
                                                  class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <button class="form-select text-start d-flex align-items-center bg-white shadow-none border-light-subtle"
                                                    type="button"
                                                    id="countryDropdownButton"
                                                    onclick="toggleDropdown('countryDropdownMenu')">
                                                {{ !empty($country) ? $country : 'Select Country' }}
                                            </button>

                                            <div id="countryDropdownMenu"
                                                 wire:ignore.self
                                                 class="dropdown-menu shadow-lg w-100 border-light-subtle px-2 pt-2"
                                                 style="display:none; position:absolute; z-index:1000; max-height:250px; overflow-y:auto;">
                                                <input type="text"
                                                       class="form-control form-control-sm mb-2"
                                                       placeholder="Search country..."
                                                       wire:model.live="countrySearch">
                                                @foreach ($filteredCountries as $c)
                                                    <a href="#"
                                                       class="dropdown-item d-flex align-items-center rounded-2 py-2"
                                                       wire:click.prevent="$set('country', '{{ $c['name'] }}'); closeDropdown()">
                                                        <img src="{{ $c['image'] }}"
                                                             class="me-2 rounded-1"
                                                             style="width:20px; height:14px; object-fit: cover;">
                                                        <span class="small">{{ $c['name'] }}</span>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                        @error('country')
                                            <span class="text-danger x-small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6"
                                         id="stateDropdownContainer">
                                        <label class="form-label small fw-bold text-secondary">State / Province
                                            <span class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <button class="form-select text-start bg-white shadow-none border-light-subtle"
                                                    type="button"
                                                    id="stateDropdownButton"
                                                    onclick="toggleDropdown('stateDropdownMenu')">
                                                {{ !empty($state) ? $state : 'Select State' }}
                                            </button>

                                            <div id="stateDropdownMenu"
                                                 wire:ignore.self
                                                 class="dropdown-menu shadow-lg w-100 border-light-subtle"
                                                 style="display:none; position:absolute; z-index:1000; max-height:200px; overflow-y:auto;">
                                                @foreach ($locations as $loc)
                                                    <a href="#"
                                                       class="dropdown-item small py-2"
                                                       wire:click.prevent="$set('state', '{{ $loc['state'] }}'); selectState('{{ $loc['state'] }}'); closeDropdown('state')">
                                                        {{ $loc['state'] }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                        @error('state')
                                            <span class="text-danger x-small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6"
                                         id="cityDropdownContainer">
                                        <label class="form-label small fw-bold text-secondary">City <span
                                                  class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <button class="form-select text-start bg-white shadow-none border-light-subtle"
                                                    type="button"
                                                    id="cityDropdownButton"
                                                    @if (!$cities) disabled @endif
                                                    onclick="toggleDropdown('cityDropdownMenu')">
                                                {{ !empty($city) ? $city : 'Select City' }}
                                            </button>

                                            <div id="cityDropdownMenu"
                                                 wire:ignore.self
                                                 class="dropdown-menu shadow-lg w-100 border-light-subtle"
                                                 style="display:none; position:absolute; z-index:1000; max-height:200px; overflow-y:auto;">
                                                @foreach ($cities as $c)
                                                    <a href="#"
                                                       class="dropdown-item small py-2"
                                                       wire:click.prevent="$set('city', '{{ $c }}'); closeDropdown('city')">
                                                        {{ $c }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                        @error('city')
                                            <span class="text-danger x-small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-secondary">Zip / Postal Code
                                            <span class="text-danger">*</span></label>
                                        <input type="text"
                                               class="form-control border-light-subtle shadow-none"
                                               wire:model="postcode"
                                               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                               placeholder="e.g. 1234">
                                        @error('postcode')
                                            <span class="text-danger x-small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>
                            </div>
                        </div>


                        <!-- State Dropdown -->
                        <div class="col-md-6"
                             id="stateDropdownContainer">



                            <div class="col-md-6">
                                <label class="form-label">
                                    Nationality <span class="text-danger">*</span>
                                </label>

                                <select class="form-select"
                                        wire:model.live="nationality">
                                    @foreach ($nationalities as $nation)
                                        <option value="{{ $nation }}">{{ $nation }}</option>
                                    @endforeach
                                </select>

                                @error('nationality')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>



                            @if ($nationality && $nationality !== 'British')
                                <div class="col-md-6 mt-2">
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


                            <!-- Phones & Email -->
                            <div class="col-md-6">
                                <label class="form-label">Home Phone</label>
                                <input type="text"
                                       class="form-control"
                                       wire:model="home_phone"
                                       placeholder="Enter Home Phone"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                @error('home_phone')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Personal Email</label>
                                <input type="email"
                                       class="form-control"
                                       placeholder="Enter Personal Email"
                                       wire:model="personal_email">
                                @error('personal_email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Tax, Immigration, BRP & Passport -->
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">National Insurance / Tax
                                    Reference <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control border-light-subtle shadow-none"
                                       wire:model="tax_reference_number"
                                       placeholder="Enter NI / Tax Reference">

                                @error('tax_reference_number')
                                    <span class="text-danger x-small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6"
                                 id="immigrationDropdownContainer">
                                <label class="form-label">Immigration Status / Visa Type</label>

                                <div style="position:relative;">
                                    <!-- Button -->
                                    <button class="btn btn-sm w-100 text-start"
                                            type="button"
                                            id="immigrationDropdownButton"
                                            style="border:1px solid #ccc; background:#fff;">
                                        {{ $immigration_status ?? 'Select Immigration Status / Visa Type' }}
                                    </button>

                                    <!-- Dropdown -->
                                    <div id="immigrationDropdownMenu"
                                         style="display:none; position:absolute; z-index:1000; width:100%; max-height:200px; overflow-y:auto; background:#fff; border:1px solid #ccc; border-radius:4px;">


                                        @foreach ($immigrationOptions as $option)
                                            @if (str_contains(strtolower($option), strtolower($immigrationSearch ?? '')))
                                                <a href="#"
                                                   class="dropdown-item"
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
                                <label class="form-label">Passport Number</label>
                                <input type="text"
                                       class="form-control"
                                       placeholder="Enter Passport No."
                                       wire:model="passport_number"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                @error('passport_number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Passport Expiry Date</label>
                                <input type="date"
                                       class="form-control"
                                       wire:model="passport_expiry_date">
                                @error('passport_expiry_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>


                            <hr>


                            @if (!empty($customFields) && $customFields->count())

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
                                <button type="submit"
                                        class="btn btn-success px-4 py-2 shadow-sm"
                                        wire:loading.attr="disabled"
                                        wire:target="save">
                                    <span wire:loading
                                          wire:target="save">
                                        <i class="fas fa-spinner fa-spin me-2"></i> Saving...
                                    </span>
                                    <span wire:loading.remove
                                          wire:target="save">Save</span>
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
