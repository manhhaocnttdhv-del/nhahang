@extends('layouts.app')

@section('sidebar')
@include('staff.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Chi Tiết Đặt Bàn #{{ $booking->id }}</h2>
        <a href="{{ route('staff.bookings.index') }}" class="btn btn-secondary">Quay lại</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông Tin Đặt Bàn</h5>
                </div>
                <div class="card-body">
                    <p><strong>Khách hàng:</strong> {{ $booking->customer_name }}</p>
                    <p><strong>Số điện thoại:</strong> {{ $booking->customer_phone }}</p>
                    <p><strong>Ngày:</strong> {{ $booking->booking_date->format('d/m/Y') }}</p>
                    <p><strong>Giờ:</strong> {{ \Carbon\Carbon::parse($booking->booking_time)->format('H:i') }}</p>
                    <p><strong>Số khách:</strong> {{ $booking->number_of_guests }} người</p>
                    @if($booking->location_preference)
                        <p><strong>Yêu cầu vị trí:</strong> {{ $booking->location_preference }}</p>
                    @endif
                    @if($booking->notes)
                        <p><strong>Ghi chú:</strong> {{ $booking->notes }}</p>
                    @endif
                    <p><strong>Trạng thái:</strong> 
                        @if($booking->status === 'pending')
                            <span class="badge bg-warning">Chờ xác nhận</span>
                        @elseif($booking->status === 'confirmed')
                            <span class="badge bg-success">Đã xác nhận</span>
                        @elseif($booking->status === 'rejected')
                            <span class="badge bg-danger">Đã từ chối</span>
                        @elseif($booking->status === 'checked_in')
                            <span class="badge bg-info">Đã check-in</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thao Tác</h5>
                </div>
                <div class="card-body">
                    @if($booking->status === 'pending')
                        <form action="{{ route('staff.bookings.confirm', $booking->id) }}" method="POST" class="mb-3">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Chọn bàn</label>
                                <select name="table_id" class="form-select">
                                    <option value="">Tự động</option>
                                    @foreach($tables as $table)
                                        <option value="{{ $table->id }}">{{ $table->name }} ({{ $table->number }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Xác Nhận</button>
                        </form>
                        <form action="{{ route('staff.bookings.reject', $booking->id) }}" method="POST" class="mb-3">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100">Từ Chối</button>
                        </form>
                    @endif

                    @if($booking->status === 'confirmed' && $booking->table_id)
                        <form action="{{ route('staff.bookings.check-in', $booking->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-info w-100">Check-in</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

