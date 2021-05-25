<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class section extends Model
{
    public static function countSections()
    {
    
    	return \DB::table('sections')
    	->select('id')
    	->where('user_id', Auth::user()->id)
    	->count();
    
    }

    public static function sPositions()

    {
    	return \DB::table('sections')
    	->select('id','barcode','position','current')
    	->where('user_id', Auth::user()->id)
    	->orderBy('id')
    	->get();
    }

}
