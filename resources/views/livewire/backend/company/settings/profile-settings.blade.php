<div>
    <div class="row g-3 align-items-center justify-content-between mb-4">
        <div class="col">
            <h5 class="fw-500">Company Profile Settings</h5>
        </div>
    </div>


    <div class="row">
        <div class="col-12">

            <div class="card">
                <div class="card-body p-lg-5 p-4 shadow-sm border rounded-3 bg-white">
                    <form class="row g-4 align-items-center"
                          wire:submit.prevent="save">

                        <h5 class="fw-bold mb-0">Company Information</h5>

                        <hr class="my-3 opacity-25">

                        <!-- Company Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Company Name <span
                                      class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control shadow-sm"
                                   wire:model="company_name"
                                   placeholder="Enter Company Name">
                            @error('company_name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>




                        <!-- House Number -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Company House Number <span class="text-danger">*</span>
                            </label>
                            <input type="number"
                                   class="form-control shadow-sm"
                                   wire:model="company_house_number"
                                   placeholder="House No. / Flat No."
                                   min="0"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('company_house_number')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>



                        {{-- Business Type --}}
                        <div class="col-md-6 mb-2 mt-3">
                            <label class="form-label">Business Type <span class="text-danger">*</span></label>
                            <select class="form-control shadow-sm"
                                    wire:model="business_type">
                                <option value="">-- Select Business Type --</option>
                                <option value="Sole Trader">Sole Trader</option>
                                <option value="Partnership">Partnership</option>
                                <option value="Limited Company">Limited Company</option>
                                <option value="Community Interest Company">Community Interest Company</option>
                                <option value="Charity">Charity</option>
                            </select>
                            @error('business_type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-12 mt-4 mb-4">
                            <div class="p-4 rounded-4  border border-light-subtle shadow-sm">

                                <div class="d-flex align-items-center mb-4">

                                    <div>
                                        <h6 class="fw-bold text-dark mb-0">Company Address Details</h5>

                                    </div>
                                </div>

                                <div class="row g-3">



                                    <hr class="my-3 opacity-25">



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
                                        <label class="form-label small fw-semibold text-secondary">Post Code
                                            <span class="text-danger">*</span></label>

                                        <input type="text"
                                               class="form-control border-light-subtle shadow-none"
                                               wire:model="postcode"
                                               oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')"
                                               placeholder="e.g. 1234AB">
                                        @error('postcode')
                                            <span class="text-danger x-small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>


                                <div class="row g-3 mt-1">


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





                        <hr class="mt-4">

                        <h5 class="fw-bold mb-0">Brand & Company Settings</h5>
                        <hr class="mt-2">

                        <div class="row mt-2">

                            <!-- Company Logo -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Company Logo</label>
                                <input type="file"
                                       class="form-control shadow-sm"
                                       wire:model="company_logo"
                                       accept="image/*">
                                @error('company_logo')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror

                                @if ($company_logo)
                                    <img src="{{ $company_logo->temporaryUrl() }}"
                                         class="img-thumbnail mt-2 shadow-sm rounded"
                                         width="90">
                                    <div wire:loading
                                         wire:target="company_logo">
                                        <span class="text-muted small">Uploading...</span>
                                    </div>
                                @elseif ($old_company_logo)
                                    <img src="{{ $old_company_logo }}"
                                         class="img-thumbnail mt-2 shadow-sm rounded"
                                         width="90">
                                @endif
                            </div>

                            <!-- Registered Domain -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Company Website</label>
                                <input type="text"
                                       class="form-control shadow-sm"
                                       wire:model="registered_domain"
                                       placeholder="yourdomain.com">
                                @error('registered_domain')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>


                        </div>

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

    <style>
        #countryDropdownButton,
        #stateDropdownButton,
        #cityDropdownButton,
        #countryDropdownMenu,
        #stateDropdownMenu,
        #cityDropdownMenu {
            box-shadow: none !important;
        }

        #countryDropdownMenu {
            overflow-y: auto;

            overflow-x: hidden;

        }
    </style>

</div>
<script>
    function copyFullDomain() {
        const input = document.getElementById('fullDomain');
        navigator.clipboard.writeText(input.value).then(() => {
            alert('Copied: ' + input.value);
        });
    }
</script>



<script>
    document.addEventListener('click', function(e) {
        ['country', 'state', 'city'].forEach(type => {
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
