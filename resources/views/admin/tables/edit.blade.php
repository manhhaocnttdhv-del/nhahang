@extends('layouts.app')

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Chỉnh Sửa Bàn: {{ $table->name }}</h2>
        <a href="{{ route('admin.tables.index') }}" class="btn btn-secondary">Quay lại</a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.tables.update', $table->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Tên bàn <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $table->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Số bàn <span class="text-danger">*</span></label>
                            <input type="text" name="number" class="form-control" value="{{ $table->number }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sức chứa <span class="text-danger">*</span></label>
                            <input type="number" name="capacity" class="form-control" value="{{ $table->capacity }}" min="1" max="100" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Khu vực</label>
                            <input type="text" name="area" class="form-control" value="{{ $table->area }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="available" {{ $table->status == 'available' ? 'selected' : '' }}>Trống</option>
                                <option value="reserved" {{ $table->status == 'reserved' ? 'selected' : '' }}>Đã đặt</option>
                                <option value="occupied" {{ $table->status == 'occupied' ? 'selected' : '' }}>Đang dùng</option>
                                <option value="maintenance" {{ $table->status == 'maintenance' ? 'selected' : '' }}>Bảo trì</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Cập Nhật</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

