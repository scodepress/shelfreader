@extends('layouts')
@section('title', 'My Reports')
@section('content')

<div style="margin-left:150px;margin-top:100px;">

<div><h2>{{\Auth::user()->name}}</h2></div>

<div>
	<p><a href="/master_keys/export">Download Inventory</a></p>
	<p><a class="myBtn" href="/clear_masterkey_confirm/$library_id">Clear Inventory</a></p>
	</div>

<h2>Item Alerts</h2>

<div style="overflow-y: scroll;height:300px;width:90%;">
	
  <br>
  <table width="90%">
	<tr style="font-weight: bold; font-size: 1.7em;">
		<td width="35%">Title</td>
		<td>Barcode</td>
 		<td>Call Number</td>
 		<td>Location/Status</td>
 		<td>Home Library</td>
 		<td>Date/Time Scanned</td>
	</tr>
 	@foreach($item_alerts as $i)
 	<tr>
 		<td>{{ \Illuminate\Support\Str::limit($i->title,60) }}</td>
 		<td>{{$i->barcode}}</td>
 		<td>{{$i->call_number}}</td>
 		<td>{{$i->current_location}}</td>
 		<td>{{$i->home_location}}</td>
 		<td>{{$i->created_at}}</td>
 	</tr>
 	@endforeach
 </table>
 </div>
  <br>
  <br>
  <br>
  
  <table width="90%">
  	<tr>
  		<td>
  	<h4 style="font-weight: bold;">Daily Totals</h4>
  <table width="90%">
  	<tr style="font-weight: bold; font-size: 1.7em;">
  		<td>Date</td>
  		<td>Scan Count</td>
  		<td>Errors</td>
  	</tr>
  	@foreach($daily_scan_total as $key=>$u)
  	<tr>
  		
		<td>{{$u->date}}</td>
		<td>{{$u->daily_total}}</td>
		<td>{{\App\Models\Report::dailyErrors($u->date)}}</td>
	
  	</tr>
  	@endforeach
	</table>
	</td>
	<td valign="top">
	<h4 style="font-weight: bold;">Overall Totals</h4>
	<table width="90%" align="right">
		<tr style="font-weight: bold; font-size: 1.7em;">
			<td>Total Scans</td>
			<td>Corrections</td>
			<td>Error Rate</td>
		</tr>
		<tr>
			<td>{{$total_scans[0]->num}}</td>
			<td>{{$corrections}}</td>
			<td>{{$erate}}&nbsp;%</td>
		</tr>
	</table>

	</td>
</tr>
</table>
  <br>

</div>
<br>
<br>
@endsection
