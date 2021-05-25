<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\CustomClasses\Node;
use App\Models\Sort;
use Illuminate\Support\Facades\Auth;

class Lister extends Model
{
   public static function Plist()
   {
   
      return \DB::table('listers')
      ->where('user_id', Auth::id())
      ->orderBy('cposition')
      ->pluck('cposition');
   
   }

   public static function listers_cpositions()

    {
        // Gets all cpositions in full_listers table ordered by cposition
        return \DB::table($this->tlisters())
        ->select('cposition')
        ->where('user_id',\Auth::id())
        ->orderBy('cposition')
        ->pluck('cposition')
        ->toArray();
    }

    public static function Lis()
    {
    	
    	$gcpositions = Sort::getPositions();

    	$list = new Node;
    	$lis = $list->seq($gcpositions);

    	return $lis;
    
    }

    public static function insert()
    {
    
    
    
    }

    


}
