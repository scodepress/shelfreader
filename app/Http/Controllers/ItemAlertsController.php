<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Shadow;
use App\Preport;
use ItemAlert;

class ItemAlertsController extends Controller
{    

	public function show()
	{

	###########################################################################################################
    # This method will get the entries in the old reports tables and insert them in the item_alerts table
	#

    // Get stuff from shadows

	$shadows = Shadow::get();

	foreach($shadows as $key=>$s)

	{
		$shad[] = array(
				  
				  'user_id' => $s->user_id,
				  'barcode' => $s->barcode,
				  'call_number' => 'unknown',
				  'title' => $s->title,
				  'current_location' => 'Users Library',
				  'home_location' => 'unknown',
				  'created_at' => $s->created_at

				);
	}

	$it = \DB::table('item_alerts')->insert($shad);


	$preports = Preport::get();

	foreach($preports as $key=>$s)

	{
		$port[] = array(
				  
				  'user_id' => $s->user_id,
				  'barcode' => $s->barcode,
				  'call_number' => $s->callnum,
				  'title' => $s->title,
				  'current_location' => $s->location_id,
				  'home_location' => 'Users Library',
				  'created_at' => $s->created_at

				);
	}

	$prep = \DB::table('item_alerts')->insert($port);

	return view('item_alerts.show', compact('prep','it'));

}

}
