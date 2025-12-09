@extends('layouts.app')

@section('title', 'Chọn Món - Bàn {{ $booking->table->name ?? "" }}')

@section('content')
<div class="container my-5">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card fade-in-up" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-2" style="font-weight: 800;">
                                <i class="bi bi-table me-2"></i>Bàn {{ $booking->table->name ?? 'Chưa gán' }}
                            </h2>
                            <p class="mb-0 opacity-75">
                                <i class="bi bi-calendar3 me-2"></i>{{ $booking->booking_date->format('d/m/Y') }} 
                                <i class="bi bi-clock ms-3 me-2"></i>{{ \Carbon\Carbon::parse($booking->booking_time)->format('H:i') }}
                                <i class="bi bi-people ms-3 me-2"></i>{{ $booking->number_of_guests }} người
                            </p>
                        </div>
                        <div class="text-end">
                            <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-light">
                                <i class="bi bi-arrow-left me-2"></i>Quay Lại
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Menu Items -->
        <div class="col-lg-8">
            @foreach($menuItems as $categoryName => $items)
                <div class="card mb-4 fade-in-up">
                    <div class="card-header text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.5rem;">
                        <h4 class="mb-0" style="font-weight: 700;">
                            <i class="bi bi-tag me-2"></i>{{ $categoryName }}
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            @foreach($items as $item)
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm h-100" style="transition: all 0.3s;">
                                        <div class="card-body p-3">
                                            <div class="d-flex">
                                                @if($item->image)
                                                    <img src="{{ asset('storage/' . $item->image) }}" 
                                                         class="rounded me-3" 
                                                         style="width: 100px; height: 100px; object-fit: cover;" 
                                                         alt="{{ $item->name }}">
                                                @else
                                                    <div class="rounded me-3 d-flex align-items-center justify-content-center" 
                                                         style="width: 100px; height: 100px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                        <i class="bi bi-image text-white" style="font-size: 2rem;"></i>
                                                    </div>
                                                @endif
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-2" style="font-weight: 700;">{{ $item->name }}</h6>
                                                    <p class="text-muted small mb-2" style="line-height: 1.4;">{{ Str::limit($item->description, 50) }}</p>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <strong class="price-tag" style="font-size: 1.2rem;">{{ number_format($item->price) }} đ</strong>
                                                        <button class="btn btn-primary btn-sm ripple add-item" 
                                                                data-item-id="{{ $item->id }}"
                                                                data-item-name="{{ $item->name }}"
                                                                data-item-price="{{ $item->price }}"
                                                                style="border-radius: 50px; width: 40px; height: 40px; padding: 0;">
                                                            <i class="bi bi-plus-lg"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Cart Sidebar -->
        <div class="col-lg-4">
            <div class="card sticky-top shadow-lg" style="top: 100px; border-radius: 20px;">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #06d6a0 0%, #048a64 100%); padding: 1.5rem; border-radius: 20px 20px 0 0;">
                    <h4 class="mb-0" style="font-weight: 700;">
                        <i class="bi bi-cart-check me-2"></i> Giỏ Hàng
                    </h4>
                </div>
                <div class="card-body p-4">
                    <div id="cartItems" style="min-height: 200px; max-height: 400px; overflow-y: auto;">
                        <div class="text-center py-5">
                            <i class="bi bi-cart-x display-1 text-muted mb-3"></i>
                            <p class="text-muted">Giỏ hàng trống</p>
                        </div>
                    </div>
                    <hr class="my-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 p-3" style="background: #f8f9fa; border-radius: 10px;">
                        <strong style="font-size: 1.1rem;">Tổng tiền:</strong>
                        <strong class="price-tag" id="cartTotal" style="font-size: 1.5rem;">0 đ</strong>
                    </div>
                    <form id="orderForm" action="{{ route('orders.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="items" id="orderItems">
                        <input type="hidden" name="order_type" value="dine_in">
                        @if($booking->table_id)
                            <input type="hidden" name="table_id" value="{{ $booking->table_id }}">
                        @endif
                        <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                        
                        <button type="submit" class="btn btn-success w-100 btn-lg py-3 ripple" id="submitOrder" disabled style="font-weight: 700;">
                            <i class="bi bi-check-circle me-2"></i> Đặt Món Ngay
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let cart = [];

    $(document).ready(function() {
        // Add item to cart
        $('.add-item').click(function() {
            const itemId = $(this).data('item-id');
            const itemName = $(this).data('item-name');
            const itemPrice = $(this).data('item-price');

            const existingItem = cart.find(item => item.id === itemId);
            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({
                    id: itemId,
                    name: itemName,
                    price: itemPrice,
                    quantity: 1
                });
            }

            updateCartDisplay();
            
            // Show toast
            const toast = $(`
                <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
                    <div class="toast show" role="alert">
                        <div class="toast-header bg-success text-white">
                            <i class="bi bi-check-circle me-2"></i>
                            <strong class="me-auto">Đã thêm</strong>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                        </div>
                        <div class="toast-body">
                            Đã thêm "${itemName}" vào giỏ hàng!
                        </div>
                    </div>
                </div>
            `);
            $('body').append(toast);
            setTimeout(() => toast.remove(), 3000);
        });

        // Update cart display
        function updateCartDisplay() {
            let html = '';
            let total = 0;

            cart.forEach((item, index) => {
                const itemTotal = item.price * item.quantity;
                total += itemTotal;
                html += `
                    <div class="card mb-3 border-0 shadow-sm" style="background: #f8f9fa;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1" style="font-weight: 700;">${item.name}</h6>
                                    <small class="text-muted">${item.price.toLocaleString()} đ x ${item.quantity}</small>
                                </div>
                                <div class="text-end">
                                    <strong class="price-tag" style="font-size: 1.1rem;">${itemTotal.toLocaleString()} đ</strong><br>
                                    <button class="btn btn-sm btn-outline-danger remove-item mt-2 ripple" data-index="${index}" style="border-radius: 50px;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            if (cart.length === 0) {
                html = `
                    <div class="text-center py-5">
                        <i class="bi bi-cart-x display-1 text-muted mb-3"></i>
                        <p class="text-muted">Giỏ hàng trống</p>
                    </div>
                `;
                $('#submitOrder').prop('disabled', true);
            } else {
                $('#submitOrder').prop('disabled', false);
            }

            $('#cartItems').html(html);
            $('#cartTotal').text(total.toLocaleString() + ' đ');

            // Remove item
            $('.remove-item').click(function() {
                const index = $(this).data('index');
                cart.splice(index, 1);
                updateCartDisplay();
            });
        }

        // Submit order
        $('#orderForm').submit(function(e) {
            if (cart.length === 0) {
                e.preventDefault();
                alert('Vui lòng chọn ít nhất một món!');
                return false;
            }
            
            const items = cart.map(item => ({
                menu_item_id: item.id,
                quantity: item.quantity
            }));

            $('#orderItems').val(JSON.stringify(items));
        });
    });
</script>
@endpush
@endsection

