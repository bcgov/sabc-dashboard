<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormCategory extends Model
{
    use SoftDeletes;

    /**
     * Attached categories
     */
    public function forms()
    {
        return $this->hasMany('App\Form', 'category_id', 'id');
    }
}
