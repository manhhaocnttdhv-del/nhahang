@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Chỉnh Sửa Món: {{ $menuItem->name }}</h2>
        <a href="{{ route('admin.menu.index') }}" class="btn btn-secondary">Quay lại</a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.menu.update', $menuItem->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $menuItem->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tên món <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $menuItem->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea name="description" class="form-control" rows="3">{{ $menuItem->description }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Hình ảnh</label>
                            @if($menuItem->image)
                                <div class="mb-2">
                                    <p class="text-muted small">Ảnh hiện tại:</p>
                                    <img src="{{ Storage::url($menuItem->image) }}" alt="{{ $menuItem->name }}" 
                                         style="max-width: 300px; max-height: 300px; border-radius: 8px; object-fit: cover; border: 1px solid #ddd;">
                                </div>
                            @endif
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" 
                                   accept="image/*" onchange="previewImage(this, 'imagePreview')">
                            <small class="form-text text-muted">Chấp nhận: JPG, PNG, GIF. Tối đa 2MB. Để trống nếu không muốn thay đổi ảnh.</small>
                            <div class="mt-2">
                                <img id="imagePreview" src="" alt="Preview" style="max-width: 300px; max-height: 300px; display: none; border-radius: 8px; object-fit: cover;">
                            </div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Giá <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" value="{{ $menuItem->price }}" min="0" step="1000" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="available" {{ $menuItem->status == 'available' ? 'selected' : '' }}>Còn món</option>
                                <option value="out_of_stock" {{ $menuItem->status == 'out_of_stock' ? 'selected' : '' }}>Hết món</option>
                            </select>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" value="1" 
                                   {{ $menuItem->is_active ? 'checked' : '' }}>
                            <label class="form-check-label">Hiển thị trên menu</label>
                        </div>

                        <button type="submit" class="btn btn-primary">Cập Nhật</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const file = input.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
}
</script>
@endsection

