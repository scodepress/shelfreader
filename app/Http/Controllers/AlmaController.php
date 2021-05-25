<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AlmaController extends Controller
{
    $client = new \GuzzleHttp\Client();
    $mms_id = 991432780000541;
    $apikey = 'l8xx085cf0de64614ac1b27417797779362e';

    $response = $client->request('POST', "https://api-na.hosted.exlibrisgroup.com/almaws/v1/bibs/$mms_id/holdings?apikey=
    	$apikey&format=json");

    $response = $response->getBody()->getContents();

    $response = json_decode($response, true);
   
    $mybook = App\GulpTest::sortInfo($response,$itemID);

    $nid = \DB::table('mains')->select('id')->where('user_id', \Auth::user()->id)->count();


    return view('gulp.show', compact('response','itemID'));
}
