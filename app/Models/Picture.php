<?php
/**
 * Created by PhpStorm.
 * User: Rickard
 * Date: 2016-10-23
 * Time: 19:34
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Picture extends Model
{
    protected $fillable = [
        'data', 'time', 'word', 'user_id', 'game_id',
    ];



    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    public function owner() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function game() {
        return $this->belongsTo('App\Models\Game');
    }

}