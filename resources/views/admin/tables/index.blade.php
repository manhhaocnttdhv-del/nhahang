@extends('layouts.app')

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-table"></i> Quản Lý Bàn</h2>
        <a href="{{ route('admin.tables.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Thêm Bàn Mới
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
                            <th>Tên bàn</th>
                            <th>Số bàn</th>
                            <th>Sức chứa</th>
                            <th>Khu vực</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tables as $table)
                            <tr>
                                <td>#{{ $table->id }}</td>
                                <td><strong>{{ $table->name }}</strong></td>
                                <td>{{ $table->number }}</td>
                                <td>{{ $table->capacity }} người</td>
                                <td>{{ $table->area ?? '-' }}</td>
                                <td>
                                    @if($table->status === 'available')
                                        <span class="badge bg-success">Trống</span>
                                    @elseif($table->status === 'reserved')
                                        <span class="badge bg-warning">Đã đặt</span>
                                    @elseif($table->status === 'occupied')
                                        <span class="badge bg-danger">Đang dùng</span>
                                    @else
                                        <span class="badge bg-secondary">Bảo trì</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.tables.edit', $table->id) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.tables.destroy', $table->id) }}" method="POST" class="d-inline">
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
                                <td colspan="7" class="text-center text-muted">Chưa có bàn nào</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $tables->links() }}
        </div>
    </div>
</div>
@endsection

