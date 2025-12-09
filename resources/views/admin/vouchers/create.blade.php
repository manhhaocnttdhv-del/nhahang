@extends('layouts.app')

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Thêm Voucher Mới</h2>
        <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">Quay lại</a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.vouchers.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Mã Voucher <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                   value="{{ old('code') }}" placeholder="VD: GIAM20" required>
                            <small class="form-text text-muted">Mã voucher phải là duy nhất, không có khoảng trắng</small>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tên Voucher <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Loại <span class="text-danger">*</span></label>
                                    <select name="type" class="form-select @error('type') is-invalid @enderror" id="voucherType" required>
                                        <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Giảm giá cố định (VNĐ)</option>
                                        <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Giảm giá phần trăm (%)</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Giá trị <span class="text-danger">*</span></label>
                                    <input type="number" name="value" class="form-control @error('value') is-invalid @enderror" 
                                           value="{{ old('value') }}" min="0" step="0.01" required>
                                    <small class="form-text text-muted" id="valueHint">Nhập số tiền giảm (VNĐ)</small>
                                    @error('value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Đơn hàng tối thiểu</label>
                                    <input type="number" name="min_order_amount" class="form-control @error('min_order_amount') is-invalid @enderror" 
                                           value="{{ old('min_order_amount') }}" min="0" step="1000">
                                    <small class="form-text text-muted">Để trống nếu không có yêu cầu</small>
                                    @error('min_order_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Giảm tối đa</label>
                                    <input type="number" name="max_discount" class="form-control @error('max_discount') is-invalid @enderror" 
                                           value="{{ old('max_discount') }}" min="0" step="1000">
                                    <small class="form-text text-muted">Chỉ áp dụng cho loại phần trăm</small>
                                    @error('max_discount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" 
                                           value="{{ old('start_date', date('Y-m-d')) }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" 
                                           value="{{ old('end_date') }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Giới hạn sử dụng</label>
                            <input type="number" name="usage_limit" class="form-control @error('usage_limit') is-invalid @enderror" 
                                   value="{{ old('usage_limit') }}" min="1">
                            <small class="form-text text-muted">Để trống nếu không giới hạn số lần sử dụng</small>
                            @error('usage_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label">Kích hoạt voucher</label>
                        </div>

                        <button type="submit" class="btn btn-primary">Thêm Voucher</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('voucherType').addEventListener('change', function() {
    const hint = document.getElementById('valueHint');
    if (this.value === 'percentage') {
        hint.textContent = 'Nhập phần trăm giảm (0-100)';
    } else {
        hint.textContent = 'Nhập số tiền giảm (VNĐ)';
    }
});
</script>
@endsection

