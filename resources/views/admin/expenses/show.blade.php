@extends('layouts.app')

@section('sidebar')
@include('admin.sidebar')
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-cash-stack"></i> Chi Tiết Chi Phí</h2>
        <div>
            <a href="{{ route('admin.expenses.edit', $expense->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Sửa
            </a>
            <a href="{{ route('admin.expenses.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">Tên chi phí:</th>
                            <td><strong>{{ $expense->name }}</strong></td>
                        </tr>
                        <tr>
                            <th>Loại chi phí:</th>
                            <td><span class="badge bg-secondary">{{ $expense->category_name }}</span></td>
                        </tr>
                        <tr>
                            <th>Ngày phát sinh:</th>
                            <td>{{ $expense->expense_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Số tiền:</th>
                            <td><h4 class="text-danger mb-0">{{ number_format($expense->amount) }} đ</h4></td>
                        </tr>
                        <tr>
                            <th>Phương thức thanh toán:</th>
                            <td>
                                @if($expense->payment_method)
                                    {{ ucfirst(str_replace('_', ' ', $expense->payment_method)) }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @if($expense->receipt_number)
                        <tr>
                            <th>Số hóa đơn/chứng từ:</th>
                            <td>{{ $expense->receipt_number }}</td>
                        </tr>
                        @endif
                        @if($expense->receipt_file)
                        <tr>
                            <th>File hóa đơn:</th>
                            <td>
                                <a href="{{ asset('storage/' . $expense->receipt_file) }}" target="_blank" class="btn btn-sm btn-info">
                                    <i class="bi bi-file-earmark"></i> Xem file
                                </a>
                            </td>
                        </tr>
                        @endif
                        @if($expense->description)
                        <tr>
                            <th>Mô tả:</th>
                            <td>{{ $expense->description }}</td>
                        </tr>
                        @endif
                        @if($expense->notes)
                        <tr>
                            <th>Ghi chú:</th>
                            <td>{{ $expense->notes }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Người tạo:</th>
                            <td>{{ $expense->creator->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Ngày tạo:</th>
                            <td>{{ $expense->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Cập nhật lần cuối:</th>
                            <td>{{ $expense->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

