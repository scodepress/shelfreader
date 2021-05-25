<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use App\Models\Institution;
use App\Models\User;
use App\Models\MasterKey;
use Illuminate\Support\Facades\DB;

trait LibraryManagementTrait {
	
	public function nextLibraryId()
	{
		$lid = Institution::orderByDesc('id')->take(1)->pluck('id')[0];

		return $lid +1;
	}

	public function newLibrary($library_info)
	{

		$institution = $library_info['school_name'];
		$library = $library_info['library_name'];
		$street = $library_info['street'];
		$city = $library_info['city'];
		$state = $library_info['state'];
		$zip = $library_info['zip'];

		$in = new Institution;

		$in->institution = $institution;
		$in->library = $library;
		$in->street = $street;
		$in->city = $city;
		$in->state = $state;
		$in->zip = $zip;

		$in->save();
	}
	
	public function inventoryUsers()
	{
		return DB::table('institutions as i')
		->join('users as u','u.institution','=','i.id')
		->select('library','i.id')
		->where('u.privs',2)
		->get();

	}

	public function deleteMasterKeys($library_id)
	{
		MasterKey::where('library_id', $library_id)->delete();
	}

	public function resortMasterKeys($library_id)
	{

		$sortedKeys = MasterKey::getFullShelfOrder($library_id);

		$delete_success = self::deleteMasterKeys($library_id);

	

		foreach($sortedKeys as $key=>$sk) {
			$skeys[] = [
			'position' => $key+1,
            'library_id' => $library_id,
            'title' => $sk->title,
            'barcode' => $sk->barcode,
            'callno' => $sk->callno,
            'prefix' => $sk->prefix,
            'tp1' => $sk->tp1,
            'tp2' => $sk->tp2,
            'pre_date' => $sk->pre_date,
            'pvn' => $sk->pvn,
            'pvl' => $sk->pvl,
            'cutter' => $sk->cutter,
            'pcd' => $sk->pcd,
            'cutter_date' => $sk->cutter_date,
            'inline_cutter' => $sk->inline_cutter,
            'inline_cutter_decimal' => $sk->inline_cutter_decimal,
            'cutter_date2' => $sk->cutter_date2,
            'cutter2' => $sk->cutter2,
            'pcd2' => $sk->pcd2,
            'part1' => $sk->part1,
            'part2' => $sk->part2,
            'part3' => $sk->part3,
            'part4' => $sk->part4,
            'part5' => $sk->part5,
            'part6' => $sk->part6,
            'part7' => $sk->part7
        	];
		}

		DB::table('master_keys')->insert($skeys);
	}
}