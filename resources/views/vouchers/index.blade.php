@extends('layouts.app')

@section('title', 'Voucher & Khuyến Mãi')

@section('content')
<div class="container my-5">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 mb-3" style="font-weight: 700; color: #667eea;">
                <i class="bi bi-ticket-perforated"></i> Voucher & Khuyến Mãi
            </h1>
            <p class="lead text-muted">Áp dụng mã giảm giá khi đặt món</p>
        </div>
    </div>

    <!-- Available Vouchers -->
    <div class="row mb-5">
        <div class="col-12 mb-4">
            <h3 class="mb-3" style="font-weight: 600;">
                <i class="bi bi-gift me-2"></i> Voucher Khả Dụng
            </h3>
        </div>
        @forelse($vouchers as $voucher)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-tag me-2"></i>{{ $voucher->name }}
                            </h5>
                            @if($voucher->type === 'percentage')
                                <span class="badge bg-light text-dark">{{ $voucher->value }}%</span>
                            @else
                                <span class="badge bg-light text-dark">{{ number_format($voucher->value) }} đ</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">{{ $voucher->description }}</p>
                        <div class="mb-3">
                            <strong class="text-primary">Mã: </strong>
                            <code style="font-size: 1.1rem; font-weight: bold; color: #667eea;">{{ $voucher->code }}</code>
                        </div>
                        @if($voucher->min_order_amount)
                            <p class="small text-muted mb-2">
                                <i class="bi bi-info-circle me-1"></i>
                                Đơn tối thiểu: <strong>{{ number_format($voucher->min_order_amount) }} đ</strong>
                            </p>
                        @endif
                        @if($voucher->max_discount)
                            <p class="small text-muted mb-2">
                                <i class="bi bi-info-circle me-1"></i>
                                Giảm tối đa: <strong>{{ number_format($voucher->max_discount) }} đ</strong>
                            </p>
                        @endif
                        <p class="small text-muted mb-3">
                            <i class="bi bi-calendar me-1"></i>
                            HSD: {{ $voucher->end_date->format('d/m/Y') }}
                        </p>
                        @if($voucher->usage_limit)
                            <div class="progress mb-3" style="height: 8px;">
                                @php
                                    $usedPercent = ($voucher->used_count / $voucher->usage_limit) * 100;
                                @endphp
                                <div class="progress-bar" role="progressbar" style="width: {{ $usedPercent }}%">
                                    {{ $voucher->used_count }}/{{ $voucher->usage_limit }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card text-center p-5">
                    <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                    <h4 class="text-muted">Hiện không có voucher nào</h4>
                    <p class="text-muted">Vui lòng quay lại sau</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Used Vouchers History -->
    @if($usedVouchers->count() > 0)
    <div class="row">
        <div class="col-12 mb-4">
            <h3 class="mb-3" style="font-weight: 600;">
                <i class="bi bi-clock-history me-2"></i> Lịch Sử Sử Dụng
            </h3>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead style="background: #f8f9fa;">
                                <tr>
                                    <th>Mã Voucher</th>
                                    <th>Tên Voucher</th>
                                    <th>Đơn Hàng</th>
                                    <th>Giảm Giá</th>
                                    <th>Ngày Sử Dụng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usedVouchers as $order)
                                    <tr>
                                        <td><code>{{ $order->voucher->code }}</code></td>
                                        <td>{{ $order->voucher->name }}</td>
                                        <td>
                                            <a href="{{ route('orders.show', $order->id) }}" class="text-decoration-none">
                                                #{{ $order->order_number }}
                                            </a>
                                        </td>
                                        <td class="text-success">
                                            <strong>-{{ number_format($order->discount_amount) }} đ</strong>
                                        </td>
                                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

