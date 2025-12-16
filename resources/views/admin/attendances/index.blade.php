@extends('layouts.app')

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-clock-history"></i> Quản Lý Điểm Danh</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Thống kê -->
    @if(isset($stats))
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h6>Tổng Bản Ghi</h6>
                        <h3>{{ $stats['total_records'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h6>Có Mặt</h6>
                        <h3>{{ $stats['present_count'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h6>Muộn</h6>
                        <h3>{{ $stats['late_count'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h6>Vắng Mặt</h6>
                        <h3>{{ $stats['absent_count'] }}</h3>
                    </div>
                </div>
            </div>
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
                        @foreach($staffList as $staff)
                            <option value="{{ $staff->id }}" {{ request('user_id') == $staff->id ? 'selected' : '' }}>
                                {{ $staff->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Có mặt</option>
                        <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Muộn</option>
                        <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Vắng mặt</option>
                        <option value="half_day" {{ request('status') == 'half_day' ? 'selected' : '' }}>Nửa ngày</option>
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

    <!-- Bảng điểm danh -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Ngày</th>
                            <th>Nhân viên</th>
                            <th>Giờ vào</th>
                            <th>Giờ ra</th>
                            <th>Giờ làm</th>
                            <th>Làm thêm</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                            <tr>
                                <td>{{ $attendance->date->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.attendances.show', $attendance->user_id) }}">
                                        {{ $attendance->user->name }}
                                    </a>
                                </td>
                                <td>
                                    @if($attendance->check_in)
                                        <span class="badge bg-success">{{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i') }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($attendance->check_out)
                                        <span class="badge bg-danger">{{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i') }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ number_format($attendance->working_hours, 2) }}h</td>
                                <td>
                                    @if($attendance->overtime_hours > 0)
                                        <span class="badge bg-warning">{{ number_format($attendance->overtime_hours, 2) }}h</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($attendance->status === 'present')
                                        <span class="badge bg-success">Có mặt</span>
                                    @elseif($attendance->status === 'late')
                                        <span class="badge bg-warning">Muộn</span>
                                    @elseif($attendance->status === 'absent')
                                        <span class="badge bg-danger">Vắng</span>
                                    @else
                                        <span class="badge bg-secondary">Nửa ngày</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.attendances.show', $attendance->user_id) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">Chưa có dữ liệu điểm danh</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($attendances->hasPages())
                <div class="mt-3">
                    {{ $attendances->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

