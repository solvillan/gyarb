<?php
/**
 * Created by PhpStorm.
 * User: Rickard
 * Date: 2016-10-23
 * Time: 22:34
 */

namespace App\Http\Controllers;


use App\Models\Game;
use App\Models\Picture;
use App\Utils\Auth;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function create(Request $request) {
        if ($request->input('token')) {
            $user = Auth::checkToken($request->input('token'));
            if ($user !== Auth::TOKEN_INVALID && $user !== Auth::TOKEN_NOT_EXIST) {
                $game = new Game();
                //$game->players->save($user);
                $picture = new Picture(['data' => 'The Data', 'owner' => $user->id, 'word' => 'Hello, world!']);
                $picture->save();
                $game->pictures->save($picture);
                $game->save();
                $user->pictures->save($picture);
                $user->plays->save($game);
                return response(201);
            }
            return response(403);
        }
        return response(400);
    }
}