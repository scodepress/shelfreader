<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{

    public static function tableBuild($table_name)

    {
		$query = "INSERT INTO main
		SELECT null,h.title,h.barcode,h.callno,null
		FROM  $table_name h 
		       LEFT JOIN main m ON (m.barcode = h.barcode)
		WHERE m.barcode IS NULL
        ";

       
		return \DB::connection()->getpdo()->exec($query);

    }

    public static function updateTable($table_name)

    {
        $query = "UPDATE main
        SET lib_name = '$table_name'
        WHERE lib_name IS NULL
        ";

        return \DB::connection()->getpdo()->exec($query);

    }


    public static function findLetters($table_name)

    {
    	return \DB::table($table_name)
    	->select('*')
    	->whereRaw("barcode REGEXP '[A-Za-z]+$'")
    	->get();
    }

    public static function deleteLetters($table_name)

    {
    	\DB::table($table_name)
    	->whereRaw("barcode REGEXP '[A-Za-z]+$'")
    	->delete();
    }


    public static function findDuplicates($table_name)

    {	
    	$duplicates = \DB::table($table_name)
    	->selectRaw("barcode, count(barcode) as b")
    	->groupBy('barcode')
    	->havingRaw("b>1")
    	->get();

    	if(count($duplicates)) {

    	foreach($duplicates as $d)

    	{
    		$dups[] = \DB::table($table_name)->select('id','barcode','callno','title')->where('barcode',$d->barcode)->get();
    	}
    	

    	$flattened = array_flatten($dups);  

    	$stuff = collect($flattened);

    	return $stuff;

    	}

    else

    { return "";}

    }

    public static function deleteDuplicates($table_name,$id)

    {
			\DB::table($table_name)->where('id', '=', $id)->delete();  

    }



        public static function importCsv($path, $filename, $table_name)
		{

		$csv = "$path/$filename"; 


		$query = sprintf("LOAD DATA local INFILE '%s' INTO TABLE $table_name FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' ESCAPED BY '\"' LINES TERMINATED BY '\\n' IGNORE 1 LINES (`title`, `barcode`, @var)
		      set callno = upper(@var)
		    ", addslashes($csv));

		   return \DB::connection()->getpdo()->exec($query);

		}

	public static function loadFile($filename)

	{
		$filepath = "/home/scott/uploads/$filename";

		$username = \Config::get('database.connections.mysql.username');
	    $password = \Config::get('database.connections.mysql.password');
	    $database = \Config::get('database.connections.mysql.database');

	    $command = "mysql -u $username -p$password $database < $filepath";

	    exec($command);

	}

    public static function dumpTable($table_name,$new_table_name)

    {   
        $filepath = "/home/scott/dumps/$new_table_name.sql";

        $username = \Config::get('database.connections.mysql.username');
        $password = \Config::get('database.connections.mysql.password');
        $database = \Config::get('database.connections.mysql.database');

        $command = "mysqldump -u $username  -p$password $database $table_name > $filepath";
       
        exec($command);

    }

    public static function Usages()

    {   
        return \DB::table('usages')
        ->join('users', 'users.id', '=', 'usages.user_id' )
        ->join('institutions', 'institutions.id', '=', 'users.institution')
        ->selectRaw("count(usages.user_id) as si, users.name, users.id, institutions.institution, Date(usages.created_at) as date")
        ->where('users.id', '!=', 1)
        ->groupBy('users.id')
        ->groupBy('users.name')
        ->groupBy('date')
        ->groupBy('institutions.institution')
        ->orderByDesc('date')
        ->orderByDesc('usages.created_at')
        ->paginate(10);
    }

    public static function dailyErrors($date,$user_id)
    {
    
        return \DB::table('shelf_errors')
        ->select('id')
        ->where('date',$date)
        ->where('user_id',$user_id)
        ->count();
    
    }

    public static function totalErrors($user_id)
    {
       return \DB::table('shelf_errors')
       ->select('id')
       ->where('user_id',$user_id)
       ->count();
    
    }


    public static function statusAlerts($date,$user_id)
    {
        
        return \DB::table('preports')
        ->select('id')
        ->where('user_id',$user_id)
        ->where('date', $date)
        ->count();
        
    }

    public static function getReport($user_id)
    {
        
        return \DB::table('preports')
        ->select('*')
        ->groupByDesc('date')
        ->paginate(10);
        
    }

    public static function Shadowed($date)
    {
    
        return \DB::table('shadows')
        ->select('id')
        ->where('user_id',\Auth::user()->id)
        ->where('created_at','>=', $date)
        ->where('created_at','<=', "$date 23:59:59")
        ->count();
    
    }

    public static function aggUsages()
    {
    return \DB::table('usages as us')
    ->join('users as u', 'u.id', '=', 'us.user_id')
    ->selectRaw("count(us.id) as num, u.name,u.id,us.date")
    ->groupBy('us.user_id')
    ->orderByDesc('num')
    ->paginate(10);

}

}
