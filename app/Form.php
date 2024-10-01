<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Form extends Model
{
    use SoftDeletes;

    /**
     * Attached categories
     */
    public function category()
    {
        return $this->hasOne('App\FormCategory', 'id', 'category_id');
    }
}
