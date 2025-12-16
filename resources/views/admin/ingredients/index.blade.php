@extends('layouts.app')

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-box-seam"></i> Quản Lý Nguyên Liệu</h2>
        <a href="{{ route('admin.ingredients.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Thêm Nguyên Liệu
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row mb-4">
        @forelse($ingredients as $ingredient)
            <div class="col-md-4 mb-3">
                <div class="card h-100 {{ $ingredient->isLowStock() ? 'border-danger' : ($ingredient->isOverStock() ? 'border-warning' : '') }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0">{{ $ingredient->name }}</h5>
                            @if($ingredient->isLowStock())
                                <span class="badge bg-danger">Tồn kho thấp</span>
                            @elseif($ingredient->isOverStock())
                                <span class="badge bg-warning">Tồn kho cao</span>
                            @else
                                <span class="badge bg-success">Bình thường</span>
                            @endif
                        </div>
                        @if($ingredient->code)
                            <small class="text-muted">Mã: {{ $ingredient->code }}</small>
                        @endif
                        <hr>
                        <div class="mb-2">
                            <strong>Tồn kho hiện tại:</strong> 
                            <span class="{{ $ingredient->isLowStock() ? 'text-danger' : '' }}">
                                {{ number_format($ingredient->getCurrentStock(), 2) }} {{ $ingredient->unit }}
                            </span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">
                                Min: {{ $ingredient->min_stock }} {{ $ingredient->unit }} | 
                                Max: {{ $ingredient->max_stock > 0 ? $ingredient->max_stock : '∞' }} {{ $ingredient->unit }}
                            </small>
                        </div>
                        <div class="mb-2">
                            <strong>Giá mua:</strong> {{ number_format($ingredient->unit_price, 0, ',', '.') }} đ/{{ $ingredient->unit }}
                        </div>
                        @if($ingredient->description)
                            <p class="text-muted small mb-2">{{ Str::limit($ingredient->description, 50) }}</p>
                        @endif
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.ingredients.show', $ingredient->id) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i> Chi tiết
                            </a>
                            <a href="{{ route('admin.ingredients.edit', $ingredient->id) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i> Sửa
                            </a>
                            <a href="{{ route('admin.ingredient-stocks.create', ['ingredient_id' => $ingredient->id]) }}" class="btn btn-sm btn-success">
                                <i class="bi bi-box-arrow-in-down"></i> Nhập/Xuất
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                    <p class="text-muted">Chưa có nguyên liệu nào</p>
                    <a href="{{ route('admin.ingredients.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Thêm Nguyên Liệu Đầu Tiên
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    @if($ingredients->hasPages())
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                {{-- Previous Page Link --}}
                @if ($ingredients->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">« Previous</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $ingredients->previousPageUrl() }}" rel="prev">« Previous</a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($ingredients->getUrlRange(1, $ingredients->lastPage()) as $page => $url)
                    @if ($page == $ingredients->currentPage())
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($ingredients->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $ingredients->nextPageUrl() }}" rel="next">Next »</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">Next »</span>
                    </li>
                @endif
            </ul>
        </nav>
        <div class="text-center text-muted mt-2">
            <small>Showing {{ $ingredients->firstItem() }} to {{ $ingredients->lastItem() }} of {{ $ingredients->total() }} results</small>
        </div>
    @endif
</div>
@endsection

