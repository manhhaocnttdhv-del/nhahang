@extends('layouts.app')

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-calendar-check"></i> Quản Lý Đặt Bàn</h2>
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
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Đã từ chối</option>
                        <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>Đã check-in</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Ngày</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
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
                            <th>Khách hàng</th>
                            <th>Ngày/Buổi</th>
                            <th>Số khách</th>
                            <th>Bàn</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                            <tr>
                                <td>#{{ $booking->id }}</td>
                                <td>
                                    <strong>{{ $booking->customer_name }}</strong><br>
                                    <small class="text-muted">{{ $booking->customer_phone }}</small>
                                    @if($booking->user)
                                        <br><small class="text-info">TK: {{ $booking->user->name }}</small>
                                    @endif
                                </td>
                                <td>
                                    {{ $booking->booking_date->format('d/m/Y') }}<br>
                                    <small>
                                        @if($booking->session)
                                            @php
                                                $sessions = [
                                                    'morning' => 'Sáng',
                                                    'lunch' => 'Trưa',
                                                    'afternoon' => 'Chiều',
                                                    'dinner' => 'Tối'
                                                ];
                                            @endphp
                                            <span class="badge bg-info">{{ $sessions[$booking->session] ?? $booking->session }}</span>
                                        @elseif($booking->booking_time)
                                            {{ \Carbon\Carbon::parse($booking->booking_time)->format('H:i') }}
                                            @if($booking->end_time)
                                                - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                            @endif
                                        @endif
                                    </small>
                                </td>
                                <td>{{ $booking->number_of_guests }} người</td>
                                <td>{{ $booking->table ? $booking->table->name : '-' }}</td>
                                <td>
                                    @if($booking->status === 'pending')
                                        <span class="badge bg-warning">Chờ xác nhận</span>
                                    @elseif($booking->status === 'confirmed')
                                        <span class="badge bg-success">Đã xác nhận</span>
                                    @elseif($booking->status === 'rejected')
                                        <span class="badge bg-danger">Đã từ chối</span>
                                    @elseif($booking->status === 'checked_in')
                                        <span class="badge bg-info">Đã check-in</span>
                                    @elseif($booking->status === 'completed')
                                        <span class="badge bg-success">Hoàn thành</span>
                                    @elseif($booking->status === 'cancelled')
                                        <span class="badge bg-secondary">Đã hủy</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Không có đặt bàn nào</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($bookings->hasPages())
                <div class="mt-3">
                    {{ $bookings->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

