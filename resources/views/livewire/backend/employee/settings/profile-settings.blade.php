<div>
    <div class="container-fluid py-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold text-dark mb-1"> Settings</h4>

            </div>
        </div>

        <form wire:submit.prevent="save">
            <div class="row g-4">
                <div class="col-lg-8">

                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-transparent border-bottom-0 pt-4 px-4">
                            <h6 class="fw-bold text-primary text-uppercase mb-0 ls-1">1. Personal Information</h6>
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
                            <h6 class="fw-bold text-primary text-uppercase mb-0 ls-1">2. Current Address Details</h6>
                        </div>
                        <div class="card-body p-4 pt-2">
                            <div class="row g-3"
                                 x-data="addressAutocomplete()">
                                <div class="col-12 mb-2"
                                     x-data="addressAutocomplete()">
                                    <label class="form-label small fw-bold text-secondary">
                                        Address
                                        <span class="text-danger">*</span></label>
                                    <div class="position-relative">
                                        <input type="text"
                                               class="form-control border-light-subtle py-2 shadow-none"
                                               x-model="@entangle('address').defer"
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


                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-secondary">Country <span
                                              class="text-danger">*</span></label>
                                    <select class="form-select border-light-subtle shadow-none"
                                            wire:model.live="country">
                                        <option value="">Select Country</option>
                                        @foreach ($filteredCountries as $c)
                                            <option value="{{ $c['name'] }}">{{ $c['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('country')
                                        <span class="text-danger x-small mt-1 d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-secondary">State / Province <span
                                              class="text-danger">*</span></label>
                                    <select class="form-select border-light-subtle shadow-none"
                                            wire:model.live="state">
                                        <option value="">Select State</option>
                                        @foreach ($locations as $loc)
                                            <option value="{{ $loc['state'] }}">{{ $loc['state'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('state')
                                        <span class="text-danger x-small mt-1 d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-secondary">City <span
                                              class="text-danger">*</span></label>
                                    <select class="form-select border-light-subtle shadow-none"
                                            wire:model="city">
                                        <option value="">Select City</option>
                                        @foreach ($cities as $c)
                                            <option value="{{ $c }}">{{ $c }}</option>
                                        @endforeach
                                    </select>
                                    @error('city')
                                        <span class="text-danger x-small mt-1 d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-secondary">Zip / Postal Code <span
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

                <div class="col-lg-4">

                    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-light bg-opacity-25">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-dark text-uppercase small mb-3 border-bottom pb-2">
                                Job Assignment
                            </h6>

                            <div class="mb-3 pb-3 border-bottom">
                                <label class="d-block text-muted x-small text-uppercase fw-bold mb-1">
                                    Job Title
                                </label>
                                <span class="fw-bold">{{ $job_title ?? 'N/A' }}</span>
                            </div>

                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="p-3 rounded-3 border border-1 border-light bg-white">
                                        <label class="d-block text-muted x-small text-uppercase fw-bold mb-1">
                                            Employment Status
                                        </label>
                                        <span class="badge bg-soft-primary text-primary">
                                            {{ ucfirst($employment_status ?? 'N/A') }}
                                        </span>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="p-3 rounded-3 border border-1 border-light bg-white">
                                        <label class="d-block text-muted x-small text-uppercase fw-bold mb-1">
                                            Hours/Week
                                        </label>
                                        <span class="fw-semibold">{{ $contract_hours ?? '0' }} hrs</span>
                                    </div>
                                </div>
                            </div>
                        </div>



                    </div>

                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-transparent border-bottom-0 pt-4 px-4">
                            <h6 class="fw-bold text-primary text-uppercase mb-0 ls-1">3. Compliance & ID</h6>
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
                                           class="form-control border-light-subtle shadow-none font-monospace"
                                           wire:model="tax_reference_number"
                                           placeholder="Enter Ref Number">
                                    @error('tax_reference_number')
                                        <span class="text-danger x-small mt-1 d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12 border-top pt-3 mt-3">
                                    <label class="form-label small fw-bold text-secondary">Passport Number <span
                                              class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control border-light-subtle shadow-none"
                                           wire:model="passport_number">
                                    @error('passport_number')
                                        <span class="text-danger x-small mt-1 d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label small fw-bold text-secondary">Passport Expiry <span
                                              class="text-danger">*</span></label>
                                    <input type="date"
                                           class="form-control border-light-subtle shadow-none"
                                           wire:model="passport_expiry_date">
                                    @error('passport_expiry_date')
                                        <span class="text-danger x-small mt-1 d-block">{{ $message }}</span>
                                    @enderror
                                </div>


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
                                                        <option value="{{ $opt }}">{{ $opt }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                    @endforeach
                                @endif
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
                    </style>


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

</div>


<script>
    function addressAutocomplete() {
        return {
            query: '',
            suggestions: [],
            showSuggestions: false,
            loading: false,

            async fetchSuggestions() {
                if (this.query.length < 3) {
                    this.suggestions = [];
                    this.showSuggestions = false;
                    return;
                }

                this.loading = true;
                try {
                    // Nominatim API call for search
                    const response = await fetch(
                        `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.query)}&addressdetails=1&limit=5`
                    );
                    const data = await response.json();

                    this.suggestions = data;
                    this.showSuggestions = true;
                } catch (error) {
                    console.error("Error fetching addresses:", error);
                } finally {
                    this.loading = false;
                }
            },

            selectSuggestion(item) {
                this.query = item.display_name;
                this.showSuggestions = false;

                // Livewire-কে ভ্যালু আপডেট করার জন্য জানানো
                @this.set('address', item.display_name);
            }
        }
    }
</script>
