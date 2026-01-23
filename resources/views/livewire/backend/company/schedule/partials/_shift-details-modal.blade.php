  <div class="modal fade" id="shiftDetailsModal-{{ $shift['id'] }}" aria-hidden="true" tabindex="-1" wire:ignore.self
      data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">

              <div class="modal-header bg-primary text-white">
                  <h5 class="modal-title text-white">{{ $content['title'] ?? '-' }}
                  </h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                      aria-label="Close"></button>
              </div>

              <div class="modal-body">
                  <div class="row mb-2">
                      <div class="col-sm-6">
                          <strong>Time:</strong> {{ $content['time'] ?? '-' }}
                      </div>
                      <div class="col-sm-6">
                          <strong>Address:</strong>
                          {{ $content['shift']['address'] ?? '-' }}
                      </div>
                  </div>

                  @if (!empty($content['employees']))
                      @php
                          $employees = collect($content['employees'])->pluck('name')->toArray();
                          $showLimit = 5;
                          $moreCount = count($employees) - $showLimit;
                      @endphp

                      <div class="mb-2">
                          <strong>Employees:</strong>
                          {{ implode(', ', array_slice($employees, 0, $showLimit)) }}
                          @if ($moreCount > 0)
                              <span class="text-muted">+{{ $moreCount }}
                                  more</span>
                          @endif
                      </div>
                  @endif


                  @if (!empty($content['shift']['note']))
                      <div class="mb-2">
                          <strong>Note:</strong>
                          {{ $content['shift']['note'] ?? '-' }}
                      </div>
                  @endif

                  @if (!empty($content['breaks']))
                      <div class="mb-2">
                          <strong>Breaks:</strong>
                          <table class="table table-sm table-bordered mt-1">
                              <thead>
                                  <tr>
                                      <th>Title</th>
                                      <th>Type</th>
                                      <th>Duration (hr)</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  @foreach ($content['breaks'] as $break)
                                      <tr>
                                          <td>{{ $break['title'] }}</td>
                                          <td>{{ $break['type'] }}</td>
                                          <td>{{ $break['duration'] }}</td>
                                      </tr>
                                  @endforeach
                              </tbody>
                          </table>
                      </div>
                  @endif
              </div>

              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              </div>

          </div>
      </div>
  </div>
