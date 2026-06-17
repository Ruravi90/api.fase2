<?php

namespace App\Domains\Sales\Listeners;

use App\Domains\Sales\Events\SaleCreated;
use App\Models\PillInventory;
use App\Models\ProductInventory;
use Exception;

class UpdateInventoryOnSale
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SaleCreated  $event
     * @return void
     * @throws Exception
     */
    public function handle(SaleCreated $event)
    {
        $sale = $event->sale;
        $_sale = $event->saleData;

        // Validar y descontar stock de la venta principal
        if (isset($_sale['pill_id'])) {
            $pillInventory = PillInventory::where('pill_id', $_sale['pill_id'])->lockForUpdate()->first();
            if (!$pillInventory || $pillInventory->count < $sale->count) {
                throw new Exception("Stock insuficiente para la pastilla ID: " . $_sale['pill_id'], 422);
            }
            $pillInventory->decrement('count', $sale->count);
        }

        if (isset($_sale['product_id'])) {
            $productInventory = ProductInventory::where('product_id', $_sale['product_id'])->lockForUpdate()->first();
            if (!$productInventory || $productInventory->count < $sale->count) {
                throw new Exception("Stock insuficiente para el producto ID: " . $_sale['product_id'], 422);
            }
            $productInventory->decrement('count', $sale->count);
        }

        // Validar y descontar stock de los adicionales
        if (isset($_sale['additionals'])) {
            foreach ($_sale['additionals'] as $_additional) {
                if (isset($_additional['pill_id'])) {
                    $addPillInv = PillInventory::where('pill_id', $_additional['pill_id'])->lockForUpdate()->first();
                    if (!$addPillInv || $addPillInv->count < $_additional['count']) {
                        throw new Exception("Stock insuficiente en adicionales para pastilla ID: " . $_additional['pill_id'], 422);
                    }
                    $addPillInv->decrement('count', $_additional['count']);
                }

                if (isset($_additional['product_id'])) {
                    $addProdInv = ProductInventory::where('product_id', $_additional['product_id'])->lockForUpdate()->first();
                    if (!$addProdInv || $addProdInv->count < $_additional['count']) {
                        throw new Exception("Stock insuficiente en adicionales para producto ID: " . $_additional['product_id'], 422);
                    }
                    $addProdInv->decrement('count', $_additional['count']);
                }
            }
        }
    }
}
