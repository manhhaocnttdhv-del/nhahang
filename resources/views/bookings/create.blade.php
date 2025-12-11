@extends('layouts.app')

@section('title', 'Đặt Bàn Tại Quán')

@section('content')
<div class="container my-5">
    <!-- Header -->
    <div class="text-center mb-5 fade-in-up">
        <div class="float-animation">
            <h1 class="display-4 mb-3 gradient-text" style="font-weight: 900;">
                <i class="bi bi-calendar-check"></i> Đặt Bàn Tại Quán
            </h1>
            <p class="lead text-muted" style="font-size: 1.2rem;">Chọn bàn và thời gian phù hợp cho bạn</p>
        </div>
    </div>

    <div class="row">
        <!-- Left: Table Map -->
        <div class="col-lg-8 mb-4">
            <div class="card fade-in-up">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.5rem;">
                    <h4 class="mb-0" style="font-weight: 700;">
                        <i class="bi bi-grid-3x3-gap me-2"></i> Sơ Đồ Bàn
                    </h4>
                </div>
                <div class="card-body p-4">
                    @if($tables->count() > 0)
                        @foreach($tables as $area => $areaTables)
                            <div class="mb-5 table-area" data-area="{{ $area ?: 'Khu vực chung' }}">
                                <h5 class="mb-3" style="color: var(--dark-color); font-weight: 700;">
                                    <i class="bi bi-geo-alt-fill me-2"></i>{{ $area ?: 'Khu vực chung' }}
                                </h5>
                                <div class="row g-3">
                                    @foreach($areaTables as $table)
                                        @php
                                            $hasBooking = $table->bookings->count() > 0;
                                            $booking = $table->bookings->first();
                                        @endphp
                                        <div class="col-md-3 col-6">
                                            <div class="table-card card h-100 text-center p-3 
                                                {{ $table->status === 'available' && !$hasBooking ? 'table-available' : '' }}
                                                {{ $table->status === 'reserved' || $hasBooking ? 'table-reserved' : '' }}
                                                {{ $table->status === 'occupied' ? 'table-occupied' : '' }}
                                                {{ $table->status === 'maintenance' ? 'table-maintenance' : '' }}"
                                                data-table-id="{{ $table->id }}"
                                                data-capacity="{{ $table->capacity }}"
                                                data-area="{{ $table->area ?? '' }}"
                                                style="cursor: {{ $table->status === 'available' && !$hasBooking ? 'pointer' : 'not-allowed' }}; transition: all 0.3s;">
                                                <div class="table-icon mb-2">
                                                    <i class="bi bi-table display-4"></i>
                                                </div>
                                                <h6 class="mb-1" style="font-weight: 700;">{{ $table->name }}</h6>
                                                <small class="text-muted d-block mb-2">Bàn {{ $table->number }}</small>
                                                <div class="mb-2">
                                                    <i class="bi bi-people"></i> {{ $table->capacity }} người
                                                </div>
                                                @if($hasBooking && $booking)
                                                    <div class="booking-info mt-2 p-2 rounded" style="background: rgba(255,193,7,0.1); font-size: 0.75rem;">
                                                        <div><strong>{{ $booking->customer_name }}</strong></div>
                                                        <div>
                                                            {{ \Carbon\Carbon::parse($booking->booking_time)->format('H:i') }}
                                                            @if($booking->end_time)
                                                                - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="table-status-badge mt-2">
                                                    @if($table->status === 'available' && !$hasBooking)
                                                        <span class="badge bg-success">Trống</span>
                                                    @elseif($table->status === 'reserved' || $hasBooking)
                                                        <span class="badge bg-warning">Đã đặt</span>
                                                    @elseif($table->status === 'occupied')
                                                        <span class="badge bg-danger">Đang dùng</span>
                                                    @else
                                                        <span class="badge bg-secondary">Bảo trì</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                            <p class="text-muted">Chưa có bàn nào được thiết lập</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Selected Date Bookings Info -->
            @if(isset($selectedDateBookings) && $selectedDateBookings->count() > 0)
            <div class="card mt-4 fade-in-up" style="animation-delay: 0.3s;">
                <div class="card-header" style="background: linear-gradient(135deg, #06d6a0 0%, #048a64 100%); color: white;">
                    <h5 class="mb-0"><i class="bi bi-calendar3"></i> Đặt Bàn Ngày {{ isset($selectedDate) ? \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') : today()->format('d/m/Y') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach($selectedDateBookings->take(6) as $booking)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-2 rounded" style="background: #f8f9fa;">
                                    <div class="flex-grow-1">
                                        <strong>{{ $booking->customer_name }}</strong>
                                        <div class="small text-muted">
                                            {{ \Carbon\Carbon::parse($booking->booking_time)->format('H:i') }}
                                            @if($booking->end_time)
                                                - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                            @endif
                                            • {{ $booking->number_of_guests }} người
                                            @if($booking->table)
                                                • Bàn {{ $booking->table->name }}
                                            @endif
                                        </div>
                                    </div>
                                    <span class="badge 
                                        {{ $booking->status === 'pending' ? 'bg-warning' : '' }}
                                        {{ $booking->status === 'confirmed' ? 'bg-success' : '' }}
                                        {{ $booking->status === 'checked_in' ? 'bg-info' : '' }}">
                                        {{ $booking->status === 'pending' ? 'Chờ' : ($booking->status === 'confirmed' ? 'Đã xác nhận' : 'Đã đến') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right: Booking Form (without date/time) -->
        <div class="col-lg-4">
            <div class="card sticky-top fade-in-up" style="top: 100px; animation-delay: 0.2s;">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.5rem;">
                    <h4 class="mb-0" style="font-weight: 700;">
                        <i class="bi bi-calendar-check me-2"></i> Thông Tin Đặt Bàn
                    </h4>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="customer_name" class="form-label fw-bold">
                            <i class="bi bi-person me-2"></i>Họ và tên <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-lg @error('customer_name') is-invalid @enderror" 
                               id="customer_name" name="customer_name" 
                               value="{{ old('customer_name', auth()->user()->name ?? '') }}" required>
                        @error('customer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="customer_phone" class="form-label fw-bold">
                            <i class="bi bi-telephone me-2"></i>Số điện thoại <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-lg @error('customer_phone') is-invalid @enderror" 
                               id="customer_phone" name="customer_phone" 
                               value="{{ old('customer_phone', auth()->user()->phone ?? '') }}" required>
                        @error('customer_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="number_of_guests" class="form-label fw-bold">
                            <i class="bi bi-people me-2"></i>Số lượng khách <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control form-control-lg @error('number_of_guests') is-invalid @enderror" 
                               id="number_of_guests" name="number_of_guests" 
                               value="{{ old('number_of_guests') }}" min="1" max="50" required>
                        @error('number_of_guests')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted" id="capacityHint"></small>
                    </div>
                    
                    <div class="mb-3" id="selectedTableInfo" style="display: none;">
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Bàn đã chọn:</strong> <span id="selectedTableName"></span> 
                            (Sức chứa: <span id="selectedTableCapacity"></span> người)
                            <br>
                            <small><i class="bi bi-geo-alt me-1"></i>Vị trí: <span id="selectedTableLocation"></span></small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label fw-bold">
                            <i class="bi bi-sticky me-2"></i>Ghi chú
                        </label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3" 
                                  placeholder="Dị ứng, trẻ em đi kèm, yêu cầu đặc biệt...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Hiển thị thông tin xung đột khi chọn ngày/giờ -->
                    <div id="conflictInfo" class="mb-3" style="display: none;">
                        <div class="alert alert-warning mb-0">
                            <h6 class="mb-2"><i class="bi bi-exclamation-triangle me-2"></i>Thông tin đặt bàn:</h6>
                            <div id="conflictInfoContent" class="small">
                                <!-- Sẽ được điền bởi JavaScript -->
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Hướng dẫn:</strong> Click vào bàn để chọn ngày và giờ đặt bàn.
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Modal -->
        <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <h5 class="modal-title" id="bookingModalLabel">
                            <i class="bi bi-calendar-check me-2"></i> Đặt Bàn: <span id="modalTableName"></span>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
                            @csrf
                            
                            <input type="hidden" id="selected_table_id" name="selected_table_id">
                            <input type="hidden" id="location_preference" name="location_preference" value="{{ old('location_preference') }}">
                            
                            <div class="mb-3">
                                <label for="modal_customer_name" class="form-label fw-bold">
                                    <i class="bi bi-person me-2"></i>Họ và tên <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-lg @error('customer_name') is-invalid @enderror" 
                                       id="modal_customer_name" name="customer_name" 
                                       value="{{ old('customer_name', auth()->user()->name ?? '') }}" required>
                                @error('customer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="modal_customer_phone" class="form-label fw-bold">
                                    <i class="bi bi-telephone me-2"></i>Số điện thoại <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-lg @error('customer_phone') is-invalid @enderror" 
                                       id="modal_customer_phone" name="customer_phone" 
                                       value="{{ old('customer_phone', auth()->user()->phone ?? '') }}" required>
                                @error('customer_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="modal_booking_date" class="form-label fw-bold">
                                    <i class="bi bi-calendar me-2"></i>Ngày <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('booking_date') is-invalid @enderror" 
                                       id="modal_booking_date" name="booking_date" 
                                       value="{{ old('booking_date', date('Y-m-d')) }}" 
                                       min="{{ date('Y-m-d') }}" required>
                                @error('booking_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Hiển thị các giờ đã đặt của bàn này -->
                            <div class="mb-4">
                                <label class="form-label fw-bold mb-2">
                                    <i class="bi bi-clock-history me-2"></i>Giờ đã đặt của bàn này (<span id="tableBookingsDate">{{ date('d/m/Y') }}</span>)
                                </label>
                                <div id="tableBookingsList" class="card" style="background: #f8f9fa;">
                                    <div class="card-body p-3">
                                        <div id="tableBookingsContent" class="small">
                                            <div class="text-center text-muted">
                                                <i class="bi bi-hourglass-split"></i> Đang tải...
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="modal_booking_time" class="form-label fw-bold">
                                        <i class="bi bi-clock me-2"></i>Giờ bắt đầu <span class="text-danger">*</span>
                                    </label>
                                    <input type="time" class="form-control @error('booking_time') is-invalid @enderror" 
                                           id="modal_booking_time" name="booking_time" 
                                           value="{{ old('booking_time', '18:00') }}" 
                                           min="08:00" max="22:00" step="1800" required>
                                    @error('booking_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <!-- Quick select buttons -->
                                    <div class="mt-2 d-flex flex-wrap gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-primary quick-time-btn" data-time="08:00">8:00</button>
                                        <button type="button" class="btn btn-sm btn-outline-primary quick-time-btn" data-time="10:00">10:00</button>
                                        <button type="button" class="btn btn-sm btn-outline-primary quick-time-btn" data-time="12:00">12:00</button>
                                        <button type="button" class="btn btn-sm btn-outline-primary quick-time-btn" data-time="14:00">14:00</button>
                                        <button type="button" class="btn btn-sm btn-outline-primary quick-time-btn" data-time="16:00">16:00</button>
                                        <button type="button" class="btn btn-sm btn-outline-primary quick-time-btn" data-time="18:00">18:00</button>
                                        <button type="button" class="btn btn-sm btn-outline-primary quick-time-btn" data-time="20:00">20:00</button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="modal_duration" class="form-label fw-bold">
                                        <i class="bi bi-hourglass-split me-2"></i>Thời lượng <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('duration') is-invalid @enderror" 
                                            id="modal_duration" name="duration" required>
                                        <option value="30" {{ old('duration', '120') == '30' ? 'selected' : '' }}>30 phút</option>
                                        <option value="60" {{ old('duration', '120') == '60' ? 'selected' : '' }}>1 giờ</option>
                                        <option value="90" {{ old('duration', '120') == '90' ? 'selected' : '' }}>1.5 giờ</option>
                                        <option value="120" {{ old('duration', '120') == '120' ? 'selected' : '' }}>2 giờ</option>
                                        <option value="150" {{ old('duration', '120') == '150' ? 'selected' : '' }}>2.5 giờ</option>
                                        <option value="180" {{ old('duration', '120') == '180' ? 'selected' : '' }}>3 giờ</option>
                                        <option value="210" {{ old('duration', '120') == '210' ? 'selected' : '' }}>3.5 giờ</option>
                                        <option value="240" {{ old('duration', '120') == '240' ? 'selected' : '' }}>4 giờ</option>
                                    </select>
                                    @error('duration')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="modal_end_time" class="form-label fw-bold">
                                        <i class="bi bi-clock-history me-2"></i>Giờ kết thúc <span class="text-danger">*</span>
                                    </label>
                                    <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                                           id="modal_end_time" name="end_time" 
                                           value="{{ old('end_time', '20:00') }}" 
                                           min="08:00" max="22:00" step="1800" required readonly>
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle me-1"></i>Tự động tính
                                    </small>
                                </div>
                            </div>
                            <small class="text-muted mb-3 d-block">
                                <i class="bi bi-info-circle me-1"></i>
                                Thời gian đặt bàn từ 8:00 - 22:00. Thời lượng tối thiểu 30 phút, tối đa 4 giờ. Hệ thống sẽ tự động kiểm tra xung đột thời gian.
                            </small>

                            <div class="mb-3">
                                <label for="modal_number_of_guests" class="form-label fw-bold">
                                    <i class="bi bi-people me-2"></i>Số lượng khách <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control form-control-lg @error('number_of_guests') is-invalid @enderror" 
                                       id="modal_number_of_guests" name="number_of_guests" 
                                       value="{{ old('number_of_guests') }}" min="1" max="50" required>
                                @error('number_of_guests')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" id="modalCapacityHint"></small>
                            </div>

                            <div class="mb-3">
                                <label for="modal_notes" class="form-label fw-bold">
                                    <i class="bi bi-sticky me-2"></i>Ghi chú
                                </label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="modal_notes" name="notes" rows="3" 
                                          placeholder="Dị ứng, trẻ em đi kèm, yêu cầu đặc biệt...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-primary" id="submitBookingBtn">
                            <i class="bi bi-check-circle me-2"></i> Đặt Bàn Ngay
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table-card {
        border: 3px solid transparent;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .table-card:hover {
        transform: translateY(-5px) scale(1.05);
    }
    
    .table-available {
        border-color: #06d6a0;
        background: linear-gradient(135deg, rgba(6, 214, 160, 0.1) 0%, rgba(4, 138, 100, 0.05) 100%);
    }
    
    .table-available:hover {
        border-color: #06d6a0;
        box-shadow: 0 10px 30px rgba(6, 214, 160, 0.3);
    }
    
    .table-available .table-icon i {
        color: #06d6a0;
    }
    
    .table-reserved {
        border-color: #ffb703;
        background: linear-gradient(135deg, rgba(255, 183, 3, 0.15) 0%, rgba(251, 133, 0, 0.1) 100%);
        opacity: 1;
    }
    
    .table-reserved .table-icon i {
        color: #ffb703;
    }
    
    .table-reserved:hover {
        border-color: #ffb703;
        box-shadow: 0 5px 15px rgba(255, 183, 3, 0.3);
    }
    
    .table-occupied {
        border-color: #667eea;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.05) 100%);
        opacity: 0.7;
    }
    
    .table-occupied .table-icon i {
        color: #667eea;
    }
    
    .table-maintenance {
        border-color: #6c757d;
        background: linear-gradient(135deg, rgba(108, 117, 125, 0.1) 0%, rgba(73, 80, 87, 0.05) 100%);
        opacity: 0.6;
    }
    
    .table-maintenance .table-icon i {
        color: #6c757d;
    }
    
    .table-card.selected {
        border-color: #667eea;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.2) 0%, rgba(118, 75, 162, 0.1) 100%);
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        transform: scale(1.1);
    }
    
    .booking-info {
        font-size: 0.7rem;
        line-height: 1.3;
    }
    
    .time-timeline-container {
        border: 2px solid #dee2e6;
    }
    
    .time-slot {
        position: absolute;
        height: 40px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 600;
        color: white;
        cursor: pointer;
        transition: all 0.2s;
        z-index: 1;
    }
    
    .time-slot:hover {
        transform: scale(1.05);
        z-index: 2;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    
    .time-slot.booked {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
    }
    
    .time-slot.available {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        opacity: 0.3;
    }
    
    .time-slot.selected {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        z-index: 3;
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
    }
    
    .time-marker {
        position: absolute;
        top: 0;
        bottom: 0;
        width: 1px;
        background: #dee2e6;
    }
    
    .time-label {
        position: absolute;
        top: -20px;
        font-size: 0.7rem;
        color: #6c757d;
        transform: translateX(-50%);
    }
    
    .quick-time-btn {
        font-size: 0.75rem;
        padding: 2px 8px;
    }
    
    .quick-time-btn:hover {
        transform: scale(1.1);
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Debug: Check if elements exist
        console.log('Available tables:', $('.table-card.table-available').length);
        console.log('Modal exists:', $('#bookingModal').length > 0);
        console.log('Bootstrap available:', typeof bootstrap !== 'undefined');
        
        // Reload page when booking date changes to show bookings for that date
        $('#booking_date').on('change', function() {
            const selectedDate = $(this).val();
            if (selectedDate) {
                window.location.href = '{{ route("bookings.create") }}?date=' + selectedDate;
            }
        });

        // Quick time select buttons - removed (now in modal)

        // Helper function: Convert time string to minutes
        function timeToMinutes(timeStr) {
            const [hours, minutes] = timeStr.split(':').map(Number);
            return hours * 60 + minutes;
        }

        // Load và hiển thị các booking của bàn đã chọn
        function loadTableBookings() {
            const tableId = $('#selected_table_id').val();
            const bookingDate = $('#modal_booking_date').val() || '{{ date('Y-m-d') }}';
            
            console.log('loadTableBookings - tableId:', tableId, 'bookingDate:', bookingDate); // Debug
            
            if (!tableId) {
                $('#tableBookingsContent').html('<div class="text-center text-muted"><i class="bi bi-info-circle"></i> Chưa chọn bàn</div>');
                return;
            }
            
            const bookings = window.currentBookings || [];
            console.log('Total bookings:', bookings.length); // Debug
            
            // Normalize date format for comparison
            const normalizeDate = function(dateStr) {
                if (!dateStr) return '';
                // Convert to YYYY-MM-DD format
                if (dateStr.includes('T')) {
                    return dateStr.split('T')[0];
                }
                return dateStr;
            };
            
            const tableBookings = bookings.filter(function(booking) {
                const bookingDateNormalized = normalizeDate(booking.booking_date);
                const selectedDateNormalized = normalizeDate(bookingDate);
                
                const matchesDate = bookingDateNormalized === selectedDateNormalized;
                // Check table_id from multiple sources
                const bookingTableId = booking.table_id || (booking.table ? booking.table.id : null);
                const matchesTable = bookingTableId && bookingTableId == tableId;
                
                console.log('Booking check:', {
                    booking_id: booking.id,
                    booking_date: bookingDateNormalized,
                    selected_date: selectedDateNormalized,
                    matches_date: matchesDate,
                    booking_table_id: bookingTableId,
                    selected_table_id: tableId,
                    matches_table: matchesTable
                }); // Debug
                
                return matchesDate && matchesTable;
            });
            
            console.log('Table bookings found:', tableBookings.length); // Debug
            
            if (tableBookings.length === 0) {
                $('#tableBookingsContent').html('<div class="text-center text-success"><i class="bi bi-check-circle"></i> Bàn này chưa có đặt bàn nào trong ngày này</div>');
            } else {
                let html = '<div class="list-group list-group-flush">';
                tableBookings.forEach(function(booking) {
                    const statusBadge = booking.status === 'pending' 
                        ? '<span class="badge bg-warning">Chờ xác nhận</span>'
                        : booking.status === 'confirmed'
                        ? '<span class="badge bg-success">Đã xác nhận</span>'
                        : '<span class="badge bg-info">Đã đến</span>';
                    
                    const timeRange = booking.booking_time.substring(0, 5) + 
                                    (booking.end_time ? ' - ' + booking.end_time.substring(0, 5) : '');
                    
                    html += `<div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${booking.customer_name}</strong><br>
                            <small class="text-muted">
                                <i class="bi bi-clock"></i> ${timeRange}
                            </small>
                        </div>
                        ${statusBadge}
                    </div>`;
                });
                html += '</div>';
                $('#tableBookingsContent').html(html);
            }
        }

        // Tự động tính end_time dựa trên booking_time và duration (for modal)
        function calculateEndTimeModal() {
            const bookingTime = $('#modal_booking_time').val();
            const duration = parseInt($('#modal_duration').val()) || 120; // Mặc định 2 giờ
            
            if (!bookingTime) {
                return;
            }
            
            // Chuyển đổi booking_time sang phút
            const [hours, minutes] = bookingTime.split(':').map(Number);
            const startMinutes = hours * 60 + minutes;
            
            // Tính end_time
            const endMinutes = startMinutes + duration;
            const endHours = Math.floor(endMinutes / 60);
            const endMins = endMinutes % 60;
            
            // Đảm bảo không vượt quá 22:00
            if (endHours > 22 || (endHours === 22 && endMins > 0)) {
                // Nếu vượt quá 22:00, đặt về 22:00 và điều chỉnh duration
                $('#modal_end_time').val('22:00');
                // Có thể hiển thị cảnh báo
                const maxEndMinutes = 22 * 60;
                const adjustedDuration = maxEndMinutes - startMinutes;
                if (adjustedDuration >= 30) {
                    $('#modal_duration').val(adjustedDuration);
                }
            } else {
                // Format lại thành HH:MM
                const endTimeStr = String(endHours).padStart(2, '0') + ':' + String(endMins).padStart(2, '0');
                $('#modal_end_time').val(endTimeStr);
            }
            
            // Load table bookings when time changes
            loadTableBookings();
        }
        
        // Tự động tính end_time khi booking_time hoặc duration thay đổi (in modal)
        $('#modal_booking_time, #modal_duration').on('change', function() {
            calculateEndTimeModal();
        });
        
        // Quick time select buttons (in modal)
        $(document).on('click', '.quick-time-btn', function() {
            const time = $(this).data('time');
            $('#modal_booking_time').val(time);
            calculateEndTimeModal();
            $(this).addClass('active').siblings().removeClass('active');
        });
        
        // Hiển thị các booking đã đặt trong khung giờ được chọn
        function checkTimeSlotBookings() {
            const bookingDate = $('#booking_date').val();
            const bookingTime = $('#booking_time').val();
            const endTime = $('#end_time').val();
            
            if (!bookingDate || !bookingTime || !endTime) {
                $('#timeSlotBookings').hide();
                return;
            }

            // Lấy danh sách booking từ server (đã có trong selectedDateBookings)
            const bookings = @json($selectedDateBookings ?? []);
            const bufferMinutes = 15; // Buffer 15 phút
            
            // Chuyển đổi thời gian sang phút để so sánh
            function timeToMinutes(timeStr) {
                const [hours, minutes] = timeStr.split(':').map(Number);
                return hours * 60 + minutes;
            }
            
            const selectedStart = timeToMinutes(bookingTime);
            const selectedEnd = timeToMinutes(endTime);
            
            // Tìm các booking xung đột
            const conflictingBookings = [];
            bookings.forEach(function(booking) {
                if (booking.booking_date !== bookingDate) return;
                
                const bookingStart = timeToMinutes(booking.booking_time.substring(0, 5));
                const bookingEnd = booking.end_time ? timeToMinutes(booking.end_time.substring(0, 5)) : bookingStart + 120;
                const bookingEndWithBuffer = bookingEnd + bufferMinutes;
                
                // Kiểm tra xung đột: selectedStart < bookingEndWithBuffer && bookingStart < selectedEnd
                if (selectedStart < bookingEndWithBuffer && bookingStart < selectedEnd) {
                    conflictingBookings.push(booking);
                }
            });

            // Hiển thị kết quả
            if (conflictingBookings.length > 0) {
                let html = '<ul class="mb-0 ps-3">';
                conflictingBookings.forEach(function(booking) {
                    const statusBadge = booking.status === 'pending' 
                        ? '<span class="badge bg-warning ms-2">Chờ xác nhận</span>'
                        : booking.status === 'confirmed'
                        ? '<span class="badge bg-success ms-2">Đã xác nhận</span>'
                        : '<span class="badge bg-info ms-2">Đã đến</span>';
                    
                    const tableInfo = booking.table 
                        ? ` - Bàn ${booking.table.name}`
                        : ' - Chưa gán bàn';
                    
                    html += `<li class="mb-1">
                        <strong>${booking.customer_name}</strong> 
                        (${booking.booking_time.substring(0, 5)}${booking.end_time ? ' - ' + booking.end_time.substring(0, 5) : ''})
                        ${tableInfo}
                        ${statusBadge}
                    </li>`;
                });
                html += '</ul>';
                $('#conflictingBookingsList').html(html);
                $('#timeSlotBookings').show();
            } else {
                $('#timeSlotBookings').hide();
            }
        }

        // Removed old booking_time/booking_date handlers (now in modal)

        // Table selection - Fill form and open modal (use event delegation)
        // Try both selectors to ensure it works
        $(document).on('click', '.table-card', function(e) {
            const $card = $(this);
            
            // Check if table is available
            if (!$card.hasClass('table-available') && $card.hasClass('table-reserved')) {
                return; // Don't open modal for reserved tables
            }
            
            // Only proceed if it's an available table
            if (!$card.hasClass('table-available')) {
                return;
            }
            e.preventDefault();
            e.stopPropagation();
            
            const tableName = $card.find('h6').text();
            const capacity = $card.data('capacity');
            const tableId = $card.data('table-id');
            
            console.log('Table clicked:', tableName, capacity, tableId); // Debug
            
            // Get area from data attribute or from parent container
            let area = $card.data('area');
            if (!area) {
                area = $card.closest('.table-area').data('area') || $card.closest('.mb-5').find('h5').text().replace(/[📍\s]/g, '').trim();
            }
            
            // Set location preference
            let locationValue = '';
            if (area) {
                if (area.includes('Tầng 1') || area === 'Tầng 1') {
                    locationValue = 'Tầng 1';
                } else if (area.includes('Tầng 2') || area === 'Tầng 2') {
                    locationValue = 'Tầng 2';
                } else if (area.includes('VIP') || area.includes('Phòng')) {
                    locationValue = 'Phòng riêng';
                } else if (area.includes('cửa sổ') || area.includes('Gần cửa sổ')) {
                    locationValue = 'Gần cửa sổ';
                } else if (area.includes('yên tĩnh') || area.includes('Yên tĩnh')) {
                    locationValue = 'Khu vực yên tĩnh';
                }
            }
            
            // Fill form bên phải
            $('#number_of_guests').val(capacity);
            $('#capacityHint').text(`Gợi ý: Bàn này có thể chứa tối đa ${capacity} người`);
            $('#selectedTableName').text(tableName);
            $('#selectedTableCapacity').text(capacity);
            $('#selectedTableLocation').text(locationValue || area || 'Tự động');
            $('#selectedTableInfo').fadeIn();
            
            // Select table card
            $('.table-card').removeClass('selected');
            $card.addClass('selected');
            
            // Fill modal
            $('#modalTableName').text(tableName);
            $('#selected_table_id').val(tableId);
            $('#modal_number_of_guests').val(capacity);
            $('#modalCapacityHint').text(`Gợi ý: Bàn này có thể chứa tối đa ${capacity} người`);
            $('#location_preference').val(locationValue);
            
            // Copy data from right form to modal
            $('#modal_customer_name').val($('#customer_name').val());
            $('#modal_customer_phone').val($('#customer_phone').val());
            $('#modal_notes').val($('#notes').val());
            
            // Reset date/time
            const today = new Date().toISOString().split('T')[0];
            $('#modal_booking_date').val(today);
            $('#modal_booking_time').val('18:00');
            $('#modal_duration').val('120');
            calculateEndTimeModal();
            
            // Load bookings for today
            loadBookingsForDate(today);
            
            // Open modal using Bootstrap 5
            try {
                const modalElement = document.getElementById('bookingModal');
                if (modalElement) {
                    // Wait a bit to ensure DOM is ready
                    setTimeout(function() {
                        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                            const bookingModal = new bootstrap.Modal(modalElement);
                            bookingModal.show();
                            console.log('Modal opened with Bootstrap'); // Debug
                        } else {
                            // Fallback: use jQuery if Bootstrap not available
                            $('#bookingModal').modal('show');
                            console.log('Modal opened with jQuery'); // Debug
                        }
                    }, 100);
                } else {
                    console.error('Modal element not found'); // Debug
                    alert('Không tìm thấy modal. Vui lòng tải lại trang.');
                }
            } catch (error) {
                console.error('Error opening modal:', error); // Debug
                // Fallback: try jQuery
                $('#bookingModal').modal('show');
            }
        });
        
        // Load bookings when date changes in modal
        $('#modal_booking_date').on('change', function() {
            const selectedDate = $(this).val();
            if (selectedDate) {
                loadBookingsForDate(selectedDate);
                // Update date label
                const dateObj = new Date(selectedDate);
                const formattedDate = String(dateObj.getDate()).padStart(2, '0') + '/' + 
                                     String(dateObj.getMonth() + 1).padStart(2, '0') + '/' + 
                                     dateObj.getFullYear();
                $('#tableBookingsDate').text(formattedDate);
                // Load table bookings and check conflicts
                setTimeout(function() {
                    loadTableBookings();
                    checkModalTimeConflicts();
                }, 200);
            }
        });
        
        // Check conflicts when time changes in modal
        $('#modal_booking_time, #modal_duration').on('change', function() {
            calculateEndTimeModal();
            setTimeout(checkModalTimeConflicts, 200);
        });
        
        // Function to check time conflicts and display in right panel
        function checkModalTimeConflicts() {
            const bookingDate = $('#modal_booking_date').val();
            const bookingTime = $('#modal_booking_time').val();
            const endTime = $('#modal_end_time').val();
            
            if (!bookingDate || !bookingTime || !endTime) {
                $('#conflictInfo').hide();
                return;
            }
            
            const bookings = window.currentBookings || [];
            const bufferMinutes = 15;
            
            function timeToMinutes(timeStr) {
                const [hours, minutes] = timeStr.split(':').map(Number);
                return hours * 60 + minutes;
            }
            
            const selectedStart = timeToMinutes(bookingTime);
            const selectedEnd = timeToMinutes(endTime);
            
            // Find conflicting bookings
            const conflictingBookings = [];
            bookings.forEach(function(booking) {
                if (booking.booking_date !== bookingDate) return;
                
                const bookingStart = timeToMinutes(booking.booking_time.substring(0, 5));
                const bookingEnd = booking.end_time ? timeToMinutes(booking.end_time.substring(0, 5)) : bookingStart + 120;
                const bookingEndWithBuffer = bookingEnd + bufferMinutes;
                
                // Check conflict: selectedStart < bookingEndWithBuffer && bookingStart < selectedEnd
                if (selectedStart < bookingEndWithBuffer && bookingStart < selectedEnd) {
                    conflictingBookings.push(booking);
                }
            });
            
            // Display in right panel
            if (conflictingBookings.length > 0) {
                let html = '<strong>Đã có đặt bàn trùng khung giờ:</strong><ul class="mb-0 ps-3 mt-2">';
                conflictingBookings.forEach(function(booking) {
                    const statusBadge = booking.status === 'pending' 
                        ? '<span class="badge bg-warning ms-2">Chờ xác nhận</span>'
                        : booking.status === 'confirmed'
                        ? '<span class="badge bg-success ms-2">Đã xác nhận</span>'
                        : '<span class="badge bg-info ms-2">Đã đến</span>';
                    
                    const tableInfo = booking.table 
                        ? ` - Bàn ${booking.table.name}`
                        : ' - Chưa gán bàn';
                    
                    html += `<li class="mb-1">
                        <strong>${booking.customer_name}</strong> 
                        (${booking.booking_time.substring(0, 5)}${booking.end_time ? ' - ' + booking.end_time.substring(0, 5) : ''})
                        ${tableInfo}
                        ${statusBadge}
                    </li>`;
                });
                html += '</ul>';
                $('#conflictInfoContent').html(html);
                $('#conflictInfo').removeClass('alert-success').addClass('alert-warning').fadeIn();
            } else {
                // Show booking info
                const dateObj = new Date(bookingDate);
                const formattedDate = String(dateObj.getDate()).padStart(2, '0') + '/' + 
                                     String(dateObj.getMonth() + 1).padStart(2, '0') + '/' + 
                                     dateObj.getFullYear();
                const html = `<strong>Thông tin đặt bàn:</strong><br>
                    <i class="bi bi-calendar me-1"></i> Ngày: ${formattedDate}<br>
                    <i class="bi bi-clock me-1"></i> Giờ: ${bookingTime} - ${endTime}<br>
                    <span class="badge bg-success mt-2">Khung giờ này có thể đặt</span>`;
                $('#conflictInfoContent').html(html);
                $('#conflictInfo').removeClass('alert-warning').addClass('alert-success').fadeIn();
            }
        }
        
        // Load bookings for a specific date
        function loadBookingsForDate(date) {
            $.ajax({
                url: '/bookings/date/' + date,
                method: 'GET',
                success: function(bookings) {
                    window.currentBookings = bookings;
                    loadTableBookings();
                    checkModalTimeConflicts();
                },
                error: function() {
                    console.error('Error loading bookings');
                    window.currentBookings = [];
                    loadTableBookings();
                }
            });
        }
        
        // Submit booking form - Copy data from right form to modal form before submit
        $('#submitBookingBtn').on('click', function() {
            // Copy data from right form to modal form
            $('#modal_customer_name').val($('#customer_name').val());
            $('#modal_customer_phone').val($('#customer_phone').val());
            $('#modal_number_of_guests').val($('#number_of_guests').val());
            $('#modal_notes').val($('#notes').val());
            
            // Submit form
            $('#bookingForm').submit();
        });
        
        // Validate capacity when guests number changes
        $('#number_of_guests').on('input', function() {
            const selectedTable = $('.table-card.selected');
            if (selectedTable.length > 0) {
                const capacity = selectedTable.data('capacity');
                const guests = parseInt($(this).val()) || 0;
                
                if (guests > capacity) {
                    $(this).addClass('is-invalid');
                    $('#capacityHint').html(`<span class="text-danger">⚠️ Bàn chỉ chứa tối đa ${capacity} người!</span>`);
                } else {
                    $(this).removeClass('is-invalid');
                    $('#capacityHint').text(`Bàn này có thể chứa tối đa ${capacity} người`);
                }
            }
        });
        
        // Validate time duration in real-time
        function validateTimeDuration() {
            const startTime = $('#booking_time').val();
            const endTime = $('#end_time').val();
            const $startInput = $('#booking_time');
            const $endInput = $('#end_time');
            
            // Remove previous error classes
            $startInput.removeClass('is-invalid');
            $endInput.removeClass('is-invalid');
            $('.time-duration-error').remove();
            
            if (!startTime || !endTime) {
                return true;
            }
            
            // Parse times
            const [startHour, startMin] = startTime.split(':').map(Number);
            const [endHour, endMin] = endTime.split(':').map(Number);
            
            const startMinutes = startHour * 60 + startMin;
            const endMinutes = endHour * 60 + endMin;
            
            let isValid = true;
            let errorMessage = '';
            let suggestion = '';
            
            // Check if end time is after start time
            if (endMinutes <= startMinutes) {
                isValid = false;
                // Provide helpful suggestion
                if (endHour < 12 && startHour >= 12) {
                    // End time is AM, start time is PM - likely user meant PM for end time
                    const suggestedEndHour = endHour + 12;
                    suggestion = ` Gợi ý: Có thể bạn muốn chọn ${String(suggestedEndHour).padStart(2, '0')}:${String(endMin).padStart(2, '0')} (PM)?`;
                } else if (endHour >= 12 && startHour < 12) {
                    // End time is PM, start time is AM - this is valid, but check duration
                    const diffMinutes = (24 * 60 - startMinutes) + endMinutes;
                    if (diffMinutes < 30) {
                        errorMessage = 'Thời gian đặt bàn tối thiểu là 30 phút.';
                    } else if (diffMinutes > 240) {
                        errorMessage = 'Thời gian đặt bàn tối đa là 4 giờ.';
                    } else {
                        isValid = true; // Valid overnight booking (though unlikely for restaurant)
                    }
                } else {
                    errorMessage = 'Thời gian kết thúc phải sau thời gian bắt đầu.';
                }
                
                if (!isValid) {
                    errorMessage += suggestion;
                    $endInput.addClass('is-invalid');
                }
            } else {
                const diffMinutes = endMinutes - startMinutes;
                
                // Check minimum duration (30 minutes)
                if (diffMinutes < 30) {
                    isValid = false;
                    const suggestedEndMinutes = startMinutes + 30;
                    const suggestedHour = Math.floor(suggestedEndMinutes / 60);
                    const suggestedMin = suggestedEndMinutes % 60;
                    errorMessage = `Thời gian đặt bàn tối thiểu là 30 phút. Gợi ý: Chọn ${String(suggestedHour).padStart(2, '0')}:${String(suggestedMin).padStart(2, '0')}?`;
                    $endInput.addClass('is-invalid');
                }
                // Check maximum duration (4 hours = 240 minutes)
                else if (diffMinutes > 240) {
                    isValid = false;
                    const suggestedEndMinutes = startMinutes + 240;
                    const suggestedHour = Math.floor(suggestedEndMinutes / 60);
                    const suggestedMin = suggestedEndMinutes % 60;
                    errorMessage = `Thời gian đặt bàn tối đa là 4 giờ. Gợi ý: Chọn ${String(suggestedHour).padStart(2, '0')}:${String(suggestedMin).padStart(2, '0')}?`;
                    $endInput.addClass('is-invalid');
                }
            }
            
            // Display error message
            if (!isValid) {
                const errorHtml = `<div class="invalid-feedback time-duration-error d-block">${errorMessage}</div>`;
                $endInput.after(errorHtml);
            }
            
            return isValid;
        }

        // Validate on time change
        $('#booking_time, #end_time').on('change blur', function() {
            validateTimeDuration();
            checkTimeSlotBookings();
        });
        
        // Form submission with selected table info
        $('#bookingForm').submit(function(e) {
            const selectedTable = $('.table-card.selected');
            if (selectedTable.length > 0) {
                const tableName = selectedTable.find('h6').text();
                const capacity = selectedTable.data('capacity');
                const guests = parseInt($('#number_of_guests').val());
                
                if (guests > capacity) {
                    e.preventDefault();
                    alert(`Bàn ${tableName} chỉ có thể chứa tối đa ${capacity} người. Vui lòng chọn bàn khác hoặc giảm số lượng khách.`);
                    return false;
                }
            }
            
            // Validate time duration before submit
            if (!validateTimeDuration()) {
                e.preventDefault();
                return false;
            }
            
            // Validate time range
            const startTime = $('#booking_time').val();
            const endTime = $('#end_time').val();
            if (startTime && endTime) {
                const [startHour, startMin] = startTime.split(':').map(Number);
                const [endHour, endMin] = endTime.split(':').map(Number);
                const startMinutes = startHour * 60 + startMin;
                const endMinutes = endHour * 60 + endMin;
                const diffMinutes = endMinutes - startMinutes;
                
                if (diffMinutes < 30 || diffMinutes > 240 || endMinutes <= startMinutes) {
                    e.preventDefault();
                    alert('Vui lòng kiểm tra lại thời gian đặt bàn. Thời gian đặt bàn phải từ 30 phút đến 4 giờ.');
                    return false;
                }
            }
        });
    });
</script>
@endpush
@endsection
