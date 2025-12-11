@extends('layouts.app')

@section('sidebar')
@include('staff.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Chi Tiết Đặt Bàn #{{ $booking->id }}</h2>
        <a href="{{ route('staff.bookings.index') }}" class="btn btn-secondary">Quay lại</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông Tin Đặt Bàn</h5>
                </div>
                <div class="card-body">
                    <p><strong>Khách hàng:</strong> {{ $booking->customer_name }}</p>
                    <p><strong>Số điện thoại:</strong> {{ $booking->customer_phone }}</p>
                    <p><strong>Ngày:</strong> {{ $booking->booking_date->format('d/m/Y') }}</p>
                    <p><strong>Giờ:</strong> 
                        {{ \Carbon\Carbon::parse($booking->booking_time)->format('H:i') }}
                        @if($booking->end_time)
                            - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                        @endif
                    </p>
                    <p><strong>Số khách:</strong> {{ $booking->number_of_guests }} người</p>
                    @if($booking->table)
                        <p><strong>Bàn hiện tại:</strong> 
                            <span class="badge bg-primary">{{ $booking->table->name }} ({{ $booking->table->number }})</span>
                            <small class="text-muted">- {{ $booking->table->area ?? 'Khu vực chung' }}</small>
                            <br>
                            <small class="text-muted">Sức chứa: {{ $booking->table->capacity }} người | Trạng thái: 
                                @if($booking->table->status === 'available')
                                    <span class="badge bg-success">Trống</span>
                                @elseif($booking->table->status === 'reserved')
                                    <span class="badge bg-warning">Đã đặt</span>
                                @elseif($booking->table->status === 'occupied')
                                    <span class="badge bg-danger">Đang dùng</span>
                                @else
                                    <span class="badge bg-secondary">{{ $booking->table->status }}</span>
                                @endif
                            </small>
                        </p>
                    @else
                        <p><strong>Bàn:</strong> <span class="text-muted">Chưa được gán</span></p>
                    @endif
                    @if($booking->location_preference)
                        <p><strong>Yêu cầu vị trí:</strong> {{ $booking->location_preference }}</p>
                    @endif
                    @if($booking->notes)
                        <p><strong>Ghi chú:</strong> 
                            <div class="text-muted" style="white-space: pre-line;">{{ $booking->notes }}</div>
                        </p>
                    @endif
                    <p><strong>Trạng thái:</strong> 
                        @if($booking->status === 'pending')
                            <span class="badge bg-warning">Chờ xác nhận</span>
                        @elseif($booking->status === 'confirmed')
                            <span class="badge bg-success">Đã xác nhận</span>
                        @elseif($booking->status === 'rejected')
                            <span class="badge bg-danger">Đã từ chối</span>
                        @elseif($booking->status === 'checked_in')
                            <span class="badge bg-info">Đã check-in</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thao Tác</h5>
                </div>
                <div class="card-body">
                    @if($booking->status === 'pending')
                        <form action="{{ route('staff.bookings.confirm', $booking->id) }}" method="POST" class="mb-3">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Chọn bàn</label>
                                <select name="table_id" class="form-select">
                                    <option value="">Tự động</option>
                                    @foreach($tables as $table)
                                        <option value="{{ $table->id }}">{{ $table->name }} ({{ $table->number }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Xác Nhận</button>
                        </form>
                        <form action="{{ route('staff.bookings.reject', $booking->id) }}" method="POST" class="mb-3">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100">Từ Chối</button>
                        </form>
                    @endif

                    @if($booking->status === 'confirmed' && $booking->table_id)
                        <form action="{{ route('staff.bookings.check-in', $booking->id) }}" method="POST" class="mb-3">
                            @csrf
                            <button type="submit" class="btn btn-info w-100">Check-in</button>
                        </form>
                    @endif

                    @if(in_array($booking->status, ['confirmed', 'checked_in']) && $booking->table_id)
                        <hr>
                        <h6 class="mb-3"><i class="bi bi-arrow-left-right me-2"></i>Chuyển Bàn</h6>
                        <form action="{{ route('staff.bookings.transfer-table', $booking->id) }}" method="POST" id="transferTableForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Chọn bàn mới <span class="text-danger">*</span></label>
                                <select name="new_table_id" class="form-select" id="new_table_id" required>
                                    <option value="">-- Chọn bàn --</option>
                                    @foreach($availableTables as $table)
                                        <option value="{{ $table->id }}" 
                                            data-capacity="{{ $table->capacity }}"
                                            data-area="{{ $table->area ?? '' }}"
                                            data-status="{{ $table->status }}">
                                            {{ $table->name }} ({{ $table->number }}) 
                                            - {{ $table->area ?? 'Khu vực chung' }}
                                            ({{ $table->capacity }} người)
                                            @if($table->status === 'occupied')
                                                - Đang dùng
                                            @elseif($table->status === 'reserved')
                                                - Đã đặt
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Chỉ hiển thị bàn có sức chứa ≥ {{ $booking->number_of_guests }} người</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Lý do chuyển bàn (tùy chọn)</label>
                                <textarea name="reason" class="form-control" rows="2" 
                                    placeholder="Ví dụ: Khách yêu cầu, bàn cần bảo trì, v.v."></textarea>
                            </div>
                            <div class="alert alert-warning mb-3" id="transferWarning" style="display: none;">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <span id="warningMessage"></span>
                            </div>
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="bi bi-arrow-left-right me-2"></i>Chuyển Bàn
                            </button>
                        </form>
                        @if($availableTables->isEmpty())
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                Không có bàn nào phù hợp để chuyển (sức chứa ≥ {{ $booking->number_of_guests }} người)
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            @if($booking->orders && $booking->orders->count() > 0)
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">Đơn Hàng Liên Quan</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @foreach($booking->orders as $order)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>Đơn #{{ $order->id }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $order->created_at->format('d/m/Y H:i') }} | 
                                                {{ number_format($order->total_amount) }} đ
                                            </small>
                                        </div>
                                        <span class="badge 
                                            @if($order->status === 'pending') bg-warning
                                            @elseif($order->status === 'processing') bg-info
                                            @elseif($order->status === 'completed') bg-success
                                            @elseif($order->status === 'cancelled') bg-danger
                                            @else bg-secondary
                                            @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('transferTableForm');
        const select = document.getElementById('new_table_id');
        const warning = document.getElementById('transferWarning');
        const warningMessage = document.getElementById('warningMessage');

        if (form && select) {
            select.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    const capacity = parseInt(selectedOption.dataset.capacity);
                    const status = selectedOption.dataset.status;
                    const guests = {{ $booking->number_of_guests }};

                    warning.style.display = 'none';

                    if (status === 'occupied') {
                        warning.style.display = 'block';
                        warningMessage.textContent = 'Cảnh báo: Bàn này đang được sử dụng. Vui lòng kiểm tra kỹ trước khi chuyển.';
                        warning.className = 'alert alert-danger mb-3';
                    } else if (status === 'reserved') {
                        warning.style.display = 'block';
                        warningMessage.textContent = 'Cảnh báo: Bàn này đã được đặt. Hệ thống sẽ kiểm tra xung đột thời gian.';
                        warning.className = 'alert alert-warning mb-3';
                    }
                } else {
                    warning.style.display = 'none';
                }
            });

            form.addEventListener('submit', function(e) {
                const selectedOption = select.options[select.selectedIndex];
                if (selectedOption.value) {
                    const tableName = selectedOption.text.split(' - ')[0];
                    if (!confirm(`Xác nhận chuyển bàn sang ${tableName}?`)) {
                        e.preventDefault();
                        return false;
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection

