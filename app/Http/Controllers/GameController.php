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
use App\Utils\Wordlist;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * Create a new Game
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function create(Request $request) {
        if ($request->input('token')) {
            $user = Auth::checkToken($request->input('token'));
            if ($user !== Auth::TOKEN_INVALID && $user !== Auth::TOKEN_NOT_EXIST) {
                $game = new Game();
                $game->status = 0;
                $game->owner()->associate($user);
                $game->currentPlayer()->associate($user);
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

    /**
     * Add a player to a game
     * @param Request $request
     * @param $gid int Game ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPlayer(Request $request, $gid) {
        if ($request->input('token') && $request->input('player_id')) {
            $user = Auth::checkToken($request->input('token'));
            if ($user !== Auth::TOKEN_INVALID && $user !== Auth::TOKEN_NOT_EXIST) {
                $game = Game::find($gid);
                if (Auth::userMemberOf($request->input('token'), $game->players)) {
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

    /**
     * Start the game
     * @param Request $request
     * @param $gid
     * @return \Illuminate\Http\JsonResponse
     */
    public function start(Request $request, $gid) {
        if ($request->input('token')) {
            $user = Auth::checkToken($request->input('token'));
            if ($user !== Auth::TOKEN_INVALID && $user !== Auth::TOKEN_NOT_EXIST) {
                $game = Game::find($gid);
                if ($game->owner->id == $user->id) {

                    if ($game->status !== 0) {
                        return response()->json(['error' => 'Game already active!'], 403);
                    }

                    //Random gen first word
                    $game->current_word = Wordlist::getWord();

                    //Random gen first player
                    $first_player = $game->players()->get()->shuffle()->first();
                    $game->currentPlayer()->associate($first_player);

                    // Activate
                    $game->status = 1;
                    $game->save();
                    $list = [];
                    for ($i = 0; $i < 20; $i++) {
                        $list[] = Wordlist::getWord();
                    }
                    return response()->json(['user' => $first_player, 'game' => $game, 'words' => $list]);
                } else {
                    return response()->json(['error' => 'Not authorized to start game'], 401);
                }
            } else {
                return response()->json(['error' => 'Not authorized'], 401);
            }
        } else {
            return response()->json(['error' => 'Malformed request'], 400);
        }
    }

}