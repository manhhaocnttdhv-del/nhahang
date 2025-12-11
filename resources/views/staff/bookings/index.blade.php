@extends('layouts.app')

@section('sidebar')
@include('staff.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-calendar-check"></i> Quản Lý Đặt Bàn</h2>
        <div>
            <a href="?status=pending" class="btn btn-warning btn-sm">Chờ xác nhận</a>
            <a href="?status=confirmed" class="btn btn-success btn-sm">Đã xác nhận</a>
            <a href="{{ route('staff.bookings.index') }}" class="btn btn-secondary btn-sm">Tất cả</a>
        </div>
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
                            <th>Khách hàng</th>
                            <th>Ngày/Giờ</th>
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
                                </td>
                                <td>
                                    {{ $booking->booking_date->format('d/m/Y') }}<br>
                                    <small>
                                        {{ \Carbon\Carbon::parse($booking->booking_time)->format('H:i') }}
                                        @if($booking->end_time)
                                            - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
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
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('staff.bookings.show', $booking->id) }}" class="btn btn-sm btn-primary">
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
            {{ $bookings->links() }}
        </div>
    </div>
</div>
@endsection

