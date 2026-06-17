<?php

namespace App\Domains\Sales\Queries;

use App\Models\Sale;
use App\Models\Department;
use Carbon\Carbon;
use DateTime;

class GetCuteSalesQuery
{
    /**
     * Obtiene el reporte de ventas con corte agrupadas por departamento.
     *
     * @param string $dateFrom
     * @return array
     */
    public function execute($dateFrom)
    {
        $dt = DateTime::createFromFormat('Y-m-d H:i:s', $dateFrom);
        if (!$dt) {
            $dt = new DateTime();
        }
        $start = Carbon::instance($dt)->startOfDay();
        $end = Carbon::instance($dt);

        $departments = Department::all()->toArray();

        $group = Sale::with([
            'client',
            'responsible',
            'sales' => function ($q) use ($start) {
                $q->with([
                    'cat_package',
                    'cat_service',
                    'cat_pill',
                    'cat_product',
                    'type',
                    'payments' => function ($q2) use ($start) {
                        $q2->where('updated_at', '>=', $start->toDateTimeString());
                    },
                    'payments.type',
                ])->where('is_cancel', 0)
                  ->where('updated_at', '>=', $start->toDateTimeString());
            }
        ])
            ->where('primary_id', null)
            ->where('is_cute', 1)
            ->where('is_cancel', 0)
            ->where('cute_date', '=', $end->toDateTimeString())
            ->get();

        $json = array();
        $count = 0;

        foreach ($departments as $_department) {
            $json[$count] = $_department;
            $jsonPrimary = array();
            $countPrimary = 0;

            foreach ($group as $_group) {
                $jsonSales = array();
                $countSales = 0;
                foreach ($_group['sales'] as $sale) {
                    if ($_department['id'] == $sale['department_id'] && $sale['is_cancel'] == 0) {
                        $jsonSales[$countSales] = $sale;
                        $countSales++;
                    }
                }

                if (count($jsonSales) > 0 && $_group['is_cancel'] == 0) {
                    $jsonPrimary[$countPrimary]['sales'] = $jsonSales;
                    $jsonPrimary[$countPrimary]['client'] = $_group->client;
                    $jsonPrimary[$countPrimary]['responsible'] = $_group->responsible;
                    $countPrimary++;
                }
            }

            if (count($jsonPrimary) > 0) {
                $json[$count]['sales'] = $jsonPrimary;
                $count++;
            }
        }

        return $json;
    }
}
