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
    </ul>
</div>

