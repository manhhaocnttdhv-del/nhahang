@extends('layouts.app')

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Thêm Bàn Mới</h2>
        <a href="{{ route('admin.tables.index') }}" class="btn btn-secondary">Quay lại</a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.tables.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Tên bàn <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Số bàn <span class="text-danger">*</span></label>
                            <input type="text" name="number" class="form-control @error('number') is-invalid @enderror" 
                                   value="{{ old('number') }}" required>
                            @error('number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sức chứa <span class="text-danger">*</span></label>
                            <input type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror" 
                                   value="{{ old('capacity') }}" min="1" max="100" required>
                            @error('capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Khu vực</label>
                            <input type="text" name="area" class="form-control" value="{{ old('area') }}" 
                                   placeholder="Ví dụ: Tầng 1, VIP, Sảnh chính...">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Trống</option>
                                <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Bảo trì</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Thêm Bàn</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

