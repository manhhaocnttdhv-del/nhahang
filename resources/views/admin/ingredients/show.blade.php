@extends('layouts.app')

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-box-seam"></i> Chi Tiết Nguyên Liệu</h2>
        <div>
            <a href="{{ route('admin.ingredients.edit', $ingredient->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Sửa
            </a>
            <a href="{{ route('admin.ingredient-stocks.create', ['ingredient_id' => $ingredient->id]) }}" class="btn btn-success">
                <i class="bi bi-box-arrow-in-down"></i> Nhập/Xuất
            </a>
            <a href="{{ route('admin.ingredients.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Thông Tin Nguyên Liệu</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Tên nguyên liệu:</strong> {{ $ingredient->name }}</p>
                            @if($ingredient->code)
                                <p><strong>Mã nguyên liệu:</strong> <code>{{ $ingredient->code }}</code></p>
                            @endif
                            <p><strong>Đơn vị tính:</strong> {{ $ingredient->unit }}</p>
                            <p><strong>Giá mua/đơn vị:</strong> {{ number_format($ingredient->unit_price, 0, ',', '.') }} đ/{{ $ingredient->unit }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Trạng thái:</strong> 
                                @if($ingredient->status === 'active')
                                    <span class="badge bg-success">Hoạt động</span>
                                @else
                                    <span class="badge bg-secondary">Ngừng sử dụng</span>
                                @endif
                            </p>
                            <p><strong>Tồn kho tối thiểu:</strong> {{ $ingredient->min_stock }} {{ $ingredient->unit }}</p>
                            <p><strong>Tồn kho tối đa:</strong> {{ $ingredient->max_stock > 0 ? $ingredient->max_stock : 'Không giới hạn' }} {{ $ingredient->unit }}</p>
                            <p><strong>Tồn kho hiện tại:</strong> 
                                <span class="badge {{ $ingredient->isLowStock() ? 'bg-danger' : ($ingredient->isOverStock() ? 'bg-warning' : 'bg-success') }}">
                                    {{ number_format($ingredient->getCurrentStock(), 2) }} {{ $ingredient->unit }}
                                </span>
                            </p>
                        </div>
                    </div>
                    @if($ingredient->description)
                        <hr>
                        <p><strong>Mô tả:</strong></p>
                        <p class="text-muted">{{ $ingredient->description }}</p>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Lịch Sử Nhập Xuất</h5>
                    <a href="{{ route('admin.ingredient-stocks.index', ['ingredient_id' => $ingredient->id]) }}" class="btn btn-sm btn-light">
                        Xem tất cả
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Ngày</th>
                                    <th>Loại</th>
                                    <th>Số lượng</th>
                                    <th>Đơn giá</th>
                                    <th>Thành tiền</th>
                                    <th>Người thực hiện</th>
                                    <th>Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ingredient->stocks()->orderBy('stock_date', 'desc')->orderBy('created_at', 'desc')->limit(10)->get() as $stock)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($stock->stock_date)->format('d/m/Y') }}</td>
                                        <td>
                                            @if($stock->type === 'import')
                                                <span class="badge bg-success">Nhập kho</span>
                                            @elseif($stock->type === 'export')
                                                <span class="badge bg-danger">Xuất kho</span>
                                            @else
                                                <span class="badge bg-warning">Điều chỉnh</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($stock->quantity, 2) }} {{ $ingredient->unit }}</td>
                                        <td>{{ number_format($stock->unit_price, 0, ',', '.') }} đ</td>
                                        <td>{{ number_format($stock->total_amount, 0, ',', '.') }} đ</td>
                                        <td>{{ $stock->createdBy->name ?? 'N/A' }}</td>
                                        <td>
                                            <small class="text-muted">{{ Str::limit($stock->notes ?? '-', 50) }}</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Chưa có lịch sử nhập xuất</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-list-ul"></i> Món Ăn Sử Dụng Nguyên Liệu Này</h5>
                </div>
                <div class="card-body">
                    @if($ingredient->menuItems->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Món ăn</th>
                                        <th>Số lượng/1 phần</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ingredient->menuItems as $menuItem)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.menu.edit', $menuItem->id) }}">{{ $menuItem->name }}</a>
                                            </td>
                                            <td>{{ number_format($menuItem->pivot->quantity, 2) }} {{ $ingredient->unit }}</td>
                                            <td>
                                                @if($menuItem->is_active && $menuItem->status === 'available')
                                                    <span class="badge bg-success">Hoạt động</span>
                                                @else
                                                    <span class="badge bg-secondary">Ngừng bán</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">Chưa có món ăn nào sử dụng nguyên liệu này</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Cảnh Báo Tồn Kho</h5>
                </div>
                <div class="card-body">
                    @if($ingredient->isLowStock())
                        <div class="alert alert-danger mb-0">
                            <i class="bi bi-exclamation-circle"></i> 
                            <strong>Tồn kho thấp!</strong><br>
                            Hiện tại: {{ number_format($ingredient->getCurrentStock(), 2) }} {{ $ingredient->unit }}<br>
                            Tối thiểu: {{ $ingredient->min_stock }} {{ $ingredient->unit }}
                        </div>
                    @elseif($ingredient->isOverStock())
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-info-circle"></i> 
                            <strong>Tồn kho cao!</strong><br>
                            Hiện tại: {{ number_format($ingredient->getCurrentStock(), 2) }} {{ $ingredient->unit }}<br>
                            Tối đa: {{ $ingredient->max_stock }} {{ $ingredient->unit }}
                        </div>
                    @else
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle"></i> 
                            <strong>Tồn kho bình thường</strong><br>
                            Hiện tại: {{ number_format($ingredient->getCurrentStock(), 2) }} {{ $ingredient->unit }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Thông Tin Khác</h5>
                </div>
                <div class="card-body">
                    <p><strong>Ngày tạo:</strong> {{ $ingredient->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Ngày cập nhật:</strong> {{ $ingredient->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

