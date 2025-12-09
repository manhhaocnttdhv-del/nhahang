@extends('layouts.app')

@section('title', 'Đặt Món')

@section('content')
<div class="container my-5">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 mb-3" style="font-weight: 700; color: #667eea;">
                <i class="bi bi-cart-check"></i> Đặt Món
            </h1>
            <p class="lead text-muted">Chọn món yêu thích và thêm vào giỏ hàng</p>
        </div>
    </div>
    
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Lỗi!</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            @foreach($menuItems as $categoryName => $items)
                <div class="card mb-4">
                    <div class="card-header text-white" style="background: #667eea; padding: 1.5rem;">
                        <h4 class="mb-0" style="font-weight: 700;">
                            <i class="bi bi-tag me-2"></i>{{ $categoryName }}
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            @foreach($items as $item)
                                <div class="col-md-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body p-3">
                                            <div class="d-flex">
                                                @if($item->image)
                                                    <img src="{{ asset('storage/' . $item->image) }}" 
                                                         class="rounded me-3" 
                                                         style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #f0f0f0;" 
                                                         alt="{{ $item->name }}">
                                                @else
                                                    <div class="rounded me-3 d-flex align-items-center justify-content-center" 
                                                         style="width: 100px; height: 100px; background: #667eea;">
                                                        <i class="bi bi-utensils text-white" style="font-size: 2.5rem;"></i>
                                                    </div>
                                                @endif
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-2" style="font-weight: 700; color: var(--dark-color);">{{ $item->name }}</h6>
                                                    <p class="text-muted small mb-2" style="line-height: 1.4;">{{ Str::limit($item->description, 50) }}</p>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <strong class="price-tag" style="font-size: 1.2rem;">{{ number_format($item->price) }} đ</strong>
                                                        <button class="btn btn-primary btn-sm ripple add-item" 
                                                                data-item-id="{{ $item->id }}"
                                                                data-item-name="{{ $item->name }}"
                                                                data-item-price="{{ $item->price }}"
                                                                style="border-radius: 50px; width: 45px; height: 45px; padding: 0; font-size: 1.3rem; font-weight: bold;">
                                                            <span>+</span>
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

        <div class="col-md-4">
            <div class="card sticky-top shadow-lg" style="top: 100px; border-radius: 8px;">
                <div class="card-header text-white" style="background: #28a745; padding: 1.5rem; border-radius: 8px 8px 0 0;">
                    <h4 class="mb-0" style="font-weight: 700;">
                        <i class="bi bi-cart-check me-2"></i> Giỏ Hàng
                    </h4>
                </div>
                <div class="card-body p-4">
                    <div id="cartItems" style="min-height: 200px; max-height: 400px; overflow-y: auto;">
                        <div class="text-center py-5 empty-cart">
                            <i class="bi bi-cart-x" style="font-size: 4rem; color: #ccc; margin-bottom: 1rem;"></i>
                            <p class="text-muted mb-1">Giỏ hàng trống</p>
                            <small class="text-muted">Hãy thêm món vào giỏ hàng</small>
                        </div>
                    </div>
                    <hr class="my-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 p-3" style="background: #f8f9fa; border-radius: 10px;">
                        <strong style="font-size: 1.1rem;">Tạm tính:</strong>
                        <strong class="price-tag" id="cartSubtotal" style="font-size: 1.2rem;">0 đ</strong>
                    </div>
                    <div id="voucherSection" class="mb-3" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-2 p-2" style="background: #e8f5e9; border-radius: 8px;">
                            <div>
                                <small class="text-success d-block">Giảm giá:</small>
                                <strong id="voucherName" class="text-success"></strong>
                            </div>
                            <strong class="text-success" id="voucherDiscount">0 đ</strong>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="removeVoucher">
                            <i class="bi bi-x-circle me-1"></i> Xóa voucher
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 p-3" style="background: #f8f9fa; border-radius: 10px;">
                        <strong style="font-size: 1.1rem;">Thuế VAT (10%):</strong>
                        <strong id="cartTax" style="font-size: 1.1rem;">0 đ</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-4 p-3" style="background: #667eea; color: white; border-radius: 10px;">
                        <strong style="font-size: 1.2rem;">Tổng tiền:</strong>
                        <strong id="cartTotal" style="font-size: 1.5rem;">0 đ</strong>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold mb-2">
                            <i class="bi bi-ticket-perforated me-2"></i> Mã giảm giá
                        </label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="voucherCode" placeholder="Nhập mã voucher">
                            <button type="button" class="btn btn-primary" id="applyVoucher">
                                <i class="bi bi-check-lg"></i> Áp dụng
                            </button>
                        </div>
                        <small class="text-muted">Có voucher? Nhập mã để nhận giảm giá</small>
                        <div id="voucherMessage" class="mt-2"></div>
                    </div>
                    <form id="orderForm" action="{{ route('orders.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="items" id="orderItems" value="">
                        <input type="hidden" name="voucher_code" id="voucherCodeInput" value="">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold mb-2">
                                <i class="bi bi-utensils me-2"></i> Hình thức đặt món
                            </label>
                            <div class="order-type-wrapper">
                                <i class="bi bi-utensils order-type-icon" id="orderTypeIcon"></i>
                                <select class="form-select form-select-lg order-type-select" name="order_type" id="orderType" required style="font-size: 1.1rem; padding: 12px;">
                                    <option value="dine_in" selected>Tại chỗ (Dine-in)</option>
                                    <option value="takeaway">Mang đi (Takeaway)</option>
                                    <option value="delivery">Giao hàng (Delivery)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3" id="tableSelect">
                            <label class="form-label fw-bold mb-2">
                                <i class="bi bi-table me-2"></i> Chọn bàn <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-lg" name="table_id" id="tableSelectDropdown" required>
                                <option value="">-- Chọn bàn --</option>
                            </select>
                            <small class="text-muted">Vui lòng chọn bàn bạn đang ngồi</small>
                        </div>

                        <div class="mb-3" id="customerInfo" style="display: none;">
                            <label class="form-label fw-bold mb-2">
                                <i class="bi bi-person me-2"></i> Thông tin khách hàng
                            </label>
                            <div class="mb-2">
                                <input type="text" class="form-control form-control-lg" name="customer_name" 
                                       placeholder="Họ và tên" value="{{ auth()->user()->name ?? '' }}">
                            </div>
                            <div class="mb-2">
                                <input type="text" class="form-control form-control-lg" name="customer_phone" 
                                       placeholder="Số điện thoại" value="{{ auth()->user()->phone ?? '' }}">
                            </div>
                            <div id="addressField" style="display: none;">
                                <input type="text" class="form-control form-control-lg" name="customer_address" 
                                       id="customerAddress" placeholder="Địa chỉ giao hàng">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 btn-lg py-3 ripple" id="submitOrder" disabled style="font-weight: 700; font-size: 1.1rem; cursor: not-allowed;">
                            <i class="bi bi-check-circle me-2"></i> Đặt Món Ngay
                        </button>
                        <small class="text-muted d-block mt-2 text-center" id="submitHint">Vui lòng thêm món vào giỏ hàng để đặt món</small>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .order-type-select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23667eea' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.306 4h9.388c.961 0 1.421 1.013.855 1.658L8.753 11.14a1.5 1.5 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    }
    
    .empty-cart {
        animation: fadeIn 0.5s;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    #cartItems::-webkit-scrollbar {
        width: 6px;
    }
    
    #cartItems::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    #cartItems::-webkit-scrollbar-thumb {
        background: #667eea;
        border-radius: 10px;
    }
    
    #cartItems::-webkit-scrollbar-thumb:hover {
        background: #764ba2;
    }
    
    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        animation: slideInRight 0.3s ease-out;
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    let cart = [];
    
    // Toast notification function
    function showToast(message, type = 'success') {
        const bgColor = type === 'success' ? '#06d6a0' : '#e63946';
        const icon = type === 'success' ? '<i class="bi bi-check-circle-fill me-2"></i>' : '<i class="bi bi-x-circle-fill me-2"></i>';
        
        const toast = $(`
            <div class="toast-notification">
                <div class="alert alert-${type === 'success' ? 'success' : 'danger'} shadow-lg mb-0" style="border-radius: 15px; border: none;">
                    <strong>${icon}</strong> ${message}
                </div>
            </div>
        `);
        
        $('body').append(toast);
        
        setTimeout(() => {
            toast.fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }

    // Function to update cart badge in navbar
    function updateCartBadge() {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        const badge = $('#cartBadge');
        if (totalItems > 0) {
            badge.text(totalItems).show();
        } else {
            badge.hide();
        }
    }
    
    // Voucher state
    let currentVoucher = null;
    
    // Function to update cart totals
    function updateCartTotals(subtotal) {
        let discount = 0;
        let tax = 0;
        let total = 0;
        
        if (currentVoucher) {
            discount = currentVoucher.discount || 0;
        }
        
        tax = (subtotal - discount) * 0.1;
        total = subtotal - discount + tax;
        
        $('#cartSubtotal').text(subtotal.toLocaleString() + ' đ');
        $('#cartTax').text(tax.toLocaleString() + ' đ');
        $('#cartTotal').text(total.toLocaleString() + ' đ');
        
        if (currentVoucher) {
            $('#voucherSection').show();
            $('#voucherName').text(currentVoucher.name);
            $('#voucherDiscount').text('-' + discount.toLocaleString() + ' đ');
        } else {
            $('#voucherSection').hide();
        }
    }
    
    // Apply voucher
    $('#applyVoucher').click(function() {
        const code = $('#voucherCode').val().trim();
        if (!code) {
            showToast('Vui lòng nhập mã voucher', 'error');
            return;
        }
        
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        
        $.ajax({
            url: '/vouchers/check',
            method: 'POST',
            data: {
                code: code,
                subtotal: subtotal,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.valid) {
                    currentVoucher = response.voucher;
                    currentVoucher.discount = response.discount;
                    $('#voucherCodeInput').val(code);
                    $('#voucherMessage').html('<div class="alert alert-success mb-0">' + 
                        '<i class="bi bi-check-circle me-2"></i>Áp dụng voucher thành công!</div>');
                    updateCartTotals(subtotal);
                    showToast('Áp dụng voucher thành công!', 'success');
                } else {
                    $('#voucherMessage').html('<div class="alert alert-danger mb-0">' + 
                        '<i class="bi bi-x-circle me-2"></i>' + response.message + '</div>');
                    showToast(response.message, 'error');
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Có lỗi xảy ra';
                $('#voucherMessage').html('<div class="alert alert-danger mb-0">' + 
                    '<i class="bi bi-x-circle me-2"></i>' + message + '</div>');
                showToast(message, 'error');
            }
        });
    });
    
    // Remove voucher
    $('#removeVoucher').click(function() {
        currentVoucher = null;
        $('#voucherCode').val('');
        $('#voucherCodeInput').val('');
        $('#voucherMessage').html('');
        $('#voucherSection').hide();
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        updateCartTotals(subtotal);
        showToast('Đã xóa voucher', 'success');
    });
    
    // Enter key to apply voucher
    $('#voucherCode').keypress(function(e) {
        if (e.which === 13) {
            $('#applyVoucher').click();
        }
    });

    $(document).ready(function() {
        // Check if order was successful (from redirect with success message)
        @if(session('success'))
            // Clear cart if order was successful
            localStorage.removeItem('cart');
            cart = [];
            updateCartDisplay();
            updateCartBadge();
        @endif
        
        // Load cart from localStorage
        const savedCart = localStorage.getItem('cart');
        if (savedCart) {
            cart = JSON.parse(savedCart);
            updateCartDisplay();
            updateCartBadge();
        }

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

            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartDisplay();
            updateCartBadge();
            
            // Show success notification
            showToast('Đã thêm "' + itemName + '" vào giỏ hàng!', 'success');
        });

        // Load available tables
        function loadTables() {
            $.get('/api/tables/available', function(data) {
                const select = $('#tableSelectDropdown');
                select.empty();
                select.append('<option value="">-- Chọn bàn --</option>');
                
                if (data.data && data.data.length > 0) {
                    data.data.forEach(function(table) {
                        select.append(`<option value="${table.id}">${table.name} (${table.number}) - ${table.capacity} người - ${table.area || 'Khu vực chung'}</option>`);
                    });
                } else {
                    select.append('<option value="">Không có bàn trống</option>');
                }
            }).fail(function() {
                // Fallback: load from page if API fails
                console.log('Could not load tables from API');
            });
        }
        
        // Load tables immediately on page load if dine_in (default)
        loadTables();

        // Function to update order type icon
        function updateOrderTypeIcon(orderType) {
            const icon = $('#orderTypeIcon');
            icon.removeClass('bi-utensils bi-bag bi-truck');
            
            switch(orderType) {
                case 'dine_in':
                    icon.addClass('bi-utensils');
                    break;
                case 'takeaway':
                    icon.addClass('bi-bag');
                    break;
                case 'delivery':
                    icon.addClass('bi-truck');
                    break;
                default:
                    icon.addClass('bi-utensils');
            }
        }

        // Order type change - Smart logic
        $('#orderType').change(function() {
            const orderType = $(this).val();
            updateOrderTypeIcon(orderType);
            
            // Show relevant fields based on order type
            if (orderType === 'dine_in') {
                // Dine-in: Show table selection
                if ($('#tableSelectDropdown option').length <= 1) {
                    loadTables();
                }
                $('#tableSelect').show(); // Show immediately
                $('#tableSelectDropdown').prop('required', true);
                // Hide customer info
                $('#customerInfo').slideUp(200);
            } else {
                // Hide table selection and remove required
                $('#tableSelect').slideUp(200);
                $('#tableSelectDropdown').prop('required', false).val('');
                
                // Show customer info for takeaway/delivery
                $('#customerInfo').slideDown(300);
            }
            
            if (orderType === 'takeaway') {
                // Takeaway: Show customer info (name, phone)
                $('#customerInfo').slideDown(300);
                $('#addressField').slideUp(200);
                $('input[name="customer_name"]').prop('required', true);
                $('input[name="customer_phone"]').prop('required', true);
                $('#customerAddress').prop('required', false);
            } else if (orderType === 'delivery') {
                // Delivery: Show customer info + address
                $('#customerInfo').slideDown(300);
                $('#addressField').slideDown(300);
                $('input[name="customer_name"]').prop('required', true);
                $('input[name="customer_phone"]').prop('required', true);
                $('#customerAddress').prop('required', true);
            }
        });
        
        // Trigger on page load - default to dine_in
        const defaultOrderType = $('#orderType').val() || 'dine_in';
        $('#orderType').val(defaultOrderType);
        updateOrderTypeIcon(defaultOrderType);
        $('#orderType').trigger('change');

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
                                    <button class="btn btn-sm btn-outline-danger remove-item mt-2 ripple" data-index="${index}" style="border-radius: 50px; width: 35px; height: 35px; padding: 0;">
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
                    <div class="text-center py-5 empty-cart">
                        <i class="bi bi-cart-x" style="font-size: 4rem; color: #ccc; margin-bottom: 1rem;"></i>
                        <p class="text-muted mb-1">Giỏ hàng trống</p>
                        <small class="text-muted">Hãy thêm món vào giỏ hàng</small>
                    </div>
                `;
                $('#submitOrder').prop('disabled', true).addClass('opacity-50').css('cursor', 'not-allowed');
                $('#submitHint').text('Vui lòng thêm món vào giỏ hàng để đặt món').show();
            } else {
                $('#submitOrder').prop('disabled', false).removeClass('opacity-50').css('cursor', 'pointer');
                $('#submitHint').text('').hide();
            }

            $('#cartItems').html(html);
            updateCartTotals(total);

            // Remove item
            $('.remove-item').click(function() {
                const index = $(this).data('index');
                const itemName = cart[index].name;
                cart.splice(index, 1);
                localStorage.setItem('cart', JSON.stringify(cart));
                updateCartDisplay();
                updateCartBadge();
                showToast('Đã xóa "' + itemName + '" khỏi giỏ hàng!', 'success');
            });
        }

        // Submit order - Use web route instead of API
        $('#orderForm').on('submit', function(e) {
            e.preventDefault(); // Prevent default first
            
            // Check if button is disabled
            if ($('#submitOrder').prop('disabled')) {
                showToast('Vui lòng thêm món vào giỏ hàng trước!', 'error');
                return false;
            }
            
            // Validate cart
            if (cart.length === 0) {
                showToast('Vui lòng chọn ít nhất một món!', 'error');
                $('#submitOrder').prop('disabled', true);
                return false;
            }
            
            const orderType = $('#orderType').val();
            let isValid = true;
            
            // Validate based on order type
            if (orderType === 'dine_in') {
                const tableId = $('#tableSelectDropdown').val();
                if (!tableId) {
                    showToast('Vui lòng chọn bàn!', 'error');
                    $('#tableSelectDropdown').focus();
                    isValid = false;
                }
            } else if (orderType === 'takeaway' || orderType === 'delivery') {
                const name = $('input[name="customer_name"]').val()?.trim();
                const phone = $('input[name="customer_phone"]').val()?.trim();
                if (!name || !phone) {
                    showToast('Vui lòng điền đầy đủ thông tin khách hàng!', 'error');
                    isValid = false;
                }
                if (orderType === 'delivery') {
                    const address = $('#customerAddress').val()?.trim();
                    if (!address) {
                        showToast('Vui lòng nhập địa chỉ giao hàng!', 'error');
                        $('#customerAddress').focus();
                        isValid = false;
                    }
                }
            }
            
            if (!isValid) {
                return false;
            }
            
            // Prepare items data
            const items = cart.map(item => ({
                menu_item_id: item.id,
                quantity: item.quantity
            }));

            // Validate items before setting
            if (items.length === 0) {
                showToast('Vui lòng chọn ít nhất một món!', 'error');
                return false;
            }

            // Set items to hidden input - MUST be done before submit
            const itemsJson = JSON.stringify(items);
            console.log('Items JSON:', itemsJson); // Debug
            $('#orderItems').val(itemsJson);
            
            // Verify items were set
            const itemsValue = $('#orderItems').val();
            console.log('Items value after set:', itemsValue); // Debug
            if (!itemsValue || itemsValue === '' || itemsValue === '[]') {
                showToast('Lỗi: Không thể lưu thông tin món ăn. Vui lòng thử lại!', 'error');
                return false;
            }
            
            // Set voucher code if exists
            if (currentVoucher) {
                $('#voucherCodeInput').val(currentVoucher.code);
            }
            
            // Disable submit button to prevent double submit
            const submitBtn = $('#submitOrder');
            submitBtn.prop('disabled', true);
            const originalText = submitBtn.html();
            submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...');
            
            // Use requestAnimationFrame to ensure DOM is updated
            requestAnimationFrame(() => {
                // Double check items are set
                const finalItemsValue = $('#orderItems').val();
                if (!finalItemsValue || finalItemsValue === '' || finalItemsValue === '[]') {
                    showToast('Lỗi: Dữ liệu món ăn không hợp lệ. Vui lòng thử lại!', 'error');
                    submitBtn.prop('disabled', false);
                    submitBtn.html(originalText);
                    return false;
                }
                
                // Submit form using FormData to ensure all values are sent
                const form = document.getElementById('orderForm');
                const formData = new FormData(form);
                
                // Verify formData has items
                if (!formData.get('items') || formData.get('items') === '') {
                    showToast('Lỗi: Dữ liệu món ăn không hợp lệ. Vui lòng thử lại!', 'error');
                    submitBtn.prop('disabled', false);
                    submitBtn.html(originalText);
                    return false;
                }
                
                // Clear cart from localStorage
                localStorage.removeItem('cart');
                cart = [];
                updateCartBadge();
                
                // Submit the form using native submit
                try {
                    // Remove jQuery event listener to allow native submit
                    const formElement = $('#orderForm');
                    formElement.off('submit');
                    form.submit();
                } catch (error) {
                    console.error('Error submitting form:', error);
                    showToast('Có lỗi xảy ra khi đặt món. Vui lòng thử lại!', 'error');
                    submitBtn.prop('disabled', false);
                    submitBtn.html(originalText);
                }
            });
        });
    });
</script>
@endpush
@endsection


