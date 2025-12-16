@extends('layouts.app')

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-cash-coin"></i> Chi Tiết Bảng Lương</h2>
        <div>
            <a href="{{ route('admin.salaries.edit', $salary->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Sửa
            </a>
            <a href="{{ route('admin.salaries.index') }}" class="btn btn-secondary">
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
                    <h5 class="mb-0"><i class="bi bi-person"></i> Thông Tin Nhân Viên</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Tên nhân viên:</strong> {{ $salary->user->name }}</p>
                            <p><strong>Email:</strong> {{ $salary->user->email }}</p>
                            <p><strong>Loại nhân viên:</strong> 
                                @if($salary->employment_type === 'full_time')
                                    <span class="badge bg-primary">Full-time</span>
                                @else
                                    <span class="badge bg-info">Part-time</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Kỳ lương:</strong> 
                                {{ \Carbon\Carbon::parse($salary->period_start)->format('d/m/Y') }} - 
                                {{ \Carbon\Carbon::parse($salary->period_end)->format('d/m/Y') }}
                            </p>
                            <p><strong>Trạng thái:</strong> 
                                @if($salary->status === 'pending')
                                    <span class="badge bg-warning">Chờ duyệt</span>
                                @elseif($salary->status === 'approved')
                                    <span class="badge bg-info">Đã duyệt</span>
                                @else
                                    <span class="badge bg-success">Đã thanh toán</span>
                                @endif
                            </p>
                            @if($salary->approved_by)
                                <p><strong>Người duyệt:</strong> {{ $salary->approvedBy->name ?? 'N/A' }}</p>
                                <p><strong>Ngày duyệt:</strong> {{ \Carbon\Carbon::parse($salary->approved_at)->format('d/m/Y H:i') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-calculator"></i> Chi Tiết Lương</h5>
                </div>
                <div class="card-body">
                    @if($salary->employment_type === 'full_time')
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Lương cơ bản:</strong> {{ number_format($salary->base_salary, 0, ',', '.') }} đ</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Số ngày làm việc:</strong> {{ $salary->working_days ?? 0 }} ngày</p>
                            </div>
                        </div>
                    @else
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Số giờ làm việc:</strong> {{ number_format($salary->working_hours ?? 0, 1) }} giờ</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Lương theo giờ:</strong> {{ number_format($salary->hourly_rate ?? 0, 0, ',', '.') }} đ/giờ</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <p><strong>Lương cơ bản (giờ × lương/giờ):</strong> 
                                    {{ number_format(($salary->working_hours ?? 0) * ($salary->hourly_rate ?? 0), 0, ',', '.') }} đ
                                </p>
                            </div>
                        </div>
                    @endif

                    @if($salary->overtime_hours > 0)
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Giờ làm thêm:</strong> {{ number_format($salary->overtime_hours, 1) }} giờ</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Lương làm thêm/giờ:</strong> {{ number_format($salary->overtime_rate, 0, ',', '.') }} đ</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <p><strong>Tổng lương làm thêm:</strong> 
                                    {{ number_format($salary->overtime_hours * $salary->overtime_rate, 0, ',', '.') }} đ
                                </p>
                            </div>
                        </div>
                    @endif

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Thưởng:</strong> <span class="text-success">+ {{ number_format($salary->bonus, 0, ',', '.') }} đ</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Khấu trừ:</strong> <span class="text-danger">- {{ number_format($salary->deduction, 0, ',', '.') }} đ</span></p>
                        </div>
                    </div>

                    <div class="alert alert-success mb-0">
                        <h4 class="mb-0">
                            <strong>Tổng lương:</strong> 
                            <span class="float-end">{{ number_format($salary->total_salary, 0, ',', '.') }} đ</span>
                        </h4>
                    </div>
                </div>
            </div>

            @if($salary->notes)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-sticky"></i> Ghi chú</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $salary->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Thông Tin Khác</h5>
                </div>
                <div class="card-body">
                    <p><strong>Ngày tạo:</strong> {{ $salary->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Ngày cập nhật:</strong> {{ $salary->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

