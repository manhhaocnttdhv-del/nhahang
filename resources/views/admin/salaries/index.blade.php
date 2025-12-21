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
                <div class="col-md-6">
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
                            <th>Tháng</th>
                            <th>Tổng lương</th>
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
                                    {{ \Carbon\Carbon::parse($salary->created_at)->format('m/Y') }}
                                </td>
                                <td><strong class="text-success">{{ number_format($salary->total_salary, 0, ',', '.') }} đ</strong></td>
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
                                <td colspan="6" class="text-center text-muted">Chưa có bảng lương nào</td>
                            </tr>
                        @endforelse
                        @else
                            <tr>
                                <td colspan="6" class="text-center text-muted">Chưa có bảng lương nào</td>
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

