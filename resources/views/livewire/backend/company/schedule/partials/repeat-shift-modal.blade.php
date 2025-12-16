<div class="modal fade" id="customRepeatShiftModal" tabindex="-1" aria-labelledby="customRepeatShiftModalLabel"
    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>

    <div class="modal-dialog modal-dialog-centered shift-modal-dialog">
        <div class="modal-content shift-modal-content">

            <!-- Modal Header -->
            <div class="modal-header border-0 pb-2 pe-4 position-relative">
                <h5 class="modal-title fs-6 text-secondary mx-auto" id="customRepeatShiftModalLabel">
                    Repeating shift settings
                </h5>
                <button type="button"
                    class="btn btn-light btn-sm rounded-circle d-flex align-items-center justify-content-center position-absolute end-0 top-50 translate-middle-y"
                    data-bs-dismiss="modal" style="width: 28px; height: 28px; padding: 0;">
                    <i class="fas fa-times" style="font-size: 14px; color: #000;"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body shift-modal-body p-3 pt-0">

                <!-- Frequency & Every -->
                <div class="mb-3 mt-5">
                    <div class="d-flex align-items-center gap-3">

                        <!-- Frequency Dropdown -->
                        <div class="dropdown flex-grow-1">
                            <button class="btn btn-sm dropdown-toggle text-center w-100" type="button"
                                data-bs-toggle="dropdown"
                                style="border: none; background-color: transparent; color: #000; font-weight: 500;">
                                {{ $frequency ?? 'Select' }}
                            </button>
                            <div class="dropdown-menu p-2"
                                style="max-height: 200px; overflow-y:auto; min-width: 120px;">
                                @foreach (['Monthly', 'Weekly', 'Daily'] as $option)
                                    <a href="#" class="dropdown-item text-center"
                                        wire:click.prevent="$set('frequency', '{{ $option }}')"
                                        style="color: #000; width: 100%; text-align: center;">
                                        {{ $option }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <!-- Every Dropdown -->
                        <span class="text-muted">Every</span>
                        <div class="dropdown flex-grow-1" style="min-width: 70px;">
                            <button class="btn btn-sm dropdown-toggle text-center w-100" type="button"
                                data-bs-toggle="dropdown"
                                style="border: none; background-color: transparent; color: #000; font-weight: 500;">
                                {{ $every ?? 'Select' }}
                            </button>
                            <div class="dropdown-menu p-2" style="max-height: 200px; overflow-y:auto; min-width: 70px;">
                                @foreach ($everyOptions as $option)
                                    <a href="#" class="dropdown-item text-center"
                                        wire:click.prevent='$set("every", {{ $option }})'
                                        style="color: #000; width: 100%; text-align: center;">
                                        {{ $option }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <span class="text-muted">
                            @if ($frequency === 'Monthly')
                                Months
                            @elseif($frequency === 'Weekly')
                                Weeks
                            @else
                                Days
                            @endif
                        </span>

                    </div>
                </div>

                <hr class="my-3">

                <!-- Repeats On Dropdown -->
                @if ($every !== 'Daily')
                    <div class="mb-3 mt-4">
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted me-2">Repeats on</span>

                            <div class="dropdown flex-grow-1" style="min-width: 200px;">
                                <button class="btn btn-sm dropdown-toggle text-center w-100" type="button"
                                    data-bs-toggle="dropdown"
                                    style="border: none; background-color: transparent; color: #000; font-weight: 500;">
                                    {{ $repeatOn ?? 'Select' }}
                                </button>

                                <div class="dropdown-menu p-2"
                                    style="max-height: 250px; overflow-y:auto; min-width: 200px;">
                                    @foreach ($repeatOptions as $option)
                                        <a href="#" class="dropdown-item text-center"
                                            wire:click.prevent='$set("repeatOn", "{{ $option }}")'
                                            style="color: #000; width: 100%; text-align: center;">
                                            {{ $option }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <hr class="my-3">

                <!-- End Repeat -->
                <div class="mb-5">
                    <div class="d-flex align-items-center gap-2">

                        <span class="text-muted">End repeat</span>

                        <!-- End Repeat Type -->
                        <div class="dropdown" style="width: 120px;">
                            <button class="btn btn-sm dropdown-toggle text-center w-100" type="button"
                                data-bs-toggle="dropdown"
                                style="border: none; background-color: transparent; color: #000; font-weight: 500;">
                                {{ $endRepeat ?? 'Select' }}
                            </button>
                            <div class="dropdown-menu p-2"
                                style="max-height: 200px; overflow-y:auto; min-width: 120px;">
                                @foreach (['After', 'On'] as $option)
                                    <a href="#" class="dropdown-item text-center"
                                        wire:click.prevent="$set('endRepeat', '{{ $option }}')"
                                        style="color: #000; width: 100%; text-align: center;">
                                        {{ $option }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <!-- Occurrences Input -->
                        <div style="width: 70px;">
                            <input type="number" class="form-control shift-input-control text-center" value="5"
                                min="0" oninput="this.value = Math.max(0, this.value)" wire:model="occurrences">
                        </div>

                        <span class="text-muted">occurrences</span>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="modal-footer shift-modal-footer p-3 justify-content-start border-top-0">
                <a href="#" class="me-auto text-decoration-none text-secondary fs-5" aria-label="Help">
                    <i class="bi bi-question-circle"></i>
                </a>

                <button type="button" class="btn btn-link text-decoration-none me-2" data-bs-dismiss="modal">
                    Cancel repeat
                </button>

                <button type="button" class="btn btn-primary">
                    Save repeat
                </button>
            </div>

        </div>
    </div>
</div>
