@extends('layouts.app')

@section('sidebar')
@include('staff.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-cash-coin me-2"></i> Chi Tiết Thanh Toán #{{ $payment->id }}</h2>
        <a href="{{ route('staff.payments.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i> Quay lại
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Payment Info -->
            <div class="card mb-4">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i> Thông Tin Thanh Toán</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong><i class="bi bi-hash me-2"></i> Mã thanh toán:</strong>
                                <span class="badge bg-primary">#{{ $payment->id }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong><i class="bi bi-receipt me-2"></i> Đơn hàng:</strong>
                                <a href="{{ route('staff.orders.show', $payment->order_id) }}" class="text-decoration-none">
                                    #{{ $payment->order->order_number }}
                                </a>
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong><i class="bi bi-credit-card me-2"></i> Phương thức:</strong>
                                @if($payment->payment_method === 'cash')
                                    <span class="badge bg-success">Tiền mặt</span>
                                @elseif($payment->payment_method === 'bank_transfer')
                                    <span class="badge bg-info">Chuyển khoản</span>
                                @elseif($payment->payment_method === 'momo')
                                    <span class="badge bg-primary">Momo</span>
                                @elseif($payment->payment_method === 'vnpay')
                                    <span class="badge bg-warning">VNPay</span>
                                @else
                                    <span class="badge bg-secondary">Thẻ ngân hàng</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong><i class="bi bi-currency-exchange me-2"></i> Số tiền:</strong>
                                <span class="price-tag" style="font-size: 1.5rem;">{{ number_format($payment->amount) }} đ</span>
                            </p>
                        </div>
                    </div>

                    @if($payment->transaction_id)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong><i class="bi bi-receipt-cutoff me-2"></i> Mã giao dịch:</strong>
                                <code>{{ $payment->transaction_id }}</code>
                            </p>
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong><i class="bi bi-clock me-2"></i> Trạng thái:</strong>
                                @if($payment->status === 'pending')
                                    <span class="badge bg-warning">Chờ xác nhận</span>
                                @elseif($payment->status === 'completed')
                                    <span class="badge bg-success">Đã xác nhận</span>
                                @elseif($payment->status === 'failed')
                                    <span class="badge bg-danger">Thất bại</span>
                                @else
                                    <span class="badge bg-secondary">Đã hoàn tiền</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong><i class="bi bi-calendar me-2"></i> Ngày tạo:</strong>
                                {{ $payment->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>

                    @if($payment->notes)
                    <div class="row">
                        <div class="col-12">
                            <p class="mb-0">
                                <strong><i class="bi bi-sticky me-2"></i> Ghi chú:</strong>
                                {{ $payment->notes }}
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Order Info -->
            <div class="card">
                <div class="card-header" style="background: #f8f9fa;">
                    <h5 class="mb-0"><i class="bi bi-cart me-2"></i> Thông Tin Đơn Hàng</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong>Khách hàng:</strong>
                                {{ $payment->order->customer_name ?? $payment->order->user->name ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong>Loại đơn:</strong>
                                @if($payment->order->order_type === 'dine_in')
                                    <span class="badge bg-primary">Tại chỗ</span>
                                @elseif($payment->order->order_type === 'takeaway')
                                    <span class="badge bg-info">Mang đi</span>
                                @else
                                    <span class="badge bg-warning">Giao hàng</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong>Tổng tiền đơn hàng:</strong>
                                {{ number_format($payment->order->total_amount) }} đ
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong>Đã thanh toán:</strong>
                                @php
                                    $totalPaid = $payment->order->payments()->where('status', 'completed')->sum('amount');
                                @endphp
                                <span class="{{ $totalPaid >= $payment->order->total_amount ? 'text-success' : 'text-warning' }}">
                                    {{ number_format($totalPaid) }} đ
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Confirm Payment -->
            @if($payment->status === 'pending')
            <div class="card mb-4" style="border: 2px solid #ffc107;">
                <div class="card-header" style="background: #ffc107; color: #000;">
                    <h5 class="mb-0"><i class="bi bi-check-circle me-2"></i> Xác Nhận Thanh Toán</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('staff.payments.confirm', $payment->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Ghi chú (tùy chọn)</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Thêm ghi chú...">{{ $payment->notes }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100 btn-lg">
                            <i class="bi bi-check-circle me-2"></i> Xác Nhận Thanh Toán
                        </button>
                    </form>
                </div>
            </div>
            @else
            <div class="card mb-4" style="border: 2px solid #10b981;">
                <div class="card-body text-center" style="background: #f0fdf4;">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-success">Đã Xác Nhận</h5>
                    <p class="text-muted mb-0">Thanh toán này đã được xác nhận</p>
                </div>
            </div>
            @endif

            <!-- Payment Summary -->
            <div class="card">
                <div class="card-header" style="background: #f8f9fa;">
                    <h5 class="mb-0"><i class="bi bi-calculator me-2"></i> Tóm Tắt</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tổng đơn hàng:</span>
                        <strong>{{ number_format($payment->order->total_amount) }} đ</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Đã thanh toán:</span>
                        <strong class="text-success">{{ number_format($totalPaid) }} đ</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span><strong>Còn lại:</strong></span>
                        <strong class="{{ ($payment->order->total_amount - $totalPaid) > 0 ? 'text-danger' : 'text-success' }}">
                            {{ number_format($payment->order->total_amount - $totalPaid) }} đ
                        </strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

