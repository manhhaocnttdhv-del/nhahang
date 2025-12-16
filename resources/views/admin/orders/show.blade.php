@extends('layouts.app')

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-receipt"></i> Chi Tiết Đơn Hàng #{{ $order->order_number }}</h2>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Thông Tin Đơn Hàng</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Khách hàng:</strong> {{ $order->customer_name ?? $order->user->name ?? 'N/A' }}</p>
                            <p><strong>Số điện thoại:</strong> {{ $order->customer_phone ?? '-' }}</p>
                            <p><strong>Địa chỉ:</strong> {{ $order->customer_address ?? '-' }}</p>
                            @if($order->user)
                                <p><strong>Tài khoản:</strong> {{ $order->user->email }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p><strong>Loại đơn:</strong> 
                                @if($order->order_type === 'dine_in')
                                    <span class="badge bg-primary">Tại chỗ</span>
                                @elseif($order->order_type === 'takeaway')
                                    <span class="badge bg-info">Mang đi</span>
                                @else
                                    <span class="badge bg-warning">Giao hàng</span>
                                @endif
                            </p>
                            <p><strong>Bàn:</strong> {{ $order->table ? $order->table->name : '-' }}</p>
                            <p><strong>Trạng thái:</strong> 
                                <span class="badge bg-{{ $order->status === 'served' || $order->status === 'delivered' ? 'success' : 'warning' }}">
                                    {{ $order->status }}
                                </span>
                            </p>
                            <p><strong>Ngày tạo:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Chi Tiết Món</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Món</th>
                                    <th>Số lượng</th>
                                    <th>Đơn giá</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                    <tr>
                                        <td>{{ $item->item_name }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->unit_price, 0, ',', '.') }} đ</td>
                                        <td>{{ number_format($item->subtotal, 0, ',', '.') }} đ</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3">Tổng tiền:</th>
                                    <th>{{ number_format($order->total_amount, 0, ',', '.') }} đ</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Thanh Toán</h5>
                </div>
                <div class="card-body">
                    @if($order->payments->count() > 0)
                        @foreach($order->payments as $payment)
                            <p><strong>Phương thức:</strong> {{ $payment->payment_method }}</p>
                            <p><strong>Số tiền:</strong> {{ number_format($payment->amount, 0, ',', '.') }} đ</p>
                            <p><strong>Trạng thái:</strong> 
                                <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : 'warning' }}">
                                    {{ $payment->status }}
                                </span>
                            </p>
                            <hr>
                        @endforeach
                    @else
                        <p class="text-muted">Chưa có thanh toán</p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Thông Tin Khác</h5>
                </div>
                <div class="card-body">
                    <p><strong>Tổng tiền:</strong> {{ number_format($order->total_amount, 0, ',', '.') }} đ</p>
                    @if($order->voucher)
                        <p><strong>Voucher:</strong> {{ $order->voucher->code }}</p>
                    @endif
                    @if($order->notes)
                        <p><strong>Ghi chú:</strong> {{ $order->notes }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

