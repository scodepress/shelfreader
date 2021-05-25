<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BadBarcode extends Model
{
	use HasFactory;

	public static function usersBadBarcodes($user_id, $date) {

		return DB::table('bad_barcodes as b')
			->join('users as u','b.user_id','=','u.id')
			->select('u.name','b.user_id','barcode','b.created_at')
			->where('user_id', $user_id)
			->whereDate('b.created_at','=', $date)
			->orderBy('b.created_at')
			->get();


	}
}
