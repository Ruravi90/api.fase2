<?php

namespace fase2\Http\Controllers;

use Illuminate\Http\Request;
use fase2\User;
use Caffeinated\Shinobi\Models\Role;
use fase2\Utilities;

/**
 * @resource Example
 *
 * Agent description
 */
class AgentController extends Controller
{
    /*
    * @hideFromAPIDocumentation
    */
    public function index(){
    	return view('agent.index');
    }

    /**
     * @header Token
	 * @Return view
	 *
	*/
    public function existUsername(Request $request){
        if($request->get('username') == '')
          return  response()->json(['status' => false]);

        $user = User::where('username',$request->get('username'))->first();
        if($user == null)
           return  response()->json(['status' => true]);
        else
           return  response()->json(['status' => false]);
    }
    /**
	 * @Return view
	 *
	*/
	public function add(Request $request){
        $user = new User;
        $user->username = $request->get('username');
        $user->name = $request->get('name');
        $user->lastname = $request->get('lastname');
        $user->motherlastname = $request->get('motherlastname');
        $user->email = "agent@fase2spa.com.mx";
        $user->initials = $request->get('initials');

        $utilities =new Utilities();
        $password = $utilities->generate_password();
        $user->password = bcrypt($password);

        $user->save();

        $roles = Role::where('slug','agent')->first();
        $user->assignRole($roles->id);

        $user->save();
        
		return ['success' => true]; 
	}
    /**
	 * @Return view
	 *
	*/
    public function update($id,Request $request){// se envia el id a $client 
		$user = User::find($id);
		$user->username = $request->get('username');
		$user->name = $request->get('name');
		$user->lastname = $request->get('lastname');
		$user->motherlastname = $request->get('motherlastname');
        $user->email = $request->get('email');
        $user->initials = $request->get('initials');
        $user->revokeAllRoles();
		$user->save();

        $roles = Role::where('slug','agent')->first();
        $user->assignRole($roles->id);
        $user->save();

    	return ['success' => true]; 
    }
    /**
	 * @Return view
	 *
	*/
    public function delete($id){
    	$user = User::find($id);
		$user->delete();
    	return ['success' => true]; 
    }
    /**
	 * @Return view
	 *
	*/
    public function getAll(){
        $roles = Role::where('slug','agent')->with('users')->first();
        $agents = $roles->users;
		return response($agents, 200)->header('Content-Type', 'application/json');
    }
    /**
	 * @Return view
	 *
	*/
    public function find($id){
        $agent = User::with('roles')->find($id);
        return response($agent, 200)->header('Content-Type', 'application/json');
    }
}
