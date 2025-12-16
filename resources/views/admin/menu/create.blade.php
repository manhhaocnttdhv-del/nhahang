@extends('layouts.app')

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Thêm Món Mới</h2>
        <a href="{{ route('admin.menu.index') }}" class="btn btn-secondary">Quay lại</a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">Chọn danh mục...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tên món <span class="text-danger">*</span></label>
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

                        <div class="mb-3">
                            <label class="form-label">Hình ảnh</label>
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" 
                                   accept="image/*" onchange="previewImage(this, 'imagePreview')">
                            <small class="form-text text-muted">Chấp nhận: JPG, PNG, GIF. Tối đa 2MB</small>
                            <div class="mt-2">
                                <img id="imagePreview" src="" alt="Preview" style="max-width: 300px; max-height: 300px; display: none; border-radius: 8px; object-fit: cover;">
                            </div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Giá <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" 
                                   value="{{ old('price') }}" min="0" step="1000" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Còn món</option>
                                <option value="out_of_stock" {{ old('status') == 'out_of_stock' ? 'selected' : '' }}>Hết món</option>
                            </select>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label">Hiển thị trên menu</label>
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3"><i class="bi bi-box-seam"></i> Nguyên Liệu Cần Thiết</h5>
                        <div class="mb-3">
                            <p class="text-muted small">Chọn các nguyên liệu và số lượng cần thiết để làm món này</p>
                            <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                                @forelse($ingredients as $ingredient)
                                    <div class="row mb-2 align-items-center">
                                        <div class="col-md-6">
                                            <label class="form-check-label">
                                                <input type="checkbox" name="ingredient_check[{{ $ingredient->id }}]" 
                                                       class="form-check-input ingredient-check" 
                                                       value="1" 
                                                       data-ingredient-id="{{ $ingredient->id }}"
                                                       onchange="toggleIngredientInput({{ $ingredient->id }})">
                                                <strong>{{ $ingredient->name }}</strong>
                                                @if($ingredient->code)
                                                    <small class="text-muted">({{ $ingredient->code }})</small>
                                                @endif
                                            </label>
                                            <br>
                                            <small class="text-muted">Đơn vị: {{ $ingredient->unit }}</small>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <input type="number" 
                                                       name="ingredients[{{ $ingredient->id }}]" 
                                                       class="form-control form-control-sm ingredient-quantity" 
                                                       id="ingredient_qty_{{ $ingredient->id }}"
                                                       value="{{ old('ingredients.'.$ingredient->id) }}" 
                                                       min="0.01" 
                                                       step="0.01" 
                                                       placeholder="Số lượng"
                                                       disabled>
                                                <span class="input-group-text">{{ $ingredient->unit }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted text-center">Chưa có nguyên liệu nào. <a href="{{ route('admin.ingredients.create') }}">Thêm nguyên liệu</a></p>
                                @endforelse
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Thêm Món</button>
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

function toggleIngredientInput(ingredientId) {
    const checkbox = document.querySelector(`input[name="ingredient_check[${ingredientId}]"]`);
    const quantityInput = document.getElementById(`ingredient_qty_${ingredientId}`);
    
    if (checkbox.checked) {
        quantityInput.disabled = false;
        quantityInput.focus();
    } else {
        quantityInput.disabled = true;
        quantityInput.value = '';
    }
}

// Enable inputs for checked ingredients on page load
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.ingredient-check').forEach(function(checkbox) {
        if (checkbox.checked) {
            const ingredientId = checkbox.dataset.ingredientId;
            const quantityInput = document.getElementById(`ingredient_qty_${ingredientId}`);
            if (quantityInput) {
                quantityInput.disabled = false;
            }
        }
    });
});
</script>
@endsection

