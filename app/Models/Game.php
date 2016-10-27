<?php
/**
 * Created by PhpStorm.
 * User: Rickard
 * Date: 2016-10-23
 * Time: 19:31
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'active'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * Users that play this Game
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function players() {
        return $this->belongsToMany('App\Models\User');
    }

    /**
     * Pictures belonging to this Game
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pictures() {
        return $this->hasMany('App\Models\Picture');
    }

    /**
     * The User that own the Game
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner() {
        return $this->belongsTo('App\Models\User');
    }

    public function currentPlayer()
    {
        return $this->hasOne('App\Models\User', 'current_player', 'game_id');
    }

}