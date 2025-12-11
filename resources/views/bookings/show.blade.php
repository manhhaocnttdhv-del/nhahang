@extends('layouts.app')

@section('title', 'Chi Tiết Đặt Bàn')

@section('content')
<div class="container my-5">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary mb-3">
                <i class="bi bi-arrow-left me-2"></i> Quay Lại
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Booking Info -->
        <div class="col-md-8 mb-4">
            <div class="card fade-in-up">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 2rem;">
                    <h3 class="mb-0" style="font-weight: 700;">
                        <i class="bi bi-calendar-check me-2"></i> Đặt Bàn #{{ $booking->id }}
                    </h3>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-circle me-3" style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-person text-white" style="font-size: 1.5rem;"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Khách hàng</small>
                                    <strong style="font-size: 1.1rem;">{{ $booking->customer_name }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-circle me-3" style="width: 50px; height: 50px; background: linear-gradient(135deg, #06d6a0 0%, #048a64 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-telephone text-white" style="font-size: 1.5rem;"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Số điện thoại</small>
                                    <strong style="font-size: 1.1rem;">{{ $booking->customer_phone }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-circle me-3" style="width: 50px; height: 50px; background: linear-gradient(135deg, #ffb703 0%, #fb8500 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-calendar3 text-white" style="font-size: 1.5rem;"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Ngày & Giờ</small>
                                    <strong style="font-size: 1.1rem;">
                                        {{ $booking->booking_date->format('d/m/Y') }} 
                                        {{ \Carbon\Carbon::parse($booking->booking_time)->format('H:i') }}
                                        @if($booking->end_time)
                                            - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                        @endif
                                    </strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-circle me-3" style="width: 50px; height: 50px; background: linear-gradient(135deg, #457b9d 0%, #1d3557 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-people text-white" style="font-size: 1.5rem;"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Số khách</small>
                                    <strong style="font-size: 1.1rem;">{{ $booking->number_of_guests }} người</strong>
                                </div>
                            </div>
                        </div>
                        @if($booking->table)
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-circle me-3" style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-table text-white" style="font-size: 1.5rem;"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Bàn</small>
                                    <strong style="font-size: 1.1rem;">{{ $booking->table->name }} ({{ $booking->table->number }})</strong>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($booking->location_preference)
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-circle me-3" style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-geo-alt text-white" style="font-size: 1.5rem;"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Yêu cầu vị trí</small>
                                    <strong style="font-size: 1.1rem;">{{ $booking->location_preference }}</strong>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <div class="mt-4 p-3 rounded" style="background: #f8f9fa;">
                        <strong class="d-block mb-2">Trạng thái:</strong>
                        @if($booking->status === 'pending')
                            <span class="badge bg-warning" style="font-size: 1rem; padding: 10px 20px;">
                                <i class="bi bi-clock me-2"></i>Chờ xác nhận
                            </span>
                        @elseif($booking->status === 'confirmed')
                            <span class="badge bg-success" style="font-size: 1rem; padding: 10px 20px;">
                                <i class="bi bi-check-circle me-2"></i>Đã xác nhận
                            </span>
                        @elseif($booking->status === 'checked_in')
                            <span class="badge bg-info" style="font-size: 1rem; padding: 10px 20px;">
                                <i class="bi bi-door-open me-2"></i>Đã check-in
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Orders from this booking -->
            <div class="card mt-4 fade-in-up" style="animation-delay: 0.2s;">
                <div class="card-header" style="background: linear-gradient(135deg, #06d6a0 0%, #048a64 100%); color: white;">
                    <h5 class="mb-0">
                        <i class="bi bi-receipt me-2"></i> Đơn Hàng Từ Đặt Bàn Này
                        <span class="badge bg-light text-dark ms-2">{{ $booking->orders->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($booking->orders->count() > 0)
                        @foreach($booking->orders as $order)
                            <div class="card mb-3 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <strong>Đơn #{{ $order->order_number }}</strong>
                                            <small class="text-muted ms-2">{{ $order->created_at->format('d/m/Y H:i') }}</small>
                                        </div>
                                        <div class="d-flex gap-2 align-items-center">
                                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-eye"></i> Chi tiết
                                            </a>
                                            <span class="badge 
                                                {{ $order->status === 'pending' ? 'bg-warning' : '' }}
                                                {{ $order->status === 'processing' ? 'bg-info' : '' }}
                                                {{ $order->status === 'preparing' ? 'bg-primary' : '' }}
                                                {{ $order->status === 'ready' ? 'bg-success' : '' }}
                                                {{ $order->status === 'served' ? 'bg-success' : '' }}
                                                {{ $order->status === 'delivered' ? 'bg-success' : '' }}
                                                {{ $order->status === 'cancelled' ? 'bg-danger' : '' }}">
                                                @if($order->status === 'pending')
                                                    <i class="bi bi-clock"></i> Chờ xử lý
                                                @elseif($order->status === 'processing')
                                                    <i class="bi bi-gear"></i> Đang xử lý
                                                @elseif($order->status === 'preparing')
                                                    <i class="bi bi-fire"></i> Đang chế biến
                                                @elseif($order->status === 'ready')
                                                    <i class="bi bi-check-circle"></i> Sẵn sàng
                                                @elseif($order->status === 'served')
                                                    <i class="bi bi-check2-all"></i> Đã phục vụ
                                                @elseif($order->status === 'delivered')
                                                    <i class="bi bi-truck"></i> Đã giao
                                                @else
                                                    <i class="bi bi-x-octagon"></i> Đã hủy
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Món</th>
                                                    <th class="text-center">SL</th>
                                                    <th class="text-end">Thành tiền</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($order->orderItems as $item)
                                                    <tr>
                                                        <td>{{ $item->item_name }}</td>
                                                        <td class="text-center">{{ $item->quantity }}</td>
                                                        <td class="text-end">{{ number_format($item->subtotal) }} đ</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="2">Tổng:</th>
                                                    <th class="text-end price-tag">{{ number_format($order->total_amount) }} đ</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-cart-x display-1 text-muted mb-3"></i>
                            <p class="text-muted mb-4">Chưa có đơn hàng nào từ đặt bàn này</p>
                            <a href="{{ route('bookings.order', $booking->id) }}" class="btn btn-success">
                                <i class="bi bi-cart-plus me-2"></i> Đặt Món Ngay
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right: Actions -->
        <div class="col-md-4">
            @if($booking->status === 'confirmed' || $booking->status === 'checked_in')
                <div class="card fade-in-up" style="animation-delay: 0.3s;">
                    <div class="card-header text-white" style="background: linear-gradient(135deg, #06d6a0 0%, #048a64 100%);">
                        <h5 class="mb-0"><i class="bi bi-cart-plus"></i> Đặt Món</h5>
                    </div>
                    <div class="card-body text-center p-4">
                        <i class="bi bi-cart-check display-1 text-success mb-3"></i>
                        <p class="mb-4">Bạn đã có bàn! Hãy đặt món ngay để thưởng thức</p>
                        <a href="{{ route('bookings.order', $booking->id) }}" class="btn btn-success btn-lg w-100 ripple">
                            <i class="bi bi-menu-button-wide me-2"></i> Chọn Món Ngay
                        </a>
                    </div>
                </div>
            @endif

            @if($booking->status === 'pending')
            <div class="card mt-4">
                <div class="card-header" style="background: #f8f9fa;">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle text-warning me-2"></i> Thao Tác</h5>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#cancelBookingModal">
                        <i class="bi bi-x-circle me-2"></i> Hủy Đặt Bàn
                    </button>
                </div>
            </div>
            @endif

            <div class="card mt-4">
                <div class="card-header" style="background: #f8f9fa;">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Thông Tin</h5>
                </div>
                <div class="card-body">
                    @if($booking->notes)
                        <div class="mb-3">
                            <strong>Ghi chú:</strong>
                            <p class="text-muted mb-0">{{ $booking->notes }}</p>
                        </div>
                    @endif
                    <div class="alert alert-info mb-0">
                        <small>
                            <i class="bi bi-info-circle me-2"></i>
                            Vui lòng đến đúng giờ đã đặt. Nếu có thay đổi, vui lòng liên hệ nhà hàng.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Booking Modal -->
@if($booking->status === 'pending')
<div class="modal fade" id="cancelBookingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hủy Đặt Bàn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('bookings.cancel', $booking->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn hủy đặt bàn này?</p>
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
@endsection

