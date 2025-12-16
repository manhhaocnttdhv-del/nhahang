@extends('layouts.app')

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-cash-coin"></i> Quản Lý Lương</h2>
        <a href="{{ route('admin.salaries.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tạo Bảng Lương
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
                    <label class="form-label">Nhân viên</label>
                    <select name="user_id" class="form-select">
                        <option value="">Tất cả</option>
                        @if(isset($staffList))
                            @foreach($staffList as $s)
                                <option value="{{ $s->id }}" {{ request('user_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Loại nhân viên</label>
                    <select name="employment_type" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="full_time" {{ request('employment_type') == 'full_time' ? 'selected' : '' }}>Full-time</option>
                        <option value="part_time" {{ request('employment_type') == 'part_time' ? 'selected' : '' }}>Part-time</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                    </select>
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
                            <th>ID</th>
                            <th>Nhân viên</th>
                            <th>Loại</th>
                            <th>Kỳ lương</th>
                            <th>Ngày làm</th>
                            <th>Giờ làm</th>
                            <th>Lương cơ bản</th>
                            <th>Làm thêm</th>
                            <th>Thưởng</th>
                            <th>Khấu trừ</th>
                            <th>Tổng lương</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($salaries))
                        @forelse($salaries as $salary)
                            <tr>
                                <td>#{{ $salary->id }}</td>
                                <td>
                                    <strong>{{ $salary->user->name }}</strong><br>
                                    <small class="text-muted">{{ $salary->user->email }}</small>
                                </td>
                                <td>
                                    @if($salary->employment_type === 'full_time')
                                        <span class="badge bg-primary">Full-time</span>
                                    @else
                                        <span class="badge bg-info">Part-time</span>
                                    @endif
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($salary->period_start)->format('d/m/Y') }} - 
                                    {{ \Carbon\Carbon::parse($salary->period_end)->format('d/m/Y') }}
                                </td>
                                <td>
                                    @if($salary->employment_type === 'full_time')
                                        {{ $salary->working_days ?? 0 }} ngày
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($salary->employment_type === 'part_time')
                                        {{ number_format($salary->working_hours ?? 0, 1) }} giờ
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ number_format($salary->base_salary, 0, ',', '.') }} đ</td>
                                <td>
                                    @if($salary->overtime_hours > 0)
                                        {{ $salary->overtime_hours }}h × {{ number_format($salary->overtime_rate, 0, ',', '.') }} đ
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ number_format($salary->bonus, 0, ',', '.') }} đ</td>
                                <td>{{ number_format($salary->deduction, 0, ',', '.') }} đ</td>
                                <td><strong class="text-success">{{ number_format($salary->total_salary, 0, ',', '.') }} đ</strong></td>
                                <td>
                                    @if($salary->status === 'pending')
                                        <span class="badge bg-warning">Chờ duyệt</span>
                                    @elseif($salary->status === 'approved')
                                        <span class="badge bg-info">Đã duyệt</span>
                                    @else
                                        <span class="badge bg-success">Đã thanh toán</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.salaries.show', $salary->id) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.salaries.edit', $salary->id) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="text-center text-muted">Chưa có bảng lương nào</td>
                            </tr>
                        @endforelse
                        @else
                            <tr>
                                <td colspan="13" class="text-center text-muted">Chưa có bảng lương nào</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @if(isset($salaries))
                {{ $salaries->links() }}
            @endif
        </div>
    </div>
</div>
@endsection

