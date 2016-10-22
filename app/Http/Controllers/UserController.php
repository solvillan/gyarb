<?php
/**
 * Created by PhpStorm.
 * User: rickard
 * Date: 2016-10-20
 * Time: 19:28
 */

namespace App\Http\Controllers;


use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function create(Request $request) {
        $request->merge(['password' => Hash::make($request->get('password'))]);
        $request->merge(['token' => $this->generateToken($request->get('email'), $request->get('password'))]);

        if ($user = User::create($request->all())) {
            return response('201', 201);
        } else {
            return response('400', 400);
        }
    }

    public function auth(Request $request) {
        if ($request->input('email') && $request->input('password')) {
            if ($user = User::where(['email' => $request->input('email')])->first()) {
                if (Hash::check($request->input('password'), $user->password)) {
                    $token = $user->token;
                    return response()->json(['email' => $request->get('email'), 'token' => $token]);
                }
            }
        }
        return response()->json(['error' => 'Failed to login'], 403);
    }

    public function checkToken(Request $request) {
        if ($request->input('token')) {
            if ($user = User::where('token', $request->input('token'))->first()) {
                return response()->json(['name' => $user->name, 'email' => $user->email]);
            } else {
                return response()->json(['error' => 'Token not valid!'], 403);
            }
        }
        return response()->json(['error' => 'No token sent'], 400);
    }

    private function generateToken($email, $password) {
        $time = date_create()->getTimestamp();
        $payload = ['email' => $email, 'timestamp' => $time, 'key' => hash('sha512', $email.$password.$time)];
        return base64_encode(json_encode($payload));
    }
}
