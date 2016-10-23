<?php
/**
 * Created by PhpStorm.
 * User: rickard
 * Date: 2016-10-20
 * Time: 19:28
 */

namespace App\Http\Controllers;


use App\Models\User;
use App\Utils\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function create(Request $request) {
        //TODO: check if user already exist

        if ($usertest = User::where(['email' => $request->input('email')])->first()) {
            return response()->json(['error' => 'User already exist'], 406);
        }

        $request->merge(['password' => Hash::make($request->get('password'))]);
        $request->merge(['token' => Auth::generateKey($request->get('email'), $request->get('password'))]);

        if ($user = User::create($request->all())) {
            return response()->json(['email' => $user->email, 'name' => $user->name], 201);
        } else {
            return response()->json(['error' => 'Malformed request'], 400);
        }
    }

    public function auth(Request $request) {
        if ($request->input('email') && $request->input('password')) {
            if ($user = User::where(['email' => $request->input('email')])->first()) {
                if (Hash::check($request->input('password'), $user->password)) {
                    $token = Auth::generateToken($user->email, $user->token);
                    return response()->json(['email' => $user->email, 'token' => $token]);
                }
            }
        }
        return response()->json(['error' => 'Failed to login'], 403);
    }

    public function checkToken(Request $request) {
        if ($request->input('token')) {
            $user = Auth::checkToken($request->input('token'));
            if ($user === Auth::TOKEN_INVALID) {
                return response()->json(['error' => 'Token not valid!'], 403);
            } else if ($user === Auth::TOKEN_NOT_EXIST) {
                return response()->json(['error' => 'Token does not exist!'], 403);
            } else if ($user) {
                return response()->json(['name' => $user->name, 'email' => $user->email]);
            } else {
                return response()->json(['error' => 'Something weird happened...'], 500);
            }
        }
        return response()->json(['error' => 'No token sent'], 400);
    }
}
