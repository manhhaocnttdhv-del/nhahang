@extends('layouts.app')

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-credit-card"></i> Chi Tiết Thanh Toán #{{ $payment->id }}</h2>
        <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Thông Tin Thanh Toán</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Mã thanh toán:</strong> #{{ $payment->id }}</p>
                            <p><strong>Đơn hàng:</strong> 
                                <a href="{{ route('admin.orders.show', $payment->order_id) }}">
                                    #{{ $payment->order->order_number }}
                                </a>
                            </p>
                            <p><strong>Khách hàng:</strong> {{ $payment->order->user->name ?? $payment->order->customer_name ?? 'N/A' }}</p>
                            <p><strong>Số tiền:</strong> <strong class="text-success">{{ number_format($payment->amount, 0, ',', '.') }} đ</strong></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Phương thức:</strong> 
                                @if($payment->payment_method === 'cash')
                                    <span class="badge bg-success">Tiền mặt</span>
                                @elseif($payment->payment_method === 'bank_transfer')
                                    <span class="badge bg-info">Chuyển khoản</span>
                                @elseif($payment->payment_method === 'momo')
                                    <span class="badge bg-primary">Momo</span>
                                @elseif($payment->payment_method === 'vnpay')
                                    <span class="badge bg-warning">VNPay</span>
                                @else
                                    <span class="badge bg-secondary">Thẻ</span>
                                @endif
                            </p>
                            <p><strong>Trạng thái:</strong> 
                                @if($payment->status === 'completed')
                                    <span class="badge bg-success">Hoàn thành</span>
                                @elseif($payment->status === 'pending')
                                    <span class="badge bg-warning">Chờ xử lý</span>
                                @elseif($payment->status === 'failed')
                                    <span class="badge bg-danger">Thất bại</span>
                                @else
                                    <span class="badge bg-secondary">Đã hủy</span>
                                @endif
                            </p>
                            <p><strong>Ngày tạo:</strong> {{ $payment->created_at->format('d/m/Y H:i') }}</p>
                            <p><strong>Ngày cập nhật:</strong> {{ $payment->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @if($payment->notes)
                        <hr>
                        <p><strong>Ghi chú:</strong> {{ $payment->notes }}</p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Chi Tiết Đơn Hàng</h5>
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
                                @foreach($payment->order->orderItems as $item)
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
                                    <th colspan="3">Tổng tiền đơn hàng:</th>
                                    <th>{{ number_format($payment->order->total_amount, 0, ',', '.') }} đ</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

