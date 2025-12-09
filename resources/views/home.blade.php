@extends('layouts.app')

@section('title', 'Trang Chủ')

@section('content')
<!-- Hero Section -->
<div class="hero-section-modern">
    <div class="hero-overlay">
        <div class="hero-content">
            <h1 class="hero-title">Chào Mừng Đến Nhà Hàng</h1>
            <p class="hero-tagline">Thực đơn phong phú • Dịch vụ tận tâm • Hương vị đậm đà</p>
            <div class="hero-buttons">
                <a href="{{ route('menu.index') }}" class="hero-btn-primary">
                    <i class="bi bi-menu-button-wide"></i>
                    <span>Khám Phá Menu</span>
                </a>
                <a href="{{ route('bookings.create') }}" class="hero-btn-secondary">
                    <i class="bi bi-calendar-check"></i>
                    <span>Đặt Bàn Ngay</span>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Feature Cards -->
    <div class="row mb-5 g-4">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="feature-card-enhanced h-100" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
                <div class="feature-icon-enhanced">
                    <div class="icon-wrapper" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="bi bi-menu-button-wide"></i>
                    </div>
                </div>
                <div class="feature-content-enhanced">
                    <h3 class="feature-title-enhanced">Xem Menu</h3>
                    <p class="feature-description-enhanced">Khám phá các món ăn ngon, đa dạng từ truyền thống đến hiện đại với hơn 100 món đặc sắc</p>
                    <a href="{{ route('menu.index') }}" class="feature-btn-enhanced">
                        <span>Xem Menu</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="feature-card-enhanced h-100" style="background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%);">
                <div class="feature-icon-enhanced">
                    <div class="icon-wrapper" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                </div>
                <div class="feature-content-enhanced">
                    <h3 class="feature-title-enhanced">Đặt Bàn</h3>
                    <p class="feature-description-enhanced">Đặt bàn trước để đảm bảo chỗ ngồi, không gian ưng ý cho bạn và gia đình</p>
                    <a href="{{ route('bookings.create') }}" class="feature-btn-enhanced" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <span>Đặt Bàn</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="feature-card-enhanced h-100" style="background: linear-gradient(135deg, #ffffff 0%, #fffbeb 100%);">
                <div class="feature-icon-enhanced">
                    <div class="icon-wrapper" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <i class="bi bi-cart-check"></i>
                    </div>
                </div>
                <div class="feature-content-enhanced">
                    <h3 class="feature-title-enhanced">Đặt Món Online</h3>
                    <p class="feature-description-enhanced">Đặt món online, giao hàng tận nơi hoặc mang đi tiện lợi chỉ trong vài phút</p>
                    <a href="{{ route('orders.create') }}" class="feature-btn-enhanced" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <span>Đặt Món</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    @auth
    <!-- User Dashboard -->
    <div class="row mb-5">
        <div class="col-12 mb-4">
            <h2 class="text-center mb-4" style="color: var(--dark-color); font-weight: 800; font-size: 2.5rem;">
                <i class="bi bi-person-circle me-2"></i> Khu Vực Của Bạn
            </h2>
        </div>
        <div class="col-md-6 mb-4">
            <div class="dashboard-card h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="dashboard-card-body">
                    <div class="dashboard-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <h3 class="dashboard-title">Lịch Sử Đặt Bàn</h3>
                    <p class="dashboard-description">Xem lại các đặt bàn của bạn và trạng thái xác nhận</p>
                    <a href="{{ route('bookings.index') }}" class="dashboard-btn">
                        <i class="bi bi-clock-history me-2"></i> Xem Lịch Sử
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="dashboard-card h-100" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="dashboard-card-body">
                    <div class="dashboard-icon">
                        <i class="bi bi-receipt-cutoff"></i>
                    </div>
                    <h3 class="dashboard-title">Lịch Sử Đơn Hàng</h3>
                    <p class="dashboard-description">Theo dõi đơn hàng và trạng thái giao hàng của bạn</p>
                    <a href="{{ route('orders.index') }}" class="dashboard-btn">
                        <i class="bi bi-list-check me-2"></i> Xem Lịch Sử
                    </a>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Call to Action -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="cta-card">
                <div class="cta-content">
                    <div class="cta-icon">
                        <i class="bi bi-gift"></i>
                    </div>
                    <h2 class="cta-title">Đăng Ký Ngay Để Nhận Ưu Đãi</h2>
                    <p class="cta-description">Thành viên mới được giảm 10% cho đơn hàng đầu tiên</p>
                    <div class="cta-buttons">
                        <a href="{{ route('register') }}" class="cta-btn-primary">
                            <i class="bi bi-person-plus me-2"></i> Đăng Ký Ngay
                        </a>
                        <a href="{{ route('login') }}" class="cta-btn-secondary">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Đăng Nhập
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endauth
</div>

@push('styles')
<style>
    .feature-card-enhanced {
        background: white;
        border-radius: 24px;
        padding: 2.5rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(102, 126, 234, 0.1);
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .feature-card-enhanced::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        transform: scaleX(0);
        transition: transform 0.4s;
    }
    
    .feature-card-enhanced:hover {
        transform: translateY(-12px);
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    
    .feature-card-enhanced:hover::before {
        transform: scaleX(1);
    }
    
    .feature-icon-enhanced {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .icon-wrapper {
        width: 120px;
        height: 120px;
        border-radius: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        transition: all 0.4s;
        position: relative;
        overflow: hidden;
    }
    
    .icon-wrapper::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);
        opacity: 0;
        transition: opacity 0.4s;
    }
    
    .icon-wrapper i {
        font-size: 3.5rem;
        color: white;
        position: relative;
        z-index: 1;
        transition: transform 0.4s;
    }
    
    .feature-card-enhanced:hover .icon-wrapper {
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 20px 45px rgba(0,0,0,0.3);
    }
    
    .feature-card-enhanced:hover .icon-wrapper::before {
        opacity: 1;
    }
    
    .feature-card-enhanced:hover .icon-wrapper i {
        transform: scale(1.1);
    }
    
    .feature-content-enhanced {
        text-align: center;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    
    .feature-title-enhanced {
        font-weight: 800;
        font-size: 2rem;
        margin-bottom: 1rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1.2;
    }
    
    .feature-description-enhanced {
        color: #64748b;
        font-size: 1.05rem;
        line-height: 1.8;
        margin-bottom: 2rem;
        min-height: 80px;
        flex: 1;
    }
    
    .feature-btn-enhanced {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        padding: 1rem 2.5rem;
        border-radius: 50px;
        color: white;
        font-weight: 700;
        font-size: 1.1rem;
        text-decoration: none;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        transition: all 0.3s;
        border: none;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        width: 100%;
    }
    
    .feature-btn-enhanced:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(102, 126, 234, 0.5);
        color: white;
    }
    
    .feature-btn-enhanced i {
        font-size: 1.2rem;
        transition: transform 0.3s;
    }
    
    .feature-btn-enhanced:hover i {
        transform: translateX(5px);
    }
    
    @media (max-width: 768px) {
        .feature-card-enhanced {
            padding: 2rem 1.5rem;
        }
        
        .icon-wrapper {
            width: 100px;
            height: 100px;
        }
        
        .icon-wrapper i {
            font-size: 2.5rem;
        }
        
        .feature-title-enhanced {
            font-size: 1.6rem;
        }
        
        .feature-description-enhanced {
            font-size: 0.95rem;
        }
    }
    
    /* Dashboard Cards */
    .dashboard-card {
        border-radius: 24px;
        padding: 0;
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        transition: all 0.4s;
        overflow: hidden;
        position: relative;
    }
    
    .dashboard-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at top right, rgba(255,255,255,0.2) 0%, transparent 50%);
        opacity: 0;
        transition: opacity 0.4s;
    }
    
    .dashboard-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 50px rgba(0,0,0,0.25);
    }
    
    .dashboard-card:hover::before {
        opacity: 1;
    }
    
    .dashboard-card-body {
        text-align: center;
        padding: 3rem 2rem;
        color: white;
        position: relative;
        z-index: 1;
    }
    
    .dashboard-icon {
        margin-bottom: 1.5rem;
    }
    
    .dashboard-icon i {
        font-size: 4.5rem;
        opacity: 0.95;
        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
        transition: transform 0.4s;
    }
    
    .dashboard-card:hover .dashboard-icon i {
        transform: scale(1.1) rotate(5deg);
    }
    
    .dashboard-title {
        font-size: 1.8rem;
        font-weight: 800;
        margin-bottom: 1rem;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }
    
    .dashboard-description {
        font-size: 1.05rem;
        opacity: 0.95;
        margin-bottom: 2rem;
        line-height: 1.7;
    }
    
    .dashboard-btn {
        display: inline-flex;
        align-items: center;
        padding: 0.875rem 2rem;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        color: white;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        font-size: 1.05rem;
        transition: all 0.3s;
        border: 2px solid rgba(255,255,255,0.3);
    }
    
    .dashboard-btn:hover {
        background: rgba(255,255,255,0.3);
        border-color: rgba(255,255,255,0.5);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        color: white;
    }
    
    /* CTA Card */
    .cta-card {
        background: linear-gradient(135deg, #1d3557 0%, #2d4a6e 100%);
        border-radius: 30px;
        padding: 4rem 3rem;
        box-shadow: 0 20px 60px rgba(29, 53, 87, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .cta-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%);
        animation: pulse 4s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 0.5; }
        50% { opacity: 1; }
    }
    
    .cta-content {
        text-align: center;
        position: relative;
        z-index: 1;
        color: white;
    }
    
    .cta-icon {
        margin-bottom: 1.5rem;
    }
    
    .cta-icon i {
        font-size: 5rem;
        background: linear-gradient(135deg, #ffd60a 0%, #ffc107 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        filter: drop-shadow(0 4px 8px rgba(255, 214, 10, 0.3));
    }
    
    .cta-title {
        font-size: 2.5rem;
        font-weight: 900;
        margin-bottom: 1rem;
        text-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }
    
    .cta-description {
        font-size: 1.2rem;
        opacity: 0.95;
        margin-bottom: 2.5rem;
        line-height: 1.7;
    }
    
    .cta-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .cta-btn-primary {
        display: inline-flex;
        align-items: center;
        padding: 1rem 2.5rem;
        background: linear-gradient(135deg, #ffd60a 0%, #ffc107 100%);
        color: #000;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 700;
        font-size: 1.1rem;
        transition: all 0.3s;
        box-shadow: 0 10px 30px rgba(255, 214, 10, 0.4);
    }
    
    .cta-btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(255, 214, 10, 0.5);
        color: #000;
    }
    
    .cta-btn-secondary {
        display: inline-flex;
        align-items: center;
        padding: 1rem 2.5rem;
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        color: white;
        border: 2px solid rgba(255,255,255,0.3);
        border-radius: 50px;
        text-decoration: none;
        font-weight: 700;
        font-size: 1.1rem;
        transition: all 0.3s;
    }
    
    .cta-btn-secondary:hover {
        background: rgba(255,255,255,0.2);
        border-color: rgba(255,255,255,0.5);
        transform: translateY(-3px);
        color: white;
    }
    
    @media (max-width: 768px) {
        .dashboard-card-body {
            padding: 2.5rem 1.5rem;
        }
        
        .dashboard-icon i {
            font-size: 3.5rem;
        }
        
        .dashboard-title {
            font-size: 1.5rem;
        }
        
        .cta-card {
            padding: 3rem 2rem;
        }
        
        .cta-title {
            font-size: 2rem;
        }
        
        .cta-description {
            font-size: 1rem;
        }
        
        .cta-buttons {
            flex-direction: column;
        }
        
        .cta-btn-primary,
        .cta-btn-secondary {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush
@endsection

