<?php
/**
 * Created by PhpStorm.
 * User: Rickard
 * Date: 2016-12-23
 * Time: 17:06
 */

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function getJar(Request $request) {
        if (!isset($_SESSION)) session_start();
        if (!isset($_SESSION['token'])) return redirect('login');
        $payload = json_decode(base64_decode($_SESSION['token']));
        User::find('token');
        return response()->download(storage_path('Pixturation-1.0.jar'));
    }
}