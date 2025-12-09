@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
@endphp

@section('title', 'Món Yêu Thích')

@section('content')
<div class="container my-5">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 mb-3" style="font-weight: 700; color: #667eea;">
                <i class="bi bi-heart-fill"></i> Món Yêu Thích
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
        @forelse($favorites as $favorite)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="position-relative">
                        <img src="{{ $favorite->menuItem->image ? Storage::url($favorite->menuItem->image) : 'https://via.placeholder.com/300x200?text=' . urlencode($favorite->menuItem->name) }}" 
                             class="card-img-top" 
                             alt="{{ $favorite->menuItem->name }}"
                             style="height: 200px; object-fit: cover;">
                        <form action="{{ route('favorites.destroy', $favorite->id) }}" method="POST" class="position-absolute top-0 end-0 m-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm rounded-circle" onclick="return confirm('Xóa khỏi yêu thích?')">
                                <i class="bi bi-heart-fill"></i>
                            </button>
                        </form>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $favorite->menuItem->name }}</h5>
                        <p class="text-muted small mb-2">{{ $favorite->menuItem->category->name }}</p>
                        <p class="card-text text-muted small">{{ Str::limit($favorite->menuItem->description, 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <strong class="price-tag">{{ number_format($favorite->menuItem->price) }} đ</strong>
                            <a href="{{ route('menu.index') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-cart-plus me-1"></i> Đặt món
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card text-center p-5">
                    <i class="bi bi-heart display-1 text-muted mb-3"></i>
                    <h4 class="text-muted">Bạn chưa có món yêu thích nào</h4>
                    <p class="text-muted mb-4">Hãy thêm món ăn vào yêu thích khi xem menu</p>
                    <a href="{{ route('menu.index') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-menu-button-wide me-2"></i> Xem Menu
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    @if($favorites->hasPages())
        <div class="mt-4">
            {{ $favorites->links() }}
        </div>
    @endif
</div>
@endsection

