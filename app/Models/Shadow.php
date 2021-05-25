<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shadow extends Model
{
    public static function getUnknown($barcode)
    {
    	return \DB::table('shadows as s')
    	->join('users as u', 'u.id', '=', 's.user_id')
    	->join('institutions as i', 'i.institution', '=', 'u.institution')
    	->select('s.barcode'.'i.institution','u.name','s.created_at')
    	->where('title', 'unknown')
    	->get();
    
    
    }

    public static function getTitled($barcode)
    {
    	return \DB::table('shadows as s')
    	->join('users as u', 'u.id', '=', 's.user_id')
    	->join('institutions as i', 'i.institution', '=', 'u.institution')
    	->select('s.barcode'.'i.institution','u.name','s.created_at')
    	->where('title', '!=', 'unknown')
    	->get();  
    }
}
