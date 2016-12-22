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
        'active', 'state'
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
        return $this->belongsToMany('App\Models\User')->withPivot('score', 'state');
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

    /**
     * The current player
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currentPlayer()
    {
        return $this->belongsTo('App\Models\User', 'current_player');
    }

    /**
     * The current picture (if there is one)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currentPicture()
    {
        return $this->belongsTo('App\Models\Picture', 'current_picture_id');
    }

}