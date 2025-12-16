@extends('layouts.app')

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-calendar-check"></i> Chi Tiết Đặt Bàn #{{ $booking->id }}</h2>
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Thông Tin Đặt Bàn</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Khách hàng:</strong> {{ $booking->customer_name }}</p>
                            <p><strong>Số điện thoại:</strong> {{ $booking->customer_phone }}</p>
                            <p><strong>Email:</strong> {{ $booking->customer_email ?? '-' }}</p>
                            @if($booking->user)
                                <p><strong>Tài khoản:</strong> {{ $booking->user->name }} ({{ $booking->user->email }})</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p><strong>Ngày đặt:</strong> {{ $booking->booking_date->format('d/m/Y') }}</p>
                            @if($booking->session)
                                @php
                                    $sessions = [
                                        'morning' => 'Sáng',
                                        'lunch' => 'Trưa',
                                        'afternoon' => 'Chiều',
                                        'dinner' => 'Tối'
                                    ];
                                @endphp
                                <p><strong>Buổi:</strong> <span class="badge bg-info">{{ $sessions[$booking->session] ?? $booking->session }}</span></p>
                            @elseif($booking->booking_time)
                                <p><strong>Giờ:</strong> {{ \Carbon\Carbon::parse($booking->booking_time)->format('H:i') }}
                                    @if($booking->end_time)
                                        - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                    @endif
                                </p>
                            @endif
                            <p><strong>Số khách:</strong> {{ $booking->number_of_guests }} người</p>
                            <p><strong>Bàn:</strong> {{ $booking->table ? $booking->table->name : '-' }}</p>
                        </div>
                    </div>
                    @if($booking->special_requests)
                        <hr>
                        <p><strong>Yêu cầu đặc biệt:</strong></p>
                        <p class="text-muted">{{ $booking->special_requests }}</p>
                    @endif
                </div>
            </div>

            @if($booking->order)
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Đơn Hàng Liên Quan</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Mã đơn:</strong> #{{ $booking->order->order_number }}</p>
                        <p><strong>Tổng tiền:</strong> {{ number_format($booking->order->total_amount, 0, ',', '.') }} đ</p>
                        <p><strong>Trạng thái:</strong> 
                            <span class="badge bg-{{ $booking->order->status === 'served' ? 'success' : 'warning' }}">
                                {{ $booking->order->status }}
                            </span>
                        </p>
                        <a href="{{ route('admin.orders.show', $booking->order->id) }}" class="btn btn-sm btn-primary">
                            Xem chi tiết đơn hàng
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Trạng Thái</h5>
                </div>
                <div class="card-body">
                    <p><strong>Trạng thái:</strong> 
                        @if($booking->status === 'pending')
                            <span class="badge bg-warning">Chờ xác nhận</span>
                        @elseif($booking->status === 'confirmed')
                            <span class="badge bg-success">Đã xác nhận</span>
                        @elseif($booking->status === 'rejected')
                            <span class="badge bg-danger">Đã từ chối</span>
                        @elseif($booking->status === 'checked_in')
                            <span class="badge bg-info">Đã check-in</span>
                        @elseif($booking->status === 'completed')
                            <span class="badge bg-success">Hoàn thành</span>
                        @elseif($booking->status === 'cancelled')
                            <span class="badge bg-secondary">Đã hủy</span>
                        @endif
                    </p>
                    <p><strong>Ngày tạo:</strong> {{ $booking->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Ngày cập nhật:</strong> {{ $booking->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

