<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Usage extends Model
{

	public static function getUsersScans($user_id,$date) {

		return DB::table('usages')
			->select('barcode','user_id','date','created_at')
			->whereNotIn('barcode', function ($query) use ($user_id,$date) {
				$query->select('barcode')->from('master_keys')
			     ->where('user_id',$user_id)
			     ->whereDate('created_at',$date);
			})
			->where('user_id',$user_id)
			->where('date',$date)
			->where('barcode','!=','')
			->distinct('barcode')
			->get();
	}

	public static function uniqueScans($user_id,$date) {

		return DB::table('usages')
			->select('barcode')
			->where('date',$date)
			->where('user_id', $user_id)
			->distinct('barcode')
			->count();

	}
	public static function getDuplicateScans($user_id,$date) {

		$bcodes = DB::table('usages') 
			->where('user_id',$user_id)
			->where('date',$date)
			->groupBy('barcode')
			->havingRaw("count(barcode)>1")
			->pluck('barcode');

		return DB::table('usages')
			->select('barcode','created_at')
			->where('user_id',$user_id)
			->where('date',$date)
			->whereIn('barcode',$bcodes)
			->orderBy('created_at')
			->get(); 


	}

}
