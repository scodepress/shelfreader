<?php

namespace App\Traits;

use App\Models\MasterKey;
use App\Models\Sortkey;
use Illuminate\Support\Facades\Auth;
use App\Jobs\InsertMasterKey;
use App\Jobs\MasterKeyOrder;
use App\Models\User;
use App\Models\Institution;


trait MasterKeyTrait {
	
	public function storeKey()

	{  
		$mkey = new MasterKey;
		
	}

	public function masterKeyCount($barcode,$library_id)
	{
		
		return MasterKey::where('barcode', $barcode)->where('library_id',$library_id)->count();
	}

	public function runMasterKeyJob($user_id,$callnumber,$barcode,$title,$pre_sort_key,$library_id)
	{
		
		$this->dispatch(new InsertMasterKey($user_id,$callnumber,$barcode,$title,$pre_sort_key,$library_id));

	
		$this->dispatch(new MasterKeyOrder($callnumber,$library_id));

	}

	public function userPrivs()
	{
		return User::where('id',Auth::user()->id)->pluck('privs')[0];
	}

	public function userLibraryId()
	{
		return User::where('id', Auth::user()->id)->pluck('institution')[0];
	}

	public function createInsertArray($books, $library_id)
	{
		
		foreach($books[0] as $row) {
	        //  dd($row[1].' '.$row[2]);
	        $arr[] = [
	            // If uncomment this id from here, remove [0] from foreach
	            // 'id' => $row[0], 
	            'position' => $row[0],
	            'library_id' => $library_id,
	            'title' => $row[2],
	            'barcode' => $row[3],
	            'callno' => $row[4],
	            'prefix' => $row[5],
	            'tp1' => $row[6],
	            'tp2' => $row[7],
	            'pre_date' => $row[8],
	            'pvn' => $row[9],
	            'pvl' => $row[10],
	            'cutter' => $row[11],
	            'pcd' => $row[12],
	            'cutter_date' => $row[13],
	            'inline_cutter' => $row[14],
	            'inline_cutter_decimal' => $row[15],
	            'cutter_date2' => $row[16],
	            'cutter2' => $row[17],
	            'pcd2' => $row[18],
	            'part1' => $row[19],
	            'part2' => $row[20],
	            'part3' => $row[21],
	            'part4' => $row[22],
	            'part5' => $row[23],
	            'part6' => $row[24],
	            'part7' => $row[25]
		        ];
	    }

	    return $arr;

	}

	public function insertIntoTempTable($created_array)
	{
		MasterKey::insertIntoTempKeys($created_array);

	}

	public function createTempKeysTable()
	{
		$create_table = MasterKey::createTempKeys();
	}

	

}
