@extends('layouts.app')

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-box-arrow-in-down"></i> Nhập/Xuất Nguyên Liệu</h2>
        <a href="{{ route('admin.ingredients.index') }}" class="btn btn-secondary">Quay lại</a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.ingredient-stocks.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Nguyên liệu <span class="text-danger">*</span></label>
                            <select name="ingredient_id" class="form-select @error('ingredient_id') is-invalid @enderror" required id="ingredient_id">
                                <option value="">-- Chọn nguyên liệu --</option>
                                @foreach($ingredients as $ing)
                                    <option value="{{ $ing->id }}" {{ (old('ingredient_id') == $ing->id || ($ingredient && $ingredient->id == $ing->id)) ? 'selected' : '' }}
                                        data-unit="{{ $ing->unit }}"
                                        data-current-stock="{{ $ing->getCurrentStock() }}">
                                        {{ $ing->name }} ({{ $ing->unit }})
                                        @if($ing->code)
                                            - {{ $ing->code }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('ingredient_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($ingredient)
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <strong>Tồn kho hiện tại:</strong> 
                                        <span id="current_stock_display">{{ number_format($ingredient->getCurrentStock(), 2) }} {{ $ingredient->unit }}</span>
                                    </small>
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Loại giao dịch <span class="text-danger">*</span></label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required id="stock_type">
                                <option value="">-- Chọn loại --</option>
                                <option value="import" {{ old('type') == 'import' ? 'selected' : '' }}>Nhập kho</option>
                                <option value="export" {{ old('type') == 'export' ? 'selected' : '' }}>Xuất kho</option>
                                <option value="adjustment" {{ old('type') == 'adjustment' ? 'selected' : '' }}>Điều chỉnh</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <span id="type_hint"></span>
                            </small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Số lượng <span class="text-danger">*</span></label>
                                    <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" 
                                           value="{{ old('quantity') }}" min="0.01" step="0.01" required id="quantity">
                                    <small class="form-text text-muted">
                                        Đơn vị: <span id="unit_display">-</span>
                                    </small>
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Giá/đơn vị</label>
                                    <input type="number" name="unit_price" class="form-control @error('unit_price') is-invalid @enderror" 
                                           value="{{ old('unit_price') }}" min="0" step="0.01" id="unit_price" placeholder="0">
                                    <small class="form-text text-muted">VNĐ (chỉ áp dụng cho nhập kho)</small>
                                    @error('unit_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ngày nhập/xuất <span class="text-danger">*</span></label>
                            <input type="date" name="stock_date" class="form-control @error('stock_date') is-invalid @enderror" 
                                   value="{{ old('stock_date', date('Y-m-d')) }}" required>
                            @error('stock_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" 
                                      rows="3" placeholder="Lý do nhập/xuất, nguồn gốc...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Preview tổng tiền -->
                        <div class="mb-3">
                            <div class="alert alert-info">
                                <strong>Tổng tiền: <span id="total_amount_display">0</span> đ</strong>
                                <small class="d-block text-muted">(Số lượng × Giá/đơn vị)</small>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i> Lưu Phiếu
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Update unit display when ingredient changes
        $('#ingredient_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const unit = selectedOption.data('unit') || '-';
            const currentStock = selectedOption.data('current-stock') || 0;
            
            $('#unit_display').text(unit);
            $('#current_stock_display').text(currentStock.toFixed(2) + ' ' + unit);
        });

        // Update type hint
        $('#stock_type').on('change', function() {
            const type = $(this).val();
            let hint = '';
            switch(type) {
                case 'import':
                    hint = 'Nhập nguyên liệu vào kho (tăng tồn kho)';
                    break;
                case 'export':
                    hint = 'Xuất nguyên liệu ra khỏi kho (giảm tồn kho)';
                    break;
                case 'adjustment':
                    hint = 'Điều chỉnh tồn kho (tăng hoặc giảm)';
                    break;
            }
            $('#type_hint').text(hint);
        });

        // Calculate total amount
        function calculateTotal() {
            const quantity = parseFloat($('#quantity').val() || 0);
            const unitPrice = parseFloat($('#unit_price').val() || 0);
            const total = quantity * unitPrice;
            
            $('#total_amount_display').text(total.toLocaleString('vi-VN'));
        }

        $('#quantity, #unit_price').on('input', calculateTotal);

        // Initialize
        $('#ingredient_id').trigger('change');
        $('#stock_type').trigger('change');
    });
</script>
@endpush
@endsection

