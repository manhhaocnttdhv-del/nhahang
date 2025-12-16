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
                                                {{ $table->status === 'maintenance' ? 'table-maintenance' : 'table-available' }}"
                                                data-table-id="{{ $table->id }}"
                                                data-capacity="{{ $table->capacity }}"
                                                data-area="{{ $table->area ?? '' }}"
                                                style="cursor: {{ $table->status === 'maintenance' ? 'not-allowed' : 'pointer' }}; transition: all 0.3s;">
                                                <div class="table-icon mb-2">
                                                    <i class="bi bi-table display-4"></i>
                                                </div>
                                                <h6 class="mb-1" style="font-weight: 700;">{{ $table->name }}</h6>
                                                <small class="text-muted d-block mb-2">Bàn {{ $table->number }}</small>
                                                <div class="mb-2">
                                                    <i class="bi bi-people"></i> {{ $table->capacity }} người
                                                </div>
                                                <div class="table-status-badge mt-2">
                                                    @if($table->status === 'maintenance')
                                                        <span class="badge bg-secondary">Bảo trì</span>
                                                    @else
                                                        <span class="badge bg-success">Trống</span>
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
                                            @if($booking->session)
                                                @php
                                                    $sessionNames = [
                                                        'morning' => 'Sáng',
                                                        'lunch' => 'Trưa',
                                                        'afternoon' => 'Chiều',
                                                        'dinner' => 'Tối'
                                                    ];
                                                    $sessionName = $sessionNames[$booking->session] ?? $booking->session;
                                                @endphp
                                                <i class="bi bi-calendar-event"></i> Buổi {{ $sessionName }}
                                            @else
                                                {{ \Carbon\Carbon::parse($booking->booking_time)->format('H:i') }}
                                                @if($booking->end_time)
                                                    - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                                @endif
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
                    <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
                        @csrf
                        
                        <input type="hidden" id="table_id" name="table_id">
                        <input type="hidden" id="location_preference" name="location_preference" value="{{ old('location_preference') }}">
                        
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

                        <!-- Chọn buổi đặt bàn -->
                        <div class="mb-4">
                            <label class="form-label fw-bold mb-3">
                                <i class="bi bi-calendar-event me-2"></i>Buổi <span class="text-danger">*</span>
                            </label>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <input type="radio" name="session" id="session_morning" value="morning" class="btn-check" 
                                           {{ old('session') == 'morning' ? 'checked' : '' }} required>
                                    <label class="btn btn-outline-primary w-100 session-btn" for="session_morning" style="height: 80px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                        <i class="bi bi-sunrise fs-4"></i>
                                        <strong>Sáng</strong>
                                        <small>8:00 - 11:00</small>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <input type="radio" name="session" id="session_lunch" value="lunch" class="btn-check"
                                           {{ old('session') == 'lunch' ? 'checked' : '' }} required>
                                    <label class="btn btn-outline-primary w-100 session-btn" for="session_lunch" style="height: 80px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                        <i class="bi bi-sun fs-4"></i>
                                        <strong>Trưa</strong>
                                        <small>11:00 - 14:00</small>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <input type="radio" name="session" id="session_afternoon" value="afternoon" class="btn-check"
                                           {{ old('session') == 'afternoon' ? 'checked' : '' }} required>
                                    <label class="btn btn-outline-primary w-100 session-btn" for="session_afternoon" style="height: 80px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                        <i class="bi bi-cloud-sun fs-4"></i>
                                        <strong>Chiều</strong>
                                        <small>14:00 - 17:00</small>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <input type="radio" name="session" id="session_dinner" value="dinner" class="btn-check"
                                           {{ old('session', 'dinner') == 'dinner' ? 'checked' : '' }} required>
                                    <label class="btn btn-outline-primary w-100 session-btn" for="session_dinner" style="height: 80px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                        <i class="bi bi-moon fs-4"></i>
                                        <strong>Tối</strong>
                                        <small>17:00 - 22:00</small>
                                    </label>
                                </div>
                            </div>
                            @error('session')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Hiển thị các buổi đã đặt của bàn này -->
                        <div class="mb-4">
                            <label class="form-label fw-bold mb-2">
                                <i class="bi bi-calendar-check me-2"></i>Buổi đã đặt của bàn này (<span id="tableBookingsDate">{{ date('d/m/Y') }}</span>)
                            </label>
                            <div id="tableBookingsList" class="card" style="background: #f8f9fa;">
                                <div class="card-body p-3">
                                    <div id="tableBookingsContent" class="small">
                                        <div class="text-center text-muted">
                                            <i class="bi bi-hourglass-split"></i> Chọn bàn và buổi để xem thông tin đặt bàn
                                        </div>
                                    </div>
                                </div>
                            </div>
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

                        <div class="alert alert-info mb-3">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Hướng dẫn:</strong> Click vào bàn để chọn bàn, sau đó chọn ngày và buổi đặt bàn.
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100" id="submitBookingBtn">
                            <i class="bi bi-check-circle me-2"></i> Đặt Bàn Ngay
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Booking Modal -->
        <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="false">
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
                            <input type="hidden" id="modal_table_id" name="table_id">
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

                            <!-- Chọn buổi đặt bàn -->
                            <div class="mb-4">
                                <label class="form-label fw-bold mb-3">
                                    <i class="bi bi-calendar-event me-2"></i>Buổi <span class="text-danger">*</span>
                                </label>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <input type="radio" name="session" id="session_morning" value="morning" class="btn-check" 
                                               {{ old('session') == 'morning' ? 'checked' : '' }} required>
                                        <label class="btn btn-outline-primary w-100 session-btn" for="session_morning" style="height: 80px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                            <i class="bi bi-sunrise fs-4"></i>
                                            <strong>Sáng</strong>
                                            <small>8:00 - 11:00</small>
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="radio" name="session" id="session_lunch" value="lunch" class="btn-check"
                                               {{ old('session') == 'lunch' ? 'checked' : '' }} required>
                                        <label class="btn btn-outline-primary w-100 session-btn" for="session_lunch" style="height: 80px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                            <i class="bi bi-sun fs-4"></i>
                                            <strong>Trưa</strong>
                                            <small>11:00 - 14:00</small>
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="radio" name="session" id="session_afternoon" value="afternoon" class="btn-check"
                                               {{ old('session') == 'afternoon' ? 'checked' : '' }} required>
                                        <label class="btn btn-outline-primary w-100 session-btn" for="session_afternoon" style="height: 80px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                            <i class="bi bi-cloud-sun fs-4"></i>
                                            <strong>Chiều</strong>
                                            <small>14:00 - 17:00</small>
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="radio" name="session" id="session_dinner" value="dinner" class="btn-check"
                                               {{ old('session', 'dinner') == 'dinner' ? 'checked' : '' }} required>
                                        <label class="btn btn-outline-primary w-100 session-btn" for="session_dinner" style="height: 80px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                            <i class="bi bi-moon fs-4"></i>
                                            <strong>Tối</strong>
                                            <small>17:00 - 22:00</small>
                                        </label>
                                    </div>
                                </div>
                                @error('session')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Hiển thị các buổi đã đặt của bàn này -->
                            <div class="mb-4">
                                <label class="form-label fw-bold mb-2">
                                    <i class="bi bi-calendar-check me-2"></i>Buổi đã đặt của bàn này (<span id="tableBookingsDate">{{ date('d/m/Y') }}</span>)
                                </label>
                                <div id="tableBookingsList" class="card" style="background: #f8f9fa;">
                                    <div class="card-body p-3">
                                        <div id="tableBookingsContent" class="small">
                                            <div class="text-center text-muted">
                                                <i class="bi bi-hourglass-split"></i> Chọn buổi để xem thông tin đặt bàn
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

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
                            
                            <!-- Hidden field for table_id -->
                            <input type="hidden" name="table_id" id="modal_table_id">
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
        pointer-events: auto !important;
        cursor: pointer !important;
    }
    
    .table-reserved .table-icon i {
        color: #ffb703;
    }
    
    .table-reserved:hover {
        border-color: #ffb703;
        box-shadow: 0 5px 15px rgba(255, 183, 3, 0.3);
        transform: translateY(-3px) scale(1.02);
    }
    
    .table-occupied {
        border-color: #667eea;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.05) 100%);
        opacity: 0.7;
        pointer-events: auto !important;
        cursor: pointer !important;
    }
    
    .table-occupied .table-icon i {
        color: #667eea;
    }
    
    .table-occupied:hover {
        border-color: #667eea;
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        transform: translateY(-3px) scale(1.02);
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
        pointer-events: none;
    }
    
    .table-status-badge {
        pointer-events: none;
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

        // Load và hiển thị các booking của bàn đã chọn theo session
        function loadTableBookings() {
            const tableId = $('#table_id').val();
            const bookingDate = $('#booking_date').val() || '{{ date('Y-m-d') }}';
            const selectedSession = $('input[name="session"]:checked').val();
            
            if (!tableId) {
                $('#tableBookingsContent').html('<div class="text-center text-muted"><i class="bi bi-info-circle"></i> Chưa chọn bàn</div>');
                return;
            }
            
            // Update date label
            if (bookingDate) {
                const dateObj = new Date(bookingDate);
                const formattedDate = String(dateObj.getDate()).padStart(2, '0') + '/' + 
                                     String(dateObj.getMonth() + 1).padStart(2, '0') + '/' + 
                                     dateObj.getFullYear();
                $('#tableBookingsDate').text(formattedDate);
            }
            
            const bookings = window.currentBookings || [];
            
            // Normalize date format for comparison
            const normalizeDate = function(dateStr) {
                if (!dateStr) return '';
                if (dateStr.includes('T')) {
                    return dateStr.split('T')[0];
                }
                return dateStr;
            };
            
            // Session names mapping
            const sessionNames = {
                'morning': 'Sáng',
                'lunch': 'Trưa',
                'afternoon': 'Chiều',
                'dinner': 'Tối'
            };
            
            // Filter bookings by date, table, and session
            const tableBookings = bookings.filter(function(booking) {
                const bookingDateNormalized = normalizeDate(booking.booking_date);
                const selectedDateNormalized = normalizeDate(bookingDate);
                const matchesDate = bookingDateNormalized === selectedDateNormalized;
                
                const bookingTableId = booking.table_id || (booking.table ? booking.table.id : null);
                const matchesTable = bookingTableId && bookingTableId == tableId;
                
                // If session is selected, filter by session too
                const matchesSession = !selectedSession || booking.session === selectedSession;
                
                return matchesDate && matchesTable && matchesSession;
            });
            
            if (tableBookings.length === 0) {
                if (selectedSession) {
                    const sessionName = sessionNames[selectedSession] || selectedSession;
                    $('#tableBookingsContent').html(`<div class="text-center text-success"><i class="bi bi-check-circle"></i> Bàn này chưa có đặt bàn nào trong buổi ${sessionName} này</div>`);
                } else {
                    $('#tableBookingsContent').html('<div class="text-center text-muted"><i class="bi bi-info-circle"></i> Chọn buổi để xem thông tin đặt bàn</div>');
                }
            } else {
                let html = '<div class="list-group list-group-flush">';
                tableBookings.forEach(function(booking) {
                    const statusBadge = booking.status === 'pending' 
                        ? '<span class="badge bg-warning">Chờ xác nhận</span>'
                        : booking.status === 'confirmed'
                        ? '<span class="badge bg-success">Đã xác nhận</span>'
                        : '<span class="badge bg-info">Đã đến</span>';
                    
                    const sessionName = sessionNames[booking.session] || booking.session || 'N/A';
                    
                    html += `<div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${booking.customer_name}</strong><br>
                            <small class="text-muted">
                                <i class="bi bi-calendar-event"></i> Buổi ${sessionName}
                            </small>
                        </div>
                        ${statusBadge}
                    </div>`;
                });
                html += '</div>';
                $('#tableBookingsContent').html(html);
            }
        }

        // Check session conflict when session or date changes
        $('input[name="session"]').on('change', function() {
            loadTableBookings();
            checkSessionConflicts();
        });
        
        $('#booking_date').on('change', function() {
            const selectedDate = $(this).val();
            if (selectedDate) {
                loadBookingsForDate(selectedDate);
                setTimeout(function() {
                    loadTableBookings();
                    checkSessionConflicts();
                }, 200);
            }
        });
        
        // Removed old time-based functions (now using session)

        // Table selection - Fill form and open modal (use event delegation)
        // Try both selectors to ensure it works
        $(document).on('click', '.table-card', function(e) {
            const $card = $(this);
            
            console.log('Table card clicked!', $card.hasClass('table-maintenance'), $card.hasClass('table-reserved'), $card.hasClass('table-occupied')); // Debug
            
            // Don't allow click on maintenance tables
            if ($card.hasClass('table-maintenance')) {
                console.log('Maintenance table, blocking click');
                return;
            }
            
            // Allow click on all other tables (available, reserved, occupied)
            // Users can still book reserved tables for different time slots
            e.preventDefault();
            e.stopPropagation();
            
            const tableName = $card.find('h6').text();
            const capacity = $card.data('capacity');
            const tableId = $card.data('table-id');
            
            console.log('Table clicked:', tableName, capacity, tableId, 'Classes:', $card.attr('class')); // Debug
            
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
            
            // Fill form fields
            $('#table_id').val(tableId);
            $('#location_preference').val(locationValue || area || '');
            
            // Show warning if table is reserved/occupied
            if ($card.hasClass('table-reserved') || $card.hasClass('table-occupied')) {
                $('#capacityHint').html(`<span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Bàn này đã có đặt bàn. Bạn vẫn có thể đặt khung giờ khác.</span>`);
            } else {
                $('#capacityHint').text(`Gợi ý: Bàn này có thể chứa tối đa ${capacity} người`);
            }
            
            // Load bookings for selected date
            const selectedDate = $('#booking_date').val() || new Date().toISOString().split('T')[0];
            loadBookingsForDate(selectedDate);
            
            // Scroll to form
            $('html, body').animate({
                scrollTop: $('#bookingForm').offset().top - 100
            }, 500);
        });
        
        // Function to check session conflicts
        function checkSessionConflicts() {
            const bookingDate = $('#booking_date').val();
            const selectedSession = $('input[name="session"]:checked').val();
            const selectedTableId = $('#table_id').val();
            
            if (!bookingDate || !selectedSession) {
                $('#submitBookingBtn').prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
                return false; // Không có conflict
            }
            
            const bookings = window.currentBookings || [];
            
            // Find conflicting bookings (same date, same table, same session)
            const conflictingBookings = bookings.filter(function(booking) {
                if (booking.booking_date !== bookingDate) return false;
                if (selectedTableId && booking.table && booking.table.id != selectedTableId) return false;
                return booking.session === selectedSession;
            });
            
            // Nếu có conflict, disable button
            if (conflictingBookings.length > 0) {
                $('#submitBookingBtn').prop('disabled', true)
                    .removeClass('btn-primary')
                    .addClass('btn-secondary')
                    .html('<i class="bi bi-x-circle me-2"></i> Không thể đặt (trùng buổi)');
                return true; // Có conflict
            } else {
                // Enable submit button
                $('#submitBookingBtn').prop('disabled', false)
                    .removeClass('btn-secondary')
                    .addClass('btn-primary')
                    .html('<i class="bi bi-check-circle me-2"></i> Đặt Bàn Ngay');
                return false; // Không có conflict
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
                    checkSessionConflicts();
                },
                error: function() {
                    console.error('Error loading bookings');
                    window.currentBookings = [];
                    loadTableBookings();
                }
            });
        }
        
        // Check session conflict when session is selected
        $('input[name="session"]').on('change', function() {
            checkModalSessionConflicts();
        });
        
        // Handle form submit event
        $('#bookingForm').on('submit', function(e) {
            console.log('Form submit event triggered'); // Debug
            
            // Validate before submit
            const bookingDate = $('#booking_date').val();
            const selectedSession = $('input[name="session"]:checked').val();
            const guests = parseInt($('#number_of_guests').val()) || 0;
            const tableId = $('#table_id').val();
            const customerName = $('#customer_name').val();
            const customerPhone = $('#customer_phone').val();
            
            console.log('Form data:', { bookingDate, selectedSession, guests, tableId, customerName, customerPhone }); // Debug
            
            if (!customerName || customerName.trim() === '') {
                e.preventDefault();
                alert('Vui lòng nhập họ và tên.');
                $('#customer_name').focus();
                return false;
            }
            
            if (!customerPhone || customerPhone.trim() === '') {
                e.preventDefault();
                alert('Vui lòng nhập số điện thoại.');
                $('#customer_phone').focus();
                return false;
            }
            
            if (!tableId) {
                e.preventDefault();
                alert('Vui lòng chọn bàn trước khi đặt.');
                return false;
            }
            
            if (!bookingDate) {
                e.preventDefault();
                alert('Vui lòng chọn ngày đặt bàn.');
                return false;
            }
            
            if (!selectedSession) {
                e.preventDefault();
                alert('Vui lòng chọn buổi đặt bàn.');
                return false;
            }
            
            if (!guests || guests < 1) {
                e.preventDefault();
                alert('Vui lòng nhập số lượng khách (tối thiểu 1 người).');
                $('#number_of_guests').focus();
                return false;
            }
            
            // Validate capacity
            const selectedTable = $(`.table-card[data-table-id="${tableId}"]`);
            if (selectedTable.length > 0) {
                const capacity = selectedTable.data('capacity');
                if (guests > capacity) {
                    e.preventDefault();
                    const tableName = selectedTable.find('h6').text();
                    alert(`Bàn ${tableName} chỉ có thể chứa tối đa ${capacity} người. Vui lòng chọn bàn khác hoặc giảm số lượng khách.`);
                    return false;
                }
            }
            
            // Check conflict one more time
            if (checkSessionConflicts()) {
                e.preventDefault();
                alert('Buổi này đã có đặt bàn. Vui lòng chọn buổi khác.');
                return false;
            }
            
            // Show loading state
            $('#submitBookingBtn').prop('disabled', true).html('<i class="bi bi-hourglass-split me-2"></i> Đang xử lý...');
            
            console.log('Form validation passed, submitting...'); // Debug
            return true; // Allow form to submit
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
        
        // Removed validateTimeDuration - no longer needed with session-based booking

        // Form submission - validation đã được xử lý trong submitBookingBtn click handler
    });
</script>
@endpush
@endsection
