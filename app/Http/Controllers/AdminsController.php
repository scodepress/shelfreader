<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Traits\ShadowInfo;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class AdminsController extends Controller
{
    use ShadowInfo;

    public function create()

    {
      $usage = Admin::Usages();

      $agg = Admin::aggUsages();
      $titled = $this->titled();
      $unknown = $this->unknown();

      $cans = array($usage,$titled,$unknown);

      sort($cans);

      $page = end($cans);

    	return view('admins.create', compact('usage','agg','unknown','titled','page'));
    }

    public function show($table_name)

    {
  
    	$build = Admin::tableBuild($table_name);
      $up = Admin::updateTable($table_name);
    	
    	return view('admins.show');
    }

    public function store(Request $request)

    {	
    	$table_name = $request['table_name'];


    	return redirect()->action('AdminsController@show', ['table_name' => $table_name]);

    }

    public function analyze_table(Request $request)

    {
    	$table_name = $request['table_name'];

    	$letters = Admin::findLetters($table_name);

    	$duplicates = Admin::findDuplicates($table_name);

    	return view('admins.analyze', compact('letters','duplicates','table_name'));
    }

    public function store_sql(Request $request)

    {
        $filename = $request['file_name'];

        $load = Admin::loadFile($filename);
    }

    public function delete_letters(Request $request)

    {
        $table_name = $request['table_name'];

        $dl = Admin::deleteLetters($table_name);

        return view('admins.create')->with('success', 'Rows were deleted.');
    }

    public function delete_duplicates(Request $request)

    {
        $table_name = $request['table_name'];
        $id = $request['id'];

        $del = Admin::deleteDuplicates($table_name,$id);

        $table_name = $request['table_name'];

        $letters = Admin::findLetters($table_name);

        $duplicates = Admin::findDuplicates($table_name);

        return view('admins.analyze', compact('letters','duplicates','table_name'))->with('success', 'Item deleted.');
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

      $count = Admin::importCsv($destinationPath, $file->getClientOriginalName(), $table_name);

      return redirect()->action('AdminsController@create')
                    ->with('message', "$count rows inserted");

   }

   public function dump_table(Request $request)

   {    
        $table_name = $request['table_name'];
        $new_table_name = $request['new_table_name'];



        $dump = Admin::dumpTable($table_name,$new_table_name);

       

        if($dump) {

        return redirect()->action('AdminsController@create')->with('message', "Table dump successful.");   

        }

        else 

        { return redirect()->action('AdminsController@create')->with('message', "Table dump failed.");   } 
   }

   public function flush_opcache()
   {
      Artisan::call('optimize');

    return view('admins.flush_cache');
   }

   public function php_info()
   {

    return view('admins.php_info');
   }
   

}
