<?php

namespace fase2\Http\Controllers;

use Illuminate\Http\Request;
use fase2\Department;

class DepartmentController extends Controller
{
    public function index()
    {
        return view('department.index');
    }

    // Api rest
    public function getAll(){
        $department = Department::all();
        return response($department, 200)->header('Content-Type', 'application/json');
    }

    public function find($id){
        $department = Department::find($id);
        return response($department, 200)->header('Content-Type', 'application/json');
    }

	public function add(Request $request){
		$department = new Department;
		$department->name = $request->get('name');
		$department->description = $request->get('description');
		$department->save();

		return ['success' => true]; 
	}

    public function update($id,Request $request){// se envia el id a $client 
    	$department = Department::find($id);
		$department->name = $request->get('name');
		$department->description = $request->get('description');
		$department->save();

    	return ['success' => true]; 
    }

    public function delete($id){// se envia el id a $client 
    	$department = Department::find($id);
		$department->delete();
    	return ['success' => true]; 
    } 

    public function getSales($id) {
        $sales = Department::find($id)->select('sales');
        return response($sales, 200)->header('Content-Type', 'application/json');
    }
}
