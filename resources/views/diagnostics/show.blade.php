@extends('layouts')
@section('title', 'Shelving')
@section('content')

<div style="margin: 50px;">
<div style="font-weight: bold;">Failed Barcodes {{$date}}</div>
<table width="50%">
<tr><td style="font-weight: bold;">User</td><td style="font-weight: bold;">Barcode</td><td style="font-weight: bold;">Timestamp</td></tr>
@foreach($badBarcodes as $b) 
<tr style="border-bottom: 1px solid black;"><td>{{$b->name}}</td><td>{{$b->barcode}}</td><td>{{$b->created_at}}<td></tr>
@endforeach
</table>
<br>
<br>


<div style="font-weight: bold;">Failed Call Numbers {{$date}}</div>
<table width="50%">
<tr><td style="font-weight: bold;">User</td><td style="font-weight: bold;">Barcode</td><td style="font-weight: bold;">Call Number</td><td style="font-weight: bold;">Timestamp</td></tr>
@foreach($failedCallNumbers as $b) 
<tr style="border-bottom: 1px solid black;"><td>{{$b->name}}</td><td>{{$b->barcode}}</td><td>{{$b->callnumber}}</td><td>{{$b->created_at}}<td></tr>
@endforeach
</table>

<br>
<br>


<div style="font-weight: bold;">Unrecorded Items</div>
<div>Unique Scans: {{$uniqueUsages}}   &nbsp;   &nbsp;   &nbsp; Recorded Items: {{$uniqueInventory}}
<table width="50%">
<tr><td style="font-weight: bold;">User</td><td style="font-weight: bold;">Barcode</td><td style="font-weight: bold;">Timestamp</td></tr>
@foreach($unRecordedScans as $s) 
<tr style="border-bottom: 1px solid black;"><td>{{$name}}</td><td>{{$s->barcode}}</td><td>{{$s->created_at}}</td></tr>
@endforeach
</table>
<br>
<br>


<div style="font-weight: bold;">Duplicate Scans</div>
<table width="50%">
<tr><td style="font-weight: bold;">User</td><td style="font-weight: bold;">Barcode</td><td style="font-weight: bold;">Timestamp</td></tr>
@foreach($duplicateScans as $s) 
<tr style="border-bottom: 1px solid black;"><td>{{$name}}</td><td>{{$s->barcode}}</td><td>{{$s->created_at}}</td></tr>
@endforeach
</table>
</div>
@endsection
