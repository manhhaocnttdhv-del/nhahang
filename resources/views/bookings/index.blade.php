@extends('layouts.app')

@section('title', 'Lịch Sử Đặt Bàn')

@section('content')
<div class="container my-5">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 mb-3" style="font-weight: 800; color: var(--dark-color);">
                <i class="bi bi-calendar-check"></i> Lịch Sử Đặt Bàn
            </h1>
            <p class="lead text-muted">Xem lại các đặt bàn của bạn</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        @forelse($bookings as $booking)
            <div class="col-md-6 mb-4 fade-in-up">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center" 
                         style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <strong><i class="bi bi-calendar3"></i> Đặt bàn #{{ $booking->id }}</strong>
                        @if($booking->status === 'pending')
                            <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Chờ xác nhận</span>
                        @elseif($booking->status === 'confirmed')
                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Đã xác nhận</span>
                        @elseif($booking->status === 'rejected')
                            <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Đã từ chối</span>
                        @elseif($booking->status === 'checked_in')
                            <span class="badge bg-info"><i class="bi bi-door-open"></i> Đã check-in</span>
                        @elseif($booking->status === 'completed')
                            <span class="badge bg-secondary"><i class="bi bi-check2-all"></i> Đã hoàn thành</span>
                        @else
                            <span class="badge bg-secondary"><i class="bi bi-x-octagon"></i> Đã hủy</span>
                        @endif
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-calendar3 text-primary me-2"></i>
                                    <strong>Ngày:</strong>
                                </div>
                                <p class="mb-0">{{ $booking->booking_date->format('d/m/Y') }}</p>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-clock text-success me-2"></i>
                                    <strong>Giờ:</strong>
                                </div>
                                <p class="mb-0">
                                    {{ \Carbon\Carbon::parse($booking->booking_time)->format('H:i') }}
                                    @if($booking->end_time)
                                        - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                    @endif
                                </p>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-people text-info me-2"></i>
                                    <strong>Số khách:</strong>
                                </div>
                                <p class="mb-0">{{ $booking->number_of_guests }} người</p>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-table text-warning me-2"></i>
                                    <strong>Bàn:</strong>
                                </div>
                                <p class="mb-0">
                                    @if($booking->table)
                                        {{ $booking->table->name }} ({{ $booking->table->number }})
                                    @else
                                        <span class="text-muted">Chưa gán bàn</span>
                                    @endif
                                </p>
                            </div>
                            @if($booking->location_preference)
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-geo-alt text-danger me-2"></i>
                                    <strong>Yêu cầu:</strong>
                                </div>
                                <p class="mb-0">{{ $booking->location_preference }}</p>
                            </div>
                            @endif
                            @if($booking->notes)
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-sticky text-secondary me-2"></i>
                                    <strong>Ghi chú:</strong>
                                </div>
                                <p class="mb-0 text-muted">{{ $booking->notes }}</p>
                            </div>
                            @endif
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-primary btn-sm me-2">
                                <i class="bi bi-eye me-1"></i> Chi tiết
                            </a>
                            @if($booking->status === 'pending')
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelBookingModal{{ $booking->id }}">
                                    <i class="bi bi-x-circle me-1"></i> Hủy
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            @if($booking->status === 'pending')
            <!-- Cancel Booking Modal -->
            <div class="modal fade" id="cancelBookingModal{{ $booking->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Hủy Đặt Bàn</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('bookings.cancel', $booking->id) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <p>Bạn có chắc chắn muốn hủy đặt bàn này?</p>
                                <div class="mb-3">
                                    <label class="form-label">Lý do hủy (tùy chọn)</label>
                                    <textarea class="form-control" name="cancel_reason" rows="3" placeholder="Nhập lý do hủy..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                <button type="submit" class="btn btn-danger">Xác nhận hủy</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        @empty
            <div class="col-12">
                <div class="card text-center p-5">
                    <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                    <h4 class="text-muted">Bạn chưa có đặt bàn nào</h4>
                    <p class="text-muted mb-4">Hãy đặt bàn ngay để trải nghiệm dịch vụ của chúng tôi</p>
                    <a href="{{ route('bookings.create') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-plus-circle"></i> Đặt Bàn Ngay
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection

