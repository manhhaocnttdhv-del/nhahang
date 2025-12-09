@extends('layouts.app')

@section('sidebar')
@include('staff.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <h2 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard Nhân Viên</h2>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>Đặt Bàn Mới</h5>
                    <h2 id="pendingBookings">{{ $pendingBookings ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5>Đơn Hàng Đang Xử Lý</h5>
                    <h2 id="processingOrders">{{ $processingOrders ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5>Đơn Hàng Đang Chế Biến</h5>
                    <h2 id="preparingOrders">{{ $preparingOrders ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>Doanh Thu Hôm Nay</h5>
                    <h2>{{ number_format($todayRevenue ?? 0) }} đ</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Đặt Bàn Gần Đây</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Khách</th>
                                    <th>Ngày/Giờ</th>
                                    <th>Số khách</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentBookings ?? [] as $booking)
                                    <tr>
                                        <td>{{ $booking->customer_name }}</td>
                                        <td>{{ $booking->booking_date->format('d/m') }} {{ \Carbon\Carbon::parse($booking->booking_time)->format('H:i') }}</td>
                                        <td>{{ $booking->number_of_guests }}</td>
                                        <td>
                                            @if($booking->status === 'pending')
                                                <span class="badge bg-warning">Chờ</span>
                                            @elseif($booking->status === 'confirmed')
                                                <span class="badge bg-success">Đã xác nhận</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Không có đặt bàn nào</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Đơn Hàng Gần Đây</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Loại</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders ?? [] as $order)
                                    <tr>
                                        <td>#{{ $order->order_number }}</td>
                                        <td>
                                            @if($order->order_type === 'dine_in')
                                                <span class="badge bg-primary">Tại chỗ</span>
                                            @elseif($order->order_type === 'takeaway')
                                                <span class="badge bg-info">Mang đi</span>
                                            @else
                                                <span class="badge bg-warning">Giao hàng</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($order->total_amount) }} đ</td>
                                        <td>
                                            @if($order->status === 'pending')
                                                <span class="badge bg-warning">Chờ</span>
                                            @elseif($order->status === 'processing')
                                                <span class="badge bg-info">Xử lý</span>
                                            @elseif($order->status === 'preparing')
                                                <span class="badge bg-primary">Chế biến</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Không có đơn hàng nào</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

