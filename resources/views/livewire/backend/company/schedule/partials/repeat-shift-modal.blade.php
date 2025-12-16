<div class="modal fade" id="customRepeatShiftModal" tabindex="-1" aria-labelledby="customRepeatShiftModalLabel"
    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>

    <div class="modal-dialog modal-dialog-centered shift-modal-dialog">
        <div class="modal-content shift-modal-content">

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

            <div class="modal-body shift-modal-body p-3 pt-0">

                <div class="mb-3 mt-5">
                    <div class="d-flex align-items-center">




                        <div class="dropdown" style="position: relative; display: inline-block;">
                            <button class="btn btn-sm dropdown-toggle text-center w-100" type="button"
                                data-bs-toggle="dropdown"
                                style="border: none; background-color: transparent; color: #000; font-weight: 500;">
                                {{ $frequency ?? 'Select' }}
                            </button>

                            <div class="dropdown-menu p-2"
                                style="max-height: 200px; overflow-y:auto; left: 0 !important; min-width: auto !important;">
                                @foreach (['Monthly', 'Weekly', 'Daily'] as $option)
                                    <a href="#" class="dropdown-item text-center"
                                        wire:change="handleChangeFrequency($event.target.value)"
                                        style="color: #000; width: 100%; text-align: center;">
                                        {{ $option }}
                                    </a>
                                @endforeach
                            </div>
                        </div>





                        <span class="text-muted me-2 ms-4">Every</span>
                        <div class="dropdown" style="position: relative; display: inline-block;">
                            <button class="btn btn-sm dropdown-toggle text-center w-100" type="button"
                                data-bs-toggle="dropdown"
                                style="border: none; background-color: transparent; color: #000; font-weight: 500;">
                                {{ $every ?? 'Select' }}
                            </button>

                            <div class="dropdown-menu p-2"
                                style="max-height: 200px; overflow-y:auto; left: 0 !important; min-width: auto !important;">
                                @foreach ($everyOptions as $option)
                                    <a href="#" class="dropdown-item text-center"
                                        wire:click.prevent="$set('every', {{ $option }})"
                                        style="color: #000; width: 100%; text-align: center;">
                                        {{ $option }}
                                    </a>
                                @endforeach
                            </div>
                        </div>





                        <span class="text-muted m-2">
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

                @if ($every !== 'Daily')
                    <div class="mb-3 mt-4">
                        <div class="d-flex align-items-center">
                            <span class="text-muted me-2">Repeats on</span>

                            <div class="dropdown" style="position: relative; display: inline-block; min-width: 200px;">
                                <button class="btn btn-sm dropdown-toggle text-center w-100" type="button"
                                    data-bs-toggle="dropdown"
                                    style="border: none; background-color: transparent; color: #000; font-weight: 500;">
                                    {{ $repeatOn ?? 'Select' }}
                                </button>

                                <div class="dropdown-menu p-2"
                                    style="max-height: 250px; overflow-y:auto; left: 0 !important; min-width: auto !important;">

                                    @php
                                        $options = [];
                                        if ($every == 'Monthly') {
                                            $options = [
                                                'First Sunday',
                                                'First Monday',
                                                'First Tuesday',
                                                'First Wednesday',
                                                'First Thursday',
                                                'First Friday',
                                                'First Saturday',
                                                'Second Sunday',
                                                'Second Monday',
                                                'Second Tuesday',
                                                'Second Wednesday',
                                                'Second Thursday',
                                                'Second Friday',
                                                'Second Saturday',
                                                'Third Sunday',
                                                'Third Monday',
                                                'Third Tuesday',
                                                'Third Wednesday',
                                                'Third Thursday',
                                                'Third Friday',
                                                'Third Saturday',
                                                'Last Sunday',
                                                'Last Monday',
                                                'Last Tuesday',
                                                'Last Wednesday',
                                                'Last Thursday',
                                                'Last Friday',
                                                'Last Saturday',
                                                'End Of Month',
                                                '1st',
                                                '2nd',
                                                '3rd',
                                                '4th',
                                                '5th',
                                                '6th',
                                                '7th',
                                                '8th',
                                                '9th',
                                                '10th',
                                                '11th',
                                                '12th',
                                                '13th',
                                                '14th',
                                                '15th',
                                                '16th',
                                                '17th',
                                                '18th',
                                                '19th',
                                                '20th',
                                                '21st',
                                                '22nd',
                                                '23rd',
                                                '24th',
                                                '25th',
                                                '26th',
                                                '27th',
                                                '28th',
                                                '29th',
                                                '30th',
                                                '31st',
                                            ];
                                        } elseif ($every == 'Weekly') {
                                            $options = [
                                                'Sunday',
                                                'Monday',
                                                'Tuesday',
                                                'Wednesday',
                                                'Thursday',
                                                'Friday',
                                                'Saturday',
                                            ];
                                        }
                                    @endphp

                                    @foreach ($options as $option)
                                        <a href="#" class="dropdown-item text-center"
                                            wire:click.prevent="$set('repeatOn', '{{ $option }}')"
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

                <div class="mb-5">
                    <div class="d-flex align-items-center">
                        <span class="text-muted me-2">End repeat</span>

                        <div class="dropdown me-2" style="position: relative; display: inline-block; width: 120px;">
                            <button class="btn btn-sm dropdown-toggle text-center w-100" type="button"
                                data-bs-toggle="dropdown"
                                style="border: none; background-color: transparent; color: #000; font-weight: 500;">
                                {{ $endRepeat ?? 'Select' }}
                            </button>

                            <div class="dropdown-menu p-2"
                                style="max-height: 200px; overflow-y:auto; left: 0 !important; min-width: auto !important;">
                                @foreach (['After', 'On'] as $option)
                                    <a href="#" class="dropdown-item text-center"
                                        wire:click.prevent="$set('endRepeat', '{{ $option }}')"
                                        style="color: #000; width: 100%; text-align: center;">
                                        {{ $option }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        <input type="number" class="form-control shift-input-control me-2 text-center" value="5"
                            min="0" style="width: 70px;" oninput="this.value = Math.max(0, this.value)">


                        <span class="text-muted">occurrences</span>
                    </div>
                </div>

            </div>

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
