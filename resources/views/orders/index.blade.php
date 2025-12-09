@extends('layouts.app')

@section('title', 'Lịch Sử Đơn Hàng')

@section('content')
<div class="container my-5">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 mb-3" style="font-weight: 800; color: var(--dark-color);">
                <i class="bi bi-receipt-cutoff"></i> Lịch Sử Đơn Hàng
            </h1>
            <p class="lead text-muted">Theo dõi đơn hàng của bạn</p>
        </div>
    </div>

    <div class="row">
        @forelse($orders as $order)
            <div class="col-12 mb-4 fade-in-up">
                <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                    <div class="card-header border-0 d-flex justify-content-between align-items-center p-4" 
                         style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <div>
                            <h5 class="mb-1 fw-bold">
                                <i class="bi bi-receipt-cutoff me-2"></i> Đơn hàng #{{ $order->order_number }}
                            </h5>
                            <small class="opacity-90">
                                <i class="bi bi-calendar3 me-1"></i>{{ $order->created_at->format('d/m/Y') }} 
                                <i class="bi bi-clock ms-2 me-1"></i>{{ $order->created_at->format('H:i') }}
                            </small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @if($order->status === 'pending')
                                <span class="badge bg-warning text-dark px-3 py-2" style="font-size: 0.9rem;">
                                    <i class="bi bi-clock"></i> Chờ xử lý
                                </span>
                            @elseif($order->status === 'processing')
                                <span class="badge bg-info px-3 py-2" style="font-size: 0.9rem;">
                                    <i class="bi bi-gear"></i> Đang xử lý
                                </span>
                            @elseif($order->status === 'preparing')
                                <span class="badge bg-primary px-3 py-2" style="font-size: 0.9rem;">
                                    <i class="bi bi-fire"></i> Đang chế biến
                                </span>
                            @elseif($order->status === 'ready')
                                <span class="badge bg-success px-3 py-2" style="font-size: 0.9rem;">
                                    <i class="bi bi-check-circle"></i> Sẵn sàng
                                </span>
                            @elseif($order->status === 'served')
                                <span class="badge bg-success px-3 py-2" style="font-size: 0.9rem;">
                                    <i class="bi bi-check2-all"></i> Đã phục vụ
                                </span>
                            @elseif($order->status === 'delivered')
                                <span class="badge bg-success px-3 py-2" style="font-size: 0.9rem;">
                                    <i class="bi bi-truck"></i> Đã giao
                                </span>
                            @else
                                <span class="badge bg-danger px-3 py-2" style="font-size: 0.9rem;">
                                    <i class="bi bi-x-octagon"></i> Đã hủy
                                </span>
                            @endif
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-light btn-sm px-3" style="border-radius: 8px;">
                                <i class="bi bi-eye me-1"></i> Chi tiết
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-lg-8">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="bi bi-list-ul text-primary fs-5"></i>
                                    </div>
                                    <h5 class="mb-0 fw-bold" style="color: #2d3748;">Chi tiết đơn hàng</h5>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead style="background: #f8f9fa; border-radius: 10px;">
                                            <tr>
                                                <th class="border-0" style="font-weight: 600; color: #4a5568;">Món</th>
                                                <th class="text-center border-0" style="font-weight: 600; color: #4a5568;">Số lượng</th>
                                                <th class="text-end border-0" style="font-weight: 600; color: #4a5568;">Giá</th>
                                                <th class="text-end border-0" style="font-weight: 600; color: #4a5568;">Thành tiền</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($order->orderItems as $item)
                                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                                    <td class="py-3">
                                                        <strong style="color: #2d3748;">{{ $item->item_name }}</strong>
                                                    </td>
                                                    <td class="text-center py-3">
                                                        <span class="badge bg-secondary bg-opacity-10 text-dark px-3 py-2" style="font-size: 0.9rem; border-radius: 20px;">
                                                            {{ $item->quantity }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end py-3" style="color: #718096;">
                                                        {{ number_format($item->item_price) }} đ
                                                    </td>
                                                    <td class="text-end py-3">
                                                        <strong style="color: #2d3748;">{{ number_format($item->subtotal) }} đ</strong>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffffff 0%, #f7fafc 100%); border-radius: 15px;">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-4">
                                            <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                                <i class="bi bi-info-circle text-info fs-5"></i>
                                            </div>
                                            <h6 class="mb-0 fw-bold" style="color: #2d3748;">Thông tin đơn hàng</h6>
                                        </div>
                                        
                                        <div class="mb-3 pb-3 border-bottom">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-tag text-primary me-2"></i>
                                                <span class="text-muted small">Loại đơn:</span>
                                            </div>
                                            <div class="ms-4">
                                                @if($order->order_type === 'dine_in')
                                                    <span class="badge bg-primary px-3 py-2" style="font-size: 0.85rem; border-radius: 8px;">Tại chỗ</span>
                                                @elseif($order->order_type === 'takeaway')
                                                    <span class="badge bg-info px-3 py-2" style="font-size: 0.85rem; border-radius: 8px;">Mang đi</span>
                                                @else
                                                    <span class="badge bg-warning px-3 py-2" style="font-size: 0.85rem; border-radius: 8px;">Giao hàng</span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        @if($order->table)
                                        <div class="mb-3 pb-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-table text-success me-2"></i>
                                                <span class="text-muted small me-2">Bàn:</span>
                                                <strong style="color: #2d3748;">{{ $order->table->name }}</strong>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @if($order->voucher)
                                        <div class="mb-3 pb-3 border-bottom">
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="bi bi-ticket-perforated text-success me-2"></i>
                                                <span class="text-muted small">Voucher:</span>
                                            </div>
                                            <div class="ms-4">
                                                <code class="bg-success bg-opacity-10 text-success px-2 py-1 rounded">{{ $order->voucher->code }}</code>
                                                <span class="text-success ms-2">(-{{ number_format($order->discount_amount) }} đ)</span>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        <div class="mt-4 pt-3 border-top">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Tạm tính:</span>
                                                <strong style="color: #2d3748;">{{ number_format($order->subtotal) }} đ</strong>
                                            </div>
                                            @if($order->discount_amount > 0)
                                                <div class="d-flex justify-content-between mb-2 text-success">
                                                    <span>Giảm giá:</span>
                                                    <strong>-{{ number_format($order->discount_amount) }} đ</strong>
                                                </div>
                                            @endif
                                            <div class="d-flex justify-content-between mb-3">
                                                <span class="text-muted">Thuế VAT:</span>
                                                <strong style="color: #2d3748;">{{ number_format($order->tax_amount) }} đ</strong>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center p-3 rounded" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                <h5 class="mb-0 text-white fw-bold">Tổng tiền:</h5>
                                                <h4 class="mb-0 text-white fw-bold">{{ number_format($order->total_amount) }} đ</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card text-center p-5">
                    <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                    <h4 class="text-muted">Bạn chưa có đơn hàng nào</h4>
                    <p class="text-muted mb-4">Hãy đặt món ngay để thưởng thức những món ăn ngon</p>
                    <a href="{{ route('orders.create') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-cart-plus"></i> Đặt Món Ngay
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection

