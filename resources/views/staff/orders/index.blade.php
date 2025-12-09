@extends('layouts.app')

@section('sidebar')
@include('staff.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-receipt"></i> Quản Lý Đơn Hàng</h2>
        <div>
            <a href="?status=pending" class="btn btn-warning btn-sm">Chờ xử lý</a>
            <a href="?status=preparing" class="btn btn-primary btn-sm">Đang chế biến</a>
            <a href="{{ route('staff.orders.index') }}" class="btn btn-secondary btn-sm">Tất cả</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Loại</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>#{{ $order->order_number }}</td>
                                <td>{{ $order->customer_name ?? $order->user->name ?? 'N/A' }}</td>
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
                                        <span class="badge bg-warning">Chờ xử lý</span>
                                    @elseif($order->status === 'processing')
                                        <span class="badge bg-info">Đang xử lý</span>
                                    @elseif($order->status === 'preparing')
                                        <span class="badge bg-primary">Đang chế biến</span>
                                    @elseif($order->status === 'ready')
                                        <span class="badge bg-success">Sẵn sàng</span>
                                    @elseif($order->status === 'served')
                                        <span class="badge bg-success">Đã phục vụ</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('staff.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Không có đơn hàng nào</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection

