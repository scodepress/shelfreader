<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\FullSortKey;
use App\Models\FullShelf;


class Location extends Model
{
    public static function bookPosition($barcode)
    {
      ##################################################
      #Get the position of the book if it's in the table
      #

    	return \DB::table('full_shelves')
    	->select('position')
    	->where('barcode',$barcode)
    	->first()->position;

    
    }

    public static function correctPosition()
    {
      ##################################################
      #Get the correct position of the book if it's not in the table
      #
    
    
    }

    public static function checkBook($barcode)
    {
      ##################################################
      # See if the book is in the table 
      #
    	return FullShelf::where('barcode',$barcode)->count();
    
    }

    public static function Api()
    {
      ##################################################
      #  Get info from API if book not in table
      #


    
    }

    public static function shelveBook()
    {
      ##################################################
      # Add book to shelf
      #
    
    
    }
}
