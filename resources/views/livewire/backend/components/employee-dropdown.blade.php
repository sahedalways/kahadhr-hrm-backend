  @push('styles')
      <link href="{{ asset('assets/css/company/custom-field.css') }}"
            rel="stylesheet" />
  @endpush


  <div class="mb-3"
       x-data="{
           open: false,
           selectedEmployees: @entangle('selectedEmployees'),
           employees: {{ json_encode($employees->map(fn($e) => ['id' => $e->id, 'full_name' => $e->full_name])) }},
           selectAll: false,
       
           toggleDropdown() {
               this.open = !this.open;
           },
       
           closeDropdown() {
               this.open = false;
           },
       
           isSelected(empId) {
               return this.selectedEmployees.includes(empId);
           },
       
           toggleEmployee(empId) {
               if (this.isSelected(empId)) {
                   this.selectedEmployees = this.selectedEmployees.filter(id => id !== empId);
               } else {
                   this.selectedEmployees = [...this.selectedEmployees, empId];
               }
           },
       
           toggleSelectAll() {
               if (this.selectAll) {
                   this.selectedEmployees = this.employees.map(emp => emp.id);
               } else {
                   this.selectedEmployees = [];
               }
           },
       
           getDisplayText() {
               if (this.selectedEmployees.length === 0) return 'Select employees...';
               const selectedNames = this.employees
                   .filter(emp => this.selectedEmployees.includes(emp.id))
                   .map(emp => emp.full_name);
       
               if (selectedNames.length <= 2) return selectedNames.join(', ');
               return selectedNames.slice(0, 2).join(', ') + ` +${selectedNames.length - 2} more`;
           }
       }"
       @click.away="closeDropdown()">

      <label class="form-label fw-bold text-secondary">Select Employees <span class="text-danger">*</span></label>

      <div class="position-relative">
          <!-- Dropdown Button -->
          <button type="button"
                  class="btn btn-white border w-100 d-flex justify-content-between align-items-center py-2 text-start"
                  @click.prevent.stop="toggleDropdown()">
              <span class="text-truncate"
                    style="max-width:85%;">
                  <span x-text="getDisplayText()"
                        :class="selectedEmployees.length === 0 ? 'text-muted' : ''"></span>
              </span>
              <i class="fas fa-chevron-down small text-muted"
                 :style="{ transform: open ? 'rotate(180deg)' : 'rotate(0deg)', transition: 'transform 0.2s' }"></i>
          </button>

          <!-- Dropdown Menu -->
          <div x-show="open"
               x-cloak
               class="position-absolute dropdown-menu-custom mt-1 bg-white border rounded-3 shadow-lg"
               style="z-index: 1050; max-height: 300px; overflow: hidden;"
               @click.stop>

              <div class="p-2 border-bottom">
                  <div class="form-check">
                      <input type="checkbox"
                             class="form-check-input"
                             id="selectAllEmployees"
                             x-model="selectAll"
                             @change="toggleSelectAll()">
                      <label class="form-check-label fw-bold"
                             for="selectAllEmployees">
                          Select All
                      </label>
                  </div>
              </div>

              <div class="overflow-auto"
                   style="max-height: 250px; padding-left: 0.7rem;">
                  <template x-for="employee in employees"
                            :key="employee.id">
                      <div class="form-check px-4 py-2 hover-bg-light"
                           style="cursor: pointer;">
                          <input type="checkbox"
                                 class="form-check-input"
                                 :id="'emp-' + employee.id"
                                 :checked="isSelected(employee.id)"
                                 @change="toggleEmployee(employee.id)">
                          <label class="form-check-label w-100"
                                 :for="'emp-' + employee.id"
                                 x-text="employee.full_name"></label>
                      </div>
                  </template>
              </div>
          </div>
      </div>

      @error('selectedEmployees')
          <div class="text-danger mt-1 text-sm">
              {{ $message }}
          </div>
      @enderror
  </div>
