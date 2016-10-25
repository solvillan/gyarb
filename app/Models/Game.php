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

    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    public function players() {
        return $this->belongsToMany('App\Models\User');
    }

    public function pictures() {
        return $this->hasMany('App\Models\Picture');
    }

}