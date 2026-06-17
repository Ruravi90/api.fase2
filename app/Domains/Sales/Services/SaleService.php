<?php

namespace App\Domains\Sales\Services;

use App\Models\Sale;
use App\Models\Payment;
use App\Models\PillInventory;
use App\Models\ProductInventory;
use App\Models\SaleAdditional;
use App\Models\CatPackage;
use App\Domains\Sales\Events\SaleCreated;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class SaleService
{
    /**
     * Registra una nueva venta con sus detalles, pagos y afecta inventario.
     *
     * @param array $salesData
     * @return Sale
     * @throws Exception
     */
    public function createSale(array $salesData): Sale
    {
        return DB::transaction(function () use ($salesData) {
            if (empty($salesData)) {
                throw new Exception("No hay ventas para procesar", 400);
            }

            // Crear la cabecera/venta primaria
            $primary = new Sale;
            $primary->department_id = 0;
            $primary->responsible_id = $salesData[0]['responsible_id'];
            $primary->client_id = $salesData[0]['client_id'];
            $primary->user_id = $salesData[0]['user_id'];
            $primary->type_sale_id = 0;
            $primary->balance = 0;
            $primary->price = 0;
            $primary->amount = 0;
            $primary->count = 0;
            $primary->is_paid = 0;
            $primary->save();

            foreach ($salesData as $_sale) {
                // Generar sub-venta
                $sale = new Sale;
                $sale->department_id = $_sale['department_id'];
                $sale->responsible_id = $_sale['responsible_id'];
                $sale->client_id = $_sale['client_id'];
                $sale->user_id = $_sale['user_id'];
                $sale->primary_id = $primary->id;
                $sale->type_sale_id = $_sale['type_sale_id'];
                $sale->count = $_sale['count'];
                $sale->price = $_sale['price'];

                $total = $_sale['price'] * $_sale['count'];
                $discount = 0;

                if (isset($_sale['discount'])) {
                    $sale->discount = $_sale['discount'];
                    $discount = (($sale->discount * $total) / 100);
                }

                $sale->total = $total - $discount;
                $sale->amount = $_sale['amount'];
                $sale->partial_payment = $_sale['amount'];
                $sale->balance = ($sale->total - $sale->amount);
                $sale->is_paid = ($sale->balance <= 0) ? 1 : 0;

                if (isset($_sale['description']))
                    $sale->description = $_sale['description'];
                if (isset($_sale['product_id']))
                    $sale->product_id = $_sale['product_id'];
                if (isset($_sale['service_id']))
                    $sale->service_id = $_sale['service_id'];
                if (isset($_sale['package_id']))
                    $sale->package_id = $_sale['package_id'];
                if (isset($_sale['pill_id']))
                    $sale->pill_id = $_sale['pill_id'];

                $sale->save();

                // Registrar pago asociado
                $payment = new Payment;
                $payment->sale_id = $sale->id;
                $payment->user_id = $sale->user_id;
                $payment->responsible_id = $_sale['responsible_id'];
                $payment->type_sale_id = $_sale['type_sale_id'];
                $payment->amount = $_sale['amount'];
                $payment->save();

                // Si es paquete
                if (isset($_sale['package_id'])) {
                    $package = new \fase2\Package; // Asumiendo uso de alias o ruta completa
                    $package->sale_id = $sale->id;
                    $package->client_id = $sale->client_id;
                    $package->cat_package_id = $_sale['package_id'];
                    $package->is_completed = false;
                    $package->save();
                }

                // Procesar adicionales
                if (isset($_sale['additionals'])) {
                    foreach ($_sale['additionals'] as $_additional) {
                        $additional = new SaleAdditional;
                        $additional->sale_id = $sale->id;
                        if (isset($_additional['pill_id']))
                            $additional->pill_id = $_additional['pill_id'];
                        if (isset($_additional['product_id']))
                            $additional->product_id = $_additional['product_id'];
                        $additional->count = $_additional['count'];
                        $additional->save();
                    }
                }

                // Disparar el evento de sub-venta creada para que los Listeners actualicen el Inventario
                event(new SaleCreated($sale, $_sale));
            }

            // Totalizar la cabecera primaria de forma limpia al terminar el bucle
            $totalSum = Sale::where('primary_id', $primary->id)->sum('total');
            $amountSum = Sale::where('primary_id', $primary->id)->sum('amount');

            $primary->total = $totalSum;
            $primary->balance = ($totalSum - $amountSum);
            $primary->is_paid = ($primary->balance <= 0) ? 1 : 0;
            $primary->save();

            return $primary;
        });
    }
}
