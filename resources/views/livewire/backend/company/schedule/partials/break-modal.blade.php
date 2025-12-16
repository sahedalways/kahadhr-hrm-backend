<div class="modal fade" id="customAddBreakModal" tabindex="-1" wire:ignore.self data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered custom-break-modal-dialog">
        <div class="modal-content">


            @if (!$showAddBreakForm)
                <div class="modal-header border-0 pb-2 pe-4 position-relative">
                    <h5 class="modal-title fs-6 text-secondary mx-auto" id="customAddBreakModalLabel">
                        Add breaks to shift details
                    </h5>
                    <button type="button"
                        class="btn btn-light btn-sm rounded-circle d-flex align-items-center justify-content-center position-absolute end-0 top-50 translate-middle-y"
                        data-bs-dismiss="modal" style="width: 28px; height: 28px; padding: 0;">
                        <i class="fas fa-times" style="font-size: 14px; color: #000;"></i>
                    </button>
                </div>
            @else
                <div class="modal-header border-0 pb-2 pe-4 position-relative">
                    <button type="button"
                        class="btn btn-light btn-sm rounded-circle d-flex align-items-center justify-content-center position-absolute end-3 top-50 translate-middle-y"
                        data-bs-dismiss="modal" style="width: 28px; height: 28px; padding: 0;">
                        <i class="fas fa-times" style="font-size: 14px; color: #000;"></i>
                    </button>
                </div>
            @endif



            <div class="modal-body text-center pt-2 mt-5">
                @if (!$showAddBreakForm)
                    <div class="d-flex justify-content-center mb-3">
                        <div class="custom-break-modal-icon-circle p-3 rounded-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"
                                fill="none" class="ct-icon" data-testid="icon"
                                style="min-width: 40px; min-height: 40px; color: var(--ct-green-6);">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M10 17.625C8.2 17.625 6.66667 16.9917 5.4 15.725C4.13333 14.4583 3.5 12.925 3.5 11.125V5C3.5 4.58333 3.646 4.22933 3.938 3.938C4.22933 3.646 4.58333 3.5 5 3.5H17.1C17.95 3.5 18.675 3.8 19.275 4.4C19.875 5 20.175 5.725 20.175 6.575C20.175 7.425 19.875 8.15 19.275 8.75C18.675 9.35 17.95 9.65 17.1 9.65H16.5V11.125C16.5 12.925 15.8667 14.4583 14.6 15.725C13.3333 16.9917 11.8 17.625 10 17.625ZM5 8.15H15V5H5V8.15ZM10 16.125C11.3833 16.125 12.5627 15.6373 13.538 14.662C14.5127 13.6873 15 12.5083 15 11.125V9.65H5V11.125C5 12.5083 5.48767 13.6873 6.463 14.662C7.43767 15.6373 8.61667 16.125 10 16.125ZM16.5 8.15H17.1C17.5333 8.15 17.904 8 18.212 7.7C18.5207 7.4 18.675 7.025 18.675 6.575C18.675 6.14167 18.5207 5.771 18.212 5.463C17.904 5.15433 17.5333 5 17.1 5H16.5V8.15ZM17.25 18.75H15C14.7875 18.75 14.6094 18.6781 14.4656 18.5343C14.3219 18.3904 14.25 18.2122 14.25 17.9997C14.25 17.7871 14.3219 17.609 14.4656 17.4654C14.6094 17.3218 14.7875 17.25 15 17.25H17.25V15C17.25 14.7875 17.3219 14.6094 17.4657 14.4656C17.6095 14.3219 17.7877 14.25 18.0003 14.25C18.2129 14.25 18.391 14.3219 18.5346 14.4656C18.6782 14.6094 18.75 14.7875 18.75 15V17.25H21C21.2125 17.25 21.3906 17.3219 21.5344 17.4657C21.6781 17.6095 21.75 17.7877 21.75 18.0003C21.75 18.2129 21.6781 18.391 21.5344 18.5346C21.3906 18.6782 21.2125 18.75 21 18.75H18.75V21C18.75 21.2125 18.6781 21.3906 18.5343 21.5343C18.3904 21.6781 18.2122 21.75 17.9997 21.75C17.7871 21.75 17.609 21.6781 17.4654 21.5344C17.3218 21.3906 17.25 21.2125 17.25 21V18.75ZM3.713 19.2121C3.85433 19.0708 4.03333 19.0001 4.25 19.0001L12.5 19C12.7167 19 12.8957 19.0707 13.037 19.212C13.179 19.354 13.25 19.5333 13.25 19.75C13.25 19.9667 13.179 20.146 13.037 20.288C12.8957 20.4293 12.7167 20.5 12.5 20.5L4.25 20.5001C4.03333 20.5001 3.85433 20.4294 3.713 20.2881C3.571 20.1461 3.5 19.9668 3.5 19.7501C3.5 19.5334 3.571 19.3541 3.713 19.2121Z"
                                    fill="currentColor"></path>
                            </svg>
                            <div class="custom-break-modal-plus-icon">
                                +
                            </div>
                        </div>
                    </div>

                    <p class="mb-4">Add the first break to this shift</p>
                @endif


                @if ($showAddBreakForm)
                    <div class="modal-body">

                        <h6 class="text-secondary mb-3 text-center">Add break types</h6>

                        @foreach ($newBreaks as $index => $break)
                            <div class="row g-2 align-items-end mb-3 text-center">

                                <div class="col-4">
                                    <label class="form-label small text-secondary mb-1">
                                        Break name
                                    </label>
                                    <input type="text" class="form-control form-control-sm text-center"
                                        wire:model.live="newBreaks.{{ $index }}.name" placeholder="Type here">
                                </div>

                                <div class="col-3">
                                    <label class="form-label small text-secondary mb-1">
                                        Type
                                    </label>
                                    <select class="form-select form-select-sm text-center"
                                        wire:model.live="newBreaks.{{ $index }}.type">
                                        <option value="">Select</option>
                                        <option value="Paid">Paid</option>
                                        <option value="Unpaid">Unpaid</option>
                                    </select>
                                </div>
                                <div class="col-3">
                                    {{-- <label class="form-label small text-secondary mb-1 text-center">
                                        Duration
                                    </label>
                                    <select class="form-select form-select-sm text-center custom-duration-dropdown"
                                        wire:model.live="newBreaks.{{ $index }}.duration">

                                        @for ($i = 0; $i <= 23; $i += 0.05)
                                            <option value="{{ number_format($i, 2) }}">{{ number_format($i, 2) }}
                                            </option>
                                        @endfor
                                    </select> --}}
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100"
                                            data-bs-toggle="dropdown">
                                            {{ $newBreaks[$index]['duration'] ?? 'Select duration' }}
                                        </button>


                                        <div class="dropdown-menu p-2" style="max-height:200px; overflow-y:auto;">
                                            @for ($i = 0; $i <= 23; $i += 0.05)
                                                <button type="button" class="dropdown-item text-center"
                                                    wire:click="$set('newBreaks.{{ $index }}.duration', '{{ number_format($i, 2) }}')">
                                                    {{ number_format($i, 2) }}
                                                </button>
                                            @endfor
                                        </div>
                                    </div>

                                </div>




                                <div class="col-2">
                                    <label class="form-label small text-secondary mb-1 d-block">
                                        &nbsp;
                                    </label>
                                    <button wire:click="removeBreakRow({{ $index }})" type="button"
                                        class="btn btn-light btn-sm rounded-circle d-flex align-items-center justify-content-center mx-auto"
                                        style="width: 28px; height: 28px; padding: 0;">
                                        <i class="fas fa-times" style="font-size: 14px; color: #000;"></i>
                                    </button>
                                </div>

                            </div>
                        @endforeach



                    </div>
                @endif





                <div class="dropdown d-inline-block">
                    <button class="btn btn-primary dropdown-toggle custom-break-dropdown-btn" type="button"
                        id="customAddBreakDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Add break
                    </button>

                    <ul class="dropdown-menu shadow-sm custom-break-dropdown-menu"
                        aria-labelledby="customAddBreakDropdown" style="max-height: 200px; overflow-y: auto;">
                        <li class="dropdown-header custom-break-title">Break types</li>

                        @forelse($breaks as $break)
                            @if (empty($break['new']))
                                @php
                                    $duration = (float) $break['duration'];
                                    $hours = floor($duration);
                                    $minutes = ($duration - $hours) * 100;
                                    $totalMinutes = $hours * 60 + $minutes;
                                @endphp

                                <li>
                                    <a class="dropdown-item text-start" href="#"
                                        wire:click.prevent="addExistingBreak({{ $break['id'] }})">
                                        {{ $break['title'] }} - {{ $break['type'] }} - {{ $totalMinutes }} min
                                    </a>
                                </li>
                            @endif


                        @empty
                            <li><span class="dropdown-item text-muted">No breaks found</span></li>
                        @endforelse

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <button type="button" class="dropdown-item text-start d-flex align-items-center"
                                wire:click.prevent="addBreakRow">
                                <span class="text-primary me-2">+</span> Add break type
                            </button>
                        </li>
                    </ul>
                </div>

            </div>

            @php
                $confirmActive = false;

                foreach ($newBreaks as $break) {
                    if (!empty($break['name']) && !empty($break['type']) && !empty($break['duration'])) {
                        $confirmActive = true;
                        break;
                    }
                }
            @endphp

            <div class="modal-footer border-0 p-3 justify-content-end">
                <button type="button"
                    class="btn custom-confirm-btn {{ $confirmActive ? 'active-confirm-btn' : 'btn-light' }}"
                    {{ $confirmActive ? '' : 'disabled' }} wire:click="confirmBreaksAndSave"
                    wire:loading.attr="disabled" wire:target="confirmBreaksAndSave">
                    <span wire:loading.remove wire:target="confirmBreaksAndSave">Confirm</span>
                    <span wire:loading wire:target="confirmBreaksAndSave">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Loading...
                    </span>
                </button>
            </div>



        </div>
    </div>


</div>
