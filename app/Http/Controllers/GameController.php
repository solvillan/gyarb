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
use App\Models\User;
use App\Utils\Auth;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function create(Request $request) {
        if ($request->input('token')) {
            $user = Auth::checkToken($request->input('token'));
            if ($user !== Auth::TOKEN_INVALID && $user !== Auth::TOKEN_NOT_EXIST) {
                $user = Auth::checkToken($request->input('token'));
                $game = Game::create();
                $game->save();
                $game->players()->attach($user);
                $picture = new Picture(['data' => 'The Data', 'word' => 'Hello world!']);
                $picture->game()->associate($game);
                $picture->owner()->associate($user);
                $picture->save();
                $user->save();
                $game->save();
                return response()->json(['user' => $user, 'game' => $game, 'picture' => $picture], 201);
            }
            return response('Forbidden', 403);
        }
        return response('Malformed request', 400);
    }
}