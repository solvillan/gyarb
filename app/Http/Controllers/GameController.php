<?php
/**
 * Created by PhpStorm.
 * User: Rickard
 * Date: 2016-10-23
 * Time: 22:34
 */

namespace App\Http\Controllers;


use App\Models\Game;
use App\Models\Guess;
use App\Models\Picture;
use App\Models\User;
use App\Utils\Auth;
use App\Utils\Wordlist;
use Illuminate\Http\Request;

class GameController extends Controller
{

    const PREPARING = 0, DRAW_PICTURE = 1, GUESS_PICTURE = 2;

    /**
     * Create a new Game
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function create(Request $request) {
        return Auth::runAsUser($request, function ($request, User $user) {
            $game = new Game();
            $game->status = GameController::PREPARING;
            $game->owner()->associate($user);
            $game->currentPlayer()->associate($user);
            $game->save();
            //$game->players()->attach($user);
            $user->plays()->attach($game);
            $user->save();
            $game->save();
            return response()->json(['user' => $user, 'game' => $game], 201);
        });
    }

    /**
     * Add a player to a game
     * @param Request $request
     * @param $gid int Game ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPlayer(Request $request, $gid) {
        if ($request->input('player_id')) {
            return Auth::runAsUser($request, function ($request, $user) use ($gid) {
                $game = Game::find($gid);
                if (Auth::userMemberOf($request->input('token'), $game->players)) {
                    $player = User::find($request->input('player_id'));
                    $player->plays()->attach($game);
                    //$game->players()->attach($player);
                    $game->save();
                    $player->save();
                    return response()->json(['game' => $game, 'added' => $player, 'added_by' => $user]);
                } else {
                    return response()->json(['error' => 'Not authorized to add players'], 401);
                }
            });
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
        return Auth::runAsUser($request, function ($req, $user) use ($gid) {
            $game = Game::find($gid);
            if ($game->owner->id == $user->id) {

                if ($game->status !== GameController::PREPARING) {
                    return response()->json(['error' => 'Game already active!'], 403);
                }

                //Random gen first word
                $game->current_word = Wordlist::getWord();

                //Random gen first player
                $first_player = $game->players()->get()->shuffle()->first();
                $game->currentPlayer()->associate($first_player);

                // Activate
                $game->status = GameController::DRAW_PICTURE;
                $game->save();
                $list = [];
                for ($i = 0; $i < 20; $i++) {
                    $list[] = Wordlist::getWord();
                }
                return response()->json(['user' => $first_player, 'game' => $game, 'words' => $list]);
            } else {
                return response()->json(['error' => 'Not authorized to start game'], 401);
            }
        });
    }

    /**
     * Submit a picture to game
     * @param Request $request
     * @param $gid
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitPicture(Request $request, $gid) {
        if ($request->input('payload')) {
            return Auth::runAsUser($request, function ($request, $user) use ($gid) {
                $game = Game::find($gid);
                if ($game->currentPlayer->id == $user->id) {
                    $payload = json_decode($request->input('payload'));
                    if ($payload->data && $payload->word) {
                        $picture = new Picture();
                        $picture->owner()->associate($user);
                        $picture->game()->associate($game);
                        $picture->data = $payload->data;
                        $picture->word = $payload->word;
                        $picture->save();
                        $game->status = GameController::GUESS_PICTURE;
                        $game->save();
                        return response()->json(["picture" => $picture, "payload" => $payload, "game" => $game], 200);
                    } else {
                        return response()->json(['error' => "Payload malformed", 'payload' => $payload], 400);
                    }
                } else {
                    return response()->json(['error' => "Not current player"], 401);
                }
            });
        } else {
            return response()->json(['error' => 'Malformed request'], 400);
        }
    }

    public function submitGuess(Request $request, $id) {
        return Auth::runAsUser($request, function (Request $request, User $user) use ($id){
            $data = json_decode($request->input('payload'));
            $game = Game::find($id);
            foreach ($game->plays() as $player) {
                if ($player->id == $user->id) {
                    $guess = new Guess();
                    $guess->owner()->associate($user);
                    $guess->game()->associate($game);
                    $guess->guess = $data->guess;
                    $guess->save();
                }
            }
            
        });
    }

    /**
     * Poll current game state
     * @param Request $request
     * @param $gid
     * @return \Illuminate\Http\JsonResponse
     */
    public function poll(Request $request, $gid) {
        return Auth::runAsUser($request, function ($request, $user) use ($gid) {
            $game = Game::find($gid);
            if (Auth::userMemberOf($request->header("Token"), $game->players)) {
                return response()->json(['game' => $game, 'status' => $]);
            } else {
                return response()->json(['error' => 'Not authorized to add players'], 401);
            }
        });
    }

}