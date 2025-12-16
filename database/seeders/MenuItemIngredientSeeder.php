<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemIngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ingredients = Ingredient::all()->keyBy('name');
        $menuItems = MenuItem::all()->keyBy('slug');

        // Gán nguyên liệu cho từng món
        $assignments = [
            // Khai Vị
            'goi-cuon-tom-thit' => [
                'Tôm sú' => 0.15, 'Thịt heo ba chỉ' => 0.1, 'Bánh tráng' => 3, 
                'Rau xà lách' => 0.05, 'Rau thơm' => 0.02, 'Nước mắm' => 0.05
            ],
            'nem-nuong-nha-trang' => [
                'Thịt heo ba chỉ' => 0.15, 'Bánh tráng' => 2, 'Rau thơm' => 0.03, 'Nước mắm' => 0.05
            ],
            'cha-gio' => [
                'Thịt heo ba chỉ' => 0.12, 'Bánh tráng' => 2, 'Trứng gà' => 1, 'Rau thơm' => 0.02
            ],
            'salad-ca-ngu' => [
                'Cá basa' => 0.2, 'Rau xà lách' => 0.1, 'Cà chua' => 0.15, 
                'Hành tây' => 0.05, 'Chanh' => 0.05
            ],
            
            // Món Chính
            'com-chien-duong-chau' => [
                'Gạo tẻ' => 0.2, 'Trứng gà' => 2, 'Thịt heo ba chỉ' => 0.1, 
                'Hành tây' => 0.05, 'Dầu ăn' => 0.05
            ],
            'pho-bo' => [
                'Phở khô' => 1, 'Thịt bò' => 0.15, 'Hành tây' => 0.05, 
                'Rau thơm' => 0.03, 'Chanh' => 0.05, 'Nước mắm' => 0.05
            ],
            'bun-bo-hue' => [
                'Bún tươi' => 0.2, 'Thịt bò' => 0.15, 'Hành tây' => 0.05, 
                'Rau thơm' => 0.03, 'Chanh' => 0.05, 'Ớt' => 0.02
            ],
            'com-ga' => [
                'Gạo tẻ' => 0.2, 'Thịt gà' => 0.2, 'Hành tây' => 0.05, 'Nước mắm' => 0.05
            ],
            'banh-mi-thit-nuong' => [
                'Bánh mì' => 1, 'Thịt heo ba chỉ' => 0.1, 'Rau thơm' => 0.02, 
                'Tương ớt' => 0.05, 'Tương đen' => 0.05
            ],
            
            // Món Nướng
            'thit-nuong-xien-que' => [
                'Thịt heo ba chỉ' => 0.2, 'Hành tây' => 0.05, 'Tỏi' => 0.02, 
                'Ớt' => 0.02, 'Nước mắm' => 0.05
            ],
            'tom-nuong-muoi-ot' => [
                'Tôm sú' => 0.3, 'Ớt' => 0.03, 'Tỏi' => 0.02, 'Chanh' => 0.05, 'Muối' => 0.01
            ],
            'ca-nuong-giay-bac' => [
                'Cá basa' => 0.3, 'Hành tây' => 0.05, 'Tỏi' => 0.02, 
                'Chanh' => 0.05, 'Rau thơm' => 0.03
            ],
            'ga-nuong-mat-ong' => [
                'Thịt gà' => 0.4, 'Đường' => 0.05, 'Tỏi' => 0.02, 
                'Chanh' => 0.05, 'Nước mắm' => 0.05
            ],
            
            // Lẩu
            'lau-thai' => [
                'Tôm sú' => 0.2, 'Cá basa' => 0.2, 'Rau xà lách' => 0.15, 
                'Cà chua' => 0.2, 'Chanh' => 0.1, 'Ớt' => 0.05
            ],
            'lau-cua' => [
                'Cua biển' => 0.5, 'Rau xà lách' => 0.15, 'Rau thơm' => 0.05, 
                'Cà chua' => 0.2, 'Hành tây' => 0.1
            ],
            'lau-tom' => [
                'Tôm sú' => 0.4, 'Rau xà lách' => 0.15, 'Rau thơm' => 0.05, 'Cà chua' => 0.2
            ],
            'lau-bo' => [
                'Thịt bò' => 0.3, 'Rau xà lách' => 0.15, 'Rau thơm' => 0.05, 
                'Cà chua' => 0.2, 'Hành tây' => 0.1
            ],
            
            // Đồ Uống
            'nuoc-ngot' => [
                'Coca Cola' => 1, 'Pepsi' => 1, '7Up' => 1
            ],
            'nuoc-ep-trai-cay' => [
                'Chanh' => 0.2, 'Đường' => 0.05, 'Nước suối' => 0.5
            ],
            'tra-da' => [
                'Trà túi lọc' => 0.1, 'Đường' => 0.02, 'Nước suối' => 0.5
            ],
            'ca-phe-den' => [
                'Cà phê nguyên chất' => 0.05, 'Đường' => 0.02, 'Nước suối' => 0.3
            ],
            'sinh-to' => [
                'Sữa tươi' => 0.2, 'Đường' => 0.05, 'Nước suối' => 0.2
            ],
            
            // Tráng Miệng
            'che-thai' => [
                'Đường' => 0.1, 'Nước dừa' => 0.3, 'Nước suối' => 0.2
            ],
            'kem' => [
                'Kem' => 1
            ],
            'banh-flan' => [
                'Trứng gà' => 2, 'Sữa tươi' => 0.2, 'Đường' => 0.05
            ],
            'trai-cay-tuoi' => [
                'Chanh' => 0.1, 'Cà chua' => 0.1
            ],
        ];

        foreach ($assignments as $slug => $ingredientData) {
            if (!isset($menuItems[$slug])) {
                $this->command->warn("Menu item với slug '{$slug}' không tồn tại!");
                continue;
            }

            $menuItem = $menuItems[$slug];
            $syncData = [];

            foreach ($ingredientData as $ingredientName => $quantity) {
                if (!isset($ingredients[$ingredientName])) {
                    $this->command->warn("Nguyên liệu '{$ingredientName}' không tồn tại cho món '{$menuItem->name}'!");
                    continue;
                }

                $syncData[$ingredients[$ingredientName]->id] = ['quantity' => $quantity];
            }

            if (!empty($syncData)) {
                $menuItem->ingredients()->sync($syncData);
                $this->command->info("Đã gán " . count($syncData) . " nguyên liệu cho món: {$menuItem->name}");
            }
        }

        $this->command->info('Hoàn thành gán nguyên liệu cho menu items!');
    }
}
