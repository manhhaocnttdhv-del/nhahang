@extends('layouts.app')

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-ticket-perforated"></i> Quản Lý Voucher</h2>
        <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Thêm Voucher Mới
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
                            <th>Mã Voucher</th>
                            <th>Tên</th>
                            <th>Loại</th>
                            <th>Giá trị</th>
                            <th>Đơn tối thiểu</th>
                            <th>Ngày bắt đầu</th>
                            <th>Ngày kết thúc</th>
                            <th>Sử dụng</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vouchers as $voucher)
                            <tr>
                                <td>#{{ $voucher->id }}</td>
                                <td><strong class="text-primary">{{ $voucher->code }}</strong></td>
                                <td>{{ $voucher->name }}</td>
                                <td>
                                    @if($voucher->type === 'percentage')
                                        <span class="badge bg-info">Phần trăm</span>
                                    @else
                                        <span class="badge bg-warning">Cố định</span>
                                    @endif
                                </td>
                                <td>
                                    @if($voucher->type === 'percentage')
                                        {{ $voucher->value }}%
                                    @else
                                        {{ number_format($voucher->value) }} đ
                                    @endif
                                </td>
                                <td>
                                    @if($voucher->min_order_amount)
                                        {{ number_format($voucher->min_order_amount) }} đ
                                    @else
                                        <span class="text-muted">Không</span>
                                    @endif
                                </td>
                                <td>{{ $voucher->start_date->format('d/m/Y') }}</td>
                                <td>{{ $voucher->end_date->format('d/m/Y') }}</td>
                                <td>
                                    @if($voucher->usage_limit)
                                        {{ $voucher->used_count }} / {{ $voucher->usage_limit }}
                                    @else
                                        {{ $voucher->used_count }} / <span class="text-muted">∞</span>
                                    @endif
                                </td>
                                <td>
                                    @if($voucher->is_active)
                                        <span class="badge bg-success">Kích hoạt</span>
                                    @else
                                        <span class="badge bg-secondary">Tắt</span>
                                    @endif
                                    @if($voucher->isValid())
                                        <span class="badge bg-primary">Hợp lệ</span>
                                    @else
                                        <span class="badge bg-danger">Hết hạn</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.vouchers.edit', $voucher->id) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.vouchers.toggle-status', $voucher->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-{{ $voucher->is_active ? 'secondary' : 'success' }}" title="{{ $voucher->is_active ? 'Tắt' : 'Bật' }}">
                                            <i class="bi bi-{{ $voucher->is_active ? 'toggle-on' : 'toggle-off' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.vouchers.destroy', $voucher->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa voucher này?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted">Chưa có voucher nào</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $vouchers->links() }}
        </div>
    </div>
</div>
@endsection

