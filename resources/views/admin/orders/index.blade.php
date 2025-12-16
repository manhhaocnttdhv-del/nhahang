@extends('layouts.app')

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-receipt"></i> Quản Lý Đơn Hàng</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                        <option value="preparing" {{ request('status') == 'preparing' ? 'selected' : '' }}>Đang chế biến</option>
                        <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Sẵn sàng</option>
                        <option value="served" {{ request('status') == 'served' ? 'selected' : '' }}>Đã phục vụ</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Đã giao</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Loại đơn</label>
                    <select name="order_type" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="dine_in" {{ request('order_type') == 'dine_in' ? 'selected' : '' }}>Tại chỗ</option>
                        <option value="takeaway" {{ request('order_type') == 'takeaway' ? 'selected' : '' }}>Mang đi</option>
                        <option value="delivery" {{ request('order_type') == 'delivery' ? 'selected' : '' }}>Giao hàng</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Ngày</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Lọc
                        </button>
                    </div>
                </div>
            </form>
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
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>#{{ $order->order_number }}</td>
                                <td>
                                    {{ $order->customer_name ?? $order->user->name ?? 'N/A' }}<br>
                                    @if($order->user)
                                        <small class="text-muted">{{ $order->user->email }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($order->order_type === 'dine_in')
                                        <span class="badge bg-primary">Tại chỗ</span>
                                    @elseif($order->order_type === 'takeaway')
                                        <span class="badge bg-info">Mang đi</span>
                                    @else
                                        <span class="badge bg-warning">Giao hàng</span>
                                    @endif
                                </td>
                                <td><strong>{{ number_format($order->total_amount, 0, ',', '.') }} đ</strong></td>
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
                                    @elseif($order->status === 'delivered')
                                        <span class="badge bg-success">Đã giao</span>
                                    @elseif($order->status === 'cancelled')
                                        <span class="badge bg-danger">Đã hủy</span>
                                    @endif
                                </td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Không có đơn hàng nào</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($orders->hasPages())
                <div class="mt-3">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

