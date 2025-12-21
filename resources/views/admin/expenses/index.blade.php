@extends('layouts.app')

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-cash-stack"></i> Quản Lý Chi Phí</h2>
        <a href="{{ route('admin.expenses.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Thêm Chi Phí
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.expenses.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Loại chi phí</label>
                    <select name="category" class="form-select">
                        <option value="">Tất cả</option>
                        @foreach($categories as $key => $name)
                            <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tháng</label>
                    <input type="month" name="month" class="form-control" value="{{ request('month') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Từ ngày</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Đến ngày</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">Lọc</button>
                        <a href="{{ route('admin.expenses.index') }}" class="btn btn-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="card mb-4 bg-info text-white">
        <div class="card-body">
            <h5><i class="bi bi-calculator me-2"></i> Tổng Chi Phí</h5>
            <h2 class="mb-0">{{ number_format($totalAmount) }} đ</h2>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Ngày</th>
                            <th>Tên chi phí</th>
                            <th>Loại</th>
                            <th class="text-end">Số tiền</th>
                            <th>Phương thức</th>
                            <th>Người tạo</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                            <tr>
                                <td>{{ $expense->expense_date->format('d/m/Y') }}</td>
                                <td>
                                    <strong>{{ $expense->name }}</strong>
                                    @if($expense->receipt_number)
                                        <br><small class="text-muted">Số HĐ: {{ $expense->receipt_number }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $expense->category_name }}</span>
                                </td>
                                <td class="text-end">
                                    <strong class="text-danger">{{ number_format($expense->amount) }} đ</strong>
                                </td>
                                <td>
                                    @if($expense->payment_method)
                                        {{ ucfirst(str_replace('_', ' ', $expense->payment_method)) }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $expense->creator->name ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.expenses.show', $expense->id) }}" class="btn btn-info" title="Chi tiết">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.expenses.edit', $expense->id) }}" class="btn btn-warning" title="Sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.expenses.destroy', $expense->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa chi phí này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox"></i> Chưa có chi phí nào
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $expenses->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

