<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Institution extends Model
{
	

	public static function info() 

	{
		return \DB::table('institutions') 
		->select('*')
		->get();
	}

	
   
}
