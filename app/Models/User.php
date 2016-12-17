<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model //implements AuthenticatableContract, AuthorizableContract
{
    //use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'token',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password','token',
    ];

    /**
     * The games a User is playing
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function plays() {
        return $this->belongsToMany('App\Models\Game', 'game_user');
    }

    /**
     * The pictures drawn by User
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pictures() {
        return $this->hasMany('App\Models\Picture');
    }

    /**
     * The Games owned by User
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function games() {
        return $this->hasMany('App\Models\Game');
    }

    /**
     * List of friends added by User
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function friends() {
        return $this->belongsToMany('App\Models\User', 'friend', 'user');
    }

    /**
     * List of Users who added User as friend
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function friendWith() {
        return $this->belongsToMany('App\Models\User', 'user', 'friend');
    }

    public function currentDraws()
    {
        return $this->belongsToMany('App\Models\Game', 'current_player', 'id');
    }

}
