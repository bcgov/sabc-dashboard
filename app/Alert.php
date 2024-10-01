<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{

    public function pages()
    {
        return $this->hasMany('App\AlertPage');
    }
}
