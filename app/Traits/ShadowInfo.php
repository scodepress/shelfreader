<?php

namespace App\Traits;

trait ShadowInfo {
	
	public function unknown()

	{
		return \DB::table('shadows as s')
    	->join('users as u', 'u.id', '=', 's.user_id')
    	->join('institutions as i', 'i.id', '=', 'u.institution')
    	->select('s.barcode','u.name','i.institution','s.created_at')
    	->where('s.title', 'unknown')
    	->paginate(10);
	}

	public function titled()
    {
    	return \DB::table('shadows as s')
    	->join('users as u', 'u.id', '=', 's.user_id')
    	->join('institutions as i', 'i.id', '=', 'u.institution')
    	->select('s.barcode','i.institution','u.name','s.created_at')
    	->where('s.title', '!=', 'unknown')
    	->paginate(10);  
    }
	
}