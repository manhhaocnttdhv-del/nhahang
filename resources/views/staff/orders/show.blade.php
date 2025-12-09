@extends('layouts.app')

@section('sidebar')
@include('staff.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Chi Tiết Đơn Hàng #{{ $order->order_number }}</h2>
        <a href="{{ route('staff.orders.index') }}" class="btn btn-secondary">Quay lại</a>
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
                    <h5 class="mb-0">Chi Tiết Đơn Hàng</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Món</th>
                                <th>Số lượng</th>
                                <th class="text-end">Giá</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderItems as $item)
                                <tr>
                                    <td>{{ $item->item_name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="text-end">{{ number_format($item->item_price) }} đ</td>
                                    <td class="text-end">{{ number_format($item->subtotal) }} đ</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3">Tổng tiền:</th>
                                <th class="text-end">{{ number_format($order->total_amount) }} đ</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông Tin</h5>
                </div>
                <div class="card-body">
                    <p><strong>Trạng thái:</strong> 
                        @if($order->status === 'pending')
                            <span class="badge bg-warning">Chờ xử lý</span>
                        @elseif($order->status === 'processing')
                            <span class="badge bg-info">Đang xử lý</span>
                        @elseif($order->status === 'preparing')
                            <span class="badge bg-primary">Đang chế biến</span>
                        @elseif($order->status === 'ready')
                            <span class="badge bg-success">Sẵn sàng</span>
                        @endif
                    </p>
                    <p><strong>Loại:</strong> 
                        @if($order->order_type === 'dine_in')
                            Tại chỗ
                        @elseif($order->order_type === 'takeaway')
                            Mang đi
                        @else
                            Giao hàng
                        @endif
                    </p>
                    @if($order->table)
                        <p><strong>Bàn:</strong> {{ $order->table->name }}</p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Cập Nhật Trạng Thái</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('staff.orders.update-status', $order->id) }}" method="POST" id="updateStatusForm">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                        <div class="mb-3">
                            <label class="form-label mb-2">Trạng thái đơn hàng</label>
                            <select name="status" class="form-select" required>
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Đang chế biến</option>
                                <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>Sẵn sàng</option>
                                <option value="served" {{ $order->status === 'served' ? 'selected' : '' }}>Đã phục vụ</option>
                                <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Đã giao</option>
                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-circle me-2"></i> Cập Nhật Trạng Thái
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

