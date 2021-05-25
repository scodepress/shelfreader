<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

class ShelfsController extends Controller
{
    public function create () 
    {

    	return view('shelfs.create');
    }

    public function show($ibarcode) 
    {

      $main_table = "main"; //Get user's library info
    	$section = \App\Models\Shelf::getSection($main_table);
 
    
    	return view('shelfs.show', compact('section'));
    }

    public function scan_in(Request $request) 
    {
    	$ibarcode = $request['ibarcode'];

       return redirect()->action('ShelfsController@show', ['ibarcode' => $ibarcode]);
    }

    public function showId(Request $request) 

    {
        // $barcode = $request['barcode'];

        // if ($request->session()->exists('lshid')) {
        // $lshid = $request->session()->pull('lshid', 'default');
        // }
        // $shelfId = \App\Shelf::shelfId($barcode);

        // $next_scanned_id = \App\Shelf::nextBook($shelfId->shelf_id);
        // if ($lshid) {
        // $next_stored_id = \App\Shelf::nextBook($lshid);
        // }
        // //See if the above 2 are the same
        // if ($lshid) {
        // if ($next_scanned_id->shelf_id != $next_stored_id->shelf_id)

        //     { 
        //         //Throw a fit
        //     }

        // }
    }

    public function shelf_links()

    {

        return view('shelfs.shelving_links');
    }



public function store_csv (Request $request)
{
     
      $file = $request->file('csv');

      $table_name = $request['table_name'];

      //Display File Name
      echo 'File Name: '.$file->getClientOriginalName();
      echo '<br>';
   
      //Display File Extension
      echo 'File Extension: '.$file->getClientOriginalExtension();
      echo '<br>';
   
      //Display File Real Path
      echo 'File Real Path: '.$file->getRealPath();
      echo '<br>';
   
      //Display File Size
      echo 'File Size: '.$file->getSize();
      echo '<br>';
   
      //Display File Mime Type
      echo 'File Mime Type: '.$file->getMimeType();
   
      //Move Uploaded File
      $destinationPath = 'uploads';
      $file->move($destinationPath,$file->getClientOriginalName());

      $count = \App\Shelf::importCsv($destinationPath, $file->getClientOriginalName(), $table_name);

      return redirect()->action('ShelfsController@create')
                    ->with('message', "$count rows inserted");

   }


 


}