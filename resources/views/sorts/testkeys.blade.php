@extends('layouts')
@section('content')

<table width="80%" align="center">
	<tr>
		<td>
<h3>Test Call Numbers</h3>
</td>
<td>
<div style="font-size: 1.1em;"  align="center">
  <form action="/store_test_keys" method="POST">
    {{csrf_field()}}
  <input type="text" name="callno" size="40" autofocus="autofocus" placeholder=" May be Real or Fictitious-All Letters Must Be Caps" />&nbsp;&nbsp;&nbsp;
  <button type="submit">Test</button>
</form>
</div>
</td>

	<td>
 <form action="/truncate_test" method="POST">
    {{csrf_field()}}
  <input type="hidden" name="barcode"  />&nbsp;&nbsp;&nbsp;
  <button type="submit">Empty Test Table</button>
</form>
</td>
</tr>
</table>
<br>
<br>

<?php $skey = \App\Models\Sort::getTest(); ?>
@if($skey->first())
<?php $lid = \App\Models\Sort::getLid()->id; ?>



<h3 style="font-weight: bold;">Ordered by Sort Key: <span style="font-weight: bold;color: orange;">(Most recent entry in orange)</span></h3>
<table border="1" style="font-size: 1em;" width="90%">
	<tr style="font-weight: bold;">
		<td>Call Number</td>
		<td>Subclass</td>
		<td>SubInt</td>
		<td>SubDec</td>
		<td>CapDate</td>
		<td>CapOrd</td>
		<td>CapInd</td>
		<td>Cutter1</td>
		<td>CutDec1</td>
		<td>Cutter Date</td>
		<td>Inline</td>
		<td>InlineDec</td>
		<td>CDate2</td>
		<td>Cutter2</td>
		<td>CutDec2</td>
		<td>Spec1</td>
		
	</tr>

	
		@foreach($skey as $key=>$k)
		<tr @if($k->id == $lid) style="background-color: orange;" @elseif ($key%2 != 0) style="background-color:#66ccff;" @endif>
		<td>{{$k->callno}}</td>
		<td>{{$k->prefix}}</td>
		<td>{{$k->tp1}}</td>
		<td>{{$k->tp2}}</td>
		<td>{{$k->pre_date}}</td>
		<td>{{$k->pvn}}</td>
		<td>{{$k->pvl}}</td>
		<td>{{$k->cutter}}</td>
		<td>{{$k->pcd}}</td>
		<td>{{$k->cutter_date}}</td>
		<td>{{$k->inline_cutter}}</td>
		<td>{{$k->inline_cutter_decimal}}</td>
		<td>{{$k->cutter_date2}}</td>
		<td>{{$k->cutter2}}</td>
		<td>{{$k->pcd2}}</td>
		<td>{{$k->part1}}</td>
		
	</tr>
		@endforeach

</table>
<br>

@endif

@endsection