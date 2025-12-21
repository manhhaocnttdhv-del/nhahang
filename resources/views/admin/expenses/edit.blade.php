@extends('layouts.app')

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-cash-stack"></i> Sửa Chi Phí</h2>
        <a href="{{ route('admin.expenses.index') }}" class="btn btn-secondary">Quay lại</a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.expenses.update', $expense->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Tên chi phí <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $expense->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Loại chi phí</label>
                                    <select name="category" class="form-select @error('category') is-invalid @enderror">
                                        <option value="">-- Chọn loại --</option>
                                        @foreach($categories as $key => $name)
                                            <option value="{{ $key }}" {{ old('category', $expense->category) == $key ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Ngày phát sinh <span class="text-danger">*</span></label>
                                    <input type="date" name="expense_date" class="form-control @error('expense_date') is-invalid @enderror" 
                                           value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required>
                                    @error('expense_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Số tiền <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" 
                                       value="{{ old('amount', $expense->amount) }}" step="0.01" min="0" required>
                                <span class="input-group-text">đ</span>
                            </div>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Phương thức thanh toán</label>
                                    <select name="payment_method" class="form-select">
                                        <option value="">-- Chọn phương thức --</option>
                                        <option value="cash" {{ old('payment_method', $expense->payment_method) == 'cash' ? 'selected' : '' }}>Tiền mặt</option>
                                        <option value="bank_transfer" {{ old('payment_method', $expense->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Chuyển khoản</option>
                                        <option value="bank_card" {{ old('payment_method', $expense->payment_method) == 'bank_card' ? 'selected' : '' }}>Thẻ ngân hàng</option>
                                        <option value="other" {{ old('payment_method', $expense->payment_method) == 'other' ? 'selected' : '' }}>Khác</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Số hóa đơn/chứng từ</label>
                                    <input type="text" name="receipt_number" class="form-control" 
                                           value="{{ old('receipt_number', $expense->receipt_number) }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">File hóa đơn (nếu có)</label>
                            @if($expense->receipt_file)
                                <div class="mb-2">
                                    <a href="{{ asset('storage/' . $expense->receipt_file) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="bi bi-file-earmark"></i> Xem file hiện tại
                                    </a>
                                </div>
                            @endif
                            <input type="file" name="receipt_file" class="form-control @error('receipt_file') is-invalid @enderror" 
                                   accept=".pdf,.jpg,.jpeg,.png">
                            <small class="form-text text-muted">Chấp nhận: PDF, JPG, PNG (tối đa 5MB). Để trống nếu không thay đổi.</small>
                            @error('receipt_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="3">{{ old('description', $expense->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" 
                                      rows="2">{{ old('notes', $expense->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Cập nhật
                            </button>
                            <a href="{{ route('admin.expenses.index') }}" class="btn btn-secondary">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

