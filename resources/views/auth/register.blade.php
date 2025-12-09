@extends('layouts.app')

@section('title', 'Đăng Ký')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center align-items-center" style="min-height: 70vh;">
        <div class="col-md-6">
            <div class="text-center mb-4 fade-in-up">
                <div class="float-animation">
                    <h1 class="display-5 mb-3 gradient-text" style="font-weight: 900;">
                        <i class="bi bi-person-plus"></i> Đăng Ký
                    </h1>
                    <p class="text-muted" style="font-size: 1.1rem;">Tạo tài khoản để nhận nhiều ưu đãi hấp dẫn</p>
                </div>
            </div>
            <div class="card shadow-lg fade-in-up" style="animation-delay: 0.2s;">
                <div class="card-body p-5">
                    <form action="{{ route('register') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold">
                                <i class="bi bi-person me-2"></i>Họ và tên
                            </label>
                            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" 
                                   placeholder="Nhập họ và tên" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label fw-bold">
                                <i class="bi bi-envelope me-2"></i>Email
                            </label>
                            <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" 
                                   placeholder="Nhập email của bạn" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="phone" class="form-label fw-bold">
                                <i class="bi bi-telephone me-2"></i>Số điện thoại
                            </label>
                            <input type="text" class="form-control form-control-lg @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone') }}" 
                                   placeholder="Nhập số điện thoại">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-bold">
                                <i class="bi bi-lock me-2"></i>Mật khẩu
                            </label>
                            <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                   id="password" name="password" placeholder="Nhập mật khẩu (tối thiểu 8 ký tự)" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-bold">
                                <i class="bi bi-lock-fill me-2"></i>Xác nhận mật khẩu
                            </label>
                            <input type="password" class="form-control form-control-lg" 
                                   id="password_confirmation" name="password_confirmation" 
                                   placeholder="Nhập lại mật khẩu" required>
                        </div>

                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-success btn-lg py-3 ripple" style="font-weight: 700;">
                                <i class="bi bi-person-plus me-2"></i>Đăng Ký Ngay
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">
                    <div class="text-center">
                        <p class="mb-0">Đã có tài khoản? 
                            <a href="{{ route('login') }}" class="text-decoration-none fw-bold" style="color: var(--primary-color);">
                                Đăng nhập ngay <i class="bi bi-arrow-right"></i>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

