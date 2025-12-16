@extends('layouts.app')

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-credit-card"></i> Quản Lý Thanh Toán</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(isset($totalAmount))
        <div class="alert alert-info">
            <strong>Tổng tiền đã thanh toán:</strong> {{ number_format($totalAmount, 0, ',', '.') }} đ
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
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Thất bại</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Phương thức</label>
                    <select name="payment_method" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Tiền mặt</option>
                        <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Chuyển khoản</option>
                        <option value="momo" {{ request('payment_method') == 'momo' ? 'selected' : '' }}>Momo</option>
                        <option value="vnpay" {{ request('payment_method') == 'vnpay' ? 'selected' : '' }}>VNPay</option>
                        <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Thẻ</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Từ ngày</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Đến ngày</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
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
                            <th>ID</th>
                            <th>Đơn hàng</th>
                            <th>Khách hàng</th>
                            <th>Phương thức</th>
                            <th>Số tiền</th>
                            <th>Trạng thái</th>
                            <th>Ngày</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>#{{ $payment->id }}</td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $payment->order_id) }}">
                                        #{{ $payment->order->order_number }}
                                    </a>
                                </td>
                                <td>
                                    {{ $payment->order->user->name ?? $payment->order->customer_name ?? 'N/A' }}
                                </td>
                                <td>
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
                                </td>
                                <td><strong>{{ number_format($payment->amount, 0, ',', '.') }} đ</strong></td>
                                <td>
                                    @if($payment->status === 'completed')
                                        <span class="badge bg-success">Hoàn thành</span>
                                    @elseif($payment->status === 'pending')
                                        <span class="badge bg-warning">Chờ xử lý</span>
                                    @elseif($payment->status === 'failed')
                                        <span class="badge bg-danger">Thất bại</span>
                                    @else
                                        <span class="badge bg-secondary">Đã hủy</span>
                                    @endif
                                </td>
                                <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">Không có thanh toán nào</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($payments->hasPages())
                <div class="mt-3">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

