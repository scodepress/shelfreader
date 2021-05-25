<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sort;

class Subsequence extends Model
{


 public static function Plist()
   {
   
      return \DB::table('listers')
      ->where('user_id', \Auth::id())
      ->orderBy('cposition')
      ->pluck('cposition');
   
   }

   public static function listers_cpositions()

    {
        // Gets all cpositions in full_listers table ordered by cposition
        return \DB::table($this->tlisters())
        ->select('cposition')
        ->where('user_id', \Auth::id())
        ->orderBy('cposition')
        ->pluck('cposition')
        ->toArray();
    }

    public static function Lis()
    {
    	
    	$gcpositions = Sort::getPositions();

    	$lis = self::seq($gcpositions);

    	return $lis;
    
    }

    public static function insert()
    {
    
    
    
    }

}
