<?php

namespace Database\Seeders;

use App\Models\Category;
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

        // Create Menu Items
        $menuItems = [
            // Khai Vị
            ['category_id' => $categoryIds[0], 'name' => 'Gỏi Cuốn Tôm Thịt', 'slug' => 'goi-cuon-tom-thit', 'description' => 'Gỏi cuốn tươi ngon với tôm và thịt', 'price' => 45000, 'status' => 'available'],
            ['category_id' => $categoryIds[0], 'name' => 'Nem Nướng Nha Trang', 'slug' => 'nem-nuong-nha-trang', 'description' => 'Nem nướng đặc sản Nha Trang', 'price' => 65000, 'status' => 'available'],
            ['category_id' => $categoryIds[0], 'name' => 'Chả Giò', 'slug' => 'cha-gio', 'description' => 'Chả giò giòn rụm, thơm ngon', 'price' => 55000, 'status' => 'available'],
            ['category_id' => $categoryIds[0], 'name' => 'Salad Cá Ngừ', 'slug' => 'salad-ca-ngu', 'description' => 'Salad cá ngừ tươi, rau xanh', 'price' => 75000, 'status' => 'available'],
            
            // Món Chính
            ['category_id' => $categoryIds[1], 'name' => 'Cơm Chiên Dương Châu', 'slug' => 'com-chien-duong-chau', 'description' => 'Cơm chiên thập cẩm đầy đủ', 'price' => 85000, 'status' => 'available'],
            ['category_id' => $categoryIds[1], 'name' => 'Phở Bò', 'slug' => 'pho-bo', 'description' => 'Phở bò truyền thống', 'price' => 75000, 'status' => 'available'],
            ['category_id' => $categoryIds[1], 'name' => 'Bún Bò Huế', 'slug' => 'bun-bo-hue', 'description' => 'Bún bò đậm đà hương vị Huế', 'price' => 80000, 'status' => 'available'],
            ['category_id' => $categoryIds[1], 'name' => 'Cơm Gà', 'slug' => 'com-ga', 'description' => 'Cơm gà thơm ngon, đậm đà', 'price' => 90000, 'status' => 'available'],
            ['category_id' => $categoryIds[1], 'name' => 'Bánh Mì Thịt Nướng', 'slug' => 'banh-mi-thit-nuong', 'description' => 'Bánh mì thịt nướng đặc sản', 'price' => 45000, 'status' => 'available'],
            
            // Món Nướng
            ['category_id' => $categoryIds[2], 'name' => 'Thịt Nướng Xiên Que', 'slug' => 'thit-nuong-xien-que', 'description' => 'Thịt nướng xiên que thơm lừng', 'price' => 120000, 'status' => 'available'],
            ['category_id' => $categoryIds[2], 'name' => 'Tôm Nướng Muối Ớt', 'slug' => 'tom-nuong-muoi-ot', 'description' => 'Tôm nướng muối ớt cay nồng', 'price' => 180000, 'status' => 'available'],
            ['category_id' => $categoryIds[2], 'name' => 'Cá Nướng Giấy Bạc', 'slug' => 'ca-nuong-giay-bac', 'description' => 'Cá nướng giấy bạc thơm ngon', 'price' => 150000, 'status' => 'available'],
            ['category_id' => $categoryIds[2], 'name' => 'Gà Nướng Mật Ong', 'slug' => 'ga-nuong-mat-ong', 'description' => 'Gà nướng mật ong ngọt ngào', 'price' => 200000, 'status' => 'available'],
            
            // Lẩu
            ['category_id' => $categoryIds[3], 'name' => 'Lẩu Thái', 'slug' => 'lau-thai', 'description' => 'Lẩu Thái chua cay đậm đà', 'price' => 250000, 'status' => 'available'],
            ['category_id' => $categoryIds[3], 'name' => 'Lẩu Cua', 'slug' => 'lau-cua', 'description' => 'Lẩu cua béo ngậy, thơm ngon', 'price' => 300000, 'status' => 'available'],
            ['category_id' => $categoryIds[3], 'name' => 'Lẩu Tôm', 'slug' => 'lau-tom', 'description' => 'Lẩu tôm ngọt thanh', 'price' => 280000, 'status' => 'available'],
            ['category_id' => $categoryIds[3], 'name' => 'Lẩu Bò', 'slug' => 'lau-bo', 'description' => 'Lẩu bò đậm đà', 'price' => 320000, 'status' => 'available'],
            
            // Đồ Uống
            ['category_id' => $categoryIds[4], 'name' => 'Nước Ngọt', 'slug' => 'nuoc-ngot', 'description' => 'Coca, Pepsi, 7Up', 'price' => 25000, 'status' => 'available'],
            ['category_id' => $categoryIds[4], 'name' => 'Nước Ép Trái Cây', 'slug' => 'nuoc-ep-trai-cay', 'description' => 'Nước ép cam, táo, dưa hấu', 'price' => 45000, 'status' => 'available'],
            ['category_id' => $categoryIds[4], 'name' => 'Trà Đá', 'slug' => 'tra-da', 'description' => 'Trà đá mát lạnh', 'price' => 15000, 'status' => 'available'],
            ['category_id' => $categoryIds[4], 'name' => 'Cà Phê Đen', 'slug' => 'ca-phe-den', 'description' => 'Cà phê đen đậm đà', 'price' => 30000, 'status' => 'available'],
            ['category_id' => $categoryIds[4], 'name' => 'Sinh Tố', 'slug' => 'sinh-to', 'description' => 'Sinh tố các loại', 'price' => 55000, 'status' => 'available'],
            
            // Tráng Miệng
            ['category_id' => $categoryIds[5], 'name' => 'Chè Thái', 'slug' => 'che-thai', 'description' => 'Chè Thái mát lạnh', 'price' => 35000, 'status' => 'available'],
            ['category_id' => $categoryIds[5], 'name' => 'Kem', 'slug' => 'kem', 'description' => 'Kem các vị', 'price' => 40000, 'status' => 'available'],
            ['category_id' => $categoryIds[5], 'name' => 'Bánh Flan', 'slug' => 'banh-flan', 'description' => 'Bánh flan mềm mịn', 'price' => 30000, 'status' => 'available'],
            ['category_id' => $categoryIds[5], 'name' => 'Trái Cây Tươi', 'slug' => 'trai-cay-tuoi', 'description' => 'Trái cây tươi theo mùa', 'price' => 50000, 'status' => 'available'],
        ];

        foreach ($menuItems as $item) {
            MenuItem::create($item);
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
