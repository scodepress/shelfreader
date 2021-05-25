<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mposition extends Model
{
       public static function mpos()

    {
        // Gets the position where the next book will be moved -- for placement of the green line, shelf correction

        // complete array of correct positions of books on the shelf in order of their position
        $gcp = $this->shelf_cpositions(); 

        // all cpositions in full_listers table ordered by cposition
        $lis = $this->listers_cpositions();

        // Gets barcode of next book to be moved
        $move = $this->move_cposition();

        $mgc = max($gcp);
        $mnc = min($gcp);
       
        $mpos = null;
        foreach($lis as $key=>$l)

        {
         if($move < $l)

         {

            //Get current position of book with cposition of $l
            $cup = \DB::table($this->table1())
            ->where('cposition', $l)
            ->where('user_id',\Auth::user()->id)
            ->pluck('position')[0];

            $mp = \DB::table($this->table1())
            ->where('cposition', $move)
            ->where('user_id',\Auth::user()->id)
            ->pluck('position')[0];
            
            if($mp > $cup) { return $cup-1; } //book is moving from right of $l to position of $l 
             
             else // book is moving from left of $l to left adjacent of $l
             
             { 
             	$mpos = $cup-1; 

             	if($mpos<$mnc) { return $move; } 
             	else 
             		{ return $mpos; }  

             }

         }

     }

     if($mpos == null)

     {
        foreach($lis as $key=>$l)

        {
            if($move>$l)

            {   

               $cup = \DB::table($this->table1())
               ->where('cposition', $l)
               ->where('user_id',\Auth::user()->id)
               ->pluck('position')[0];
               $mpos = $cup+($mgc-$cup); 
            }
        }

        return $mpos;

    }

}
}
