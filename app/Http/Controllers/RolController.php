<?php

namespace fase2\Http\Controllers;

use Illuminate\Http\Request;
use Caffeinated\Shinobi\Models\Role;

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
        $rol->slug        = $request->input("slug");
        $rol->description = $request->input("description");
        $rol->save();
        foreach ($request->input("permissions") as $key => $value) {
           $rol->assignPermission($value["id"]);
        }
        $rol->save(); 

        return ['success' => true];
    }

    public function update($id,Request $request)
    {
        $rol              = Role::find($id);
        $rol->name        = $request->input("name");
        $rol->slug        = $request->input("slug");
        $rol->description = $request->input("description");
        $rol->revokeAllPermissions();
        $rol->save();
        foreach ($request->input("permissions") as $key => $value) {
            $rol->assignPermission($value["id"]);
        }
        $rol->save();
        return ['success' => true];
    }

    public function delete($id)
    {
        $rol = Role::find($id);
        $rol->revokeAllPermissions();
        $rol->delete();
        return ['success' => true];
    }

}
