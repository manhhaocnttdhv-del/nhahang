<div class="p-3">
    <h5 class="mb-3">Menu Nhân Viên</h5>
    <ul class="list-unstyled">
        <li><a href="{{ route('staff.dashboard') }}" class="d-block sidebar-item text-decoration-none text-dark {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a></li>
        <li><a href="{{ route('staff.bookings.index') }}" class="d-block sidebar-item text-decoration-none text-dark {{ request()->routeIs('staff.bookings.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-check"></i> Quản Lý Đặt Bàn
        </a></li>
        <li><a href="{{ route('staff.orders.index') }}" class="d-block sidebar-item text-decoration-none text-dark {{ request()->routeIs('staff.orders.*') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i> Quản Lý Đơn Hàng
        </a></li>
        <li><a href="{{ route('staff.payments.index') }}" class="d-block sidebar-item text-decoration-none text-dark {{ request()->routeIs('staff.payments.*') ? 'active' : '' }}">
            <i class="bi bi-cash-coin"></i> Thanh Toán
        </a></li>
        <li><a href="{{ route('staff.attendance.index') }}" class="d-block sidebar-item text-decoration-none text-dark {{ request()->routeIs('staff.attendance.*') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i> Điểm Danh
        </a></li>
    </ul>
</div>

