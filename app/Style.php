<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Style extends Model
{
    public $table = "styles";

    public function brand()
    {
        return $this->belongsTo('App\Brand');
    }

    public function category()
    {
        return $this->belongsTo('App\Category');
    }
}
