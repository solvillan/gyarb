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
        $request->merge(['token' => $this->generateToken($request->get('email'))]);

        if ($user = User::create($request->all())) {
            return response('201', 201);
        } else {
            return response('400', 400);
        }
    }

    public function auth(Request $request) {
        //$token = Auth::attempt(['email' => $request->get('email'), 'password' => $request->get('password')]);
        if ($request->user()) {
            $user = Auth::user();
            $token = $user->getHidden()['token'];
            return response()->json(['email' => $request->get('email'), 'token' => $token]);
        } else {
            return response()->json(['error' => 'Failed to login'], 403);
        }
        //return response("Test!");
    }

    private function generateToken($email) {
        $payload = ['email' => $email, 'timestamp' => date_create()];
        return base64_encode(json_encode($payload));
    }
}
