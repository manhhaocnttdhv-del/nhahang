@extends('layouts.app')

@section('sidebar')
@include('staff.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Thanh Toán Đơn Hàng #{{ $order->order_number }}</h2>
        <a href="{{ route('staff.orders.show', $order->id) }}" class="btn btn-secondary">Quay lại</a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Chi Tiết Đơn Hàng</h5>
                </div>
                <div class="card-body">
                    <p><strong>Tổng tiền:</strong> {{ number_format($order->total_amount) }} đ</p>
                    <p><strong>Đã thanh toán:</strong> {{ number_format($order->payments->sum('amount')) }} đ</p>
                    <p><strong>Còn lại:</strong> {{ number_format($order->total_amount - $order->payments->sum('amount')) }} đ</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thanh Toán</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('staff.payments.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        
                        <div class="mb-3">
                            <label class="form-label">Phương thức thanh toán</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="cash">Tiền mặt</option>
                                <option value="bank_transfer">Chuyển khoản</option>
                                <option value="momo">Momo</option>
                                <option value="vnpay">VNPay</option>
                                <option value="bank_card">Thẻ ngân hàng</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Số tiền</label>
                            <input type="number" name="amount" class="form-control" 
                                   value="{{ $order->total_amount - $order->payments->sum('amount') }}" 
                                   max="{{ $order->total_amount - $order->payments->sum('amount') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mã giao dịch (nếu có)</label>
                            <input type="text" name="transaction_id" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Xác Nhận Thanh Toán</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

