@extends('layouts.app')

@section('title', 'Thông Tin Tài Khoản')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="container my-5">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 mb-3" style="font-weight: 700; color: #667eea;">
                <i class="bi bi-person-circle"></i> Thông Tin Tài Khoản
            </h1>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Profile Info -->
            <div class="card mb-4">
                <div class="card-header" style="background: #667eea; color: white;">
                    <h5 class="mb-0"><i class="bi bi-person me-2"></i> Thông Tin Cá Nhân</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <img src="{{ $user->avatar ? Storage::url($user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=150&background=667eea&color=fff' }}" 
                                     alt="Avatar" 
                                     class="rounded-circle" 
                                     style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #667eea;">
                                <label for="avatar" class="position-absolute bottom-0 end-0 btn btn-primary btn-sm rounded-circle" style="width: 40px; height: 40px; cursor: pointer;">
                                    <i class="bi bi-camera"></i>
                                </label>
                                <input type="file" name="avatar" id="avatar" class="d-none" accept="image/*" onchange="previewImage(this)">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Họ và tên</label>
                            <input type="text" class="form-control form-control-lg" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control form-control-lg" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Số điện thoại</label>
                            <input type="text" class="form-control form-control-lg" name="phone" value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-check-circle me-2"></i> Cập Nhật Thông Tin
                        </button>
                    </form>
                </div>
            </div>

            <!-- Change Password -->
            <div class="card">
                <div class="card-header" style="background: #f8f9fa;">
                    <h5 class="mb-0"><i class="bi bi-key me-2"></i> Đổi Mật Khẩu</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update-password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mật khẩu hiện tại</label>
                            <input type="password" class="form-control form-control-lg" name="current_password" required>
                            @error('current_password')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Mật khẩu mới</label>
                            <input type="password" class="form-control form-control-lg" name="password" required minlength="8">
                            @error('password')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Xác nhận mật khẩu mới</label>
                            <input type="password" class="form-control form-control-lg" name="password_confirmation" required minlength="8">
                        </div>

                        <button type="submit" class="btn btn-warning btn-lg w-100">
                            <i class="bi bi-shield-lock me-2"></i> Đổi Mật Khẩu
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = input.closest('.position-relative').querySelector('img');
            img.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection

