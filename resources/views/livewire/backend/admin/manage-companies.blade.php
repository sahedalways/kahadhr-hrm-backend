<div>
    <div class="row g-3 align-items-center justify-content-between mb-4">

        <!-- LEFT: Title -->
        <div class="col-auto">
            <h5 class="fw-500 text-primary m-0">Companies Management</h5>
        </div>



        <!-- RIGHT: Export Buttons -->
        <div class="col-auto d-flex gap-2">


            <button wire:click="exportCompanies('pdf')" class="btn btn-sm btn-white text-primary">
                <i class="fa fa-file-pdf me-1"></i> PDF
            </button>

            <button wire:click="exportCompanies('excel')" class="btn btn-sm btn-white text-success">
                <i class="fa fa-file-excel me-1"></i> Excel
            </button>

            <button wire:click="exportCompanies('csv')" class="btn btn-sm btn-white text-info">
                <i class="fa fa-file-csv me-1"></i> CSV
            </button>
        </div>

    </div>


    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
               
                    <div class="card-body">
                        <div class="row g-3 align-items-center mb-3">
                            <!-- Search Input -->
                            <div class="col-md-8">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass"></i></span>
                                    <input type="text" class="form-control border-start-0"
                                        placeholder="Search by company name, email, or phone" wire:model="search"
                                        wire:keyup="set('search', $event.target.value)" />
                                </div>
                            </div>

                            <!-- Sort Dropdown -->
                            <div class="col-md-4 d-flex gap-2">
                                <select class="form-select form-select-lg"
                                    wire:change="handleSort($event.target.value)">
                                    <option value="desc">Newest First</option>
                                    <option value="asc">Oldest First</option>
                                </select>


                                <select class="form-select form-select-lg"
                                    wire:change="handleFilter($event.target.value)">
                                    <option value="">All Status</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>

                        </div>

                        <!-- Live Search Result Indicator -->
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <p class="text-muted small mb-0">
                                Showing results for: <strong>{{ $search ?: 'All Companies' }}</strong>
                            </p>
                            <div wire:loading wire:target="search">
                                <span class="spinner-border spinner-border-sm text-primary" role="status"
                                    aria-hidden="true"></span>
                                <span class="text-primary small">Searching...</span>
                            </div>
                        </div>
                    </div>
             
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Company Name</th>
                                    <th>Email</th>
                                    <th>Phone No.</th>
                                    <th>Logo</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1; @endphp
                                @forelse($infos as $row)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $row->company_name }}</td>
                                        <td>
                                            <span onclick="copyToClipboard('{{ $row->company_email ?? '' }}')"
                                                onmouseover="this.style.backgroundColor='#f0f0f0';"
                                                onmouseout="this.style.backgroundColor='transparent';"
                                                style="cursor: pointer; color: inherit; padding: 2px 4px; border-radius: 4px;"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Click to copy">
                                                {{ $row->company_email ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span onclick="copyToClipboard('{{ $row->company_mobile ?? '' }}')"
                                                onmouseover="this.style.backgroundColor='#f0f0f0';"
                                                onmouseout="this.style.backgroundColor='transparent';"
                                                style="cursor: pointer; color: inherit; padding: 2px 4px; border-radius: 4px;"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Click to copy">
                                                {{ $row->company_mobile ?? 'N/A' }}
                                            </span>
                                        </td>



                                        <td>
                                            @if ($row->company_logo_url)
                                                <img src="{{ $row->company_logo_url }}" alt="Logo"
                                                    style="width:50px; height:auto;">
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            <a href="#" wire:click.prevent="toggleStatus({{ $row->id }})"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="Click to change status">

                                                {!! statusBadge($row->status) !!}
                                            </a>
                                        </td>


                                        <td>

                                            <a href="{{ route('super-admin.company.details.show', $row->id) }}"
                                                class="badge badge-xs fw-600 text-xs"
                                                style="background-color: #5acaa3; color: #000; text-decoration: none; transition: 0.3s;"
                                                onmouseover="this.style.backgroundColor='#3aa57a'; this.style.color='#fff';"
                                                onmouseout="this.style.backgroundColor='#5acaa3'; this.style.color='#000';">
                                                View Details
                                            </a>


                                            <a data-bs-toggle="modal" data-bs-target="#manageCompanyProfile"
                                                wire:click="manageCompanyProfile({{ $row->id }})"
                                                class="badge badge-info badge-xs fw-600 text-xs"
                                                style="background-color: #4ba3f7; color: #fff; text-decoration:none; transition:0.3s; cursor: pointer;"
                                                onmouseover="this.style.backgroundColor='#1d74d8';"
                                                onmouseout="this.style.backgroundColor='#4ba3f7';">
                                                Manage Profile
                                            </a>




                                            <a href="#" class="badge badge-xs badge-danger fw-600 text-xs"
                                                wire:click.prevent="$dispatch('confirmDelete', {{ $row->id }})">
                                                Delete
                                            </a>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No companies found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>


                        @if ($hasMore)
                            <div class="text-center mt-4">
                                <button wire:click="loadMore" class="btn btn-outline-primary rounded-pill px-4 py-2">
                                    Load More
                                </button>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>


    <div wire:ignore.self class="modal fade" id="manageCompanyProfile" tabindex="-1" role="dialog"
        aria-labelledby="manageCompanyProfile" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-600">Manage Company Info</h6>
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="update">
                    <div class="modal-body">
                        <div class="row g-2">

                            {{-- Company Name --}}
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Company Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control shadow-sm" wire:model="company_name"
                                    required>
                                @error('company_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- House Number --}}
                            <div class="col-md-6 mb-2">
                                <label class="form-label">House Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control shadow-sm"
                                    wire:model="company_house_number">
                                @error('company_house_number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>





                            <div class="col-md-6 d-flex align-items-end">
                                <div class="flex-grow-1">
                                    <label class="form-label"> Mobile No.<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control shadow-sm" wire:model="company_mobile"
                                        readonly oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    @error('company_mobile')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <button type="button" class="btn btn-primary ms-2 mb-0"
                                    wire:click="openModal('mobile')" data-bs-toggle="modal"
                                    data-bs-target="#verifyModal">
                                    Change
                                </button>
                            </div>

                            <div class="col-md-6 d-flex align-items-end">
                                <div class="flex-grow-1">
                                    <label class="form-label"> Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" wire:model="company_email" readonly>
                                    @error('company_email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <button type="button" class="btn btn-primary ms-2 mb-0"
                                    wire:click="openModal('email')" data-bs-toggle="modal"
                                    data-bs-target="#verifyModal">
                                    Change
                                </button>
                            </div>




                            {{-- Business Type --}}
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Business Type</label>
                                <input type="text" class="form-control shadow-sm" wire:model="business_type">
                                @error('business_type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Contact Address --}}
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Contact Address</label>
                                <textarea class="form-control" rows="2" wire:model="address_contact_info"></textarea>
                                @error('address_contact_info')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Registered Domain --}}
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Registered Domain</label>
                                <input type="text" class="form-control shadow-sm" wire:model="registered_domain"
                                    pattern="^(?!:\/\/)([a-zA-Z0-9-_]+\.)+[a-zA-Z]{2,11}?$"
                                    title="Enter a valid domain, e.g., example.com"
                                    oninput="this.value = this.value.replace(/[^a-zA-Z0-9\.\-]/g,'')">
                                @error('registered_domain')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>


                            {{-- Calendar Year --}}
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Calendar Year</label>
                                <select class="form-control" wire:model.live="calendar_year">
                                    <option value="english">English</option>
                                    <option value="hmrc">HMRC</option>
                                </select>

                                @error('calendar_year')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>



                            {{-- Subscription Status --}}
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Subscription Status</label>
                                <select class="form-control" wire:model="subscription_status">
                                    <option value="active">Active</option>
                                    <option value="trial">Trial</option>
                                    <option value="expired">Expired</option>
                                    <option value="suspended">Suspended</option>
                                </select>

                                @error('subscription_status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Subscription Start --}}
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Subscription Start</label>
                                <input type="date" class="form-control" wire:model="subscription_start">
                                @error('subscription_start')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Subscription End --}}
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Subscription End</label>
                                <input type="date" class="form-control" wire:model="subscription_end">
                                @error('subscription_end')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Company Logo --}}
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Company Logo</label>
                                <input type="file" class="form-control" wire:model="company_logo"
                                    accept="image/*">


                                @if ($company_logo)
                                    <img src="{{ $company_logo->temporaryUrl() }}" class="img-thumbnail mt-2"
                                        width="80">

                                    <div wire:loading wire:target="company_logo">
                                        <span class="text-muted">Uploading...</span>
                                    </div>
                                @elseif ($company_logo_preview)
                                    <img src="{{ $company_logo_preview }}" class="img-thumbnail mt-2"
                                        width="80">
                                @endif

                                @error('company_logo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>




                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
                            wire:target="update">
                            <span wire:loading wire:target="update">
                                <i class="fas fa-spinner fa-spin me-2"></i> Saving...
                            </span>
                            <span wire:loading.remove wire:target="update">Save</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div wire:ignore.self class="modal fade" id="verifyModal" tabindex="-1" role="dialog"
        aria-labelledby="verifyModal" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-600">Verification Centre</h6>
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="verifyAndUpdate">
                    <div class="modal-body">

                        <!-- Email Input -->
                        @if ($updating_field === 'email')
                            <div class="mb-3">
                                <label>New Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="email" class="form-control form-control-sm shadow-sm"
                                        wire:model="new_email" placeholder="Enter new email" style="height: 38px;">

                                    <button
                                        class="btn btn-primary btn-sm d-flex align-items-center justify-content-center"
                                        type="button" style="height: 38px;"
                                        wire:click.prevent.stop="requestVerification('{{ $updating_field }}')"
                                        wire:loading.attr="disabled" wire:target="requestVerification"
                                        @if ($otpCooldown > 0) disabled @endif>
                                        <span wire:loading wire:target="requestVerification">
                                            <i class="fas fa-spinner fa-spin me-2"></i> Sending...
                                        </span>
                                        <span wire:loading.remove wire:target="requestVerification">
                                            @if ($otpCooldown > 0)
                                                Resend In
                                                {{ floor($otpCooldown / 60) }}:{{ str_pad($otpCooldown % 60, 2, '0', STR_PAD_LEFT) }}
                                            @elseif($code_sent)
                                                Resend OTP
                                            @else
                                                Send OTP
                                            @endif
                                        </span>
                                    </button>





                                </div>

                                <!-- Livewire polling for countdown -->
                                @if ($otpCooldown > 0)
                                    <div wire:poll.1000ms="tick"></div>
                                @endif

                                @error('new_email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                        <!-- Mobile Input -->
                        @if ($updating_field === 'mobile')
                            <div class="mb-3">
                                <label>New Mobile No. <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control shadow-sm form-control-sm"
                                        wire:model="new_mobile" placeholder="Enter new mobile no."
                                        style="height: 38px;">
                                    <button
                                        class="btn btn-primary btn-sm d-flex align-items-center justify-content-center"
                                        type="button" style="height: 38px;"
                                        wire:click.prevent.stop="requestVerification('{{ $updating_field }}')"
                                        wire:loading.attr="disabled" wire:target="requestVerification"
                                        @if ($otpCooldown > 0) disabled @endif>
                                        <span wire:loading wire:target="requestVerification">
                                            <i class="fas fa-spinner fa-spin me-2"></i> Sending...
                                        </span>
                                        <span wire:loading.remove wire:target="requestVerification">
                                            @if ($otpCooldown > 0)
                                                Resend In
                                                {{ floor($otpCooldown / 60) }}:{{ str_pad($otpCooldown % 60, 2, '0', STR_PAD_LEFT) }}
                                            @elseif($code_sent)
                                                Resend OTP
                                            @else
                                                Send OTP
                                            @endif
                                        </span>
                                    </button>
                                </div>


                                @if ($otpCooldown > 0)
                                    <div wire:poll.1000ms="tick"></div>
                                @endif

                                @error('new_mobile')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                        <!-- Verification Code Input -->
                        @if ($code_sent)
                            <div class="mb-3">
                                <label>Verification Code <span class="text-danger">*</span></label>
                                <div class="d-flex gap-2">
                                    @for ($i = 0; $i < 6; $i++)
                                        <input type="text" wire:model="otp.{{ $i }}"
                                            class="form-control text-center otp-field" maxlength="1" placeholder="-"
                                            
                                            oninput="handleOtpInput(this)"
                                            onkeydown="handleOtpBackspace(event, this)">
                                    @endfor
                                </div>
                                @error('verification_code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif




                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        @if ($code_sent)
                            <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
                                wire:target="verifyOtp">
                                <span wire:loading wire:target="verifyOtp">
                                    <i class="fas fa-spinner fa-spin me-2"></i> Verifying...
                                </span>
                                <span wire:loading.remove wire:target="verifyOtp">Verify</span>
                            </button>
                        @endif

                    </div>
                </form>
            </div>
        </div>
    </div>



</div>

<script>
    Livewire.on('confirmDelete', companyId => {
        if (confirm("Are you sure you want to delete this company? This action cannot be undone.")) {
            Livewire.dispatch('deleteCompany', {
                id: companyId
            });
        }
    });
</script>
<script>
    function copyToClipboard(text) {
        if (!text) return;
        navigator.clipboard.writeText(text)
            .then(() => {
                alert("Copied: " + text);
            })
            .catch(err => {
                console.error('Failed to copy: ', err);
            });
    }
</script>


<script>
    function handleOtpInput(el) {

        el.value = el.value.replace(/[^0-9]/g, '');


        if (el.value.length === 1) {
            const next = el.nextElementSibling;
            if (next && next.classList.contains('otp-field')) {
                next.focus();
            }
        }
    }

    function handleOtpBackspace(e, el) {

        if (e.key === 'Backspace') {
            if (el.value) {

                el.value = '';
            } else {

                const prev = el.previousElementSibling;
                if (prev && prev.classList.contains('otp-field')) {
                    prev.focus();
                    prev.value = '';
                }
            }

            e.preventDefault();
        }
    }
</script>
