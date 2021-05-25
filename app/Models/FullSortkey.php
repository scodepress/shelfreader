<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FullSortkey extends Model
{
    public function fshelf()
    {
        return $this->hasOne(FullShelf::class,'user_id');
    }
}
