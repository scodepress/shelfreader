<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\ShelfError;

class ShelfError extends Model
{
    public static function insertError($barcode)
    {
    
    	$shelfee = new ShelfError;

    	$shelfee->user_id = Auth::user()->id;
    	$shelfee->date = date('Y-m-d');
    	$shelfee->barcode = $barcode;

    	$shelfee->save();
    
    }
}
