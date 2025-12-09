@extends('layouts.app')

@section('title', 'Địa Chỉ Giao Hàng')

@section('content')
<div class="container my-5">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 mb-3" style="font-weight: 700; color: #667eea;">
                <i class="bi bi-geo-alt"></i> Địa Chỉ Giao Hàng
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
            <!-- Add New Address -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: #667eea; color: white;">
                    <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i> Thêm Địa Chỉ Mới</h5>
                    <button type="button" class="btn btn-light btn-sm" data-bs-toggle="collapse" data-bs-target="#addAddressForm">
                        <i class="bi bi-chevron-down"></i>
                    </button>
                </div>
                <div class="collapse" id="addAddressForm">
                    <div class="card-body">
                        <form action="{{ route('addresses.store') }}" method="POST">
                            @csrf
                            @include('addresses._form', ['address' => null])
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-check-circle me-2"></i> Thêm Địa Chỉ
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Address List -->
            @forelse($addresses as $address)
                <div class="card mb-3 {{ $address->is_default ? 'border-primary' : '' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                @if($address->is_default)
                                    <span class="badge bg-primary mb-2">Mặc định</span>
                                @endif
                                @if($address->label)
                                    <h5 class="mb-2">{{ $address->label }}</h5>
                                @endif
                                <p class="mb-1"><strong>{{ $address->recipient_name }}</strong> - {{ $address->phone }}</p>
                                <p class="text-muted mb-1">{{ $address->full_address }}</p>
                                @if($address->notes)
                                    <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i>{{ $address->notes }}</p>
                                @endif
                            </div>
                            <div class="ms-3">
                                <div class="btn-group-vertical">
                                    @if(!$address->is_default)
                                        <form action="{{ route('addresses.set-default', $address->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-outline-primary mb-2">
                                                <i class="bi bi-star me-1"></i> Đặt mặc định
                                            </button>
                                        </form>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-outline-secondary mb-2" data-bs-toggle="collapse" data-bs-target="#editAddress{{ $address->id }}">
                                        <i class="bi bi-pencil me-1"></i> Sửa
                                    </button>
                                    <form action="{{ route('addresses.destroy', $address->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa địa chỉ này?')">
                                            <i class="bi bi-trash me-1"></i> Xóa
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Edit Form -->
                        <div class="collapse mt-3" id="editAddress{{ $address->id }}">
                            <div class="card card-body bg-light">
                                <form action="{{ route('addresses.update', $address->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    @include('addresses._form', ['address' => $address])
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-check-circle me-2"></i> Cập Nhật
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card text-center p-5">
                    <i class="bi bi-geo-alt display-1 text-muted mb-3"></i>
                    <h4 class="text-muted">Bạn chưa có địa chỉ nào</h4>
                    <p class="text-muted">Thêm địa chỉ để đặt hàng nhanh hơn</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

