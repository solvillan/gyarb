<?php
/**
 * Created by PhpStorm.
 * User: rickard
 * Date: 2016-10-20
 * Time: 19:28
 */

namespace App\Http\Controllers;


use App\Models\User;
use App\Utils\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Register a new User
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request) {

        if ($usertest = User::where(['email' => $request->input('email')])->first()) {
            return response()->json(['error' => 'User already exist'], 406);
        }

        $request->merge(['password' => Hash::make($request->get('password'))]);
        $request->merge(['token' => Auth::generateKey($request->get('email'), $request->get('password'))]);

        if ($user = User::create($request->all())) {
            return response()->json(['id' => $user->id, 'email' => $user->email, 'name' => $user->name], 201);
        } else {
            return response()->json(['error' => 'Malformed request'], 400);
        }
    }

    /**
     * Authenticate User and return a Token
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function auth(Request $request) {
        if ($request->input('email') && $request->input('password')) {
            if ($user = User::where(['email' => $request->input('email')])->first()) {
                if (Hash::check($request->input('password'), $user->password)) {
                    $user->token = Auth::generateKey($user->email, $user->password);
                    $user->save();
                    $token = Auth::generateToken($user->email, $user->token);
                    return response()->json(['id' => $user->id,'email' => $user->email, 'token' => $token]);
                }
            }
        }
        return response()->json(['error' => 'Failed to login'], 403);
    }

    /**
     * Auth and init session
     * (For web login)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authSession(Request $request) {
        if ($request->input('email') && $request->input('password')) {
            if ($user = User::where(['email' => $request->input('email')])->first()) {
                if (Hash::check($request->input('password'), $user->password)) {
                    $user->token = Auth::generateKey($user->email, $user->password);
                    $token = Auth::generateToken($user->email, $user->token);
                    session_start();
                    $_SESSION['token'] = $token;
                    $_SESSION['name'] = $user->name;
                    session_commit();
                    return response()->json(['id' => $user->id,'email' => $user->email, 'token' => $token]);
                }
            }
        }
        return response()->json(['error' => 'Failed to login'.($request->input('email') ? '' : ' - No email').($request->input('password') ? '' : ' - No password')], 403);
    }

    public function logout() {
        session_start();
        $_SESSION['token'] = null;
        $_SESSION['name'] = null;
        session_destroy();
        return redirect("/");
    }

    /**
     * Check if Token exist
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkToken(Request $request) {
        if ($request->header('Token')) {
            $user = Auth::checkToken($request->header('Token'));
            if ($user === Auth::TOKEN_INVALID) {
                return response()->json(['error' => 'Token not valid!'], 403);
            } else if ($user === Auth::TOKEN_NOT_EXIST) {
                return response()->json(['error' => 'Token does not exist!'], 403);
            } else if ($user === Auth::TOKEN_EXPIRED) {
                return response()->json(['error' => 'Token expired'], 498);
            } else if ($user) {
                return response()->json(['name' => $user->name, 'email' => $user->email, 'id' => $user->id]);
            } else {
                return response()->json(['error' => 'Something weird happened...'], 500);
            }
        }
        return response()->json(['error' => 'No token sent'], 400);
    }

    /**
     * Add a friend
     * @param Request $request
     * @param $fid
     * @return \Illuminate\Http\JsonResponse
     */
    public function addFriend(Request $request, $fid) {
        return Auth::runAsUser($request, function ($req, $user) use ($fid) {
            $user->friendWith()->attach($fid);
            $user->save();
            return response()->json(['friends' => $user->friends()]);
        });
    }

    /**
     * List all users
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function listUsers() {
        return $this->listUsersFiltered(null);
    }

    /**
     * List users where name like %filter%
     * @param $filter
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function listUsersFiltered($filter) {
        if ($filter) {
            $users = User::where('name', 'like', '%'.$filter.'%')->get();
            if ($users->count() > 0) {
                return response($users->toJson());
            } else {
                return response()->json(['error' => 'No user found for: '.$filter], 404);
            }
        } else {
            $users = User::all()->toJson();
            return response($users);
        }
    }

    /**
     * List friends for user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listFriends(Request $request) {
        return Auth::runAsUser($request, function ($req, $user) {
            return $this->listFriendsForId($req, $user->id);
        });
    }

    /**
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function listFriendsForId(Request $request, $id) {
        return Auth::runAsUser($request, function ($req, $user) use ($id) {
            $other = User::where('id', $id)->first();
            return response()->json(['dummy' => 'dummy', 'friends' => $other->friends()]);
        });
    }

    /**
     * List all games a user play
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listGames(Request $request) {
        return Auth::runAsUser($request, function ($req, User $user) {
            return response()->json($user->plays);
        });
    }

    /**
     * Get player info from id
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function infoAsUser(Request $request, $id) {
        return Auth::runAsUser($request, function ($req, User $user) use ($id){
            $other = User::where('id', $id)->first();
            return response()->json($other);
        });
    }

    /**
     * Get info for current player
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function info(Request $request) {
        return Auth::runAsUser($request, function ($req, User $user) {
            return $this->infoAsUser($req, $user->id);
        });
    }

    public function refreshToken(Request $request){
        //TODO Return new token based on the old, unless token is too old
        Auth::runAsUser($request, function ($req, User $user) {
            $user->token = Auth::generateKey($user->email, $user->password);
            $user->save();
            return response()->json(['token' => Auth::generateToken($user->email, $user->token)]);
        });
    }

}
