<?php

namespace App\Http\Controllers;
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
use App\Http\Requests\SalePostRequest;
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

	public function getPaginate(Request $request)
	{
		$perPage = $request->get('perPage', 15);
		$isPaid = $request->get('isPaid');

		// Reutilizamos la definición de relaciones para evitar código repetido en el switch
		$relations = [
			'department',
			'client',
			'responsible',
			'type',
			'user',
			'sales' => function ($q) use ($isPaid) {
				$q->where('is_paid', $isPaid);
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
				$sales = Sale::with($relations)
					->where('primary_id', null)
					->where('is_paid', 0)
					->where('is_cancel', 0)
					->orderBy('updated_at', 'desc')->paginate($perPage);
				break;
			case 1:
				$sales = Sale::with($relations)
					->where('primary_id', null)
					->orderBy('updated_at', 'desc')->paginate($perPage);
				break;
			case 2:
				$sales = Sale::select('cute_date')
					->groupBy('cute_date')
					->where('is_cute', 1)
					->orderBy('cute_date', 'desc')
					->paginate($perPage);
				break;
			case 3:
				$sales = Sale::with($relations)
					->where('primary_id', null)
					->where('is_cancel', 1)
					->orderBy('updated_at', 'desc')->paginate($perPage);
				break;
			default:
				return response()->json(['error' => 'Parámetro isPaid no válido'], 400);
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

	public function getCuteSales(Request $request)
	{
		$from = $request->get('date', Carbon::now()->toDateTimeString());
		$dt = DateTime::createFromFormat('Y-m-d H:i:s', $from);
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

		return response($json, 200)->header('Content-Type', 'application/json');
	}

	/**
	 * REGISTRO DE VENTAS (MÉTODO CRÍTICO REFACTORIZADO)
	 * Cambiado Request por SalePostRequest para asegurar data limpia.
	 */
	public function add(SalePostRequest $request)
	{
		// SOLUCIÓN EN TRANSACCIÓN: Todo o nada. Si falla el inventario o un guardado, se revierte todo de la BD.
		$primarySale = DB::transaction(function () use ($request) {

			$salesData = $request->get('sales');
			if (empty($salesData)) {
				throw new \Exception("No hay ventas para procesar", 400);
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
				// 1. CONTROL DE INVENTARIO PREVIO (Validación de Stock)
				if (isset($_sale['pill_id'])) {
					$pillInventory = PillInventory::where('pill_id', $_sale['pill_id'])->lockForUpdate()->first();
					if (!$pillInventory || $pillInventory->count < $_sale['count']) {
						throw new \Exception("Stock insuficiente para la pastilla ID: " . $_sale['pill_id'], 422);
					}
				}
				if (isset($_sale['product_id'])) {
					$productInventory = ProductInventory::where('product_id', $_sale['product_id'])->lockForUpdate()->first();
					if (!$productInventory || $productInventory->count < $_sale['count']) {
						throw new \Exception("Stock insuficiente para el producto ID: " . $_sale['product_id'], 422);
					}
				}

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

				// SOLUCIÓN ATÓMICA DE INVENTARIO: Evita race conditions con decrement()
				if (isset($_sale['pill_id'])) {
					$pillInventory->decrement('count', $sale->count);
				}
				if (isset($_sale['product_id'])) {
					$productInventory->decrement('count', $sale->count);
				}

				// Procesar adicionales
				if (isset($_sale['additionals'])) {
					foreach ($_sale['additionals'] as $_additional) {

						if (isset($_additional['pill_id'])) {
							$addPillInv = PillInventory::where('pill_id', $_additional['pill_id'])->lockForUpdate()->first();
							if (!$addPillInv || $addPillInv->count < $_additional['count']) {
								throw new \Exception("Stock insuficiente en adicionales para pastilla ID: " . $_additional['pill_id'], 422);
							}
							$addPillInv->decrement('count', $_additional['count']);
						}

						if (isset($_additional['product_id'])) {
							$addProdInv = ProductInventory::where('product_id', $_additional['product_id'])->lockForUpdate()->first();
							if (!$addProdInv || $addProdInv->count < $_additional['count']) {
								throw new \Exception("Stock insuficiente en adicionales para producto ID: " . $_additional['product_id'], 422);
							}
							$addProdInv->decrement('count', $_additional['count']);
						}

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

		return response($primarySale, 200)->header('Content-Type', 'application/json');
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