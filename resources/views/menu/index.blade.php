@extends('layouts.app')

@section('title', 'Menu')

@section('content')
<div class="container my-5">
    <!-- Header Section -->
    <div class="row mb-5">
        <div class="col-12 text-center mb-4 fade-in-up">
            <div class="float-animation">
                <h1 class="display-3 mb-3 gradient-text" style="font-weight: 900;">
                    <i class="bi bi-menu-button-wide"></i> Thực Đơn
                </h1>
                <p class="lead text-muted" style="font-size: 1.3rem;">Khám phá hương vị đặc biệt của chúng tôi</p>
            </div>
        </div>
        <div class="col-12 col-md-8 mx-auto mb-4 fade-in-up" style="animation-delay: 0.2s;">
            <div class="input-group shadow-lg">
                <span class="input-group-text bg-white border-0">
                    <i class="bi bi-search text-primary"></i>
                </span>
                <input type="text" class="form-control border-0" id="searchMenu" 
                       placeholder="Tìm kiếm món ăn yêu thích của bạn...">
                <button class="btn btn-primary border-0" type="button">
                    <i class="bi bi-search"></i> Tìm
                </button>
            </div>
        </div>
    </div>

    <!-- Categories Filter -->
    <div class="row mb-5 fade-in-up" style="animation-delay: 0.3s;">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <button type="button" class="btn btn-primary active px-5 py-2 ripple" data-category="all" style="font-weight: 600;">
                    <i class="bi bi-grid-3x3-gap"></i> Tất Cả
                </button>
                @foreach($categories as $category)
                    <button type="button" class="btn btn-outline-primary px-5 py-2 ripple" 
                            data-category="{{ $category->id }}" style="font-weight: 600; border-width: 2px;">
                        <i class="bi bi-tag"></i> {{ $category->name }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Menu Items -->
    <div class="row" id="menuItems">
        @forelse($menuItems as $item)
            <div class="col-lg-4 col-md-6 mb-4 menu-item fade-in-up" data-category="{{ $item->category_id }}" data-name="{{ strtolower($item->name) }}">
                <div class="card menu-item-card h-100 position-relative">
                    @auth
                    <button type="button" class="btn btn-sm position-absolute top-0 end-0 m-2 favorite-btn" 
                            data-item-id="{{ $item->id }}" 
                            style="z-index: 10; background: rgba(255,255,255,0.9); border-radius: 50%; width: 40px; height: 40px; padding: 0;">
                        <i class="bi {{ in_array($item->id, $userFavorites ?? []) ? 'bi-heart-fill text-danger' : 'bi-heart' }}"></i>
                    </button>
                    @endauth
                    <div style="overflow: hidden; position: relative;">
                        @if($item->image)
                            <img src="{{ asset('storage/' . $item->image) }}" class="card-img-top menu-item-image" alt="{{ $item->name }}">
                        @else
                            <div class="menu-item-image bg-gradient d-flex align-items-center justify-content-center" style="background: #667eea;">
                                <i class="bi bi-utensils text-white" style="font-size: 4rem; opacity: 0.8;"></i>
                            </div>
                        @endif
                        @if($item->status === 'available')
                            <span class="badge bg-success badge-status" style="position: absolute; top: 15px; right: 15px; font-size: 0.85rem;">
                                <i class="bi bi-check-circle me-1"></i> Còn món
                            </span>
                        @else
                            <span class="badge bg-danger badge-status" style="position: absolute; top: 15px; right: 15px; font-size: 0.85rem;">
                                <i class="bi bi-x-circle me-1"></i> Hết món
                            </span>
                        @endif
                    </div>
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3" style="font-weight: 800; color: var(--dark-color); font-size: 1.4rem;">
                            {{ $item->name }}
                        </h5>
                        <p class="card-text text-muted mb-4" style="min-height: 60px; line-height: 1.6;">
                            {{ Str::limit($item->description, 100) }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <small class="text-muted d-block">Giá</small>
                                <span class="price-tag">{{ number_format($item->price) }} đ</span>
                            </div>
                        </div>
                        @auth
                            <button class="btn btn-primary w-100 add-to-cart ripple" 
                                    data-item-id="{{ $item->id }}" 
                                    data-item-name="{{ $item->name }}" 
                                    data-item-price="{{ $item->price }}"
                                    style="font-weight: 600; padding: 15px; font-size: 1rem;"
                                    {{ $item->status !== 'available' ? 'disabled' : '' }}>
                                <i class="bi bi-cart-plus me-2"></i> Thêm vào giỏ
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-primary w-100" style="font-weight: 600; padding: 15px;">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Đăng nhập để đặt món
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card text-center p-5">
                    <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                    <h4 class="text-muted">Chưa có món ăn nào trong menu</h4>
                    <p class="text-muted">Vui lòng quay lại sau</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Cart Modal -->
@auth
<div class="modal fade" id="cartModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Giỏ Hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="cartItems">
                <!-- Cart items will be loaded here -->
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Tổng tiền:</strong>
                        <strong class="text-primary" id="cartTotal">0 đ</strong>
                    </div>
                    <a href="{{ route('orders.create') }}" class="btn btn-primary w-100">Đặt Món</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endauth

@push('scripts')
<script>
    $(document).ready(function() {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];

        // Category filter
        $('[data-category]').click(function() {
            const category = $(this).data('category');
            $('[data-category]').removeClass('active');
            $(this).addClass('active').removeClass('btn-outline-primary').addClass('btn-primary');
            $('[data-category]').not(this).removeClass('btn-primary').addClass('btn-outline-primary');
            
            $('.menu-item').fadeOut(200, function() {
                if (category === 'all') {
                    $('.menu-item').fadeIn(300);
                } else {
                    $(`.menu-item[data-category="${category}"]`).fadeIn(300);
                }
            });
        });

        // Search with debounce
        let searchTimeout;
        $('#searchMenu').on('keyup', function() {
            clearTimeout(searchTimeout);
            const search = $(this).val().toLowerCase();
            
            searchTimeout = setTimeout(function() {
                if (search === '') {
                    $('.menu-item').fadeIn(300);
                } else {
                    $('.menu-item').each(function() {
                        const name = $(this).data('name');
                        if (name.includes(search)) {
                            $(this).fadeIn(300);
                        } else {
                            $(this).fadeOut(200);
                        }
                    });
                }
            }, 300);
        });

        // Add to cart
        $('.add-to-cart').click(function() {
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
            
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartDisplay();
            
            // Update cart badge in navbar
            const cartBadge = $('#cartBadge');
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            if (totalItems > 0) {
                cartBadge.text(totalItems).show();
            } else {
                cartBadge.hide();
            }
            
            // Show toast notification
            const toast = $(`
                <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
                    <div class="toast show" role="alert">
                        <div class="toast-header bg-success text-white">
                            <i class="bi bi-check-circle me-2"></i>
                            <strong class="me-auto">Thành công</strong>
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

        function updateCartDisplay() {
            let total = 0;
            let html = '';
            
            cart.forEach(item => {
                const itemTotal = item.price * item.quantity;
                total += itemTotal;
                html += `
                    <div class="d-flex justify-content-between mb-2">
                        <div>
                            <strong>${item.name}</strong><br>
                            <small>${item.price.toLocaleString()} đ x ${item.quantity}</small>
                        </div>
                        <div>
                            <strong>${itemTotal.toLocaleString()} đ</strong>
                        </div>
                    </div>
                `;
            });
            
            $('#cartItems').html(html || '<p class="text-muted">Giỏ hàng trống</p>');
            $('#cartTotal').text(total.toLocaleString() + ' đ');
        }

        // Show cart when clicking cart icon
        $('#cartModal').on('show.bs.modal', function() {
            updateCartDisplay();
        });
    });
</script>
@endpush
@endsection

