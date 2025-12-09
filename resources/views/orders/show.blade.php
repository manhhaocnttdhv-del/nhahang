@extends('layouts.app')

@section('title', 'Chi Tiết Đơn Hàng')

@section('content')
<div class="container my-5">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary mb-3">
                <i class="bi bi-arrow-left me-2"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Order Header -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center" 
                     style="background: #667eea; color: white;">
                    <div>
                        <h4 class="mb-0">
                            <i class="bi bi-receipt me-2"></i> Đơn Hàng #{{ $order->order_number }}
                        </h4>
                        <small class="opacity-75">{{ $order->created_at->format('d/m/Y H:i') }}</small>
                    </div>
                    <div>
                        @if($order->status === 'pending')
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-clock"></i> Chờ xử lý
                            </span>
                        @elseif($order->status === 'processing')
                            <span class="badge bg-info">
                                <i class="bi bi-gear"></i> Đang xử lý
                            </span>
                        @elseif($order->status === 'preparing')
                            <span class="badge bg-primary">
                                <i class="bi bi-fire"></i> Đang chế biến
                            </span>
                        @elseif($order->status === 'ready')
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle"></i> Sẵn sàng
                            </span>
                        @elseif($order->status === 'served')
                            <span class="badge bg-success">
                                <i class="bi bi-check2-all"></i> Đã phục vụ
                            </span>
                        @elseif($order->status === 'delivered')
                            <span class="badge bg-success">
                                <i class="bi bi-truck"></i> Đã giao
                            </span>
                        @else
                            <span class="badge bg-danger">
                                <i class="bi bi-x-octagon"></i> Đã hủy
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Order Items -->
                    <h5 class="mb-3" style="font-weight: 600;">
                        <i class="bi bi-list-ul me-2"></i> Chi Tiết Đơn Hàng
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead style="background: #f8f9fa;">
                                <tr>
                                    <th>Món</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-end">Giá</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                    <tr>
                                        <td><strong>{{ $item->item_name }}</strong></td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $item->quantity }}</span>
                                        </td>
                                        <td class="text-end">{{ number_format($item->item_price) }} đ</td>
                                        <td class="text-end">
                                            <strong>{{ number_format($item->subtotal) }} đ</strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="card mb-4">
                <div class="card-header" style="background: #f8f9fa;">
                    <h5 class="mb-0" style="font-weight: 600;">
                        <i class="bi bi-clock-history me-2"></i> Tiến Trình Đơn Hàng
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item {{ $order->status !== 'cancelled' ? 'active' : '' }}">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6>Đơn hàng đã được tạo</h6>
                                <small class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                        @if(in_array($order->status, ['processing', 'preparing', 'ready', 'served', 'delivered']))
                        <div class="timeline-item active">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6>Đang xử lý</h6>
                                <small class="text-muted">Nhà hàng đang chuẩn bị đơn hàng của bạn</small>
                            </div>
                        </div>
                        @endif
                        @if(in_array($order->status, ['preparing', 'ready', 'served', 'delivered']))
                        <div class="timeline-item active">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6>Đang chế biến</h6>
                                <small class="text-muted">Đầu bếp đang nấu món của bạn</small>
                            </div>
                        </div>
                        @endif
                        @if(in_array($order->status, ['ready', 'served', 'delivered']))
                        <div class="timeline-item active">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6>Sẵn sàng</h6>
                                <small class="text-muted">Món ăn đã sẵn sàng</small>
                            </div>
                        </div>
                        @endif
                        @if(in_array($order->status, ['served', 'delivered']))
                        <div class="timeline-item active">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6>
                                    @if($order->order_type === 'delivery')
                                        Đã giao hàng
                                    @else
                                        Đã phục vụ
                                    @endif
                                </h6>
                                <small class="text-muted">Đơn hàng đã hoàn thành</small>
                            </div>
                        </div>
                        @endif
                        @if($order->status === 'cancelled')
                        <div class="timeline-item">
                            <div class="timeline-marker bg-danger"></div>
                            <div class="timeline-content">
                                <h6>Đã hủy</h6>
                                <small class="text-muted">Đơn hàng đã bị hủy</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Order Info -->
            <div class="card mb-4">
                <div class="card-header" style="background: #f8f9fa;">
                    <h5 class="mb-0" style="font-weight: 600;">
                        <i class="bi bi-info-circle me-2"></i> Thông Tin Đơn Hàng
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <i class="bi bi-tag text-primary me-2"></i>
                        <strong>Loại đơn:</strong> 
                        @if($order->order_type === 'dine_in')
                            <span class="badge bg-primary">Tại chỗ</span>
                        @elseif($order->order_type === 'takeaway')
                            <span class="badge bg-info">Mang đi</span>
                        @else
                            <span class="badge bg-warning">Giao hàng</span>
                        @endif
                    </p>
                    @if($order->table)
                        <p class="mb-2">
                            <i class="bi bi-table text-success me-2"></i>
                            <strong>Bàn:</strong> {{ $order->table->name }}
                        </p>
                    @endif
                    @if($order->customer_address)
                        <p class="mb-2">
                            <i class="bi bi-geo-alt text-danger me-2"></i>
                            <strong>Địa chỉ:</strong> {{ $order->customer_address }}
                        </p>
                    @endif
                    @if($order->voucher)
                        <p class="mb-2">
                            <i class="bi bi-ticket-perforated text-success me-2"></i>
                            <strong>Voucher:</strong> 
                            <code>{{ $order->voucher->code }}</code>
                        </p>
                    @endif
                    @if($order->notes)
                        <p class="mb-2">
                            <i class="bi bi-sticky text-secondary me-2"></i>
                            <strong>Ghi chú:</strong> {{ $order->notes }}
                        </p>
                    @endif
                </div>
            </div>

            <!-- Order Summary -->
            <div class="card mb-4">
                <div class="card-header" style="background: #f8f9fa;">
                    <h5 class="mb-0" style="font-weight: 600;">
                        <i class="bi bi-calculator me-2"></i> Tổng Kết
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tạm tính:</span>
                        <strong>{{ number_format($order->subtotal) }} đ</strong>
                    </div>
                    @if($order->discount_amount > 0)
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Giảm giá:</span>
                            <strong>-{{ number_format($order->discount_amount) }} đ</strong>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between mb-2">
                        <span>Thuế VAT (10%):</span>
                        <strong>{{ number_format($order->tax_amount) }} đ</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Tổng tiền:</h5>
                        <h4 class="mb-0 price-tag">{{ number_format($order->total_amount) }} đ</h4>
                    </div>
                </div>
            </div>

            <!-- Payment Status -->
            @php
                $totalPaid = $order->payments()->where('status', 'completed')->sum('amount');
                $remainingAmount = $order->total_amount - $totalPaid;
            @endphp
            
            @if($remainingAmount > 0)
            <div class="card mb-4" style="border: 2px solid #667eea;">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="mb-0">
                        <i class="bi bi-credit-card me-2"></i> Thanh Toán
                    </h5>
                </div>
                <div class="card-body text-center">
                    <p class="mb-3">
                        <strong>Số tiền cần thanh toán:</strong>
                        <span class="price-tag" style="font-size: 1.5rem;">{{ number_format($remainingAmount) }} đ</span>
                    </p>
                    @if($totalPaid > 0)
                        <p class="text-muted mb-3">
                            <small>Đã thanh toán: {{ number_format($totalPaid) }} đ</small>
                        </p>
                    @endif
                    <a href="{{ route('payments.qr', $order->id) }}" class="btn btn-primary btn-lg w-100">
                        <i class="bi bi-qr-code me-2"></i> Thanh Toán QR Code
                    </a>
                </div>
            </div>
            @else
            <div class="card mb-4" style="border: 2px solid #10b981;">
                <div class="card-body text-center" style="background: #f0fdf4;">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-success">Đã Thanh Toán Đầy Đủ</h5>
                    <p class="text-muted mb-0">Tổng đã thanh toán: {{ number_format($order->total_amount) }} đ</p>
                </div>
            </div>
            @endif

            <!-- Actions -->
            @if($order->status === 'pending')
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3" style="font-weight: 600;">
                        <i class="bi bi-exclamation-triangle text-warning me-2"></i> Thao Tác
                    </h6>
                    <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                        <i class="bi bi-x-circle me-2"></i> Hủy Đơn Hàng
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
@if($order->status === 'pending')
<div class="modal fade" id="cancelOrderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hủy Đơn Hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('orders.cancel', $order->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn hủy đơn hàng này?</p>
                    <div class="mb-3">
                        <label class="form-label">Lý do hủy (tùy chọn)</label>
                        <textarea class="form-control" name="cancel_reason" rows="3" placeholder="Nhập lý do hủy..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-danger">Xác nhận hủy</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    
    .timeline-item {
        position: relative;
        padding-bottom: 30px;
    }
    
    .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        left: -24px;
        top: 20px;
        width: 2px;
        height: calc(100% - 10px);
        background: #e0e0e0;
    }
    
    .timeline-item.active:not(:last-child)::before {
        background: #667eea;
    }
    
    .timeline-marker {
        position: absolute;
        left: -30px;
        top: 5px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 3px solid white;
        box-shadow: 0 0 0 2px #e0e0e0;
    }
    
    .timeline-item.active .timeline-marker {
        box-shadow: 0 0 0 2px #667eea;
    }
    
    .timeline-content h6 {
        font-weight: 600;
        margin-bottom: 5px;
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto refresh order status every 30 seconds if order is not completed
    @if(!in_array($order->status, ['served', 'delivered', 'cancelled']))
    setInterval(function() {
        location.reload();
    }, 30000);
    @endif
</script>
@endpush
@endsection

