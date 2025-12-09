@extends('layouts.app')

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-graph-up"></i> Báo Cáo & Thống Kê</h2>
        <div class="btn-group">
            <a href="?period=today" class="btn btn-sm {{ $period == 'today' ? 'btn-primary' : 'btn-outline-primary' }}">Hôm nay</a>
            <a href="?period=week" class="btn btn-sm {{ $period == 'week' ? 'btn-primary' : 'btn-outline-primary' }}">Tuần này</a>
            <a href="?period=month" class="btn btn-sm {{ $period == 'month' ? 'btn-primary' : 'btn-outline-primary' }}">Tháng này</a>
            <a href="?period=year" class="btn btn-sm {{ $period == 'year' ? 'btn-primary' : 'btn-outline-primary' }}">Năm nay</a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>Doanh Thu</h5>
                    <h2>{{ number_format($revenue) }} đ</h2>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>Số Đơn Hàng</h5>
                    <h2>{{ $orderCount }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Món Bán Chạy</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Món</th>
                            <th>Số lượng</th>
                            <th class="text-end">Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($popularItems as $item)
                            <tr>
                                <td><strong>{{ $item->item_name }}</strong></td>
                                <td>{{ $item->total_quantity }}</td>
                                <td class="text-end">{{ number_format($item->total_revenue) }} đ</td>
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
@endsection

