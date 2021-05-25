<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Auth;

use App\Jobs\InsertItemAlert;

class GulpTest extends Model
{
    public static function makeResponse($barcode)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', "Your API Url goes here");

        $response = $response->getBody()->getContents();

        $response = collect(json_decode($response, true));
        //dd($response);
        return $response;

    }

    public static function itemInfo($response,$barcode)
    {
    
       $title = $response['TitleInfo'][0]['title'];
       $num = $response['TitleInfo'][0]['numberOfCallNumbers']-1;

       $alt_info = array();


       foreach ($response as $key => $r)
       {
        for($i=0; $i<=$num; $i++)
        {
            $num_copies = $r[0]['CallInfo'][$i]['numberOfCopies']-1;
            for($v=0; $v <= $num_copies; $v++)
            {


                if($r[0]['CallInfo'][$i]['ItemInfo'][$v]['itemID'] == $barcode)
                {
                    $alt_info[] = $title;
                    $alt_info[] = strtoupper($r[0]['CallInfo'][$i]['callNumber']);
                    $alt_info[] = $r[0]['CallInfo'][$i]['ItemInfo'][$v]['itemID'];
                    $alt_info[] = $r[0]['CallInfo'][$i]['ItemInfo'][$v]['homeLocationID'];
                    $alt_info[] = $r[0]['CallInfo'][$i]['ItemInfo'][$v]['currentLocationID'];
                    $alt_info[] = $r[0]['CallInfo'][$i]['ItemInfo'][$v]['dueDate'];
                    $alt_info[] = $r[0]['CallInfo'][$i]['ItemInfo'][$v]['recallDueDate'];
                    $alt_info[] = $r[0]['CallInfo'][$i]['ItemInfo'][$v]['transitSourceLibraryID'];
                    $alt_info[] = $r[0]['CallInfo'][$i]['ItemInfo'][$v]['transitDestinationLibraryID'];
                    $alt_info[] = $r[0]['CallInfo'][$i]['ItemInfo'][$v]['transitReason'];
                    $alt_info[] = $r[0]['CallInfo'][$i]['ItemInfo'][$v]['transitDate'];
                    $alt_info[] = $r[0]['CallInfo'][$i]['ItemInfo'][$v]['chargeable'];
                    $alt_info[] = $r[0]['CallInfo'][$i]['ItemInfo'][$v]['numberOfHolds'];
                    $alt_info[] = $r[0]['CallInfo'][$i]['ItemInfo'][$v]['itemTypeID'];

                }
            }

        }


    }

    //dd($alt_info);

    if(!empty($alt_info) AND !empty($title)) 
    {
        // The specific book is found

        return $alt_info;
    }

    elseif(empty($alt_info) AND !empty($title)) 

    { 
        // Specific book not found but at least one copy is 

        $base = self::baseCall($response,$barcode);

        $callnum = $response['TitleInfo'][0]['baseCallNumber'];
        $title = $response['TitleInfo'][0]['title'];
        $current_location = "Users Library";
        $home_location = "Unknown";

        dispatch(new InsertItemAlert($barcode,$callnum,$title,$current_location,$home_location));

        return $base;
        
    }

    elseif(empty($alt_info) AND empty($title))

    {
        // this is an empty response

        $callnum = "Unknown";
        $title = "Unknown";
        $current_location = "Users Library";
        $home_location = "Unknown";

        dispatch(new InsertItemAlert($barcode,$callnum,$title,$current_location,$home_location));

        return false;
    }



}

    public static function baseCall($response,$barcode)
    {
        $base_info = array();
        
        $base_info[] = $response['TitleInfo'][0]['title'];
        $base_info[] = $response['TitleInfo'][0]['baseCallNumber'];
        $base_info[] = $barcode;
        $base_info[] = 'UNKNOWN';
        $base_info[] = 'SHADOW';

        return $base_info;

    }


    public static function insertInfo($alt_info)
    {
        
    	$nid = \DB::table('item_statuses')->select('id')->where('user_id', Auth::id())->count();

    	$ist = new ItemStatus;

    	$ist->id = $nid+1;
    	$ist->user_id = Auth::id();
    	$ist->barcode = $alt_info[2];
    	$ist->home_location = $alt_info[3];
    	$ist->current_location = $alt_info[4];
    	$ist->due_date = $alt_info[5];
    	$ist->recall_duedate = $alt_info[6];
    	$ist->source_library = $alt_info[7];
    	$ist->destination_library = $alt_info[8];
    	$ist->transit_reason = $alt_info[9];
    	$ist->transit_date = $alt_info[10];
    	$ist->chargeable = $alt_info[11];
    	$ist->number_holds = $alt_info[12];
    	$ist->item_type = $alt_info[13];

    	$ist->save();
    
    }

    public static function locationStatus($barcode)

    {
    
    	return \DB::table('item_statuses')
    	->select('current_location')
    	->where('barcode', $barcode)
    	->first();
    
    }

    public static function getAlert($title,$location)
    {
        $stitle = str_limit($title,15);

        $err = "Alert: $stitle is $location. To place the book on the shelf, rescan the barcode. 
        To continue without placing the book on the shelf, scan the next book.";

        //\Session::put('message', $err); 

        return $err;

    }

    public static function holdTest($locationID)
    {
    
        $locstring = substr($locationID, 0, 7);
        if($locstring == 'ONHOLD-') 

        {
            
            return 1;
        }

        else

        {
            return 0;
        }
    
    }

    public static function alertTest($locationID)
    {
        $ids = array();
        $ids = array('LOST-ASSUM','CHECKEDOUT','MISSING','LOST','LOST-CLAIM','Z-MISSING','WITHDRAWN','CANCELED','Z-REMOVED','INTRANSIT','DISCARD','PALCI','SHADOW');
        $hold = self::holdTest($locationID);
     
        if(in_array($locationID,$ids) != false OR $hold === 1)
            { return 1; }

            else

                { return 0; }

    }

    public static function dueDate($barcode)

    {

    	$ts = \DB::table('item_statuses')
    	->select('due_date')
    	->where('barcode', $barcode)
    	->first();

    	return \Carbon\Carbon::createFromTimestamp($ts->due_date/1000)->toFormattedDateString();

    }
    
    }
