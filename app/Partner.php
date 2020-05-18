<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    public function bonuses()
    {
        return $this->hasMany(Bonus::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function points()
    {
        return $this->hasMany(PointOfSale::class);
    }

    public function sales()
    {
        return $this->hasMany(SaleRecord::class);
    }
}
