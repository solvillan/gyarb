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

    const PREPARING = 0, DRAW_PICTURE = 1, GUESS_PICTURE = 2, FINISHED = 3;

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
            $game->currentPicture()->associate(null);
            $game->save();
            //$game->players()->attach($user);
            $user->plays()->attach($game);
            $user->save();
            $game->save();
            $game_pivot = $user->plays()->where('game_id', $game->id)->first();
            $game_pivot->pivot->score = 0;
            $game_pivot->pivot->save();
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
            return Auth::runAsUser($request, function (Request $request, $user) use ($gid) {
                $game = Game::find($gid);
                if (Auth::userMemberOf($request->header('Token'), $game->players)) {
                    $player = User::find($request->input('player_id'));
                    $player->plays()->attach($game);
                    //$game->players()->attach($player);
                    $game->save();
                    $player->save();
                    $game_pivot = $player->plays()->where('game_id', $game->id)->first();
                    $game_pivot->pivot->score = 0;
                    $game_pivot->pivot->save();
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
     * Submit a picture to game where post contain a JSON payload with data-property containing Base64encoded GZipped Json formatted picture data (phew...)
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
                        $picture->time = date_create()->getTimestamp();
                        $picture->save();
                        $game->status = GameController::GUESS_PICTURE;
                        $game->currentPicture()->associate($picture);
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

    /**
     * Submit a guess via JSON formatted payload
     * @param Request $request
     * @param $gid
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitGuess(Request $request, $gid) {
        return Auth::runAsUser($request, function (Request $request, User $user) use ($gid){
            $payload = json_decode($request->input('payload'));
            /** @var Game $game */
            $game = Game::findOrFail($gid);
            if (Auth::userMemberOf($request->header("Token"), $game->players)) {
                $hasGuessed = Guess::where(['user_id' => $user->id, 'picture_id' => $game->currentPicture->id])->count();
                if ($hasGuessed > 0) {
                    return response()->json(['error' => 'Guess already submitted']);
                }
                $guess = new Guess();
                $guess->owner()->associate($user);
                $guess->game()->associate($game);
                $guess->picture()->associate($game->currentPicture);
                $guess->guess = strtolower($payload->guess);
                $guess->time = $payload->time;
                $guess->save();
                $guess_count = Guess::where(['game_id' => $game->id, 'picture_id' => $game->currentPicture->id])->count();
                $player_count = $game->players()->count();
                $correct = $guess->guess == $game->current_word;
                if ($guess_count == $player_count - 1) {
                    $this->resetGame($game);
                }
                if ($correct) {
                    $score = $this->calcScore($user, $guess, $game);
                } else {
                    $game_pivot = $user->plays()->where('game_id', $game->id)->first()->pivot;
                    $score = ['guess_score' => 0, 'total_score' => $game_pivot->score];
                }
                return response()->json(['correct' => $correct, 'guess_score' => $score['guess_score'], 'total_score' => $score['total_score']]);
            } else {
                return response()->json(['error' => 'Player not part of game'], 403);
            }
            
        });
    }

    /**
     * Calculates the score for a player
     *
     * Equation
     * y = x^(-(x/600))*500
     *
     * @param User $user
     * @param Guess $guess
     * @param Game $game
     * @return array
     */
    private function calcScore(User $user, Guess $guess, Game $game) {
        $game_pivot = $user->plays()->where('game_id', $game->id)->first()->pivot;
        $score = floor((pow($guess->time, - ($guess->time / 600)) * 500) + 0.5);
        $game_pivot->score += $score;
        $game_pivot->save();
        return ['guess_score' => $score, 'total_score' => $game_pivot->score];
    }

    /**
     * Reset game to drawing state with new player and new word
     * @param Game $game
     */
    private function resetGame(Game $game) {
        if ($game->pictures()->count() < $game->players()->count()*2) {
            $game->status = GameController::DRAW_PICTURE;
            $game->current_word = Wordlist::getWord();
            $new_player = $game->players()->get()->shuffle()->first();
            $game->currentPlayer()->associate($new_player);
            $game->save();
        } else {
            $game->status = GameController::FINISHED;
        }
    }

    /**
     * Poll current game state
     * @param Request $request
     * @param $gid
     * @return \Illuminate\Http\JsonResponse
     */
    public function poll(Request $request, $gid) {
        return Auth::runAsUser($request, function ($request, $user) use ($gid) {
            $game = Game::findOrFail($gid);
            if (Auth::userMemberOf($request->header("Token"), $game->players)) {
                if ($game->currentPicture != null) {
                    if (date_create()->getTimestamp() - $game->currentPicture->time > 86400000) {
                        $this->resetGame($game);
                    }
                }
                $game->currentPicture;
                return response()->json(['game' => $game, 'has_move' => $this->userHasMove($user, $game)]);
            } else {
                return response()->json(['error' => 'Not authorized to add players'], 401);
            }
        });
    }

    /**
     * Check if user can make a move in the current game state
     * @param User $user
     * @param Game $game
     * @return bool
     */
    private function userHasMove(User $user, Game $game) {
        /*if ($game->status = GameController::DRAW_PICTURE && $game->currentPlayer == $user) return true;
        if ($game->status = GameController::GUESS_PICTURE && $game->currentPlayer != $user) return true;
        return false;*/
        return ($game->status == GameController::DRAW_PICTURE && $game->currentPlayer == $user) || ($game->status == GameController::GUESS_PICTURE && $game->currentPlayer != $user);
    }

}