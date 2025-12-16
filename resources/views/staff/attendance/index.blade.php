@extends('layouts.app')

@section('sidebar')
@include('staff.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-clock-history"></i> Điểm Danh Làm Việc</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error') || $errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Điểm danh hôm nay -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card {{ $todayAttendance && $todayAttendance->check_in && $todayAttendance->check_out ? 'border-success' : 'border-primary' }}">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Điểm Danh Hôm Nay - {{ now()->format('d/m/Y') }}</h5>
                </div>
                <div class="card-body">
                    @if($todayAttendance && $todayAttendance->check_in)
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Giờ vào:</strong> 
                                    <span class="badge bg-success">{{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('H:i:s') }}</span>
                                </p>
                                @if($todayAttendance->check_out)
                                    <p><strong>Giờ ra:</strong> 
                                        <span class="badge bg-danger">{{ \Carbon\Carbon::parse($todayAttendance->check_out)->format('H:i:s') }}</span>
                                    </p>
                                    <p><strong>Tổng giờ làm:</strong> 
                                        <span class="badge bg-info">{{ number_format($todayAttendance->working_hours, 2) }} giờ</span>
                                    </p>
                                    @if($todayAttendance->overtime_hours > 0)
                                        <p><strong>Giờ làm thêm:</strong> 
                                            <span class="badge bg-warning">{{ number_format($todayAttendance->overtime_hours, 2) }} giờ</span>
                                        </p>
                                    @endif
                                @else
                                    <p class="text-muted">Chưa điểm danh ra</p>
                                    <form action="{{ route('staff.attendance.check-out') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">
                                            <i class="bi bi-box-arrow-right"></i> Điểm Danh Ra
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <p><strong>Trạng thái:</strong> 
                                    @if($todayAttendance->status === 'present')
                                        <span class="badge bg-success">Có mặt</span>
                                    @elseif($todayAttendance->status === 'late')
                                        <span class="badge bg-warning">Muộn</span>
                                    @elseif($todayAttendance->status === 'absent')
                                        <span class="badge bg-danger">Vắng mặt</span>
                                    @else
                                        <span class="badge bg-secondary">Nửa ngày</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="mb-3">Bạn chưa điểm danh vào hôm nay</p>
                            <form action="{{ route('staff.attendance.check-in') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-box-arrow-in-right"></i> Điểm Danh Vào
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê tháng này -->
    @if(isset($monthStats))
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h6>Tổng Giờ Làm</h6>
                        <h3>{{ number_format($monthStats['total_working_hours'], 1) }}h</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h6>Giờ Làm Thêm</h6>
                        <h3>{{ number_format($monthStats['total_overtime_hours'], 1) }}h</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h6>Ngày Có Mặt</h6>
                        <h3>{{ $monthStats['present_days'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h6>Ngày Muộn</h6>
                        <h3>{{ $monthStats['late_days'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Lịch sử điểm danh -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-list-ul"></i> Lịch Sử Điểm Danh (30 Ngày Gần Nhất)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Ngày</th>
                            <th>Giờ vào</th>
                            <th>Giờ ra</th>
                            <th>Giờ làm</th>
                            <th>Làm thêm</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                            <tr>
                                <td>{{ $attendance->date->format('d/m/Y') }}</td>
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Chưa có lịch sử điểm danh</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

