<?php

namespace App\Domains\Sales\Queries;

use App\Models\Sale;

class GetPaginatedSalesQuery
{
    /**
     * Ejecuta la consulta para obtener ventas paginadas basado en el filtro isPaid.
     *
     * @param int $perPage
     * @param int|null $isPaid
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function execute($perPage = 15, $isPaid = null)
    {
        $relations = [
            'department',
            'client',
            'responsible',
            'type',
            'user',
            'sales' => function ($q) use ($isPaid) {
                if ($isPaid !== null && $isPaid != 2) {
                    $q->where('is_paid', $isPaid);
                }
            },
            'sales.department',
            'sales.cat_package',
            'sales.cat_service',
            'sales.cat_pill',
            'sales.type',
            'sales.cat_product',
            'sales.payments'
        ];

        switch ($isPaid) {
            case 0:
                return Sale::with($relations)
                    ->where('primary_id', null)
                    ->where('is_paid', 0)
                    ->where('is_cancel', 0)
                    ->orderBy('updated_at', 'desc')->paginate($perPage);
            case 1:
                return Sale::with($relations)
                    ->where('primary_id', null)
                    ->orderBy('updated_at', 'desc')->paginate($perPage);
            case 2:
                return Sale::select('cute_date')
                    ->groupBy('cute_date')
                    ->where('is_cute', 1)
                    ->orderBy('cute_date', 'desc')
                    ->paginate($perPage);
            case 3:
                return Sale::with($relations)
                    ->where('primary_id', null)
                    ->where('is_cancel', 1)
                    ->orderBy('updated_at', 'desc')->paginate($perPage);
            default:
                throw new \InvalidArgumentException("Parámetro isPaid no válido");
        }
    }
}
