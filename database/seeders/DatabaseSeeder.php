<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\MenuItem;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin',
            'email' => 'admin@nhahang.com',
            'password' => Hash::make('password'),
            'phone' => '0123456789',
            'role' => 'admin',
        ]);

        // Create Staff User
        User::create([
            'name' => 'Nhân Viên',
            'email' => 'staff@nhahang.com',
            'password' => Hash::make('password'),
            'phone' => '0987654321',
            'role' => 'staff',
        ]);

        // Create Customer User
        User::create([
            'name' => 'Khách Hàng',
            'email' => 'customer@nhahang.com',
            'password' => Hash::make('password'),
            'phone' => '0912345678',
            'role' => 'customer',
        ]);

        // Create Categories
        $categories = [
            ['name' => 'Khai Vị', 'slug' => 'khai-vi', 'description' => 'Các món khai vị hấp dẫn', 'sort_order' => 1],
            ['name' => 'Món Chính', 'slug' => 'mon-chinh', 'description' => 'Các món chính đặc sắc', 'sort_order' => 2],
            ['name' => 'Món Nướng', 'slug' => 'mon-nuong', 'description' => 'Các món nướng thơm ngon', 'sort_order' => 3],
            ['name' => 'Lẩu', 'slug' => 'lau', 'description' => 'Các loại lẩu đa dạng', 'sort_order' => 4],
            ['name' => 'Đồ Uống', 'slug' => 'do-uong', 'description' => 'Nước uống giải khát', 'sort_order' => 5],
            ['name' => 'Tráng Miệng', 'slug' => 'trang-mieng', 'description' => 'Các món tráng miệng ngọt ngào', 'sort_order' => 6],
        ];

        $categoryIds = [];
        foreach ($categories as $cat) {
            $category = Category::create($cat);
            $categoryIds[] = $category->id;
        }

        // Seed Ingredients first (needed for menu items)
        $this->call(IngredientSeeder::class);

        // Get all ingredients for assignment
        $ingredients = Ingredient::all()->keyBy('name');
        
        // Create Menu Items
        $menuItemsData = [
            // Khai Vị
            ['category_id' => $categoryIds[0], 'name' => 'Gỏi Cuốn Tôm Thịt', 'slug' => 'goi-cuon-tom-thit', 'description' => 'Gỏi cuốn tươi ngon với tôm và thịt', 'price' => 45000, 'status' => 'available', 'ingredients' => [
                'Tôm sú' => 0.15, 'Thịt heo ba chỉ' => 0.1, 'Bánh tráng' => 3, 'Rau xà lách' => 0.05, 'Rau thơm' => 0.02, 'Nước mắm' => 0.05
            ]],
            ['category_id' => $categoryIds[0], 'name' => 'Nem Nướng Nha Trang', 'slug' => 'nem-nuong-nha-trang', 'description' => 'Nem nướng đặc sản Nha Trang', 'price' => 65000, 'status' => 'available', 'ingredients' => [
                'Thịt heo ba chỉ' => 0.15, 'Bánh tráng' => 2, 'Rau thơm' => 0.03, 'Nước mắm' => 0.05
            ]],
            ['category_id' => $categoryIds[0], 'name' => 'Chả Giò', 'slug' => 'cha-gio', 'description' => 'Chả giò giòn rụm, thơm ngon', 'price' => 55000, 'status' => 'available', 'ingredients' => [
                'Thịt heo ba chỉ' => 0.12, 'Bánh tráng' => 2, 'Trứng gà' => 1, 'Rau thơm' => 0.02
            ]],
            ['category_id' => $categoryIds[0], 'name' => 'Salad Cá Ngừ', 'slug' => 'salad-ca-ngu', 'description' => 'Salad cá ngừ tươi, rau xanh', 'price' => 75000, 'status' => 'available', 'ingredients' => [
                'Cá basa' => 0.2, 'Rau xà lách' => 0.1, 'Cà chua' => 0.15, 'Hành tây' => 0.05, 'Chanh' => 0.05
            ]],
            
            // Món Chính
            ['category_id' => $categoryIds[1], 'name' => 'Cơm Chiên Dương Châu', 'slug' => 'com-chien-duong-chau', 'description' => 'Cơm chiên thập cẩm đầy đủ', 'price' => 85000, 'status' => 'available', 'ingredients' => [
                'Gạo tẻ' => 0.2, 'Trứng gà' => 2, 'Thịt heo ba chỉ' => 0.1, 'Hành tây' => 0.05, 'Dầu ăn' => 0.05
            ]],
            ['category_id' => $categoryIds[1], 'name' => 'Phở Bò', 'slug' => 'pho-bo', 'description' => 'Phở bò truyền thống', 'price' => 75000, 'status' => 'available', 'ingredients' => [
                'Phở khô' => 1, 'Thịt bò' => 0.15, 'Hành tây' => 0.05, 'Rau thơm' => 0.03, 'Chanh' => 0.05, 'Nước mắm' => 0.05
            ]],
            ['category_id' => $categoryIds[1], 'name' => 'Bún Bò Huế', 'slug' => 'bun-bo-hue', 'description' => 'Bún bò đậm đà hương vị Huế', 'price' => 80000, 'status' => 'available', 'ingredients' => [
                'Bún tươi' => 0.2, 'Thịt bò' => 0.15, 'Hành tây' => 0.05, 'Rau thơm' => 0.03, 'Chanh' => 0.05, 'Ớt' => 0.02
            ]],
            ['category_id' => $categoryIds[1], 'name' => 'Cơm Gà', 'slug' => 'com-ga', 'description' => 'Cơm gà thơm ngon, đậm đà', 'price' => 90000, 'status' => 'available', 'ingredients' => [
                'Gạo tẻ' => 0.2, 'Thịt gà' => 0.2, 'Hành tây' => 0.05, 'Nước mắm' => 0.05
            ]],
            ['category_id' => $categoryIds[1], 'name' => 'Bánh Mì Thịt Nướng', 'slug' => 'banh-mi-thit-nuong', 'description' => 'Bánh mì thịt nướng đặc sản', 'price' => 45000, 'status' => 'available', 'ingredients' => [
                'Bánh mì' => 1, 'Thịt heo ba chỉ' => 0.1, 'Rau thơm' => 0.02, 'Tương ớt' => 0.05, 'Tương đen' => 0.05
            ]],
            
            // Món Nướng
            ['category_id' => $categoryIds[2], 'name' => 'Thịt Nướng Xiên Que', 'slug' => 'thit-nuong-xien-que', 'description' => 'Thịt nướng xiên que thơm lừng', 'price' => 120000, 'status' => 'available', 'ingredients' => [
                'Thịt heo ba chỉ' => 0.2, 'Hành tây' => 0.05, 'Tỏi' => 0.02, 'Ớt' => 0.02, 'Nước mắm' => 0.05
            ]],
            ['category_id' => $categoryIds[2], 'name' => 'Tôm Nướng Muối Ớt', 'slug' => 'tom-nuong-muoi-ot', 'description' => 'Tôm nướng muối ớt cay nồng', 'price' => 180000, 'status' => 'available', 'ingredients' => [
                'Tôm sú' => 0.3, 'Ớt' => 0.03, 'Tỏi' => 0.02, 'Chanh' => 0.05, 'Muối' => 0.01
            ]],
            ['category_id' => $categoryIds[2], 'name' => 'Cá Nướng Giấy Bạc', 'slug' => 'ca-nuong-giay-bac', 'description' => 'Cá nướng giấy bạc thơm ngon', 'price' => 150000, 'status' => 'available', 'ingredients' => [
                'Cá basa' => 0.3, 'Hành tây' => 0.05, 'Tỏi' => 0.02, 'Chanh' => 0.05, 'Rau thơm' => 0.03
            ]],
            ['category_id' => $categoryIds[2], 'name' => 'Gà Nướng Mật Ong', 'slug' => 'ga-nuong-mat-ong', 'description' => 'Gà nướng mật ong ngọt ngào', 'price' => 200000, 'status' => 'available', 'ingredients' => [
                'Thịt gà' => 0.4, 'Đường' => 0.05, 'Tỏi' => 0.02, 'Chanh' => 0.05, 'Nước mắm' => 0.05
            ]],
            
            // Lẩu
            ['category_id' => $categoryIds[3], 'name' => 'Lẩu Thái', 'slug' => 'lau-thai', 'description' => 'Lẩu Thái chua cay đậm đà', 'price' => 250000, 'status' => 'available', 'ingredients' => [
                'Tôm sú' => 0.2, 'Cá basa' => 0.2, 'Rau xà lách' => 0.15, 'Cà chua' => 0.2, 'Chanh' => 0.1, 'Ớt' => 0.05
            ]],
            ['category_id' => $categoryIds[3], 'name' => 'Lẩu Cua', 'slug' => 'lau-cua', 'description' => 'Lẩu cua béo ngậy, thơm ngon', 'price' => 300000, 'status' => 'available', 'ingredients' => [
                'Cua biển' => 0.5, 'Rau xà lách' => 0.15, 'Rau thơm' => 0.05, 'Cà chua' => 0.2, 'Hành tây' => 0.1
            ]],
            ['category_id' => $categoryIds[3], 'name' => 'Lẩu Tôm', 'slug' => 'lau-tom', 'description' => 'Lẩu tôm ngọt thanh', 'price' => 280000, 'status' => 'available', 'ingredients' => [
                'Tôm sú' => 0.4, 'Rau xà lách' => 0.15, 'Rau thơm' => 0.05, 'Cà chua' => 0.2
            ]],
            ['category_id' => $categoryIds[3], 'name' => 'Lẩu Bò', 'slug' => 'lau-bo', 'description' => 'Lẩu bò đậm đà', 'price' => 320000, 'status' => 'available', 'ingredients' => [
                'Thịt bò' => 0.3, 'Rau xà lách' => 0.15, 'Rau thơm' => 0.05, 'Cà chua' => 0.2, 'Hành tây' => 0.1
            ]],
            
            // Đồ Uống
            ['category_id' => $categoryIds[4], 'name' => 'Nước Ngọt', 'slug' => 'nuoc-ngot', 'description' => 'Coca, Pepsi, 7Up', 'price' => 25000, 'status' => 'available', 'ingredients' => [
                'Coca Cola' => 1, 'Pepsi' => 1, '7Up' => 1
            ]],
            ['category_id' => $categoryIds[4], 'name' => 'Nước Ép Trái Cây', 'slug' => 'nuoc-ep-trai-cay', 'description' => 'Nước ép cam, táo, dưa hấu', 'price' => 45000, 'status' => 'available', 'ingredients' => [
                'Chanh' => 0.2, 'Đường' => 0.05, 'Nước suối' => 0.5
            ]],
            ['category_id' => $categoryIds[4], 'name' => 'Trà Đá', 'slug' => 'tra-da', 'description' => 'Trà đá mát lạnh', 'price' => 15000, 'status' => 'available', 'ingredients' => [
                'Trà túi lọc' => 0.1, 'Đường' => 0.02, 'Nước suối' => 0.5
            ]],
            ['category_id' => $categoryIds[4], 'name' => 'Cà Phê Đen', 'slug' => 'ca-phe-den', 'description' => 'Cà phê đen đậm đà', 'price' => 30000, 'status' => 'available', 'ingredients' => [
                'Cà phê nguyên chất' => 0.05, 'Đường' => 0.02, 'Nước suối' => 0.3
            ]],
            ['category_id' => $categoryIds[4], 'name' => 'Sinh Tố', 'slug' => 'sinh-to', 'description' => 'Sinh tố các loại', 'price' => 55000, 'status' => 'available', 'ingredients' => [
                'Sữa tươi' => 0.2, 'Đường' => 0.05, 'Nước suối' => 0.2
            ]],
            
            // Tráng Miệng
            ['category_id' => $categoryIds[5], 'name' => 'Chè Thái', 'slug' => 'che-thai', 'description' => 'Chè Thái mát lạnh', 'price' => 35000, 'status' => 'available', 'ingredients' => [
                'Đường' => 0.1, 'Nước dừa' => 0.3, 'Nước suối' => 0.2
            ]],
            ['category_id' => $categoryIds[5], 'name' => 'Kem', 'slug' => 'kem', 'description' => 'Kem các vị', 'price' => 40000, 'status' => 'available', 'ingredients' => [
                'Kem' => 1
            ]],
            ['category_id' => $categoryIds[5], 'name' => 'Bánh Flan', 'slug' => 'banh-flan', 'description' => 'Bánh flan mềm mịn', 'price' => 30000, 'status' => 'available', 'ingredients' => [
                'Trứng gà' => 2, 'Sữa tươi' => 0.2, 'Đường' => 0.05
            ]],
            ['category_id' => $categoryIds[5], 'name' => 'Trái Cây Tươi', 'slug' => 'trai-cay-tuoi', 'description' => 'Trái cây tươi theo mùa', 'price' => 50000, 'status' => 'available', 'ingredients' => [
                'Chanh' => 0.1, 'Cà chua' => 0.1
            ]],
        ];

        foreach ($menuItemsData as $itemData) {
            $ingredientsData = $itemData['ingredients'] ?? [];
            unset($itemData['ingredients']);
            
            $menuItem = MenuItem::create($itemData);
            
            // Assign ingredients
            if (!empty($ingredientsData)) {
                $syncData = [];
                foreach ($ingredientsData as $ingredientName => $quantity) {
                    if (isset($ingredients[$ingredientName])) {
                        $syncData[$ingredients[$ingredientName]->id] = ['quantity' => $quantity];
                    }
                }
                if (!empty($syncData)) {
                    $menuItem->ingredients()->sync($syncData);
                }
            }
        }

        // Create Tables
        $tables = [
            ['name' => 'Bàn 1', 'number' => 'T001', 'capacity' => 2, 'area' => 'Tầng 1', 'status' => 'available'],
            ['name' => 'Bàn 2', 'number' => 'T002', 'capacity' => 4, 'area' => 'Tầng 1', 'status' => 'available'],
            ['name' => 'Bàn 3', 'number' => 'T003', 'capacity' => 4, 'area' => 'Tầng 1', 'status' => 'available'],
            ['name' => 'Bàn 4', 'number' => 'T004', 'capacity' => 6, 'area' => 'Tầng 1', 'status' => 'available'],
            ['name' => 'Bàn 5', 'number' => 'T005', 'capacity' => 2, 'area' => 'Tầng 1', 'status' => 'available'],
            ['name' => 'Bàn 6', 'number' => 'T006', 'capacity' => 4, 'area' => 'Tầng 2', 'status' => 'available'],
            ['name' => 'Bàn 7', 'number' => 'T007', 'capacity' => 6, 'area' => 'Tầng 2', 'status' => 'available'],
            ['name' => 'Bàn 8', 'number' => 'T008', 'capacity' => 8, 'area' => 'Tầng 2', 'status' => 'available'],
            ['name' => 'Bàn 9', 'number' => 'T009', 'capacity' => 4, 'area' => 'Tầng 2', 'status' => 'available'],
            ['name' => 'Bàn 10', 'number' => 'T010', 'capacity' => 10, 'area' => 'Phòng VIP', 'status' => 'available'],
            ['name' => 'Bàn 11', 'number' => 'T011', 'capacity' => 4, 'area' => 'Gần cửa sổ', 'status' => 'available'],
            ['name' => 'Bàn 12', 'number' => 'T012', 'capacity' => 6, 'area' => 'Gần cửa sổ', 'status' => 'available'],
        ];

        foreach ($tables as $table) {
            Table::create($table);
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin: admin@nhahang.com / password');
        $this->command->info('Staff: staff@nhahang.com / password');
        $this->command->info('Customer: customer@nhahang.com / password');
    }
}
