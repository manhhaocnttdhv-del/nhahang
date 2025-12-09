@extends('layouts.app')

@section('sidebar')
@include('staff.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-cash-coin"></i> Quản Lý Thanh Toán</h2>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Đơn hàng</th>
                            <th>Phương thức</th>
                            <th>Số tiền</th>
                            <th>Ngày</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>#{{ $payment->id }}</td>
                                <td>#{{ $payment->order->order_number }}</td>
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
                                <td>{{ number_format($payment->amount) }} đ</td>
                                <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('staff.payments.show', $payment->id) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Không có thanh toán nào</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $payments->links() }}
        </div>
    </div>
</div>
@endsection

