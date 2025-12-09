@extends('layouts.app')

@section('title', 'Đăng Nhập')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center align-items-center" style="min-height: 70vh;">
        <div class="col-md-5">
            <div class="text-center mb-4 fade-in-up">
                <div class="float-animation">
                    <h1 class="display-5 mb-3 gradient-text" style="font-weight: 900;">
                        <i class="bi bi-box-arrow-in-right"></i> Đăng Nhập
                    </h1>
                    <p class="text-muted" style="font-size: 1.1rem;">Chào mừng bạn trở lại!</p>
                </div>
            </div>
            <div class="card shadow-lg fade-in-up" style="animation-delay: 0.2s;">
                <div class="card-body p-5">
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="email" class="form-label fw-bold">
                                <i class="bi bi-envelope me-2"></i>Email
                            </label>
                            <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" 
                                   placeholder="Nhập email của bạn" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-bold">
                                <i class="bi bi-lock me-2"></i>Mật khẩu
                            </label>
                            <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                   id="password" name="password" placeholder="Nhập mật khẩu" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                        </div>

                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-lg py-3 ripple" style="font-weight: 700;">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Đăng Nhập
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">
                    <div class="text-center">
                        <p class="mb-0">Chưa có tài khoản? 
                            <a href="{{ route('register') }}" class="text-decoration-none fw-bold" style="color: var(--primary-color);">
                                Đăng ký ngay <i class="bi bi-arrow-right"></i>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

