<?php

namespace App\Domains\Sales\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Sale;

class SaleCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sale;
    public $saleData;

    /**
     * Create a new event instance.
     *
     * @param Sale $sale El modelo de la sub-venta recién creada
     * @param array $saleData La data específica de esa sub-venta enviada en la request
     */
    public function __construct(Sale $sale, array $saleData)
    {
        $this->sale = $sale;
        $this->saleData = $saleData;
    }
}
