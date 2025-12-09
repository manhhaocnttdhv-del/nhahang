@extends('layouts.app')

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Chỉnh Sửa Nhân Viên: {{ $staff->name }}</h2>
        <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary">Quay lại</a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông Tin</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.staff.update', $staff->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $staff->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ $staff->email }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="staff" {{ $staff->role == 'staff' ? 'selected' : '' }}>Nhân viên phục vụ</option>
                                <option value="cashier" {{ $staff->role == 'cashier' ? 'selected' : '' }}>Thu ngân</option>
                                <option value="kitchen_manager" {{ $staff->role == 'kitchen_manager' ? 'selected' : '' }}>Quản lý bếp</option>
                                <option value="admin" {{ $staff->role == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" value="{{ $staff->phone }}">
                        </div>

                        <button type="submit" class="btn btn-primary">Cập Nhật</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Đặt Lại Mật Khẩu</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.staff.reset-password', $staff->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu mới <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" min="8" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" min="8" required>
                        </div>
                        <button type="submit" class="btn btn-warning">Đặt Lại Mật Khẩu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

