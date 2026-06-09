<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{

    public function getAll(){
        $permissions = Permission::all();
        return response($permissions, 200)->header('Content-Type', 'application/json');
    }

    public function find($id){
        $permission = Permission::find($id);
        return response($permission, 200)->header('Content-Type', 'application/json');
    }

    public function add(Request $request)
    {
        $permiso              = new Permission;
        $permiso->name        = $request->input("name");
        $permiso->guard_name  = 'web';
        $permiso->save();
        return ['success' => true];
    }


    public function update($id, Request $request)
    {
        $permiso              = Permission::find($id);
        $permiso->name        = $request->input("name");
                        $permiso->save();
        return ['success' => true];
    }

    public function delete($id)
    {
        $permission = Permission::find($id);
        $permission->delete();
        return ['success' => true];
    }
}
