<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;

class DepartmentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/departments",
     *     tags={"departments"},
     *     summary="Get departments",
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Valida existencia de usuario."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function getAll(){
        $department = Department::all();
        return response($department, 200)->header('Content-Type', 'application/json');
    }

    /**
     * @OA\Get(
     *     path="/api/departments/{id}",
     *     tags={"departments"},
     *     summary="Get department",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *        name="id",
     *        in="query",
     *        description="",
     *        required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Valida existencia de usuario."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function find($id){
        $department = Department::find($id);
        return response($department, 200)->header('Content-Type', 'application/json');
    }

    /**
     * @OA\Post(
     *     path="/api/departments",
     *     tags={"departments"},
     *     summary="Add department",
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Valida existencia de usuario."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
	public function add(Request $request){
		$department = new Department;
		$department->name = $request->get('name');
		$department->description = $request->get('description');
		$department->save();

		return ['success' => true];
	}

    /**
     * @OA\Put(
     *     path="/api/departments/{id}",
     *     tags={"departments"},
     *     summary="Update department",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *        name="id",
     *        in="query",
     *        description="",
     *        required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Valida existencia de usuario."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function update($id,Request $request){// se envia el id a $client
    	$department = Department::find($id);
		$department->name = $request->get('name');
		$department->description = $request->get('description');
		$department->save();

    	return ['success' => true];
    }

    /**
     * @OA\Delete(
     *     path="/api/departments/{id}",
     *     tags={"departments"},
     *     summary="Delete department",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *        name="id",
     *        in="query",
     *        description="",
     *        required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Valida existencia de usuario."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function delete($id){// se envia el id a $client
    	$department = Department::find($id);
		$department->delete();
    	return ['success' => true];
    }

    /**
     * @OA\Get(
     *     path="/api/departments/{id}/sales",
     *     tags={"departments"},
     *     summary="Get sales per department",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *        name="id",
     *        in="query",
     *        description="",
     *        required=true,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Valida existencia de usuario."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function getSales($id) {
        $sales = Department::find($id)->select('sales');
        return response($sales, 200)->header('Content-Type', 'application/json');
    }
}
