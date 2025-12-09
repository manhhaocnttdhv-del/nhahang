@extends('layouts.app')

@section('title', 'Thông Báo')

@section('content')
<div class="container my-5">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 mb-3" style="font-weight: 700; color: #667eea;">
                <i class="bi bi-bell"></i> Thông Báo
            </h1>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8 mx-auto">
            @forelse($notifications as $notification)
                <div class="card mb-3 {{ !$notification->is_read ? 'border-primary' : '' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h5 class="mb-2 {{ !$notification->is_read ? 'fw-bold' : '' }}">
                                    @if(!$notification->is_read)
                                        <span class="badge bg-primary me-2">Mới</span>
                                    @endif
                                    {{ $notification->title }}
                                </h5>
                                <p class="text-muted mb-2">{{ $notification->message }}</p>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ $notification->created_at->diffForHumans() }}
                                </small>
                            </div>
                            <div class="ms-3">
                                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa thông báo này?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card text-center p-5">
                    <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                    <h4 class="text-muted">Không có thông báo nào</h4>
                    <p class="text-muted">Bạn sẽ nhận được thông báo khi có cập nhật về đơn hàng hoặc đặt bàn</p>
                </div>
            @endforelse

            @if($notifications->hasPages())
                <div class="mt-4">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

