<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/users/paginate",
     *     tags={"users"},
     *     summary="Get users per paginate",
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
    public function getPaginate(Request $request) {
		//per_page
		$perPage = 15;
		if($request->has('perPage')){
			$perPage = $request->get('perPage');
		}

		$Users = User::paginate($perPage);

		return response($Users, 200)->header('Content-Type', 'application/json');
	}

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
     *     path="/api/users",
     *     tags={"users"},
     *     summary="Add user",
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
        $user->motherlastname = $request->has('motherlastname') ? $request->get('motherlastname') : '';
        $user->email = 'usuario@fase2spa.com.mx';
        $user->initials = $request->get('initials');
        $user->password = bcrypt($user->username.'1');
        $user->profile = $request->get('profile');
        $user->save();

		return response()->json([
            'password' =>  $user->username.'1'
        ]);
	}

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     tags={"users"},
     *     summary="Update user",
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
		$user->motherlastname = $request->has('motherlastname') ? $request->get('motherlastname') : '';
        //$user->email = $request->get('email');
        $user->initials = $request->get('initials');
        $user->profile = $request->get('profile');

        if($request->has('reset_password')){
              $user->password = bcrypt($request->get('reset_password'));
        }

		$user->save();
    	return ['success' => true];
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     tags={"users"},
     *     summary="Delete user",
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
     *     path="/api/users",
     *     tags={"users"},
     *     summary="Get users",
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
        $user = User::where('profile','!=' ,"agent")->get();
        return response($user, 200)->header('Content-Type', 'application/json');
    }


    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     tags={"users"},
     *     summary="Get user",
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
        $user = User::find($id);
        return response($user, 200)->header('Content-Type', 'application/json');
    }


    public function login(Request $request){// se envia el id a $client
		$user = User::where('username', $request->get('username'));
		$user->username = $request->get('username');
		$user->name = $request->get('name');
		$user->lastname = $request->get('lastname');
        //$user = User::with('roles')->find($id);
        return response(200)->header('Content-Type', 'application/json');
    }



    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"auth"},
     *     summary="Inicio de sesion",
    *     @OA\RequestBody(
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                 @OA\Property(
    *                     property="username",
    *                     type="string"
    *                 ),
    *                 @OA\Property(
    *                     property="password",
    *                     type="string"
    *                 ),
    *                 example={"username": "user", "password": "123"}
    *             )
    *         )
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
    public function apiLogin(Request $request){

        $credentials = $request->only('username', 'password');
        $token = \Tymon\JWTAuth\Facades\JWTAuth::attempt($credentials);

        if(!$token){
            return response([
                'status' => 'error',
                'message' => 'Unauthorized',
            ],401)->header('Content-Type', 'application/json');
        }

        return response([
                'claims'=> [
                    'id'         => auth()->user()->id,
                    'username'   => auth()->user()->username,
                    'name'       => auth()->user()->name,
                    'lastname'   => auth()->user()->last_name,
                    'profile'    => auth()->user()->profile,
                    'initials'   => auth()->user()->initials,
                ],
                'token' => $token,
                'type' => 'bearer',
            ])->header('Content-Type', 'application/json');
    }

    /**
     * Display a listing of the resource.
     * Mostramos el listado de los regitros solicitados.
     * @return \Illuminate\Http\Response
     *
     * @OA\Post(
     *     path="/api/auth/register",
     *     tags={"auth"},
     *     summary="Registra usuario",
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
    public function apiRegister(Request $request)
    {
        $request->validate($request->all(), [
            'name' => 'required',
            'username' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);

        return response([
            'profile' => $input['profile'],
            'token' => $token,
            'type' => 'bearer',
        ])->header('Content-Type', 'application/json');

    }
}
