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
        $inventoryManager = new \App\Domains\Inventory\Services\InventoryManagerService();

        // Validar y descontar stock de la venta principal
        if (isset($_sale['pill_id'])) {
            $inventoryManager->decrementPill($_sale['pill_id'], $sale->count);
        }

        if (isset($_sale['product_id'])) {
            $inventoryManager->decrementProduct($_sale['product_id'], $sale->count);
        }

        // Validar y descontar stock de los adicionales
        if (isset($_sale['additionals'])) {
            foreach ($_sale['additionals'] as $_additional) {
                if (isset($_additional['pill_id'])) {
                    $inventoryManager->decrementPill($_additional['pill_id'], $_additional['count']);
                }

                if (isset($_additional['product_id'])) {
                    $inventoryManager->decrementProduct($_additional['product_id'], $_additional['count']);
                }
            }
        }
    }
}
