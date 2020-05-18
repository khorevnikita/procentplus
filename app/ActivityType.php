<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityType extends Model
{
    public function partners()
    {
        return $this->hasMany(Partner::class);
    }
}
