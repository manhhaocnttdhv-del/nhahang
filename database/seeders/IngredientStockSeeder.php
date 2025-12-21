<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\IngredientStock;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class IngredientStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy admin để làm created_by
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $this->command->warn('Không có admin! Vui lòng tạo admin trước.');
            return;
        }
        
        // Lấy danh sách nguyên liệu
        $ingredients = Ingredient::where('status', 'active')->get();
        
        if ($ingredients->isEmpty()) {
            $this->command->warn('Không có nguyên liệu nào! Vui lòng chạy IngredientSeeder trước.');
            return;
        }
        
        $today = Carbon::now();
        $startOfMonth = Carbon::now()->startOfMonth();
        $yesterday = $today->copy()->subDay(); // Tính đến hôm qua
        
        $createdCount = 0;
        $totalAmount = 0;
        
        // Tạo nhập kho từ đầu tháng đến hôm qua
        // Mỗi tuần nhập 2-3 lần, mỗi lần nhập 5-15 nguyên liệu
        $currentDate = $startOfMonth->copy();
        
        while ($currentDate->lte($yesterday)) {
            // Chọn ngẫu nhiên 2-3 ngày mỗi tuần để nhập hàng (trừ Chủ nhật)
            if ($currentDate->dayOfWeek !== Carbon::SUNDAY && rand(1, 7) <= 3) {
                // Mỗi lần nhập 5-15 nguyên liệu
                $ingredientsToImport = $ingredients->random(rand(5, min(15, $ingredients->count())));
                
                foreach ($ingredientsToImport as $ingredient) {
                    // Random giờ trong ngày (8:00 - 17:00)
                    $hour = rand(8, 17);
                    $minute = rand(0, 59);
                    $createdAt = $currentDate->copy()->setTime($hour, $minute, rand(0, 59));
                    
                    // Số lượng nhập (phụ thuộc vào đơn vị)
                    $quantity = $this->getRandomQuantity($ingredient->unit);
                    
                    // Giá mua có thể dao động ±10% so với unit_price mặc định
                    $basePrice = $ingredient->unit_price ?? 0;
                    $priceVariation = $basePrice * (rand(90, 110) / 100);
                    $unitPrice = round($priceVariation / 1000) * 1000; // Làm tròn đến hàng nghìn
                    
                    // Kiểm tra xem đã có nhập kho trong ngày này chưa (tránh trùng)
                    $existing = IngredientStock::where('ingredient_id', $ingredient->id)
                        ->where('type', 'import')
                        ->whereDate('stock_date', $currentDate->format('Y-m-d'))
                        ->first();
                    
                    if (!$existing) {
                        try {
                            $stock = IngredientStock::create([
                                'ingredient_id' => $ingredient->id,
                                'type' => 'import',
                                'quantity' => $quantity,
                                'unit_price' => $unitPrice,
                                'stock_date' => $currentDate->format('Y-m-d'),
                                'notes' => 'Nhập kho định kỳ - ' . $currentDate->format('d/m/Y'),
                                'created_by' => $admin->id,
                                'created_at' => $createdAt,
                                'updated_at' => $createdAt,
                            ]);
                            
                            $createdCount++;
                            $totalAmount += $stock->total_amount;
                        } catch (\Exception $e) {
                            // Skip nếu có lỗi
                        }
                    }
                }
            }
            
            $currentDate->addDay();
        }
        
        $this->command->info("Đã tạo {$createdCount} bản ghi nhập kho từ {$startOfMonth->format('d/m/Y')} đến {$yesterday->format('d/m/Y')}");
        $this->command->info("Tổng chi phí nguyên liệu: " . number_format($totalAmount) . " đ");
    }
    
    /**
     * Lấy số lượng ngẫu nhiên dựa trên đơn vị
     */
    private function getRandomQuantity($unit)
    {
        switch (strtolower($unit)) {
            case 'kg':
            case 'kilogram':
                return round(rand(10, 100) / 10, 2); // 1.0 - 10.0 kg
            case 'g':
            case 'gram':
                return round(rand(100, 5000) / 100, 2); // 1.0 - 50.0 g
            case 'l':
            case 'liter':
            case 'lít':
                return round(rand(5, 50) / 10, 2); // 0.5 - 5.0 l
            case 'cái':
            case 'con':
            case 'quả':
            case 'trái':
                return rand(10, 100); // 10 - 100 cái
            case 'gói':
            case 'hộp':
            case 'chai':
                return rand(5, 50); // 5 - 50 gói/hộp/chai
            case 'bó':
            case 'cành':
                return rand(5, 30); // 5 - 30 bó/cành
            default:
                return round(rand(10, 100) / 10, 2); // Mặc định
        }
    }
}

