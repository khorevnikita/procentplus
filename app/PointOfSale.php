<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PointOfSale extends Model
{
    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}
