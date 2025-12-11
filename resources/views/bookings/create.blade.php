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

            <!-- Today's Bookings Info -->
            @if($todayBookings->count() > 0)
            <div class="card mt-4 fade-in-up" style="animation-delay: 0.3s;">
                <div class="card-header" style="background: linear-gradient(135deg, #06d6a0 0%, #048a64 100%); color: white;">
                    <h5 class="mb-0"><i class="bi bi-calendar3"></i> Đặt Bàn Hôm Nay</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach($todayBookings->take(6) as $booking)
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

        <!-- Right: Booking Form -->
        <div class="col-lg-4">
            <div class="card sticky-top fade-in-up" style="top: 100px; animation-delay: 0.2s;">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.5rem;">
                    <h4 class="mb-0" style="font-weight: 700;">
                        <i class="bi bi-calendar-check me-2"></i> Thông Tin Đặt Bàn
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
                        @csrf
                        
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
                            <label for="booking_date" class="form-label fw-bold">
                                <i class="bi bi-calendar me-2"></i>Ngày <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control @error('booking_date') is-invalid @enderror" 
                                   id="booking_date" name="booking_date" 
                                   value="{{ old('booking_date', date('Y-m-d')) }}" 
                                   min="{{ date('Y-m-d') }}" required>
                            @error('booking_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="booking_time" class="form-label fw-bold">
                                    <i class="bi bi-clock me-2"></i>Giờ bắt đầu <span class="text-danger">*</span>
                                </label>
                                <input type="time" class="form-control @error('booking_time') is-invalid @enderror" 
                                       id="booking_time" name="booking_time" 
                                       value="{{ old('booking_time', '18:00') }}" required>
                                @error('booking_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="end_time" class="form-label fw-bold">
                                    <i class="bi bi-clock-history me-2"></i>Giờ kết thúc <span class="text-danger">*</span>
                                </label>
                                <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                                       id="end_time" name="end_time" 
                                       value="{{ old('end_time', '20:00') }}" required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <small class="text-muted mb-3 d-block">
                            <i class="bi bi-info-circle me-1"></i>
                            Thời gian đặt bàn tối thiểu 30 phút, tối đa 4 giờ. Hệ thống sẽ tự động kiểm tra xung đột thời gian.
                        </small>

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

                        <!-- Location preference - Hidden, auto-filled when table is selected -->
                        <input type="hidden" id="location_preference" name="location_preference" value="{{ old('location_preference') }}">

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

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg py-3 ripple" style="font-weight: 700;">
                                <i class="bi bi-check-circle me-2"></i> Đặt Bàn Ngay
                            </button>
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i> Quay Lại
                            </a>
                        </div>
                    </form>
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
        background: linear-gradient(135deg, rgba(255, 183, 3, 0.1) 0%, rgba(251, 133, 0, 0.05) 100%);
        opacity: 0.8;
    }
    
    .table-reserved .table-icon i {
        color: #ffb703;
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
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Table selection - Auto fill form
        $('.table-card.table-available').click(function() {
            const $card = $(this);
            const tableName = $card.find('h6').text();
            const capacity = $card.data('capacity');
            const tableId = $card.data('table-id');
            // Get area from data attribute or from parent container
            let area = $card.data('area');
            if (!area) {
                area = $card.closest('.table-area').data('area') || $card.closest('.mb-5').find('h5').text().replace(/[📍\s]/g, '').trim();
            }
            
            if ($card.hasClass('selected')) {
                // Deselect
                $card.removeClass('selected');
                $('#selectedTableInfo').hide();
                $('#capacityHint').text('');
            } else {
                // Select
                $('.table-card').removeClass('selected');
                $card.addClass('selected');
                
                // Auto fill form
                $('#number_of_guests').val(capacity);
                $('#capacityHint').text(`Gợi ý: Bàn này có thể chứa tối đa ${capacity} người`);
                
                // Auto set location preference based on area (hidden field)
                const locationInput = $('#location_preference');
                let locationValue = '';
                
                if (area) {
                    // Map area to location preference
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
                    
                    if (locationValue) {
                        locationInput.val(locationValue);
                    }
                }
                
                // Show selected table info
                $('#selectedTableName').text(tableName);
                $('#selectedTableCapacity').text(capacity);
                $('#selectedTableLocation').text(locationValue || area || 'Tự động');
                $('#selectedTableInfo').fadeIn();
                
                // Scroll to form
                $('html, body').animate({
                    scrollTop: $('#bookingForm').offset().top - 100
                }, 500);
            }
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
        
        // Validate time range
        $('#booking_time, #end_time').on('change', function() {
            const startTime = $('#booking_time').val();
            const endTime = $('#end_time').val();
            
            if (startTime && endTime) {
                const start = new Date('2000-01-01 ' + startTime);
                const end = new Date('2000-01-01 ' + endTime);
                const diffMinutes = (end - start) / 1000 / 60;
                
                if (diffMinutes < 30) {
                    $('#end_time').addClass('is-invalid');
                    alert('Thời gian đặt bàn tối thiểu là 30 phút');
                } else if (diffMinutes > 240) {
                    $('#end_time').addClass('is-invalid');
                    alert('Thời gian đặt bàn tối đa là 4 giờ');
                } else if (end <= start) {
                    $('#end_time').addClass('is-invalid');
                    alert('Thời gian kết thúc phải sau thời gian bắt đầu');
                } else {
                    $('#end_time').removeClass('is-invalid');
                }
            }
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
            
            // Validate time range before submit
            const startTime = $('#booking_time').val();
            const endTime = $('#end_time').val();
            if (startTime && endTime) {
                const start = new Date('2000-01-01 ' + startTime);
                const end = new Date('2000-01-01 ' + endTime);
                const diffMinutes = (end - start) / 1000 / 60;
                
                if (diffMinutes < 30 || diffMinutes > 240 || end <= start) {
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
