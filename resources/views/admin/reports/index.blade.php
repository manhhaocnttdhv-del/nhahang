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

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <h5><i class="bi bi-cash-coin me-2"></i> Doanh Thu</h5>
                    <h2 class="mb-0">{{ number_format($revenue) }} đ</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <h5><i class="bi bi-receipt me-2"></i> Số Đơn Hàng</h5>
                    <h2 class="mb-0">{{ $orderCount }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <h5><i class="bi bi-people me-2"></i> Khách Hàng</h5>
                    <h2 class="mb-0">{{ $customerStats->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body">
                    <h5><i class="bi bi-table me-2"></i> Bàn Đã Sử Dụng</h5>
                    <h2 class="mb-0">{{ $tableStats->where('order_count', '>', 0)->count() }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Profit Analysis (Chỉ hiển thị khi period là month) -->
    @if($period === 'month' && isset($profitData))
    <div class="card mb-4 border-success">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-graph-up-arrow me-2"></i> Phân Tích Lợi Nhuận Tháng Này</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="p-3 bg-light rounded">
                        <h6 class="text-muted mb-2"><i class="bi bi-cash-coin me-2"></i> Tổng Doanh Thu</h6>
                        <h4 class="mb-0 text-primary">{{ number_format($profitData['revenue']) }} đ</h4>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="p-3 bg-light rounded">
                        <h6 class="text-muted mb-2"><i class="bi bi-box-seam me-2"></i> Chi Phí Nguyên Vật Liệu</h6>
                        <h4 class="mb-0 text-danger">{{ number_format($profitData['ingredient_cost']) }} đ</h4>
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="p-3 bg-light rounded">
                        <h6 class="text-muted mb-2"><i class="bi bi-person-workspace me-2"></i> Chi Phí Nhân Viên</h6>
                        <h4 class="mb-0 text-warning">{{ number_format($profitData['salary_cost']) }} đ</h4>
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="p-3 bg-light rounded">
                        <h6 class="text-muted mb-2">
                            <i class="bi bi-receipt-cutoff me-2"></i> Chi Phí Khác
                            <button type="button" class="btn btn-sm btn-link p-0 ms-2" data-bs-toggle="modal" data-bs-target="#editOtherCostsModal" title="Chỉnh sửa">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </h6>
                        <h4 class="mb-0 text-info">{{ number_format($profitData['other_costs']) }} đ</h4>
                        <small class="text-muted">(Tự nhập)</small>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="p-3 bg-success text-white rounded">
                        <h6 class="mb-2"><i class="bi bi-trophy me-2"></i> Lợi Nhuận</h6>
                        <h4 class="mb-0">{{ number_format($profitData['profit']) }} đ</h4>
                        <small class="opacity-75">Tỷ suất: {{ number_format($profitData['profit_margin'], 2) }}%</small>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <h6 class="mb-3">Chi Tiết Chi Phí:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Khoản Mục</th>
                                    <th class="text-end">Số Tiền</th>
                                    <th class="text-end">Tỷ Lệ %</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Tổng Doanh Thu</strong></td>
                                    <td class="text-end"><strong>{{ number_format($profitData['revenue']) }} đ</strong></td>
                                    <td class="text-end"><strong>100%</strong></td>
                                </tr>
                                <tr>
                                    <td>Chi Phí Nguyên Vật Liệu</td>
                                    <td class="text-end text-danger">- {{ number_format($profitData['ingredient_cost']) }} đ</td>
                                    <td class="text-end text-danger">
                                        {{ $profitData['revenue'] > 0 ? number_format(($profitData['ingredient_cost'] / $profitData['revenue']) * 100, 2) : 0 }}%
                                    </td>
                                </tr>
                                <tr>
                                    <td>Chi Phí Nhân Viên</td>
                                    <td class="text-end text-warning">- {{ number_format($profitData['salary_cost']) }} đ</td>
                                    <td class="text-end text-warning">
                                        {{ $profitData['revenue'] > 0 ? number_format(($profitData['salary_cost'] / $profitData['revenue']) * 100, 2) : 0 }}%
                                    </td>
                                </tr>
                                <tr>
                                    <td>Chi Phí Khác</td>
                                    <td class="text-end text-info">- {{ number_format($profitData['other_costs']) }} đ</td>
                                    <td class="text-end text-info">
                                        {{ $profitData['revenue'] > 0 ? number_format(($profitData['other_costs'] / $profitData['revenue']) * 100, 2) : 0 }}%
                                    </td>
                                </tr>
                                <tr class="table-success">
                                    <td><strong>Lợi Nhuận Ròng</strong></td>
                                    <td class="text-end"><strong>{{ number_format($profitData['profit']) }} đ</strong></td>
                                    <td class="text-end"><strong>{{ number_format($profitData['profit_margin'], 2) }}%</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal để nhập chi phí khác -->
    @if($period === 'month')
    <div class="modal fade" id="editOtherCostsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nhập Chi Phí Khác ({{ now()->format('m/Y') }})</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="GET" action="{{ route('admin.reports.index') }}">
                    <input type="hidden" name="period" value="month">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tổng chi phí khác (đ)</label>
                            <div class="input-group">
                                <input type="number" name="other_costs" class="form-control" 
                                       value="{{ request('other_costs', $profitData['other_costs'] ?? 0) }}" 
                                       step="0.01" min="0" placeholder="Nhập số tiền">
                                <span class="input-group-text">đ</span>
                            </div>
                            <small class="form-text text-muted">
                                Nhập tổng các chi phí khác như: thuê mặt bằng, điện nước, marketing, v.v.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Daily Revenue Chart -->
    @if(!empty($dailyRevenue) && count($dailyRevenue) > 0)
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i> Biểu Đồ Doanh Thu Theo Ngày</h5>
        </div>
        <div class="card-body">
            <canvas id="revenueChart" height="80"></canvas>
        </div>
    </div>
    @endif

    <div class="row">
        <!-- Revenue by Payment Method -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i> Doanh Thu Theo Phương Thức Thanh Toán</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Phương thức</th>
                                    <th class="text-end">Doanh thu</th>
                                    <th class="text-end">Tỷ lệ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($revenueByPaymentMethod as $method)
                                    @php
                                        $methodName = [
                                            'cash' => 'Tiền mặt',
                                            'bank_transfer' => 'Chuyển khoản',
                                            'momo' => 'MoMo',
                                            'vnpay' => 'VNPay',
                                            'bank_card' => 'Thẻ ngân hàng'
                                        ][$method->payment_method] ?? $method->payment_method;
                                        $percentage = $revenue > 0 ? ($method->total / $revenue * 100) : 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $methodName }}</td>
                                        <td class="text-end">{{ number_format($method->total) }} đ</td>
                                        <td class="text-end">
                                            <span class="badge bg-primary">{{ number_format($percentage, 1) }}%</span>
                                        </td>
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

        <!-- Revenue by Order Type -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-bag me-2"></i> Doanh Thu Theo Loại Đơn</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Loại đơn</th>
                                    <th class="text-end">Doanh thu</th>
                                    <th class="text-end">Tỷ lệ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($revenueByOrderType as $type)
                                    @php
                                        $typeName = [
                                            'dine_in' => 'Tại chỗ',
                                            'takeaway' => 'Mang đi',
                                            'delivery' => 'Giao hàng'
                                        ][$type->order_type] ?? $type->order_type;
                                        $percentage = $revenue > 0 ? ($type->total / $revenue * 100) : 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $typeName }}</td>
                                        <td class="text-end">{{ number_format($type->total) }} đ</td>
                                        <td class="text-end">
                                            <span class="badge bg-success">{{ number_format($percentage, 1) }}%</span>
                                        </td>
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
    </div>

    <div class="row">
        <!-- Top Customers -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-star me-2"></i> Top 10 Khách Hàng</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Khách hàng</th>
                                    <th class="text-center">Số đơn</th>
                                    <th class="text-end">Tổng chi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customerStats as $customer)
                                    <tr>
                                        <td>{{ $customer->user->name ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $customer->order_count }}</td>
                                        <td class="text-end">{{ number_format($customer->total_spent) }} đ</td>
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

        <!-- Table Statistics -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-table me-2"></i> Thống Kê Bàn</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Bàn</th>
                                    <th class="text-center">Số đơn</th>
                                    <th class="text-end">Doanh thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tableStats->where('order_count', '>', 0)->take(10) as $table)
                                    <tr>
                                        <td>{{ $table->name }}</td>
                                        <td class="text-center">{{ $table->order_count }}</td>
                                        <td class="text-end">{{ number_format($table->revenue ?? 0) }} đ</td>
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
    </div>

    <!-- Popular Items -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-fire me-2"></i> Top 10 Món Bán Chạy</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Món</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-end">Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($popularItems as $item)
                            <tr>
                                <td><strong>{{ $item->item_name }}</strong></td>
                                <td class="text-center">{{ $item->total_quantity }}</td>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    @if(!empty($dailyRevenue) && count($dailyRevenue) > 0)
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueData = @json($dailyRevenue);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: revenueData.map(item => {
                const date = new Date(item.date);
                return date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' });
            }),
            datasets: [{
                label: 'Doanh Thu (đ)',
                data: revenueData.map(item => item.revenue),
                borderColor: 'rgb(102, 126, 234)',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' đ';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
                        }
                    }
                }
            }
        }
    });
    @endif
</script>
@endpush
@endsection
