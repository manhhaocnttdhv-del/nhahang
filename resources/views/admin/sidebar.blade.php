<div class="p-3">
    <h5 class="mb-3">Menu Quản Trị</h5>
    <ul class="list-unstyled">
        <li><a href="{{ route('admin.dashboard') }}" class="d-block sidebar-item text-decoration-none text-dark {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a></li>
        <li><a href="{{ route('admin.menu.index') }}" class="d-block sidebar-item text-decoration-none text-dark {{ request()->routeIs('admin.menu.*') ? 'active' : '' }}">
            <i class="bi bi-menu-button-wide"></i> Quản Lý Menu
        </a></li>
        <li><a href="{{ route('admin.tables.index') }}" class="d-block sidebar-item text-decoration-none text-dark {{ request()->routeIs('admin.tables.*') ? 'active' : '' }}">
            <i class="bi bi-table"></i> Quản Lý Bàn
        </a></li>
        <li><a href="{{ route('admin.staff.index') }}" class="d-block sidebar-item text-decoration-none text-dark {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Quản Lý Nhân Viên
        </a></li>
        <li><a href="{{ route('admin.vouchers.index') }}" class="d-block sidebar-item text-decoration-none text-dark {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}">
            <i class="bi bi-ticket-perforated"></i> Quản Lý Voucher
        </a></li>
        <li><a href="{{ route('admin.reports.index') }}" class="d-block sidebar-item text-decoration-none text-dark {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <i class="bi bi-graph-up"></i> Báo Cáo & Thống Kê
        </a></li>
        <li><a href="{{ route('admin.salaries.index') }}" class="d-block sidebar-item text-decoration-none text-dark {{ request()->routeIs('admin.salaries.*') ? 'active' : '' }}">
            <i class="bi bi-cash-coin"></i> Quản Lý Lương
        </a></li>
        <li><a href="{{ route('admin.ingredients.index') }}" class="d-block sidebar-item text-decoration-none text-dark {{ request()->routeIs('admin.ingredients.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i> Quản Lý Nguyên Liệu
        </a></li>
        <li><a href="{{ route('admin.attendances.index') }}" class="d-block sidebar-item text-decoration-none text-dark {{ request()->routeIs('admin.attendances.*') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i> Quản Lý Điểm Danh
        </a></li>
        <li><a href="{{ route('admin.ingredient-stocks.index') }}" class="d-block sidebar-item text-decoration-none text-dark {{ request()->routeIs('admin.ingredient-stocks.*') ? 'active' : '' }}">
            <i class="bi bi-arrow-left-right"></i> Lịch Sử Nhập Xuất
        </a></li>
        <li><a href="{{ route('admin.bookings.index') }}" class="d-block sidebar-item text-decoration-none text-dark {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-check"></i> Quản Lý Đặt Bàn
        </a></li>
        <li><a href="{{ route('admin.orders.index') }}" class="d-block sidebar-item text-decoration-none text-dark {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i> Quản Lý Đơn Hàng
        </a></li>
        <li><a href="{{ route('admin.payments.index') }}" class="d-block sidebar-item text-decoration-none text-dark {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
            <i class="bi bi-credit-card"></i> Quản Lý Thanh Toán
        </a></li>
    </ul>
</div>

