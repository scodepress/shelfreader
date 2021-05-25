<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shelf extends Model
{
    public static function getSection($main_table)
    {
    	return \DB::table($main_table) 
    	->select('id','barcode','title','callno')
        ->take(2000)
        ->inRandomOrder()
    	->get();
    }

    public static function shelfId ($ibarcode) 

    {    
    	return \DB::table('shelfs') 
    	->select('shelf_id')
    	->where('barcode', '=', $ibarcode)
    	->orderBy('shelf_id')
    	->first();

    	$request->session()->put('lshid', $shelfId->shelf_id);

    }

    public static function nextBook($shelf_id)
    {
    	return \DB::table('shelfs')
    	->select('barcode')
    	->where('shelf_id', '>', $shelf_id)
    	->orderBy('shelf_id')
    	->limit(1)
    	->get();
    }

      public static function getPosition($shelf_id)

    {
        return \DB::table('shelfs') 
        ->select('barcode')
        ->where('shelf_id', '<=', $shelf_id)
        ->count();

    }

    public static function importCsv($path, $filename,$table_name)
{

$csv = "$path/$filename"; 


$query = sprintf("LOAD DATA local INFILE '%s' INTO TABLE $table_name FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' ESCAPED BY '\"' LINES TERMINATED BY '\\n' IGNORE 1 LINES (`title`, `barcode`, @var)
      set callno = upper(@var)
    ", addslashes($csv));

   return \DB::connection()->getpdo()->exec($query);

}



}


