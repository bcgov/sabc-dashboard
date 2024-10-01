<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeclarationField extends Model
{
    public function declaration()
    {
        return $this->belongsTo('App\Declaration');
    }
}
