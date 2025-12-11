@extends('layouts.app')

@section('title', 'Đặt Bàn Thành Công')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card text-center fade-in-up">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                    </div>
                    <h2 class="mb-3 gradient-text" style="font-weight: 800;">Đặt Bàn Thành Công!</h2>
                    <p class="lead text-muted mb-4">Yêu cầu đặt bàn của bạn đã được gửi. Vui lòng chờ xác nhận từ nhà hàng.</p>
                    
                    <div class="card mb-4" style="background: #f8f9fa;">
                        <div class="card-body">
                            <h5 class="mb-3">Thông Tin Đặt Bàn</h5>
                            <div class="row text-start">
                                <div class="col-md-6 mb-2">
                                    <strong>Ngày:</strong> {{ $booking->booking_date->format('d/m/Y') }}
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Giờ:</strong> 
                                    {{ \Carbon\Carbon::parse($booking->booking_time)->format('H:i') }}
                                    @if($booking->end_time)
                                        - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                    @endif
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Số khách:</strong> {{ $booking->number_of_guests }} người
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Trạng thái:</strong> 
                                    <span class="badge bg-warning">Chờ xác nhận</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-eye me-2"></i> Xem Chi Tiết
                        </a>
                        <a href="{{ route('bookings.order', $booking->id) }}" class="btn btn-success btn-lg">
                            <i class="bi bi-cart-plus me-2"></i> Chọn Món Ngay (Pre-order)
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-house me-2"></i> Về Trang Chủ
                        </a>
                    </div>
                    
                    <div class="alert alert-info mt-4 mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Lưu ý:</strong> Bạn có thể chọn món trước (pre-order) để tiết kiệm thời gian khi đến quán. 
                        Hoặc chờ đến quán mới chọn món.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

