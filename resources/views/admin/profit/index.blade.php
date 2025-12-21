@extends('layouts.app')

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-calculator"></i> Tính Lợi Nhuận</h2>
    </div>

    <!-- Chọn tháng -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.profit.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Chọn tháng</label>
                    <input type="month" name="month" class="form-control" value="{{ $data['month'] }}" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Xem
                    </button>
                </div>
                <div class="col-md-7">
                    <div class="alert alert-info mb-0 py-2">
                        <small><i class="bi bi-info-circle"></i> <strong>Lưu ý:</strong> Hệ thống tính lợi nhuận từ đầu tháng đến ngày hiện tại (nếu tháng đang chọn là tháng hiện tại). Ví dụ: Hôm nay 21/12, hệ thống tính từ 01/12 đến 20/12.</small>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tóm tắt lợi nhuận -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <h5><i class="bi bi-cash-coin me-2"></i> Doanh Thu</h5>
                    <h2 class="mb-0">{{ number_format($data['revenue']) }} đ</h2>
                    <small>{{ $data['period_label'] }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <h5><i class="bi bi-box-seam me-2"></i> Chi Phí NVL</h5>
                    <h2 class="mb-0">{{ number_format($data['ingredient_cost']) }} đ</h2>
                    <small>{{ $data['ingredient_cost_percent'] }}% doanh thu</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <h5><i class="bi bi-person-workspace me-2"></i> Chi Phí Nhân Viên</h5>
                    <h2 class="mb-0">{{ number_format($data['salary_cost']) }} đ</h2>
                    <small>{{ $data['salary_cost_percent'] }}% doanh thu</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <h5><i class="bi bi-trophy me-2"></i> Lợi Nhuận</h5>
                    <h2 class="mb-0">{{ number_format($data['profit']) }} đ</h2>
                    <small>Tỷ suất: {{ $data['profit_margin'] }}%</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Chi tiết tính toán -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-list-check me-2"></i> Chi Tiết Tính Toán</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="50%">Khoản Mục</th>
                                    <th class="text-end" width="25%">Số Tiền</th>
                                    <th class="text-end" width="25%">Tỷ Lệ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Tổng Doanh Thu</strong></td>
                                    <td class="text-end"><strong class="text-primary">{{ number_format($data['revenue']) }} đ</strong></td>
                                    <td class="text-end"><strong>100%</strong></td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="bi bi-dash-circle text-danger me-2"></i>
                                        Chi Phí Nguyên Vật Liệu
                                    </td>
                                    <td class="text-end text-danger">- {{ number_format($data['ingredient_cost']) }} đ</td>
                                    <td class="text-end text-danger">{{ $data['ingredient_cost_percent'] }}%</td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="bi bi-dash-circle text-warning me-2"></i>
                                        Chi Phí Nhân Viên
                                    </td>
                                    <td class="text-end text-warning">- {{ number_format($data['salary_cost']) }} đ</td>
                                    <td class="text-end text-warning">{{ $data['salary_cost_percent'] }}%</td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="bi bi-dash-circle text-info me-2"></i>
                                        Chi Phí Khác
                                        <button type="button" class="btn btn-sm btn-link p-0 ms-2" data-bs-toggle="modal" data-bs-target="#editOtherCostsModal" title="Chỉnh sửa">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </td>
                                    <td class="text-end text-info">- {{ number_format($data['other_costs']) }} đ</td>
                                    <td class="text-end text-info">{{ $data['other_costs_percent'] }}%</td>
                                </tr>
                                <tr class="table-success">
                                    <td><strong><i class="bi bi-equals me-2"></i>Lợi Nhuận Ròng</strong></td>
                                    <td class="text-end"><strong class="text-success">{{ number_format($data['profit']) }} đ</strong></td>
                                    <td class="text-end"><strong>{{ $data['profit_margin'] }}%</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Chi tiết chi phí NVL -->
                    @if($data['ingredient_details']->count() > 0)
                    <div class="mt-4">
                        <h6 class="mb-3"><i class="bi bi-box-seam text-danger me-2"></i>Chi tiết Chi Phí Nguyên Vật Liệu:</h6>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>Nguyên liệu</th>
                                        <th class="text-end">Số lượng nhập</th>
                                        <th class="text-end">Tổng chi phí</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data['ingredient_details'] as $detail)
                                        <tr>
                                            <td>{{ $detail->ingredient->name ?? 'N/A' }}</td>
                                            <td class="text-end">{{ number_format($detail->total_quantity, 2) }} {{ $detail->ingredient->unit ?? '' }}</td>
                                            <td class="text-end text-danger">{{ number_format($detail->total_amount) }} đ</td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-danger">
                                        <td><strong>Tổng cộng</strong></td>
                                        <td class="text-end">-</td>
                                        <td class="text-end"><strong>{{ number_format($data['ingredient_cost']) }} đ</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Chi tiết chi phí nhân viên -->
                    @if(isset($data['salary_details']) && count($data['salary_details']) > 0)
                    <div class="mt-4">
                        <h6 class="mb-3"><i class="bi bi-person-workspace text-warning me-2"></i>Chi tiết Chi Phí Nhân Viên:</h6>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>Nhân viên</th>
                                        <th>Loại</th>
                                        <th class="text-end">Ngày làm</th>
                                        <th class="text-end">Giờ làm</th>
                                        <th class="text-end">Chi phí</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data['salary_details'] as $detail)
                                        <tr>
                                            <td>{{ $detail['user']->name }}</td>
                                            <td>
                                                @if($detail['employment_type'] === 'full_time')
                                                    <span class="badge bg-primary">Full-time</span>
                                                @else
                                                    <span class="badge bg-info">Part-time</span>
                                                @endif
                                            </td>
                                            <td class="text-end">{{ $detail['working_days'] }} ngày</td>
                                            <td class="text-end">{{ number_format($detail['total_working_hours'], 1) }}h</td>
                                            <td class="text-end text-warning"><strong>{{ number_format($detail['cost']) }} đ</strong></td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-warning">
                                        <td colspan="4"><strong>Tổng cộng</strong></td>
                                        <td class="text-end"><strong>{{ number_format($data['salary_cost']) }} đ</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @else
                    <div class="mt-4">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Chưa có chi phí nhân viên được tính</strong><br>
                            <small class="mb-0">
                                <strong>Nguyên nhân có thể:</strong><br>
                                • Không có điểm danh (Attendance) trong kỳ {{ $data['period_label'] }}<br>
                                • Nhân viên chưa được cấu hình lương (base_salary cho full-time hoặc hourly_rate cho part-time)<br>
                                • Nhân viên chưa được set employment_type (full_time/part_time)
                            </small>
                        </div>
                        <div class="alert alert-info mt-2">
                            <small>
                                <strong>Lưu ý:</strong> Để tính lương, nhân viên cần:<br>
                                1. Có điểm danh trong kỳ với status = 'present', 'late', hoặc 'half_day'<br>
                                2. Có base_salary > 0 (nếu full-time) hoặc hourly_rate > 0 (nếu part-time)
                            </small>
                        </div>
                    </div>
                    @endif

                    <!-- Công thức -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="mb-2">Công thức tính:</h6>
                        <p class="mb-0">
                            <strong>Lợi Nhuận</strong> = Doanh Thu - Chi Phí NVL - Chi Phí Nhân Viên - Chi Phí Khác
                        </p>
                        <p class="mb-0 text-muted small mt-2">
                            = {{ number_format($data['revenue']) }} - {{ number_format($data['ingredient_cost']) }} - {{ number_format($data['salary_cost']) }} - {{ number_format($data['other_costs']) }}
                            = <strong>{{ number_format($data['profit']) }} đ</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal để nhập chi phí khác -->
<div class="modal fade" id="editOtherCostsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nhập Chi Phí Khác ({{ $data['period_label'] }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="GET" action="{{ route('admin.profit.index') }}">
                <input type="hidden" name="month" value="{{ $data['month'] }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tổng chi phí khác (đ)</label>
                        <div class="input-group">
                            <input type="number" name="other_costs" class="form-control" 
                                   value="{{ $data['other_costs'] }}" 
                                   step="0.01" min="0" placeholder="Nhập số tiền" required>
                            <span class="input-group-text">đ</span>
                        </div>
                        <small class="form-text text-muted">
                            Nhập tổng các chi phí khác như: thuê mặt bằng, điện nước, marketing, bảo trì, thuế, v.v.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Tính Lại</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

