<?php
/**
 * Created by PhpStorm.
 * User: Rickard
 * Date: 2016-10-23
 * Time: 19:34
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Guess extends Model
{
    protected $fillable = [
        'guess', 'word', 'time', 'user_id', 'game_id',
    ];



    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * The User that own the Picture
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    /**
     * The Game that the Picture belong to
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function game() {
        return $this->belongsTo('App\Models\Game');
    }

}