@php
    $id = request('id');
@endphp




<div>
    <div class="row g-3 align-items-center justify-content-between mb-4">

        <!-- LEFT: Title -->
        <div class="col-auto">
            <h5 class="fw-500 text-primary m-0">Documents By Type</h5>
        </div>

        <!-- RIGHT: Export Buttons -->
        <div class="col-auto d-flex gap-2">
            <button wire:click="exportDocuments('pdf')"
                    class="btn btn-sm btn-white text-primary">
                <i class="fa fa-file-pdf me-1"></i> PDF
            </button>

            <button wire:click="exportDocuments('excel')"
                    class="btn btn-sm btn-white text-success">
                <i class="fa fa-file-excel me-1"></i> Excel
            </button>

            <button wire:click="exportDocuments('csv')"
                    class="btn btn-sm btn-white text-info">
                <i class="fa fa-file-csv me-1"></i> CSV
            </button>
        </div>

        <div class="col-auto">
            <a data-bs-toggle="modal"
               data-bs-target="#add"
               wire:click="resetInputFields"
               class="btn btn-icon btn-3 btn-white text-primary mb-0">
                <i class="fa fa-plus me-2"></i> Add New Document
            </a>
        </div>
    </div>

    <!-- Search + Sort -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="row g-3 align-items-center mb-3">

                            {{-- Filter by Employees --}}
                            <div class="col-lg-4 col-md-6 col-12">
                                <div class="dropdown w-100"
                                     wire:ignore>
                                    <button class="btn border shadow-none dropdown-toggle w-100 d-flex justify-content-between align-items-center"
                                            type="button"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                        Filter by Employees
                                    </button>

                                    <div class="dropdown-menu p-3 w-100 shadow"
                                         style="max-height: 250px; overflow-y: auto;"
                                         data-bs-auto-close="outside">

                                        <div class="form-check mb-2 border-bottom pb-2">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   wire:model.live="selectAllUsers"
                                                   id="emp-all">
                                            <label class="form-check-label fw-semibold"
                                                   for="emp-all">
                                                All Employees
                                            </label>
                                        </div>

                                        <div class="fw-semibold text-muted small mb-2">
                                            Select Employees
                                        </div>

                                        @foreach ($employees as $emp)
                                            <div class="form-check mb-1">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       value="{{ $emp->user_id }}"
                                                       wire:model.live="filterUsers"
                                                       id="emp-{{ $emp->user_id }}">
                                                <label class="form-check-label"
                                                       for="emp-{{ $emp->user_id }}">
                                                    {{ $emp->full_name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- Sort --}}
                            <div class="col-lg-4 col-md-6 col-12">
                                <select class="form-select form-select-lg"
                                        wire:change="handleSort($event.target.value)">
                                    <option value="desc">Newest First</option>
                                    <option value="asc">Oldest First</option>
                                </select>
                            </div>

                            {{-- Filter by Type --}}
                            <div class="col-lg-4 col-md-6 col-12">
                                <select class="form-select"
                                        wire:change="filterByType($event.target.value)">
                                    <option value="">All Types</option>
                                    @foreach ($docTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>


                    </div>
                </div>



            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- Table -->

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle">

                            <thead class="table-light">
                                <tr>
                                    <th>Employee</th>
                                    <th>Documents</th>

                                </tr>
                            </thead>

                            <tbody>
                                @php $i = 1; @endphp

                                @forelse($infos as $employee)
                                    <tr class="{{ $id == $employee->id ? 'table-primary' : '' }}">




                                        <td style="text-align: center; vertical-align: middle;">
                                            <div
                                                 style="display: inline-flex; flex-direction: column; align-items: center; gap: 8px; text-align: left;">

                                                <div class="mt-2"
                                                     style="font-weight: 700; color: #1a1c1e; font-size: 15px; letter-spacing: -0.01em; text-align: center; width: 100%;">
                                                    {{ trim(($employee->f_name ?? '') . ' ' . ($employee->l_name ?? '')) ?: $employee->email ?? 'N/A' }}
                                                </div>


                                                @if ($employee->nationality !== 'British')
                                                    <div class="mt-2"
                                                         style="display: flex; flex-direction: column; gap: 6px;">

                                                        <div style="display: flex; align-items: center; gap: 10px;">
                                                            <small
                                                                   style="width: 75px; color: #6c757d; font-size: 10px; text-transform: uppercase; font-weight: 700; line-height: 1;">
                                                                Share Code
                                                            </small>
                                                            @if ($employee->share_code)
                                                                <span onclick="copyToClipboard('{{ $employee->share_code }}')"
                                                                      data-bs-toggle="tooltip"
                                                                      title="Click to copy Share Code"
                                                                      style="cursor: pointer; background-color: #f0f4ff; color: #0056b3; border: 1px solid #cfe2ff; padding: 3px 10px; border-radius: 6px; font-family: 'SFMono-Regular', Consolas, monospace; font-size: 12px; font-weight: 600; min-width: 100px; text-align: center;">
                                                                    {{ $employee->share_code }}
                                                                </span>
                                                            @else
                                                                <span
                                                                      style="background-color: #f8f9fa; color: #6c757d; border: 1px solid #e9ecef; padding: 3px 10px; border-radius: 6px; font-size: 12px; min-width: 100px; text-align: center;">
                                                                    N/A
                                                                </span>
                                                            @endif

                                                        </div>

                                                        <div style="display: flex; align-items: center; gap: 10px;">
                                                            <small
                                                                   style="width: 75px; color: #6c757d; font-size: 10px; text-transform: uppercase; font-weight: 700; line-height: 1;">
                                                                DOB
                                                            </small>
                                                            <span onclick="copyToClipboard('{{ $employee->date_of_birth ? date('d M, Y', strtotime($employee->date_of_birth)) : '' }}')"
                                                                  data-bs-toggle="tooltip"
                                                                  title="Click to copy Date of Birth"
                                                                  style="cursor: pointer; background-color: #f8f9fa; color: #333; border: 1px solid #e9ecef; padding: 3px 10px; border-radius: 6px; font-size: 12px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-width: 100px;">
                                                                <i class="bi bi-calendar3"
                                                                   style="font-size: 11px; color: #6c757d;"></i>
                                                                {{ $employee->date_of_birth ? date('d M, Y', strtotime($employee->date_of_birth)) : 'N/A' }}
                                                            </span>
                                                        </div>

                                                    </div>
                                                @endif


                                            </div>
                                        </td>


                                        <td class="text-start"
                                            style="width:260px; max-width:260px; white-space:nowrap;">





                                            @if ($employee->documents && $employee->documents->count() > 0)
                                                @php
                                                    $grouped = $employee->documents
                                                        ->sortByDesc('created_at')
                                                        ->groupBy(function ($doc) {
                                                            return $doc->documentType->name ?? 'Unknown Type';
                                                        });
                                                @endphp




                                                @foreach ($grouped as $type => $docs)
                                                    @php
                                                        // Latest 3 documents only
                                                        $latestDocs = $docs
                                                            ->sortByDesc('created_at')
                                                            ->take(3)
                                                            ->values();

                                                        $latestDoc = $docs->sortByDesc('created_at')->first();

                                                        $latestExpiresAt =
                                                            $latestDoc && $latestDoc->expires_at
                                                                ? \Carbon\Carbon::parse($latestDoc->expires_at)
                                                                : null;

                                                        $showTypeNotify = false;
                                                        $notificationType = null;

                                                        if ($latestExpiresAt) {
                                                            // Expired
                                                            if ($latestExpiresAt->isPast()) {
                                                                $showTypeNotify = true;
                                                                $notificationType = 'expired';
                                                            }
                                                            // Expiring within 60 days (future only)
                                                            elseif (
                                                                now()->diffInDays($latestExpiresAt, false) > 0 &&
                                                                now()->diffInDays($latestExpiresAt, false) <= 60
                                                            ) {
                                                                $showTypeNotify = true;
                                                                $notificationType = 'soon';
                                                            }
                                                        }

                                                    @endphp

                                                    <div class="border rounded p-2 mb-2"
                                                         style="max-width:240px; position:relative;">



                                                        @if ($showTypeNotify)
                                                            <span wire:click.stop="notifyEmployee({{ $latestDoc->doc_type_id }}, {{ $employee->id }}, '{{ $notificationType }}')"
                                                                  wire:loading.attr="disabled"
                                                                  title="Notify employee"
                                                                  style="position:absolute; top:6px; right:6px; background:#dc3545; color:#fff; width:22px; height:22px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; cursor:pointer; transition:all 0.25s ease; box-shadow:0 3px 8px rgba(0,0,0,0.25);"
                                                                  onmouseover="this.style.transform='scale(1.2)'; this.style.background='#b02a37';"
                                                                  onmouseout="this.style.transform='scale(1)'; this.style.background='#dc3545';">

                                                                <i class="bi bi-bell-fill"
                                                                   wire:loading.remove
                                                                   wire:target="notifyEmployee({{ $latestDoc->doc_type_id }}, {{ $employee->id }}, '{{ $notificationType }}')">
                                                                </i>

                                                                <i class="bi bi-arrow-repeat"
                                                                   wire:loading
                                                                   wire:target="notifyEmployee({{ $latestDoc->doc_type_id }}, {{ $employee->id }}, '{{ $notificationType }}')"
                                                                   style="animation:spin 1s linear infinite;">
                                                                </i>

                                                            </span>
                                                        @endif




                                                        <div class="mb-2">
                                                            <strong>{{ $type }}</strong>
                                                        </div>

                                                        <div class="d-flex align-items-center"
                                                             style="
                        overflow-x:auto;
                        overflow-y:hidden;
                        padding:16px 8px;
                        min-height:105px;
                        max-width:230px;
                        scrollbar-width:none;
                     ">

                                                            @foreach ($latestDocs as $index => $doc)
                                                                @php
                                                                    $colors = [
                                                                        '#4e73df',
                                                                        '#1cc88a',
                                                                        '#36b9cc',
                                                                        '#f6c23e',
                                                                        '#e74a3b',
                                                                    ];
                                                                    $currentColor = $colors[$index % count($colors)];
                                                                    $extension = pathinfo(
                                                                        $doc->file_path,
                                                                        PATHINFO_EXTENSION,
                                                                    );
                                                                    $expiresAt = $doc->expires_at
                                                                        ? \Carbon\Carbon::parse($doc->expires_at)
                                                                        : null;

                                                                    $isExpired = $expiresAt && $expiresAt->isPast();

                                                                    $isExpiringSoon =
                                                                        $expiresAt &&
                                                                        !$isExpired &&
                                                                        $expiresAt->isFuture() &&
                                                                        $expiresAt->lte(now()->addDays(60));
                                                                    $zIndex = count($latestDocs) - $index;

                                                                @endphp

                                                                @php
                                                                    $zIndex = count($latestDocs) - $index;
                                                                @endphp





                                                                <div class="doc-card-wrapper"
                                                                     style="
            position:relative;
            min-width:90px;
            margin-right:-70px;
            z-index:{{ $zIndex }};
            transition:all 0.4s cubic-bezier(0.165,0.84,0.44,1);
            cursor:pointer;
         "
                                                                     onmouseover="this.style.zIndex='999';this.style.transform='translateY(-12px) scale(1.08)';this.style.marginRight='10px';"
                                                                     onmouseout="this.style.zIndex='{{ $zIndex }}';this.style.transform='translateY(0) scale(1)';this.style.marginRight='-70px';"
                                                                     data-bs-toggle="modal"
                                                                     data-bs-target="#documentModal"
                                                                     wire:click="openDocModal({{ $doc->id }}, {{ $index + 1 }})">



                                                                    <div
                                                                         style="
                                background:white;
                                border-radius:10px;
                                padding:10px;
                                box-shadow:-5px 0 12px rgba(0,0,0,0.08);
                                border-top:3px solid {{ $currentColor }};
                                text-align:center;
                                border:1px solid #eee;
                            ">

                                                                        <!-- Icon -->
                                                                        <div
                                                                             style="
                                    width:36px;
                                    height:36px;
                                    background:{{ $currentColor }}15;
                                    color:{{ $currentColor }};
                                    border-radius:50%;
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    margin:0 auto 6px;
                                    font-size:1.1rem;
                                ">
                                                                            @if (in_array($extension, ['jpg', 'png', 'jpeg']))
                                                                                <i class="bi bi-image"></i>
                                                                            @elseif ($extension === 'pdf')
                                                                                <i class="bi bi-file-earmark-pdf"></i>
                                                                            @else
                                                                                <i class="bi bi-file-earmark-text"></i>
                                                                            @endif
                                                                        </div>

                                                                        <!-- Filename -->
                                                                        <div
                                                                             style="font-weight:700;font-size:0.65rem;color:#333;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                                                            File-{{ $index + 1 }}.{{ $extension }}
                                                                        </div>

                                                                        <!-- Expiry -->
                                                                        <div style="font-size:0.6rem;margin-top:4px;">
                                                                            <div
                                                                                 style="color:{{ $isExpired ? '#dc3545' : ($isExpiringSoon ? '#fd7e14' : '#999') }};">
                                                                                {{ $doc->expires_at ? date('d M, Y', strtotime($doc->expires_at)) : 'No Expiry' }}
                                                                            </div>

                                                                            @if ($isExpired)
                                                                                <span class="badge bg-danger mt-1"
                                                                                      style="font-size:0.55rem;">Expired</span>
                                                                            @elseif ($isExpiringSoon)
                                                                                <span class="badge bg-warning text-dark mt-1"
                                                                                      style="font-size:0.55rem;">Expires
                                                                                    Soon</span>
                                                                            @endif
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>



                                                    </div>
                                                @endforeach

                                                <style>
                                                    .d-flex::-webkit-scrollbar {
                                                        display: none;
                                                    }
                                                </style>

                                                <link rel="stylesheet"
                                                      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
                                            @else
                                                <div
                                                     style="display:flex; align-items:center; justify-content:center; height:100px;">
                                                    <span class="text-muted">No documents found</span>
                                                </div>
                                            @endif
                                        </td>




                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="7"
                                            class="text-center">No employees found</td>
                                    </tr>
                                @endforelse
                            </tbody>



                        </table>

                        @if ($hasMore)
                            <div class="text-center mt-4">
                                <button wire:click="loadMore"
                                        class="btn btn-outline-primary rounded-pill px-4 py-2">
                                    Load More
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>



    <!-- Add Modal -->
    <div wire:ignore.self
         class="modal fade"
         id="add"
         data-bs-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h6 class="modal-title fw-600">Add New Document</h6>
                    <button type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="row g-2">



                            <div class="col-md-12 mb-2">
                                <label class="form-label">Document Type <span class="text-danger">*</span></label>

                                <select class="form-select"
                                        wire:model.live="doc_type_id">
                                    <option value=""
                                            selected>-- Select Document Type --</option>

                                    @foreach ($docTypes as $type)
                                        <option value="{{ $type->id }}">
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('doc_type_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>


                            <div class="mb-3">
                                <label class="form-label">Select Employee <span class="text-danger">*</span></label>
                                <select id="employeeSelect"
                                        class="form-select"
                                        wire:model.live="emp_id"
                                        wire:key="emp_id">
                                    <option value=""
                                            selected>-- Select Employee --</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}">
                                            {{ trim(($emp->f_name ?? '') . ' ' . ($emp->l_name ?? '')) ?: $emp->email }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('emp_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>



                            <div class="col-md-12 mb-2">
                                <label class="form-label">File <span class="text-danger">*</span></label>
                                <input type="file"
                                       class="form-control"
                                       wire:model="file_path"
                                       accept="application/pdf">
                                @error('file_path')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-2">
                                <label class="form-label">Expires At <span class="text-danger">*</span></label>
                                <input type="date"
                                       class="form-control"
                                       wire:model="expires_at"
                                       min="{{ date('Y-m-d') }}">

                                @error('expires_at')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-check mb-1">
                                <input class="form-check-input"
                                       type="checkbox"
                                       wire:model.live="send_email"
                                       id="emailCheck">

                                <label class="form-check-label fw-bold"
                                       for="emailCheck">
                                    Send employee an email notification
                                </label>
                            </div>


                            @if ($emailGatewayMissing)
                                <div class="small text-danger mt-1">
                                    ⚠️ Email notification is not configured yet.
                                    Please set up your email gateway from
                                    <a href="{{ route('company.dashboard.settings.mail', ['company' => app('authUser')->company->sub_domain]) }}"
                                       class="text-decoration-underline fw-semibold">
                                        Settings → Mail Settings
                                    </a>
                                </div>
                            @endif



                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                        <button type="submit"
                                class="btn btn-success"
                                wire:loading.attr="disabled"
                                wire:target="save">
                            <span wire:loading
                                  wire:target="save"><i class="fas fa-spinner fa-spin me-2"></i>Saving...</span>
                            <span wire:loading.remove
                                  wire:target="save">Save</span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>



    <div wire:ignore.self
         class="modal fade"
         id="documentModal"
         tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <div class="d-flex align-items-start flex-column">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h5 class="modal-title"
                                style="font-weight: 800; color: #2d3748; letter-spacing: -0.5px; margin: 0;">
                                File-{{ $modalFileIndex ?? '' }}
                            </h5>

                            <span
                                  style="background: #edf2f7; padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: 700; color: #4a5568; text-transform: uppercase;">
                                DOC
                            </span>
                        </div>

                        <div class="d-flex align-items-center">
                            @if ($modalDocument && $modalDocument->expires_at)
                                @php
                                    $expiry = \Carbon\Carbon::parse($modalDocument->expires_at);
                                    $daysLeft = now()->startOfDay()->diffInDays($expiry->startOfDay(), false);
                                @endphp

                                <div
                                     style="
                display: flex;
                align-items: center;
                gap: 6px;
                padding: 4px 12px;
                border-radius: 8px;
                font-size: 0.75rem;
                font-weight: 600;
                @if ($daysLeft < 0) background: #fff5f5; color: #c53030; border: 1px solid #feb2b2;
                @elseif($daysLeft === 0) background: #fffaf0; color: #975a16; border: 1px solid #fbd38d;
                @else background: #f0fff4; color: #276749; border: 1px solid #9ae6b4; @endif
            ">
                                    <span
                                          style="
                    height: 8px; width: 8px; border-radius: 50%;
                    @if ($daysLeft < 0) background: #c53030;
                    @elseif($daysLeft === 0) background: #975a16;
                    @else background: #276749; @endif
                "></span>

                                    @if ($daysLeft < 0)
                                        Expired ({{ abs($daysLeft) }} days ago)
                                    @elseif ($daysLeft === 0)
                                        Expires Today
                                    @else
                                        Expires in {{ $daysLeft }} days
                                    @endif
                                </div>
                            @else
                                <div
                                     style="display: flex; align-items: center; gap: 5px; color: #718096; font-size: 0.75rem; font-weight: 500;">
                                    <i class="bi bi-calendar-x"
                                       style="font-size: 0.85rem;"></i>
                                    No Expiry Set
                                </div>
                            @endif
                        </div>
                    </div>


                    <button type="button"
                            class="btn btn-light rounded-pill"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body p-0">

                    <div class="container-fluid">
                        <div class="row g-0"
                             style="min-height: 70vh;">

                            {{-- LEFT SIDE : Fields --}}
                            <div class="col-md-4 p-3 border-end"
                                 style="background:#f8fafc;">

                                <h6 class="fw-bold mb-3 text-uppercase text-muted"
                                    style="font-size:12px;">
                                    Document Details
                                </h6>

                                {{-- Document Type --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Document Type <span
                                              class="text-danger">*</span></label>
                                    <select class="form-select form-select-sm"
                                            wire:model.live="doc_type_id">
                                        <option value="">-- Select --</option>
                                        @foreach ($docTypes as $type)
                                            <option value="{{ $type->id }}">
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Employee --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Employee <span
                                              class="text-danger">*</span></label>
                                    <select class="form-select form-select-sm"
                                            wire:model.live="emp_id">
                                        <option value="">-- Select --</option>
                                        @foreach ($employees as $emp)
                                            <option value="{{ $emp->id }}">
                                                {{ trim(($emp->f_name ?? '') . ' ' . ($emp->l_name ?? '')) ?: $emp->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Expiry --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Expires At <span
                                              class="text-danger">*</span></label>
                                    <input type="date"
                                           class="form-control form-control-sm"
                                           wire:model.live="expires_at">
                                </div>


                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Change File (PDF)</label>

                                    <input type="file"
                                           class="form-control form-control-sm"
                                           wire:model="new_file"
                                           accept="application/pdf">

                                    @error('new_file')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                    @if ($new_file)
                                        <div class="small text-success mt-1">
                                            New file selected: {{ $new_file->getClientOriginalName() }}
                                        </div>
                                    @endif
                                </div>

                            </div>

                            {{-- RIGHT SIDE : Document Preview --}}
                            <div class="col-md-8 p-0">

                                @if ($modalDocument && $modalDocument->file_path)
                                    <iframe src="{{ asset('storage/' . $modalDocument->file_path) }}"
                                            style="width:100%; height:100%; min-height:70vh; border:0;">
                                    </iframe>
                                @else
                                    <div class="h-100 d-flex align-items-center justify-content-center text-muted">
                                        No file found
                                    </div>
                                @endif

                            </div>

                        </div>
                    </div>

                </div>


                <div class="modal-footer d-flex justify-content-between align-items-center">


                    <div class="d-flex gap-2 position-relative">

                        @if ($modalDocument)
                            <button wire:click="updateDocument({{ $modalDocument->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="updateDocument"
                                    class="btn btn-sm btn-primary">


                                <span wire:loading
                                      wire:target="updateDocument">
                                    <i class="fas fa-spinner fa-spin me-1"></i> Updating...
                                </span>

                                <span wire:loading.remove
                                      wire:target="updateDocument">
                                    <i class="fa fa-edit me-1"></i> Update
                                </span>
                            </button>
                        @endif



                        @if ($modalDocument && $confirmDeleteId === $modalDocument->id)
                            <div style="position:absolute; bottom:45px; left:0; z-index:999;">
                                <div
                                     style="background:#fff; border:1px solid #ddd; padding:8px 10px;
                            border-radius:8px; box-shadow:0 3px 10px rgba(0,0,0,0.15);">
                                    <div style="font-size:12px; margin-bottom:6px;">
                                        Are you sure?
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button wire:click="deleteDocument({{ $modalDocument->id }})"
                                                wire:loading.attr="disabled"
                                                class="btn btn-sm btn-danger">
                                            Yes
                                        </button>
                                        <button wire:click="$set('confirmDeleteId', null)"
                                                class="btn btn-sm btn-secondary">
                                            No
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @elseif ($modalDocument)
                            <button wire:click="confirmDelete({{ $modalDocument->id }})"
                                    class="btn btn-sm btn-danger">
                                <i class="fa fa-trash me-1"></i> Delete
                            </button>
                        @endif

                    </div>

                    {{-- Right side: Close --}}
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Close
                    </button>

                </div>


            </div>
        </div>
    </div>





</div>

<script>
    Livewire.on('confirmDelete', documentId => {
        if (confirm("Delete this document? This action cannot be undone.")) {
            Livewire.dispatch('deleteDocument', {
                id: documentId
            });
        }
    });

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text)
            .then(() => alert("Copied: " + text))
            .catch(err => console.log(err));
    }
</script>


<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>


<script>
    document.addEventListener('livewire:load', function() {
        const element = document.getElementById('employeeSelect');
        const choices = new Choices(element, {
            searchEnabled: true,
            itemSelectText: '',
            shouldSort: false,
            maxItemCount: 1,
            position: 'bottom',
            placeholderValue: '-- Select Employee --',
            searchPlaceholderValue: 'Search employee...',
            renderChoiceLimit: 5 // visible items, baki scrollable
        });

        Livewire.hook('message.processed', (message, component) => {
            // re-initialize on update
            choices.destroy();
            new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                shouldSort: false,
                maxItemCount: 1,
                position: 'bottom',
                placeholderValue: '-- Select Employee --',
                searchPlaceholderValue: 'Search employee...',
                renderChoiceLimit: 5
            });
        });
    });

    document.addEventListener('livewire:load', function() {
        Livewire.on('documentModalOpened', () => {
            var modal = new bootstrap.Modal(document.getElementById('documentModal'));
            modal.show();
        });
    });
</script>


<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('reload-page', () => {
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        });
    });
</script>
