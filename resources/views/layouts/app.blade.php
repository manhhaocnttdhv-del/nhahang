<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Nhà Hàng') - Hệ Thống Quản Lý</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #f1faee;
            --accent-color: #764ba2;
            --dark-color: #1d3557;
            --light-color: #f8f9fa;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            color: #333;
        }
        
        .container {
            background: #ffffff;
            border-radius: 8px;
            padding: 2rem;
            margin-top: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .navbar {
            background: #667eea;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .navbar-brand {
            font-weight: 600;
            font-size: 1.5rem;
        }
        
        .navbar-brand i {
            color: #ffd60a;
        }
        
        .nav-link {
            font-weight: 500;
        }
        
        .card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            background: white;
        }
        
        .card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .btn {
            border-radius: 6px;
            padding: 10px 24px;
            font-weight: 500;
            border: none;
        }
        
        .btn-primary {
            background: #667eea;
        }
        
        .btn-primary:hover {
            background: #5568d3;
        }
        
        .btn-success {
            background: #28a745;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #000;
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }
        
        .menu-item-card {
            cursor: pointer;
            height: 100%;
        }
        
        .menu-item-image {
            height: 200px;
            object-fit: cover;
        }
        
        .badge-status {
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        /* Modern Hero Section */
        .hero-section-modern {
            min-height: 80vh;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%),
                        url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1200') center/cover;
            background-attachment: fixed;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 80px 20px;
            margin-bottom: 60px;
            overflow: hidden;
        }
        
        .hero-section-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1200') center/cover;
            filter: blur(20px) brightness(0.7);
            transform: scale(1.1);
            z-index: 0;
        }
        
        .hero-overlay {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
        }
        
        .hero-content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 40px;
            padding: 4rem 3rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
            border: 1px solid rgba(255,255,255,0.3);
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 900;
            color: #1d3557;
            margin-bottom: 1.5rem;
            letter-spacing: -1px;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.1);
            line-height: 1.2;
        }
        
        .hero-tagline {
            font-size: 1.3rem;
            color: #666;
            margin-bottom: 3rem;
            font-weight: 400;
            line-height: 1.6;
        }
        
        .hero-buttons {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .hero-btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1.2rem 2.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            transition: all 0.3s;
            border: none;
        }
        
        .hero-btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.5);
            color: white;
        }
        
        .hero-btn-primary i {
            font-size: 1.3rem;
        }
        
        .hero-btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1.2rem 2.5rem;
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        
        .hero-btn-secondary:hover {
            transform: translateY(-3px);
            background: #667eea;
            color: white;
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.3);
        }
        
        .hero-btn-secondary i {
            font-size: 1.3rem;
        }
        
        @media (max-width: 768px) {
            .hero-content {
                padding: 3rem 2rem;
                border-radius: 30px;
            }
            
            .hero-title {
                font-size: 2.2rem;
            }
            
            .hero-tagline {
                font-size: 1.1rem;
                margin-bottom: 2rem;
            }
            
            .hero-buttons {
                flex-direction: column;
            }
            
            .hero-btn-primary,
            .hero-btn-secondary {
                width: 100%;
                justify-content: center;
            }
        }
        
        .hero-section {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.95) 0%, rgba(118, 75, 162, 0.95) 100%),
                        url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1200') center/cover;
            padding: 120px 0;
            color: white;
            text-align: center;
            border-radius: 0 0 80px 80px;
            margin-bottom: 60px;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>');
            animation: float 20s infinite;
        }
        
        .hero-section h1 {
            font-size: 4rem;
            font-weight: 900;
            text-shadow: 3px 3px 15px rgba(0,0,0,0.4);
            margin-bottom: 1.5rem;
            letter-spacing: -1px;
            position: relative;
            z-index: 1;
        }
        
        .hero-section p {
            font-size: 1.5rem;
            opacity: 0.95;
            font-weight: 300;
            position: relative;
            z-index: 1;
        }
        
        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2.5rem;
            }
            .hero-section p {
                font-size: 1.1rem;
            }
        }
        
        /* Modern Feature Cards */
        .feature-card-modern {
            background: white;
            border-radius: 24px;
            padding: 3rem 2.5rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(102, 126, 234, 0.1);
        }
        
        .feature-card-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            transform: scaleX(0);
            transition: transform 0.4s;
        }
        
        .feature-card-modern:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        
        .feature-card-modern:hover::before {
            transform: scaleX(1);
        }
        
        .feature-icon-wrapper {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .feature-icon-bg {
            width: 100px;
            height: 100px;
            border-radius: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            transition: all 0.4s;
        }
        
        .feature-icon-bg i {
            font-size: 3rem;
            color: white;
        }
        
        .feature-card-modern:hover .feature-icon-bg {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }
        
        .feature-content {
            text-align: center;
        }
        
        .feature-title {
            font-weight: 800;
            font-size: 1.8rem;
            margin-bottom: 1rem;
            color: var(--dark-color);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .feature-description {
            color: #666;
            font-size: 1rem;
            line-height: 1.7;
            margin-bottom: 2rem;
            min-height: 80px;
        }
        
        .feature-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            transition: all 0.3s;
            border: none;
        }
        
        .feature-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(0,0,0,0.2);
            color: white;
        }
        
        .feature-btn i {
            font-size: 1.3rem;
            transition: transform 0.3s;
        }
        
        .feature-btn:hover i {
            transform: translateX(5px);
        }
        
        .feature-card {
            text-align: center;
            padding: 3rem 2rem;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.5s;
        }
        
        .feature-card:hover::before {
            opacity: 1;
        }
        
        .feature-card i {
            font-size: 5rem;
            margin-bottom: 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: inline-block;
            transition: all 0.5s;
            position: relative;
            z-index: 1;
        }
        
        .feature-card:hover i {
            transform: scale(1.2) rotate(5deg);
        }
        
        .feature-card h3 {
            font-weight: 800;
            margin-bottom: 1.5rem;
            color: var(--dark-color);
            font-size: 1.8rem;
            position: relative;
            z-index: 1;
        }
        
        .feature-card p {
            color: #666;
            margin-bottom: 2rem;
            font-size: 1.1rem;
            line-height: 1.8;
            position: relative;
            z-index: 1;
        }
        
        .sidebar {
            min-height: calc(100vh - 56px);
            background: white;
            box-shadow: 2px 0 20px rgba(0,0,0,0.1);
        }
        
        .sidebar-item {
            padding: 15px 25px;
            border-left: 4px solid transparent;
            transition: all 0.3s;
            border-radius: 0 50px 50px 0;
            margin: 5px 0;
        }
        
        .sidebar-item:hover, .sidebar-item.active {
            background: linear-gradient(90deg, #f8f9fa 0%, #e9ecef 100%);
            border-left-color: var(--primary-color);
            transform: translateX(5px);
        }
        
        .table-responsive {
            border-radius: 15px;
            overflow: hidden;
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .input-group {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-radius: 50px;
            overflow: hidden;
        }
        
        .input-group .form-control {
            border: none;
            padding: 15px 25px;
        }
        
        .input-group .btn {
            border-radius: 0;
            padding: 15px 25px;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .price-tag {
            font-size: 2rem;
            font-weight: 900;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
        }
        
        .price-tag::before {
            content: '';
            position: absolute;
            left: -10px;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 80%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
        }
        
        /* Success message animation */
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
        
        .alert {
            border-radius: 6px;
        }
        
        .badge {
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.85rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .dropdown-menu {
            border-radius: 6px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .dropdown-item {
            padding: 0.5rem 1rem;
        }
        
        .dropdown-item:hover {
            background: #f8f9fa;
        }
        
        .fade-in-up {
            /* Remove animation */
        }
        
        .gradient-text {
            color: #667eea;
        }
        
        /* Ripple effect */
        .ripple {
            position: relative;
            overflow: hidden;
        }
        
        .ripple::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .ripple:active::after {
            width: 300px;
            height: 300px;
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-dark navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="bi bi-cup-hot"></i> Nhà Hàng
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item me-3">
                        <a class="nav-link" href="{{ route('menu.index') }}">
                            <i class="bi bi-menu-button-wide"></i> Menu
                        </a>
                    </li>
                    @auth
                        <li class="nav-item me-3">
                            <a class="nav-link" href="{{ route('vouchers.index') }}">
                                <i class="bi bi-ticket-perforated"></i> Voucher
                            </a>
                        </li>
                        <li class="nav-item me-3">
                            <a class="nav-link position-relative" href="{{ route('orders.create') }}">
                                <i class="bi bi-cart3" style="font-size: 1.3rem;"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark" id="cartBadge" style="display: none;">0</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('profile.index') }}"><i class="bi bi-person-circle me-2"></i> Thông tin tài khoản</a></li>
                                <li><a class="dropdown-item" href="{{ route('notifications.index') }}">
                                    <i class="bi bi-bell me-2"></i> Thông báo
                                    @php
                                        $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count();
                                    @endphp
                                    @if($unreadCount > 0)
                                        <span class="badge bg-danger ms-2">{{ $unreadCount }}</span>
                                    @endif
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('favorites.index') }}"><i class="bi bi-heart me-2"></i> Món yêu thích</a></li>
                                <li><a class="dropdown-item" href="{{ route('addresses.index') }}"><i class="bi bi-geo-alt me-2"></i> Địa chỉ</a></li>
                                <li><hr class="dropdown-divider"></li>
                                @if(!Auth::user()->isStaff() && !Auth::user()->isAdmin())
                                <li><a class="dropdown-item" href="{{ route('bookings.index') }}"><i class="bi bi-calendar-check me-2"></i> Đặt bàn</a></li>
                                <li><a class="dropdown-item" href="{{ route('orders.index') }}"><i class="bi bi-receipt me-2"></i> Đơn hàng</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                @if(Auth::user()->isStaff() && !Auth::user()->isAdmin())
                                    <li><a class="dropdown-item" href="{{ route('staff.dashboard') }}">
                                        <i class="bi bi-speedometer2"></i> Dashboard Nhân Viên
                                    </a></li>
                                @endif
                                @if(Auth::user()->isAdmin())
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="bi bi-speedometer2"></i> Dashboard Admin
                                    </a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-box-arrow-right"></i> Đăng xuất
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right"></i> Đăng nhập
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-light btn-sm ms-2" href="{{ route('register') }}" style="border-radius: 50px;">
                                <i class="bi bi-person-plus"></i> Đăng ký
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            @hasSection('sidebar')
                <div class="col-md-2 sidebar p-0">
                    @yield('sidebar')
                </div>
                <div class="col-md-10">
                    @yield('content')
                </div>
            @else
                <div class="col-12">
                    @yield('content')
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
        // CSRF token setup for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Update cart badge
        function updateCartBadge() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const badge = $('#cartBadge');
            if (cart.length > 0) {
                const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
                badge.text(totalItems).show();
            } else {
                badge.hide();
            }
        }
        
        // Update on page load
        $(document).ready(function() {
            updateCartBadge();
            
            // Scroll animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, observerOptions);
            
            document.querySelectorAll('.fade-in-up').forEach(el => {
                observer.observe(el);
            });
        });
    </script>
    @stack('scripts')
</body>
</html>

