@extends('layouts.app')

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <h2 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard Quản Trị</h2>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>Doanh Thu Hôm Nay</h5>
                    <h2>{{ number_format($todayRevenue ?? 0) }} đ</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>Đơn Hàng Hôm Nay</h5>
                    <h2>{{ $todayOrders ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5>Đặt Bàn Hôm Nay</h5>
                    <h2>{{ $todayBookings ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5>Tổng Nhân Viên</h5>
                    <h2>{{ $totalStaff ?? 0 }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Cảnh báo nguyên liệu sắp hết -->
    @if(isset($lowStockIngredients) && $lowStockIngredients->count() > 0)
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <h5 class="alert-heading">
                <i class="bi bi-exclamation-triangle-fill"></i> Cảnh Báo: Nguyên Liệu Sắp Hết
            </h5>
            <p class="mb-2">Có <strong>{{ $lowStockIngredients->count() }}</strong> nguyên liệu đang ở mức tồn kho thấp:</p>
            <div class="row">
                @foreach($lowStockIngredients as $ingredient)
                    <div class="col-md-6 mb-2">
                        <a href="{{ route('admin.ingredients.show', $ingredient->id) }}" class="text-danger text-decoration-none">
                            <i class="bi bi-arrow-right-circle"></i> 
                            <strong>{{ $ingredient->name }}</strong> - 
                            Tồn kho: {{ number_format($ingredient->getCurrentStock(), 2) }} {{ $ingredient->unit }} 
                            (Tối thiểu: {{ $ingredient->min_stock }} {{ $ingredient->unit }})
                        </a>
                    </div>
                @endforeach
            </div>
            <hr>
            <div class="mb-0">
                <a href="{{ route('admin.ingredients.index') }}" class="btn btn-danger btn-sm">
                    <i class="bi bi-box-seam"></i> Xem Tất Cả Nguyên Liệu
                </a>
                <a href="{{ route('admin.ingredient-stocks.create') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-box-arrow-in-down"></i> Nhập Nguyên Liệu
                </a>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Món Bán Chạy (Tháng này)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Món</th>
                                    <th>Số lượng</th>
                                    <th>Doanh thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($popularItems ?? [] as $item)
                                    <tr>
                                        <td>{{ $item->item_name }}</td>
                                        <td>{{ $item->total_quantity }}</td>
                                        <td>{{ number_format($item->total_revenue) }} đ</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Chưa có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thống Kê Tháng</h5>
                </div>
                <div class="card-body">
                    <p><strong>Tổng doanh thu:</strong> {{ number_format($monthRevenue ?? 0) }} đ</p>
                    <p><strong>Tổng đơn hàng:</strong> {{ $monthOrders ?? 0 }}</p>
                    <p><strong>Đơn hàng trung bình:</strong> {{ number_format($avgOrderValue ?? 0) }} đ</p>
                    <p><strong>Tổng đặt bàn:</strong> {{ $monthBookings ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

