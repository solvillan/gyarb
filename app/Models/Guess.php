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
        'guess', 'word', 'time', 'user_id', 'game_id', 'picture_id',
    ];



    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * The User that own the Guess
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    /**
     * The Game that the Guess belong to
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function game() {
        return $this->belongsTo('App\Models\Game');
    }

    /**
     * The {@link App\Model\Picture} that the Guess belongs to
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function picture(){
        return $this->belongsTo('App\Models\Picture');
    }

}