@extends('layouts.app')

@section('title', 'Thanh Toán QR Code')

@section('content')
<div class="container my-5">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-secondary mb-3">
                <i class="bi bi-arrow-left me-2"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header text-center py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h3 class="mb-0">
                        <i class="bi bi-qr-code me-2"></i> Thanh Toán QR Code
                    </h3>
                    <p class="mb-0 mt-2 opacity-75">Đơn hàng #{{ $order->order_number }}</p>
                </div>
                
                <div class="card-body p-5 text-center">
                    <!-- QR Code -->
                    <div class="qr-code-wrapper mb-4">
                        <div class="qr-code-container" style="
                            background: white;
                            padding: 20px;
                            border-radius: 15px;
                            display: inline-block;
                            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                            border: 3px solid #f0f0f0;
                        ">
                            <div style="width: 300px; height: 300px; display: inline-block;">
                                {!! $qrCodeSvg !!}
                            </div>
                        </div>
                    </div>

                    <!-- Amount -->
                    <div class="amount-display mb-4">
                        <p class="text-muted mb-2">Số tiền cần thanh toán</p>
                        <h2 class="price-tag mb-0" style="font-size: 2.5rem; font-weight: 900;">
                            {{ number_format($remainingAmount) }} đ
                        </h2>
                    </div>

                    <!-- Instructions -->
                    <div class="instructions mb-4" style="background: #f8f9fa; padding: 20px; border-radius: 15px;">
                        <h5 class="mb-3" style="color: var(--dark-color); font-weight: 700;">
                            <i class="bi bi-info-circle me-2"></i> Hướng Dẫn Thanh Toán
                        </h5>
                        <ol class="text-start" style="line-height: 2;">
                            <li>Mở ứng dụng ngân hàng hoặc ví điện tử (MoMo, VNPay, v.v.)</li>
                            <li>Chọn chức năng quét QR code</li>
                            <li>Quét mã QR ở trên</li>
                            <li>Nhập số tiền: <strong>{{ number_format($remainingAmount) }} đ</strong></li>
                            <li>Xác nhận thanh toán</li>
                            <li>Nhập mã giao dịch vào form bên dưới</li>
                        </ol>
                    </div>

                    <!-- Payment Form -->
                    <form action="{{ route('payments.qr.process', $order->id) }}" method="POST" class="payment-form">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label text-start d-block mb-2" style="font-weight: 600;">
                                <i class="bi bi-phone me-2"></i> Phương Thức Thanh Toán
                            </label>
                            <select name="payment_method" class="form-select form-select-lg" required>
                                <option value="">-- Chọn phương thức --</option>
                                <option value="qr_momo">MoMo QR</option>
                                <option value="qr_vnpay">VNPay QR</option>
                                <option value="qr_bank">Ngân Hàng QR</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-start d-block mb-2" style="font-weight: 600;">
                                <i class="bi bi-receipt me-2"></i> Mã Giao Dịch
                            </label>
                            <input type="text" 
                                   name="transaction_id" 
                                   class="form-control form-control-lg" 
                                   placeholder="Nhập mã giao dịch từ ứng dụng"
                                   required>
                            <small class="form-text text-muted text-start d-block mt-2">
                                <i class="bi bi-info-circle me-1"></i> 
                                Mã giao dịch thường có dạng: TXN123456789 hoặc số tham chiếu từ ứng dụng
                            </small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100" style="
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            border: none;
                            padding: 15px;
                            font-weight: 700;
                            font-size: 1.1rem;
                            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
                        ">
                            <i class="bi bi-check-circle me-2"></i> Xác Nhận Đã Thanh Toán
                        </button>
                    </form>

                    <!-- Order Info -->
                    <div class="mt-4 pt-4 border-top">
                        <div class="row text-start">
                            <div class="col-6">
                                <small class="text-muted d-block">Mã đơn hàng</small>
                                <strong>{{ $order->order_number }}</strong>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-muted d-block">Ngày đặt</small>
                                <strong>{{ $order->created_at->format('d/m/Y H:i') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card mt-4" style="background: #fff3cd; border: 1px solid #ffc107;">
                <div class="card-body">
                    <p class="mb-0 text-center">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Lưu ý:</strong> Sau khi thanh toán, vui lòng nhập mã giao dịch để chúng tôi xác nhận. 
                        Đơn hàng sẽ được cập nhật sau khi nhân viên xác nhận thanh toán.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .qr-code-wrapper {
        animation: fadeInUp 0.6s ease-out;
    }
    
    .qr-code-container {
        position: relative;
    }
    
    .qr-code-container::before {
        content: '';
        position: absolute;
        top: -5px;
        left: -5px;
        right: -5px;
        bottom: -5px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        z-index: -1;
        opacity: 0.1;
    }
    
    .amount-display {
        animation: fadeInUp 0.8s ease-out;
    }
    
    .instructions {
        animation: fadeInUp 1s ease-out;
    }
    
    .payment-form {
        animation: fadeInUp 1.2s ease-out;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .form-select-lg,
    .form-control-lg {
        border-radius: 10px;
        border: 2px solid #e9ecef;
        padding: 12px 15px;
        transition: all 0.3s;
    }
    
    .form-select-lg:focus,
    .form-control-lg:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 40px rgba(102, 126, 234, 0.5) !important;
    }
</style>
@endpush
@endsection

