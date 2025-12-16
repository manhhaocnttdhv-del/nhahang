@extends('layouts.app')

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-clock-history"></i> Lịch Sử Nhập Xuất Nguyên Liệu</h2>
        <a href="{{ route('admin.ingredient-stocks.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tạo Phiếu Nhập/Xuất
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Nguyên liệu</label>
                    <select name="ingredient_id" class="form-select">
                        <option value="">Tất cả</option>
                        @foreach($ingredients as $ing)
                            <option value="{{ $ing->id }}" {{ request('ingredient_id') == $ing->id ? 'selected' : '' }}>
                                {{ $ing->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Loại</label>
                    <select name="type" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="import" {{ request('type') == 'import' ? 'selected' : '' }}>Nhập kho</option>
                        <option value="export" {{ request('type') == 'export' ? 'selected' : '' }}>Xuất kho</option>
                        <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Điều chỉnh</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Từ ngày</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Đến ngày</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Lọc
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Ngày</th>
                            <th>Nguyên liệu</th>
                            <th>Loại</th>
                            <th>Số lượng</th>
                            <th>Đơn giá</th>
                            <th>Thành tiền</th>
                            <th>Người thực hiện</th>
                            <th>Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stocks as $stock)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($stock->stock_date)->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.ingredients.show', $stock->ingredient_id) }}">
                                        {{ $stock->ingredient->name }}
                                    </a>
                                    @if($stock->ingredient->code)
                                        <br><small class="text-muted">{{ $stock->ingredient->code }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($stock->type === 'import')
                                        <span class="badge bg-success">Nhập kho</span>
                                    @elseif($stock->type === 'export')
                                        <span class="badge bg-danger">Xuất kho</span>
                                    @else
                                        <span class="badge bg-warning">Điều chỉnh</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="{{ $stock->type === 'export' ? 'text-danger' : ($stock->type === 'import' ? 'text-success' : 'text-warning') }}">
                                        {{ $stock->type === 'export' ? '-' : ($stock->type === 'import' ? '+' : '±') }}
                                        {{ number_format($stock->quantity, 2) }} {{ $stock->ingredient->unit }}
                                    </span>
                                </td>
                                <td>{{ number_format($stock->unit_price, 0, ',', '.') }} đ</td>
                                <td>
                                    @if($stock->total_amount > 0)
                                        <strong>{{ number_format($stock->total_amount, 0, ',', '.') }} đ</strong>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $stock->createdBy->name ?? 'N/A' }}</td>
                                <td>
                                    <small class="text-muted">{{ Str::limit($stock->notes ?? '-', 50) }}</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">Chưa có lịch sử nhập xuất nào</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($stocks->hasPages())
                <div class="mt-3">
                    {{ $stocks->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

