<?php

namespace fase2\Http\Controllers;
use fase2\User;
use Caffeinated\Shinobi\Models\Role;
use Caffeinated\Shinobi\Models\RoleUser;
use Caffeinated\Shinobi\Models\Permission; 
use Illuminate\Support\Facades\Auth; 
use fase2\Utilities;
use fase2\Mail\NewUser;
use fase2\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mail;

class UserController extends Controller
{

    public function index()
    {
        return view('user.index');
    }

    // Api rest

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

	public function add(Request $request){
        $user = new User;
        $user->username = $request->get('username');
        $user->name = $request->get('name');
        $user->lastname = $request->get('lastname');
        $user->motherlastname = $request->has('motherlastname') ? $request->get('motherlastname') : '';
        $user->email = 'usuario@fase2spa.com.mx';
        $user->initials = $request->get('initials');


        $utilities =new Utilities();
        //$password = $utilities->generate_password();

        $user->password = bcrypt($user->username.'1');

        $user->save();

        foreach ($request->input("roles") as $key => $value) {
           $user->assignRole($value["id"]);
        }

        $user->save();

        //$user->password =  $password;
        //try{
        //    Mail::to($user->email)->send(new NewUser($user));
        //} catch (Exception $ex) {
        //    dd($ex);
        //}
        
		return response()->json([
            'password' =>  $user->username.'1'
        ]);
	}

    public function update($id,Request $request){// se envia el id a $client 
		$user = User::find($id);
		$user->username = $request->get('username');
		$user->name = $request->get('name');
		$user->lastname = $request->get('lastname');
		$user->motherlastname = $request->has('motherlastname') ? $request->get('motherlastname') : '';
        //$user->email = $request->get('email');
        $user->initials = $request->get('initials');
    
        if($request->has('reset_password')){
              $user->password = bcrypt($request->get('reset_password'));
        }

        $user->revokeAllRoles();
		$user->save();
        foreach ($request->input("roles") as $key => $value) {
           $user->assignRole($value["id"]);
        }
        $user->save();
    	return ['success' => true]; 
    }

    public function delete($id){
    	$user = User::find($id);
		$user->delete();
    	return ['success' => true]; 
    }

    public function getAll(){
        $role = Role::where('slug','agent')->get()->first();
        $roleUser = RoleUser::where('role_id',$role->id)->select('user_id')->get();
        $user = User::whereNotIn('id',$roleUser)->get();
        return response($user, 200)->header('Content-Type', 'application/json');
    }

    public function find($id){
        $user = User::with('roles')->find($id);
        return response($user, 200)->header('Content-Type', 'application/json');
    }

    public function login(Request $request){// se envia el id a $client 
		$user = User::where('username', $request->get('username'));
		$user->username = $request->get('username');
		$user->name = $request->get('name');
		$user->lastname = $request->get('lastname');
        $user = User::with('roles')->find($id);
        return response($user, 200)->header('Content-Type', 'application/json');
    }

    /** 
     * Login api 
     * POST /api/users/login
     * @param string username
     * @param string password
     * @return Response
     */ 
    public function apiLogin(Request $request){ 
        
        if(Auth::attempt(['username' => $request['username'], 'password' => $request['password']])){ 
            $user = User::with('roles')->find(Auth::user()->id); 
            $user->token =  $user->createToken('fase2spa')->accessToken; 
            return response()->json(['success' => $user], 200); 
        } 
        else{ 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }
    /** 
     * Register api 
     * POST /api/users/register
     * @param string name
     * @param string email
     * @param string username
     * @param string password
     * @param string c_password
     * @return Response
     */ 
    public function apiRegister(Request $request) 
    { 
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'email' => 'required|email', 
            'password' => 'required', 
            'c_password' => 'required|same:password', 
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $input = $request->all(); 
        $input['password'] = bcrypt($input['password']); 
        $user = User::create($input); 
        $success['token'] =  $user->createToken('fase2spa')->accessToken; 
        $success['name'] =  $user->name;
        return response()->json(['success'=>$success], 200); 
    }
}
