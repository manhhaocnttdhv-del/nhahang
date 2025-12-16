@extends('layouts.app')

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-box-seam"></i> Sửa Nguyên Liệu</h2>
        <a href="{{ route('admin.ingredients.show', $ingredient->id) }}" class="btn btn-secondary">Quay lại</a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.ingredients.update', $ingredient->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Tên nguyên liệu <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $ingredient->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mã nguyên liệu</label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                   value="{{ old('code', $ingredient->code) }}" placeholder="VD: NL001">
                            <small class="form-text text-muted">Mã duy nhất để quản lý (tùy chọn)</small>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="3" placeholder="Mô tả về nguyên liệu...">{{ old('description', $ingredient->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Đơn vị tính <span class="text-danger">*</span></label>
                                    <select name="unit" class="form-select @error('unit') is-invalid @enderror" required>
                                        <option value="">-- Chọn đơn vị --</option>
                                        <option value="kg" {{ old('unit', $ingredient->unit) == 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                                        <option value="gram" {{ old('unit', $ingredient->unit) == 'gram' ? 'selected' : '' }}>Gram (g)</option>
                                        <option value="lít" {{ old('unit', $ingredient->unit) == 'lít' ? 'selected' : '' }}>Lít (l)</option>
                                        <option value="ml" {{ old('unit', $ingredient->unit) == 'ml' ? 'selected' : '' }}>Mililit (ml)</option>
                                        <option value="cái" {{ old('unit', $ingredient->unit) == 'cái' ? 'selected' : '' }}>Cái</option>
                                        <option value="gói" {{ old('unit', $ingredient->unit) == 'gói' ? 'selected' : '' }}>Gói</option>
                                        <option value="hộp" {{ old('unit', $ingredient->unit) == 'hộp' ? 'selected' : '' }}>Hộp</option>
                                        <option value="chai" {{ old('unit', $ingredient->unit) == 'chai' ? 'selected' : '' }}>Chai</option>
                                        <option value="lon" {{ old('unit', $ingredient->unit) == 'lon' ? 'selected' : '' }}>Lon</option>
                                        <option value="thùng" {{ old('unit', $ingredient->unit) == 'thùng' ? 'selected' : '' }}>Thùng</option>
                                    </select>
                                    @error('unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Giá mua/đơn vị</label>
                                    <input type="number" name="unit_price" class="form-control @error('unit_price') is-invalid @enderror" 
                                           value="{{ old('unit_price', $ingredient->unit_price) }}" min="0" step="0.01" placeholder="0">
                                    <small class="form-text text-muted">Giá mua mỗi đơn vị (VNĐ)</small>
                                    @error('unit_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tồn kho tối thiểu</label>
                                    <input type="number" name="min_stock" class="form-control @error('min_stock') is-invalid @enderror" 
                                           value="{{ old('min_stock', $ingredient->min_stock) }}" min="0" step="0.01" placeholder="0">
                                    <small class="form-text text-muted">Cảnh báo khi tồn kho ≤ giá trị này</small>
                                    @error('min_stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tồn kho tối đa</label>
                                    <input type="number" name="max_stock" class="form-control @error('max_stock') is-invalid @enderror" 
                                           value="{{ old('max_stock', $ingredient->max_stock) }}" min="0" step="0.01" placeholder="0">
                                    <small class="form-text text-muted">Cảnh báo khi tồn kho ≥ giá trị này (0 = không giới hạn)</small>
                                    @error('max_stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="active" {{ old('status', $ingredient->status) == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="inactive" {{ old('status', $ingredient->status) == 'inactive' ? 'selected' : '' }}>Ngừng sử dụng</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <strong>Tồn kho hiện tại:</strong> 
                            <span class="{{ $ingredient->isLowStock() ? 'text-danger' : '' }}">
                                {{ number_format($ingredient->getCurrentStock(), 2) }} {{ $ingredient->unit }}
                            </span>
                            @if($ingredient->isLowStock())
                                <br><small class="text-danger"><i class="bi bi-exclamation-triangle"></i> Tồn kho đang ở mức thấp!</small>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i> Cập Nhật Nguyên Liệu
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

