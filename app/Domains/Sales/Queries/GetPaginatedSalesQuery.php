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
     * @param string|null $search  Busca por nombre/apellido del cliente
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function execute($perPage = 15, $isPaid = null, $search = null)
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

        // Closure reutilizable para aplicar filtro de cliente
        $applySearch = function ($query) use ($search) {
            if ($search && trim($search) !== '') {
                $term = trim($search);
                $query->whereHas('client', function ($q) use ($term) {
                    $q->where('name', 'LIKE', "%{$term}%")
                      ->orWhere('lastname', 'LIKE', "%{$term}%")
                      ->orWhere('motherlastname', 'LIKE', "%{$term}%");
                });
            }
        };

        switch ($isPaid) {
            case 0:
                $q = Sale::with($relations)
                    ->where('primary_id', null)
                    ->where('is_paid', 0)
                    ->where('is_cancel', 0);
                $applySearch($q);
                return $q->orderBy('updated_at', 'desc')->paginate($perPage);

            case 1:
                $q = Sale::with($relations)->where('primary_id', null);
                $applySearch($q);
                return $q->orderBy('updated_at', 'desc')->paginate($perPage);

            case 2:
                // Fechas de corte — no aplica búsqueda por cliente
                return Sale::select('cute_date')
                    ->groupBy('cute_date')
                    ->where('is_cute', 1)
                    ->orderBy('cute_date', 'desc')
                    ->paginate($perPage);

            case 3:
                $q = Sale::with($relations)
                    ->where('primary_id', null)
                    ->where('is_cancel', 1);
                $applySearch($q);
                return $q->orderBy('updated_at', 'desc')->paginate($perPage);

            default:
                throw new \InvalidArgumentException("Parámetro isPaid no válido");
        }
    }
}
