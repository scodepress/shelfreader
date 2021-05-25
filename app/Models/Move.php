<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Subsequence;

class Move extends Model
{
    public static function nextMove()
    {
        return \DB::table('moves')
        ->join('sorts', 'sorts.barcode', '=', 'moves.barcode')
        ->selectRaw("moves.barcode")
        ->where('moves.moved', 0)
        ->where('moves.user_id',Auth::id())
        ->where('sorts.user_id',Auth::id())
        ->orderBy('moves.cposition')
        ->first();

    }

    public static function Moves()
    {
    
    	$moves = array_diff($gcpositions,$liss); // Get positions of books not in lis
       

        return array_values($moves);
    
    }



}
