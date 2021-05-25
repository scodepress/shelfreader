<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationName extends Model
{
    public static function Remove($barcode)
    {
    	$action = \DB::table('location_names as l')
    	->join('item_statuses as i', 'l.name', '=', 'i.current_location')
    	->select('action')
    	->where('i.barcode',$barcode)
    	->first(); 

    	 if($action) { return $action->action; }
    
    }
}
