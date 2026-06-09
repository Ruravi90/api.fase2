<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RolController extends Controller
{
    public function index()
    {
        return view('rol.index');
    }

    public function getAll(){
        $roles = Role::all();
        return response($roles, 200)->header('Content-Type', 'application/json');
    }

    public function find($id){
        $rol = Role::with('permissions')->find($id);
        return response($rol, 200)->header('Content-Type', 'application/json');
    }

    public function add(Request $request)
    {

        $rol              = new Role;
        $rol->name        = $request->input("name");
        $rol->guard_name  = 'web';
        $rol->save();
        $rol->syncPermissions($this->permissionIds($request));

        return ['success' => true];
    }

    public function update($id,Request $request)
    {
        $rol              = Role::find($id);
        $rol->name        = $request->input("name");
        $rol->save();
        $rol->syncPermissions($this->permissionIds($request));
        return ['success' => true];
    }

    public function delete($id)
    {
        $rol = Role::find($id);
        $rol->syncPermissions([]);
        $rol->delete();
        return ['success' => true];
    }

    private function permissionIds(Request $request): array
    {
        return collect($request->input('permissions', []))
            ->pluck('id')
            ->filter()
            ->values()
            ->all();
    }

}
