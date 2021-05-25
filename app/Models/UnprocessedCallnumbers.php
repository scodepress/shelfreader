<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UnprocessedCallnumbers extends Model
{
    use HasFactory;
    public static function getUnprocessedCallNumbers($user_id,$date) {
    
		return DB::table('unprocessed_callnumbers as c')
			->join('users as u','c.user_id','=','u.id')
			->select('u.name','c.user_id','barcode','callnumber','c.created_at')
			->where('user_id', $user_id)
			->whereDate('c.created_at','=', $date)
			->orderBy('c.created_at')
			->get();

    }
}
