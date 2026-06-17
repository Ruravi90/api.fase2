<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Create a record.
     *
     * @group User
     * @authenticated
     *
     * @bodyParam payload object required The request payload.
     *
     * @response 200 {"success":true}
     */
    public function add(Request $request)
    {
        $user = new User;
        $user->username = $request->get('username');
        $user->name = $request->get('name');
        $user->lastname = $request->get('lastname');
        $user->motherlastname = $request->has('motherlastname') ? $request->get('motherlastname') : '';
        $user->email = 'usuario@fase2spa.com.mx';
        $user->initials = $request->get('initials');


        $user->password = bcrypt($user->username . '1');

        $user->save();

        $user->syncRoles(collect($request->input('roles', []))->pluck('id')->all());

        $user->save();

        //$user->password =  $password;
        //try{
        //    Mail::to($user->email)->send(new NewUser($user));
        //} catch (Exception $ex) {
        //    dd($ex);
        //}

        return response()->json([
            'password' => $user->username . '1'
        ]);
    }

    /**
     * Authenticate a user and return a token.
     *
     * @group User
     * @unauthenticated
     *
     * @bodyParam username string required The username.
     * @bodyParam password string required The password.
     *
     * @response 200 {"success":true}
     */
    public function apiLogin(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('username', $request->input('username'))->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            return response()->json(['error' => 'Unauthorised'], 401);
        }

        if (Schema::hasTable('roles') && Schema::hasTable('model_has_roles')) {
            $user->load('roles');
        }

        $user->token = $user->createToken('fase2spa')->accessToken;

        return response()->json(['success' => $user], 200);
    }

    /**
     * Register a new user account.
     *
     * @group User
     * @unauthenticated
     *
     * @bodyParam name string required The display name.
     * @bodyParam email string required The email address.
     * @bodyParam username string required The username.
     * @bodyParam password string required The password.
     * @bodyParam c_password string required Confirmation password.
     *
     * @response 200 {"success":true}
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
            return response()->json(['error' => $validator->errors()], 401);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('fase2spa')->accessToken;
        $success['name'] = $user->name;
        return response()->json(['success' => $success], 200);
    }

    /**
     * Delete a record.
     *
     * @group User
     * @authenticated
     *
     * @urlParam id integer required The resource ID.
     *
     * @response 200 {"success":true}
     */
    public function delete($id)
    {
        $user = User::find($id);
        $user->delete();
        return ['success' => true];
    }

    /**
     * Check whether a username already exists.
     *
     * @group User
     * @authenticated
     *
     * @bodyParam username string required The username to check.
     *
     * @response 200 {"success":true}
     */
    public function existUsername(Request $request)
    {
        if ($request->get('username') == '')
            return response()->json(['status' => false]);

        $user = User::where('username', $request->get('username'))->first();
        if ($user == null)
            return response()->json(['status' => true]);
        else
            return response()->json(['status' => false]);
    }

    /**
     * Show one record.
     *
     * @group User
     * @authenticated
     *
     * @urlParam id integer required The resource ID.
     *
     * @response 200 {"success":true}
     */
    public function find($id)
    {
        $user = User::with('roles')->find($id);
        return response($user, 200)->header('Content-Type', 'application/json');
    }

    /**
     * List records.
     *
     * @group User
     * @authenticated
     *
     * @response 200 {"success":true}
     */
    public function getAll()
    {
        $user = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'agent');
        })->get();

        return response($user, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Return a paginated listing.
     *
     * @group User
     * @authenticated
     *
     * @bodyParam per_page integer The number of items per page.
     *
     * @response 200 {"success":true}
     */
    public function getPaginate(Request $request)
    {
        //per_page
        $perPage = 15;
        if ($request->has('perPage')) {
            $perPage = $request->get('perPage');
        }

        $Users = User::paginate($perPage);

        return response($Users, 200)->header('Content-Type', 'application/json');
    }

    /**
     * Update a record.
     *
     * @group User
     * @authenticated
     *
     * @urlParam id integer required The resource ID.
     *
     * @bodyParam payload object required The request payload.
     *
     * @response 200 {"success":true}
     */
    public function update($id, Request $request)
    {// se envia el id a $client 
        $user = User::find($id);
        $user->username = $request->get('username');
        $user->name = $request->get('name');
        $user->lastname = $request->get('lastname');
        $user->motherlastname = $request->has('motherlastname') ? $request->get('motherlastname') : '';
        //$user->email = $request->get('email');
        $user->initials = $request->get('initials');

        if ($request->has('reset_password')) {
            $user->password = bcrypt($request->get('reset_password'));
        }

        $user->save();

        $user->syncRoles(collect($request->input('roles', []))->pluck('id')->all());
        $user->save();
        return ['success' => true];
    }

}
