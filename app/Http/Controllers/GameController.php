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
                $user->save();
                $game->save();
                return response()->json(['user' => $user, 'game' => $game], 201);
            }
            return response('Unauthorized', 401);
        }
        return response('Malformed request', 400);
    }

    public function addPlayer(Request $request, $gid) {
        if ($request->input('token') && $request->input('player_id')) {
            $user = Auth::checkToken($request->input('token'));
            $payload = json_decode(base64_decode($request->input('token')));
            if ($user !== Auth::TOKEN_INVALID && $user !== Auth::TOKEN_NOT_EXIST) {
                $game = Game::find($gid);
                if ($this->checkUser($request->input('token'), $game->players)) {
                    $player = User::find($request->input('player_id'));
                    $game->players()->attach($player);
                    $game->save();
                    return response()->json(['game' => $game, 'added' => $player, 'added_by' => $user]);
                } else {
                    return response()->json(['error' => 'Not authorized to add players'], 401);
                }
            } else {
                return response()->json(['error' => 'Not authorized'], 401);
            }
        } else {
            return response()->json(['error' => 'Malformed request'], 400);
        }
    }

    private function checkUser($token, $list) {
        $payload = json_decode(base64_decode($token));
        $auth = false;
        foreach ($list as $player) {
            if ($player->token === $payload->key) {
                $auth = true;
                break;
            }
        }
        return $auth;
    }

}