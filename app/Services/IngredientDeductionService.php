<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\IngredientStock;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IngredientDeductionService
{
    /**
     * Trừ nguyên liệu khi đơn hàng được xác nhận/chế biến
     * 
     * @param Order $order
     * @return bool
     */
    public function deductIngredientsForOrder(Order $order): bool
    {
        DB::beginTransaction();
        try {
            // Kiểm tra xem đã trừ nguyên liệu chưa (tránh trừ 2 lần)
            if ($order->ingredients_deducted) {
                Log::info("Order {$order->id} đã trừ nguyên liệu rồi");
                return true;
            }

            $orderItems = $order->orderItems()->with('menuItem.ingredients')->get();
            
            if ($orderItems->isEmpty()) {
                Log::warning("Order {$order->id} không có món nào");
                DB::rollBack();
                return false;
            }

            // Kiểm tra xem có món nào có nguyên liệu không
            $hasIngredients = false;
            foreach ($orderItems as $orderItem) {
                if ($orderItem->menuItem && $orderItem->menuItem->ingredients->isNotEmpty()) {
                    $hasIngredients = true;
                    break;
                }
            }

            // Nếu không có món nào có nguyên liệu, đánh dấu đã xử lý nhưng không trừ gì
            if (!$hasIngredients) {
                $order->update(['ingredients_deducted' => true]);
                DB::commit();
                Log::info("Order {$order->id} không có món nào cần nguyên liệu");
                return true;
            }

            $deductionRecords = [];

            foreach ($orderItems as $orderItem) {
                $menuItem = $orderItem->menuItem;
                $quantity = $orderItem->quantity; // Số lượng món được đặt

                if (!$menuItem) {
                    Log::warning("OrderItem {$orderItem->id} không có menuItem");
                    continue;
                }

                // Lấy danh sách nguyên liệu của món
                $ingredients = $menuItem->ingredients;

                foreach ($ingredients as $ingredient) {
                    // Số lượng nguyên liệu cần trừ = số lượng món × số lượng nguyên liệu/món
                    $requiredQuantity = $quantity * $ingredient->pivot->quantity;
                    
                    // Kiểm tra tồn kho
                    $currentStock = $ingredient->getCurrentStock();
                    
                    if ($currentStock < $requiredQuantity) {
                        Log::error("Không đủ nguyên liệu: {$ingredient->name}. Cần: {$requiredQuantity}, Có: {$currentStock}");
                        DB::rollBack();
                        throw new \Exception("Không đủ nguyên liệu: {$ingredient->name}. Cần: {$requiredQuantity} {$ingredient->unit}, Hiện có: {$currentStock} {$ingredient->unit}");
                    }

                    // Tạo bản ghi xuất kho
                    $stock = IngredientStock::create([
                        'ingredient_id' => $ingredient->id,
                        'type' => 'export',
                        'quantity' => $requiredQuantity,
                        'unit_price' => $ingredient->unit_price,
                        'stock_date' => now()->format('Y-m-d'),
                        'notes' => "Xuất kho cho đơn hàng #{$order->order_number} - Món: {$menuItem->name} (x{$quantity})",
                        'created_by' => auth()->id(),
                    ]);

                    $deductionRecords[] = [
                        'ingredient' => $ingredient->name,
                        'quantity' => $requiredQuantity,
                        'unit' => $ingredient->unit,
                    ];

                    Log::info("Đã trừ {$requiredQuantity} {$ingredient->unit} {$ingredient->name} cho đơn hàng #{$order->order_number}");
                }
            }

            // Đánh dấu đã trừ nguyên liệu
            $order->update(['ingredients_deducted' => true]);

            DB::commit();

            Log::info("Đã trừ nguyên liệu cho đơn hàng #{$order->order_number}", [
                'order_id' => $order->id,
                'deductions' => $deductionRecords
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Lỗi khi trừ nguyên liệu cho đơn hàng #{$order->order_number}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Hoàn trả nguyên liệu khi đơn hàng bị hủy
     * 
     * @param Order $order
     * @return bool
     */
    public function returnIngredientsForOrder(Order $order): bool
    {
        if (!$order->ingredients_deducted) {
            return true; // Chưa trừ thì không cần hoàn trả
        }

        DB::beginTransaction();
        try {
            $orderItems = $order->orderItems()->with('menuItem.ingredients')->get();
            
            foreach ($orderItems as $orderItem) {
                $menuItem = $orderItem->menuItem;
                $quantity = $orderItem->quantity;

                if (!$menuItem) {
                    continue;
                }

                $ingredients = $menuItem->ingredients;

                foreach ($ingredients as $ingredient) {
                    $returnQuantity = $quantity * $ingredient->pivot->quantity;

                    // Tạo bản ghi điều chỉnh (tăng lại)
                    IngredientStock::create([
                        'ingredient_id' => $ingredient->id,
                        'type' => 'adjustment',
                        'quantity' => $returnQuantity,
                        'unit_price' => $ingredient->unit_price,
                        'stock_date' => now()->format('Y-m-d'),
                        'notes' => "Hoàn trả nguyên liệu do hủy đơn hàng #{$order->order_number} - Món: {$menuItem->name} (x{$quantity})",
                        'created_by' => auth()->id(),
                    ]);
                }
            }

            // Đánh dấu đã hoàn trả
            $order->update(['ingredients_deducted' => false]);

            DB::commit();
            Log::info("Đã hoàn trả nguyên liệu cho đơn hàng #{$order->order_number}");

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Lỗi khi hoàn trả nguyên liệu: " . $e->getMessage());
            throw $e;
        }
    }
}

