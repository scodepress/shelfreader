<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Search extends Model
{
    public static function searchShelfs($word,$main_table)
    {
    	return \DB::table($main_table)
    	->select('title', 'callno')
    	->where('title', 'like', '%' . $word . '%')
    	->get();
    }

    public static function getMain()

    {
        return \DB::table('institutions')
        ->join('users', 'users.institution', '=', 'institutions.id')
        ->select('main_table')
        ->where('users.id', '=', Auth::user()->id)
        ->first();
    }
}
