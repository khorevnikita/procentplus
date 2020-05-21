<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    public function point()
    {
        return $this->belongsTo(PointOfSale::class);
    }
}
