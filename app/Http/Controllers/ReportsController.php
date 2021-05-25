<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use ItemAlert;

class ReportsController extends Controller
{
    public function show()
    {
    	$item_alerts = Report::itemAlerts();

    	$usages = Report::Usages();

    	$numUsages = Report::numUsages();

    	$total_scans = Report::aggUsages(); 

    	$corrections = Report::userErrors(); 

    	$erate = Report::errorRate();

        $daily_scan_total = Report::dailyScanTotal();

        $library_id = User::where('id', Auth::user()->id)->first()->institution;

    	return view('reports.show', compact('item_alerts','usages','total_scans','erate','corrections','daily_scan_total','library_id'));
    }
}
