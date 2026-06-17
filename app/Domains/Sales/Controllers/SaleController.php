<?php

namespace App\Domains\Sales\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SellingElements;
use App\Models\Department;
use App\Models\Client;
use App\Models\User;
use App\Models\Package;
use App\Models\CatPackage;
use App\Models\PackageTracking;
use App\Models\Payment;
use App\Models\PillInventory;
use App\Models\ProductInventory;
use App\Models\SaleAdditional;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Exception\HttpResponseException;
use App\Domains\Sales\Requests\SalePostRequest;
use App\Domains\Sales\Services\SaleService;
use App\Domains\Sales\Queries\GetPaginatedSalesQuery;
use App\Domains\Sales\Queries\GetCuteSalesQuery;
use Illuminate\Support\Facades\DB;
use App\Models\Log;

/**
 * @resource Sale Management
 *
 * Controlador optimizado y protegido para la gestión de ventas e inventario.
 */
class SaleController extends Controller
{
	public function index(Request $request)
	{
		return view('sale.index');
	}

	public function getAll()
	{
		$sale = Sale::where('primary_id', null)->with([
			'department',
			'client',
			'sales' => function ($query) {
				$query->with(['department', 'client', 'responsible', 'type', 'cat_package', 'cat_service', 'cat_pill', 'cat_product']);
			}
		])->get();

		return response($sale, 200)->header('Content-Type', 'application/json');
	}

	public function getPaginate(Request $request, GetPaginatedSalesQuery $query)
	{
		$perPage = $request->get('perPage', 15);
		$isPaid = $request->get('isPaid');

		try {
			$sales = $query->execute($perPage, $isPaid);
		} catch (\InvalidArgumentException $e) {
			return response()->json(['error' => $e->getMessage()], 400);
		}

		return response($sales, 200)->header('Content-Type', 'application/json');
	}

	public function findId($id)
	{
		$sale = Sale::with(
			'department',
			'client',
			'responsible',
			'type',
			'user',
			'sales',
			'sales.type',
			'sales.cat_package',
			'sales.cat_service',
			'sales.cat_pill',
			'sales.cat_product'
		)->find($id);

		return response($sale, 200)->header('Content-Type', 'application/json');
	}

	public function getForDay()
	{
		$from = Carbon::today()->toDateTimeString();
		$sales = Sale::with(
			'department',
			'client',
			'responsible',
			'type',
			'user',
			'sales',
			'sales.cat_package',
			'sales.cat_service',
			'sales.cat_pill',
			'sales.type',
			'sales.cat_product'
		)
			->where('primary_id', null)
			->where('updated_at', '>=', $from)
			->where('is_cute', 0)
			->orderBy('id', 'desc') // Corregido primary_id por id para ordenamiento lógico
			->get();

		return response($sales, 200)->header('Content-Type', 'application/json');
	}

	public function getSalesUserDay($user_id)
	{
		$from = Carbon::today()->toDateTimeString();
		$sales = Sale::with([
			'department',
			'client',
			'responsible',
			'type',
			'user',
			'sales',
			'sales.cat_package',
			'sales.cat_service',
			'sales.cat_pill',
			'sales.type',
			'sales.cat_product',
			'sales.payments' => function ($query) use ($from) {
				$query->where('created_at', '>=', $from);
			}
		])
			->where('primary_id', null)
			->where('updated_at', '>=', $from)
			->where('is_cute', 0)
			->where('user_id', $user_id)
			->orderBy('id', 'desc')
			->get();

		return response($sales, 200)->header('Content-Type', 'application/json');
	}

	public function cuteSales(Request $request)
	{
		if (!$request->has('user_id')) {
			return response()->json(['error' => 'No se encontró el parámetro user_id'], 442);
		}

		$from = Carbon::today()->startOfDay()->toDateTimeString();
		$departments = Department::all()->toArray();

		// Traemos las ventas del día que no han sido cortadas
		$salesGroup = Sale::with([
			'client',
			'responsible',
			'sales' => function ($q) use ($from) {
				$q->with([
					'cat_package',
					'cat_service',
					'cat_pill',
					'cat_product',
					'type',
					'payments' => function ($q2) use ($from) {
						$q2->where('updated_at', '>=', $from);
					},
					'payments.type',
				])->where('is_cancel', 0);
			}
		])
			->where('primary_id', null)
			->where('is_cute', 0)
			->where('is_cancel', 0)
			->where('updated_at', '>=', $from)
			->get();

		$json = array();
		$count = 0;

		// SE OPTIMIZÓ: Usamos una transacción por si falla la actualización masiva de estados de corte
		DB::transaction(function () use ($salesGroup, $departments, $request, &$json, &$count) {
			foreach ($departments as $_department) {
				$json[$count] = $_department;
				$jsonPrimary = array();
				$countPrimary = 0;

				foreach ($salesGroup as $group) {
					// SOLUCIÓN N+1: $group ya es la instancia de la venta. No hace falta hacer Sale::find($group['id'])
					$group->cute_user_id = $request->get('user_id');
					$group->is_cute = 1;
					$group->cute_date = Carbon::now();
					$group->save();

					$jsonSales = array();
					$countSales = 0;
					foreach ($group->sales as $sale) {
						if ($_department['id'] == $sale['department_id'] && $sale['is_cancel'] == 0) {
							$jsonSales[$countSales] = $sale;
							$countSales++;
						}
					}

					if (count($jsonSales) > 0 && $group['is_cancel'] == 0) {
						$jsonPrimary[$countPrimary]['sales'] = $jsonSales;
						$jsonPrimary[$countPrimary]['client'] = $group->client;
						$jsonPrimary[$countPrimary]['responsible'] = $group->responsible;
						$countPrimary++;
					}
				}

				if (count($jsonPrimary) > 0) {
					$json[$count]['sales'] = $jsonPrimary;
					$count++;
				}
			}
		});

		return response($json, 200)->header('Content-Type', 'application/json');
	}

	public function getCuteSales(Request $request, GetCuteSalesQuery $query)
	{
		$from = $request->get('date', Carbon::now()->toDateTimeString());
		$json = $query->execute($from);

		return response($json, 200)->header('Content-Type', 'application/json');
	}

	/**
	 * REGISTRO DE VENTAS (MÉTODO CRÍTICO REFACTORIZADO)
	 * Cambiado Request por SalePostRequest para asegurar data limpia.
	 */
	public function add(SalePostRequest $request, SaleService $saleService)
	{
		try {
			$primarySale = $saleService->createSale($request->get('sales'));
			return response($primarySale, 200)->header('Content-Type', 'application/json');
		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
		}
	}

	public function update($id, Request $request)
	{
		return response()->json(['message' => 'Método no implementado originalmente'], 200);
	}

	/**
	 * CANCELACIÓN DE VENTAS PROTEGIDA
	 */
	public function cancel($id, Request $request)
	{
		$userId = $request->get('user_id');

		$primary = Sale::with(['sales'])->find($id);
		if (!$primary) {
			return response()->json(['error' => 'Venta no encontrada'], 404);
		}

		if ($primary->is_cancel == 1) {
			return response('La venta ya fue cancelada', 200)->header('Content-Type', 'application/json');
		}

		// Envolvemos la cancelación en una transacción para asegurar la devolución al inventario
		DB::transaction(function () use ($primary, $userId) {
			if ($primary->primary_id == null) {
				foreach ($primary->sales as $sale) {
					if ($sale->product_id != null) {
						ProductInventory::where('product_id', $sale->product_id)->increment('count', $sale->count);
					} else if ($sale->package_id != null) {
						$package = CatPackage::with(['complements'])->find($sale->package_id);
						if ($package) {
							foreach ($package->complements as $complement) {
								ProductInventory::where('product_id', $complement->product_id)->increment('count', $complement->count);
							}
						}
					}

					$log = new Log;
					$log->user_id = $userId;
					$log->table = 'sales';
					$log->table_id = $sale->id;
					$log->description = 'Se cancelo la venta con ID: ' . $sale->id;
					$log->save();

					$sale->is_cancel = 1;
					$sale->save();
				}

				$primary->is_cancel = 1;
				$primary->save();

				$log = new Log;
				$log->user_id = $userId;
				$log->table = 'sales';
				$log->table_id = $primary->id;
				$log->description = 'Se cancelo la venta principal';
				$log->save();
			} else {
				if ($primary->product_id != null) {
					ProductInventory::where('product_id', $primary->product_id)->increment('count', $primary->count);
				} else if ($primary->package_id != null) {
					$package = CatPackage::with(['complements'])->find($primary->package_id);
					if ($package) {
						foreach ($package->complements as $complement) {
							ProductInventory::where('product_id', $complement->product_id)->increment('count', $complement->count);
						}
					}
				}

				$primary->is_cancel = 1;
				$primary->save();

				$log = new Log;
				$log->user_id = $userId;
				$log->table = 'sales';
				$log->table_id = $primary->id;
				$log->description = 'Se canceló la subventa';
				$log->save();
			}
		});

		return response($primary, 200)->header('Content-Type', 'application/json');
	}

	public function delete($id)
	{
		// Se recomienda manejar bajas lógicas en lugar de deletes físicos en ventas heredadas,
		// pero manteniendo tu estructura original protegida:
		DB::transaction(function () use ($id) {
			$Sale = Sale::find($id);
			if (!$Sale)
				return;

			$primary_id = $Sale->primary_id;

			if ($Sale->sales()->count() > 0) {
				foreach ($Sale->sales as $_sale) {
					if ($_sale->product_id != null) {
						ProductInventory::where('product_id', $_sale->product_id)->increment('count', $_sale->count);
					}
				}
				$Sale->additionals()->delete();
				$Sale->sales()->delete();
				$Sale->payments()->delete();
				$Sale->delete();
			} else {
				if ($Sale->product_id != null) {
					ProductInventory::where('product_id', $Sale->product_id)->increment('count', $Sale->count);
				}
				$Sale->additionals()->delete();
				$Sale->payments()->delete();
				$Sale->delete();
			}

			if ($primary_id == null) {
				$mainSale = Sale::find($primary_id);
				if ($mainSale) {
					$mainSale->additionals()->delete();
					$mainSale->sales()->delete();
					$mainSale->payments()->delete();
					$mainSale->delete();
				}
			}
		});

		return response()->json(null, 204);
	}
}