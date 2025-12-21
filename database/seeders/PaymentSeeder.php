<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy nhân viên để làm user_id cho payment
        $staff = User::whereIn('role', ['admin', 'staff', 'cashier'])->first();
        if (!$staff) {
            $this->command->warn('Không có nhân viên nào! Vui lòng tạo nhân viên trước.');
            return;
        }
        
        $today = Carbon::now();
        $startOfMonth = Carbon::now()->startOfMonth();
        $yesterday = $today->copy()->subDay(); // Tính đến hôm qua
        
        // Lấy các orders có sẵn (nếu có)
        $existingOrders = Order::pluck('id')->toArray();
        
        $createdCount = 0;
        $totalAmount = 0;
        
        // Tạo payments từ đầu tháng đến hôm qua
        $currentDate = $startOfMonth->copy();
        
        while ($currentDate->lte($yesterday)) {
            // Bỏ qua Chủ nhật (ngày 0)
            if ($currentDate->dayOfWeek !== Carbon::SUNDAY) {
                // Tạo 5-15 payments mỗi ngày
                $paymentsPerDay = rand(5, 15);
                
                for ($i = 0; $i < $paymentsPerDay; $i++) {
                    // Random giờ trong ngày (8:00 - 22:00)
                    $hour = rand(8, 22);
                    $minute = rand(0, 59);
                    $createdAt = $currentDate->copy()->setTime($hour, $minute, rand(0, 59));
                    
                    // Số tiền ngẫu nhiên (50,000 - 2,000,000đ)
                    $amount = rand(50000, 2000000);
                    // Làm tròn đến hàng nghìn
                    $amount = round($amount / 1000) * 1000;
                    
                    // Phương thức thanh toán ngẫu nhiên
                    $paymentMethods = ['cash', 'bank_transfer', 'momo', 'vnpay', 'bank_card'];
                    $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
                    
                    // Sử dụng order_id có sẵn nếu có, nếu không thì null (có thể cần tạo order trước)
                    $orderId = null;
                    if (!empty($existingOrders)) {
                        $orderId = $existingOrders[array_rand($existingOrders)];
                    }
                    
                    // Tạo payment với status = 'completed'
                    try {
                        $payment = Payment::create([
                            'order_id' => $orderId,
                            'user_id' => $staff->id,
                            'payment_method' => $paymentMethod,
                            'amount' => $amount,
                            'status' => 'completed',
                            'transaction_id' => 'FAKE_' . strtoupper(uniqid()),
                            'created_at' => $createdAt,
                            'updated_at' => $createdAt,
                        ]);
                        
                        $createdCount++;
                        $totalAmount += $amount;
                    } catch (\Exception $e) {
                        // Nếu order_id bị constraint, thử tạo với order_id = 1 hoặc skip
                        if (str_contains($e->getMessage(), 'order_id')) {
                            // Tạo order đơn giản trước
                            try {
                                $order = Order::first();
                                if ($order) {
                                    $payment = Payment::create([
                                        'order_id' => $order->id,
                                        'user_id' => $staff->id,
                                        'payment_method' => $paymentMethod,
                                        'amount' => $amount,
                                        'status' => 'completed',
                                        'transaction_id' => 'FAKE_' . strtoupper(uniqid()),
                                        'created_at' => $createdAt,
                                        'updated_at' => $createdAt,
                                    ]);
                                    $createdCount++;
                                    $totalAmount += $amount;
                                }
                            } catch (\Exception $e2) {
                                // Skip nếu không tạo được
                            }
                        }
                    }
                }
            }
            
            $currentDate->addDay();
        }
        
        $this->command->info("Đã tạo {$createdCount} payments từ {$startOfMonth->format('d/m/Y')} đến {$yesterday->format('d/m/Y')}");
        $this->command->info("Tổng doanh thu: " . number_format($totalAmount) . " đ");
    }
}

