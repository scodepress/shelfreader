<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Report;
use App\Models\Usage;



class Report extends Model
{
    public static function store($barcode,$title,$callnum,$location,$shelf)
    {
    
    	$report = new Report;

        $report->user_id = Auth::id();
        $report->barcode = $barcode;
        $report->title = $title;
        $report->callnum = $callnum;
        $report->location_id = $location;
        $report->shelf = $shelf;

        $report->save();

    }

    public static function checkShelf($barcode)
    {

    	$shelf = \DB::table('reports')
    	->select('shelf')
        ->where('barcode',$barcode)
    	->where('user_id',Auth::id())
    	->get();

    	if($shelf->first())
    	{
    		$shelf = $shelf[0]->shelf;
    		
    		return $shelf;
    	}

    	else 

    		{ return 3; }

    }

    public static function checkFshelf($barcode)
    {

        $shelf = \DB::table('full_reports')
        ->select('shelf')
        ->where('barcode',$barcode)
        ->where('user_id', Auth::id())
        ->get();

        if($shelf->first())
        {
            $shelf = $shelf[0]->shelf;
            
            return $shelf;
        }

        else 

            { return 3; }

    }

     public static function itemAlerts()
    {
        return \DB::table('item_alerts')
        ->select('created_at','barcode','title','call_number','current_location','home_location')
        ->where('user_id', Auth::user()->id)
        ->orderByDesc('created_at')
        ->get();
    }

    public static function Usages()

    {   
        return \DB::table('usages')
        ->join('users', 'users.id', '=', 'usages.user_id' )
        ->join('institutions', 'institutions.id', '=', 'users.institution')
        ->select('users.name', 'users.id', 'institutions.institution', 'usages.created_at as date' )
        ->where('users.id', Auth::user()->id)
        ->orderByDesc('usages.created_at')
        ->get();
    }

    public static function numUsages()
    {
        return Usage::where('user_id', Auth::user()->id)->count();
    }


    public static function dailyErrors($date)
    {
    
        return \DB::table('shelf_errors')
        ->select('id')
        ->where('date',$date)
        ->where('user_id', Auth::user()->id)
        ->count();
    
    }

    

    public static function unFound()
    {

        return \DB::table('shadows')
        ->selectRaw("date(created_at) as date,barcode,title")
        ->where('user_id', Auth::user()->id)
        ->where('title','unknown')
        ->orderByDesc('created_at')
        ->paginate(10);

    }

    public static function aggUsages()
    {
        return \DB::table('usages as us')
        ->join('users as u', 'u.id', '=', 'us.user_id')
        ->selectRaw("count(us.id) as num, u.name,u.id,us.date")
        ->where('user_id', Auth::user()->id)
        ->groupBy('u.name','u.id','us.date')
        ->orderByDesc('num')
        ->get();

    }

    public static function scansByDate($date) 
    {   
        return Usage::where('user_id',Auth::user()->id)
        ->whereDate('created_at','=', $date)
        ->count();
    }

    public static function dailyScanTotal()
    {
    
        return DB::table('usages')
        ->selectRaw("date, count(id) as daily_total")
        ->where('user_id', Auth::user()->id)
        ->groupBy('date')
        ->get();
        
    }
    
    public static function userErrors()
    {
    
        return \DB::table('shelf_errors')
        ->select('id')
        ->where('user_id', Auth::user()->id)
        ->count();
    
    }

    public static function errorRate()
    {

        $errors = self::userErrors();
        
        $scans = \DB::table('usages')
        ->select('id')
        ->where('user_id',  Auth::user()->id)
        ->where('date', '>=', "2019-03-20")
        ->count();

        $alerts = \DB::table('preports')->select('id')->where('user_id',\Auth::user()->id)->count();

        if($scans>0)
        {
            $erate = round((($errors+$alerts)/$scans)*100,1);

            return $erate;
        }

        else

            { return 0; }

    }


}
