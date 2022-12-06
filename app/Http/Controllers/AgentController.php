<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Caffeinated\Shinobi\Models\Role;


class AgentController extends Controller
{
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
     * @OA\Post(
     *     path="/api/agents",
     *     tags={"agents"},
     *     summary="Add agent",
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
        $user = new User;
        $user->username = $request->get('username');
        $user->name = $request->get('name');
        $user->lastname = $request->get('lastname');
        $user->motherlastname = $request->get('motherlastname');
        $user->email = "agent@Appspa.com.mx";
        $user->initials = $request->get('initials');
        $user->profile = $request->get('profile');
        $user->password = bcrypt($request->get('password'));

        $user->save();

		return ['success' => true];
	}

    /**
     * @OA\Put(
     *     path="/api/agents/{id}",
     *     tags={"agents"},
     *     summary="Update agent",
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
		$user = User::find($id);
		$user->username = $request->get('username');
		$user->name = $request->get('name');
		$user->lastname = $request->get('lastname');
		$user->motherlastname = $request->get('motherlastname');
        $user->email = $request->get('email');
        $user->initials = $request->get('initials');

        if($request->has('reset_password')){
            $user->password = bcrypt($request->get('reset_password'));
        }

		$user->save();

    	return ['success' => true];
    }

/**
     * @OA\Delete(
     *     path="/api/agents/{id}",
     *     tags={"agents"},
     *     summary="Delete agent",
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
    public function delete($id){
    	$user = User::find($id);
		$user->delete();
    	return ['success' => true];
    }

/**
     * @OA\Get(
     *     path="/api/agents",
     *     tags={"agents"},
     *     summary="Get all agents",
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
        $agents = User::where("profile","agent")->get();
		return response($agents, 200)->header('Content-Type', 'application/json');
    }

    /**
     * @OA\Get(
     *     path="/api/agents/{id}",
     *     tags={"agents"},
     *     summary="Get agent",
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
        $agent = User::with('roles')->find($id);
        return response($agent, 200)->header('Content-Type', 'application/json');
    }
}
