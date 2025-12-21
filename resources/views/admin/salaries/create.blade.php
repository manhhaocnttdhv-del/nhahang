@extends('layouts.app')

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-cash-coin"></i> Tạo Bảng Lương</h2>
        <a href="{{ route('admin.salaries.index') }}" class="btn btn-secondary">Quay lại</a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.salaries.store') }}" method="POST" id="salaryForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Nhân viên <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required id="user_id">
                                <option value="">-- Chọn nhân viên --</option>
                                @foreach($staff as $s)
                                    <option value="{{ $s->id }}" {{ old('user_id') == $s->id ? 'selected' : '' }}
                                        data-employment-type="{{ $s->employment_type }}"
                                        data-base-salary="{{ $s->base_salary ?? 0 }}"
                                        data-hourly-rate="{{ $s->hourly_rate ?? 0 }}">
                                        {{ $s->name }} ({{ $s->employment_type === 'full_time' ? 'Full-time' : 'Part-time' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Loại nhân viên <span class="text-danger">*</span></label>
                            <select name="employment_type" class="form-select @error('employment_type') is-invalid @enderror" required id="employment_type">
                                <option value="full_time" {{ old('employment_type') == 'full_time' ? 'selected' : '' }}>Full-time</option>
                                <option value="part_time" {{ old('employment_type') == 'part_time' ? 'selected' : '' }}>Part-time</option>
                            </select>
                            @error('employment_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <!-- Full-time fields -->
                        <div id="full_time_fields">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Lương cơ bản <span class="text-danger">*</span></label>
                                        <input type="number" name="base_salary" class="form-control @error('base_salary') is-invalid @enderror" 
                                               value="{{ old('base_salary') }}" min="0" step="0.01" id="base_salary">
                                        @error('base_salary')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Số ngày làm việc</label>
                                        <input type="number" name="working_days" class="form-control @error('working_days') is-invalid @enderror" 
                                               value="{{ old('working_days', 22) }}" min="0" max="31">
                                        @error('working_days')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Part-time fields -->
                        <div id="part_time_fields" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Số giờ làm việc <span class="text-danger">*</span></label>
                                        <input type="number" name="working_hours" class="form-control @error('working_hours') is-invalid @enderror" 
                                               value="{{ old('working_hours') }}" min="0" step="0.5" id="working_hours">
                                        @error('working_hours')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Lương theo giờ <span class="text-danger">*</span></label>
                                        <input type="number" name="hourly_rate" class="form-control @error('hourly_rate') is-invalid @enderror" 
                                               value="{{ old('hourly_rate') }}" min="0" step="0.01" id="hourly_rate">
                                        @error('hourly_rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Salary (Auto-calculated) -->
                        <div class="mb-3">
                            <div class="alert alert-info">
                                <strong>Tổng lương: <span id="total_salary_display">0</span> đ</strong>
                                <input type="hidden" name="total_salary" id="total_salary" value="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i> Tạo Bảng Lương
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Toggle fields based on employment type
        function toggleEmploymentFields() {
            const employmentType = $('#employment_type').val();
            if (employmentType === 'full_time') {
                $('#full_time_fields').show();
                $('#part_time_fields').hide();
                $('#working_hours').removeAttr('required');
                $('#hourly_rate').removeAttr('required');
                $('#base_salary').attr('required', 'required');
            } else {
                $('#full_time_fields').hide();
                $('#part_time_fields').show();
                $('#base_salary').removeAttr('required');
                $('#working_hours').attr('required', 'required');
                $('#hourly_rate').attr('required', 'required');
            }
            calculateTotal();
        }

        // Auto-fill from user selection
        $('#user_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            if (selectedOption.val()) {
                const employmentType = selectedOption.data('employment-type');
                const baseSalary = selectedOption.data('base-salary');
                const hourlyRate = selectedOption.data('hourly-rate');
                
                if (employmentType) {
                    $('#employment_type').val(employmentType).trigger('change');
                }
                if (baseSalary) {
                    $('#base_salary').val(baseSalary);
                }
                if (hourlyRate) {
                    $('#hourly_rate').val(hourlyRate);
                }
            }
        });

        // Calculate total salary
        function calculateTotal() {
            const employmentType = $('#employment_type').val();
            let total = 0;

            if (employmentType === 'full_time') {
                total = parseFloat($('#base_salary').val() || 0);
            } else {
                const workingHours = parseFloat($('#working_hours').val() || 0);
                const hourlyRate = parseFloat($('#hourly_rate').val() || 0);
                total = workingHours * hourlyRate;
            }


            // Update display
            $('#total_salary_display').text(total.toLocaleString('vi-VN'));
            $('#total_salary').val(total.toFixed(2));
        }

        // Event listeners
        $('#employment_type').on('change', toggleEmploymentFields);
        $('#base_salary, #working_hours, #hourly_rate').on('input', calculateTotal);

        // Initialize
        toggleEmploymentFields();
    });
</script>
@endpush
@endsection

