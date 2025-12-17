<div class="modal fade" id="customAddMultipleShiftModal" tabindex="-1" wire:ignore.self data-bs-backdrop="static"
  data-bs-keyboard="false">

  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">

      {{-- HEADER --}}
      <div class="modal-header border-0">
        <button type="button" class="btn btn-sm btn-transparent p-0 text-white ms-auto" aria-label="Remove">
          <i class="fas fa-times"></i>
        </button>

      </div>

      {{-- BODY --}}
      <div class="modal-body pt-0">

        @foreach ($multipleShifts as $index => $shift)
        <div class="border rounded p-3 mb-3">

          {{-- Row header --}}
          <div class="d-flex justify-content-between mb-2">
            <strong class="small">Shift {{ $index + 1 }}</strong>

            @if (count($multipleShifts) > 1)
            <button type="button" wire:click="removeShiftRow({{ $index }})" class="btn btn-sm btn-outline-danger">
              Remove
            </button>
            @endif
          </div>

          {{-- DATE --}}
          <div class="row g-2 mb-2">
            <div class="col-3">
              <label class="fw-semibold small">Date *</label>
            </div>
            <div class="col-9">
              <input type="date" class="form-control form-control-sm" wire:model="multipleShifts.{{ $index }}.date">
            </div>
          </div>

          {{-- TIME --}}
          <div class="row g-2 mb-2">
            <div class="col-3">
              <label class="fw-semibold small">Time *</label>
            </div>
            <div class="col-9 d-flex gap-2">
              <input type="time" class="form-control form-control-sm"
                wire:model="multipleShifts.{{ $index }}.start_time">
              <span>→</span>
              <input type="time" class="form-control form-control-sm" wire:model="multipleShifts.{{ $index }}.end_time">
            </div>
          </div>

          {{-- TITLE --}}
          <div class="row g-2 mb-2">
            <div class="col-3">
              <label class="fw-semibold small">Title *</label>
            </div>
            <div class="col-9">
              <input type="text" class="form-control form-control-sm"
                wire:model.defer="multipleShifts.{{ $index }}.title">
            </div>
          </div>

          {{-- JOB + COLOR --}}
          <div class="row g-2 mb-2">
            <div class="col-3">
              <label class="fw-semibold small">Job *</label>
            </div>
            <div class="col-9 d-flex gap-2">
              <input type="text" class="form-control form-control-sm"
                wire:model.defer="multipleShifts.{{ $index }}.job">

              <input type="color" wire:model="multipleShifts.{{ $index }}.color"
                class="form-control form-control-color border-0">
            </div>
          </div>

          {{-- NOTE --}}
          <div class="row g-2">
            <div class="col-3">
              <label class="fw-semibold small">Note</label>
            </div>
            <div class="col-9">
              <textarea rows="2" class="form-control form-control-sm"
                wire:model.defer="multipleShifts.{{ $index }}.note"></textarea>
            </div>
          </div>

        </div>
        @endforeach

        {{-- ADD ROW --}}
        <button type="button" wire:click="addShiftRow" class="btn btn-sm btn-outline-primary w-100">
          + Add another shift
        </button>

      </div>

      {{-- FOOTER --}}
      <div class="modal-footer">
        <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button wire:click="saveMultipleShifts" class="btn btn-primary">
          Save Shifts
        </button>
      </div>

    </div>
  </div>
</div>