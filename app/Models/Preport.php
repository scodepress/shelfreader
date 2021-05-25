<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Preport extends Model
{
    public static function getReport()
    {
    	return \DB::table('preports')
    	->select('date','barcode','title','callnum','location_id')
    	->where('user_id', Auth::user()->id)
    	->orderByDesc('date')
    	->paginate(10);
    }

    public static function Usages()

    {   
        return \DB::table('usages')
        ->join('users', 'users.id', '=', 'usages.user_id' )
        ->join('institutions', 'institutions.id', '=', 'users.institution')
        ->selectRaw("count(usages.user_id) as si, users.name, users.id, institutions.institution, Date(usages.created_at) as date")
        ->where('users.id', Auth::user()->id)
        ->groupBy('date')
        ->orderByDesc('date')
        ->orderByDesc('usages.created_at')
        ->paginate(10);
    }

    public static function dailyErrors($date)
    {
    
        return \DB::table('shelf_errors')
        ->select('id')
        ->where('date',$date)
        ->where('user_id',\Auth::user()->id)
        ->count();
    
    }

    public static function statusAlerts($date)
    {

    	return \DB::table('preports')
    	->select('id')
    	->where('user_id',\Auth::user()->id)
        ->where('date',$date)
    	->count();

    }

    public static function Shadowed($date)
    {

    	return \DB::table('shadows')
        ->select('id')
        ->where('user_id',\Auth::user()->id)
        ->where('created_at','>=', $date)
        ->where('created_at','<=', "$date 23:59:59")
        ->count();

    }

    public static function unFound()
    {

    	return \DB::table('shadows')
    	->selectRaw("date(created_at) as date,barcode,title")
    	->where('user_id',\Auth::user()->id)
        ->where('title','unknown')
    	->orderByDesc('created_at')
    	->paginate(10);

    }

    public static function aggUsages()
    {
    	return \DB::table('usages as us')
    	->join('users as u', 'u.id', '=', 'us.user_id')
    	->selectRaw("count(us.id) as num, u.name,u.id,us.date")
    	->where('user_id',\Auth::user()->id)
    	->orderByDesc('num')
    	->paginate(10);

    }

    public static function errorRate()
    {

        $errors = \DB::table('shelf_errors')
        ->select('id')
        ->where('user_id',\Auth::user()->id)
        ->count();

        $scans = \DB::table('usages')
        ->select('id')
        ->where('user_id', \Auth::user()->id)
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
