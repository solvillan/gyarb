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

    /**
     * The game that this picture is the current one (if there is one)
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function isCurrentPictureAt()
    {
        return$this->hasOne('App\Models\Picture', 'current_picture_id');
    }

    /**
     * The guesses for the picture
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function guesses()
    {
        return $this->hasMany('App\Model\Guess');
    }

}