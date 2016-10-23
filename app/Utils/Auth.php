<?php
/**
 * Created by PhpStorm.
 * User: Rickard
 * Date: 2016-10-22
 * Time: 20:41
 */

namespace App\Utils;


use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Auth
{

    const TOKEN_VALID = 1, TOKEN_NOT_EXIST = 2, TOKEN_INVALID = 3;

    public static function generateKey($email, $password) {
        $time = date_create()->getTimestamp();
        return Hash::make($email.$password.$time);
    }

    public static function generateToken($email, $key) {
        $time = date_create()->getTimestamp();
        $payload = ['email' => $email, 'timestamp' => $time, 'key' => $key];
        return base64_encode(json_encode($payload));
    }

    public static function checkToken($token) {
        $payload = json_decode(base64_decode($token));
        if (is_object($payload)) {
            if ($user = User::where('token', $payload->key)->first()) {
                return $user;
            } else {
                return Auth::TOKEN_NOT_EXIST;
            }
        } else {
            return Auth::TOKEN_INVALID;
        }
    }

}