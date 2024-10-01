<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $primaryKey = 'rid';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'weight',
    ];

    /**
     * The roles that belong to the user.
     */
    public function users()
    {
        return $this->belongsToMany('App\User', 'role_user', 'rid', 'uid', null, 'uid');
    }
}
