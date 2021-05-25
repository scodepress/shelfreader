<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App;

class GulpTestcontroller extends Controller
{
    public function postRequest($itemID = '000061345119')
{
	
    $client = new \GuzzleHttp\Client();
    $response = $client->request('POST', "Your API URL Goes Here");

    $response = $response->getBody()->getContents();

    $response = json_decode($response, true);
   
    $mybook = App\GulpTest::sortInfo($response,$itemID);

    $nid = \DB::table('mains')->select('id')->where('user_id', \Auth::user()->id)->count();
    
    	$main = new App\Main;
    	$main->id = $nid+1;
    	$main->user_id = $mybook[0];
    	$main->title = $mybook[1];
    	$main->barcode = $mybook[2];
    	$main->callno = $mybook[3];
    	$main->library = $mybook[4];

    	$main->save();


    return view('gulp.show', compact('response','itemID'));
}

public function store(Request $request)
    {
        $data = new GuzzlePost();
        $data->name=$request->get('name');
        $data->save();
        return response()->json('Successfully added');

    }

}
