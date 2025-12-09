@extends('layouts.app')

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-people"></i> Quản Lý Nhân Viên</h2>
        <a href="{{ route('admin.staff.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Thêm Nhân Viên
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Vai trò</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staff as $s)
                            <tr>
                                <td>#{{ $s->id }}</td>
                                <td><strong>{{ $s->name }}</strong></td>
                                <td>{{ $s->email }}</td>
                                <td>{{ $s->phone ?? '-' }}</td>
                                <td>
                                    @if($s->role === 'admin')
                                        <span class="badge bg-danger">Admin</span>
                                    @elseif($s->role === 'staff')
                                        <span class="badge bg-primary">Nhân viên</span>
                                    @elseif($s->role === 'cashier')
                                        <span class="badge bg-success">Thu ngân</span>
                                    @else
                                        <span class="badge bg-info">Quản lý bếp</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.staff.edit', $s->id) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.staff.destroy', $s->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Chưa có nhân viên nào</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $staff->links() }}
        </div>
    </div>
</div>
@endsection

