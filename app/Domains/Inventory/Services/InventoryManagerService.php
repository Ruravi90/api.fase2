<?php

namespace App\Domains\Inventory\Services;

use App\Models\PillInventory;
use App\Models\ProductInventory;
use Exception;
use Illuminate\Support\Facades\DB;

class InventoryManagerService
{
    /**
     * Decrement the stock for a given pill.
     *
     * @param int $pillId
     * @param int $count
     * @throws Exception
     */
    public function decrementPill(int $pillId, int $count): void
    {
        $pillInventory = PillInventory::where('pill_id', $pillId)->lockForUpdate()->first();
        
        if (!$pillInventory || $pillInventory->count < $count) {
            throw new Exception("Stock insuficiente para la pastilla ID: {$pillId}", 422);
        }
        
        $pillInventory->decrement('count', $count);
        $this->logTransaction('pill', $pillId, -$count, 'Sale deduction');
    }

    /**
     * Decrement the stock for a given product.
     *
     * @param int $productId
     * @param int $count
     * @throws Exception
     */
    public function decrementProduct(int $productId, int $count): void
    {
        $productInventory = ProductInventory::where('product_id', $productId)->lockForUpdate()->first();
        
        if (!$productInventory || $productInventory->count < $count) {
            throw new Exception("Stock insuficiente para el producto ID: {$productId}", 422);
        }
        
        $productInventory->decrement('count', $count);
        $this->logTransaction('product', $productId, -$count, 'Sale deduction');
    }

    /**
     * Log inventory transaction for audit purposes.
     * 
     * @param string $type
     * @param int $itemId
     * @param int $quantity
     * @param string $reason
     */
    private function logTransaction(string $type, int $itemId, int $quantity, string $reason): void
    {
        // TODO: En un futuro, guardar en tabla 'inventory_transactions'
        // DB::table('inventory_transactions')->insert([...]);
    }
}
