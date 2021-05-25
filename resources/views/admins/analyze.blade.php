@extends('layouts')
@section('title', 'Analyze Table')
@section('content')
<style type="text/css">
	td {
		padding: 15px;
		font-size: 1.3em;
	}
</style>
<br>
@if(!empty($success))
  <div style="margin-left: 200px;margin-right:200px;" class="alert alert-success"> {{ $success }}</div>
@endif
<div align="center">
<h2>Barcodes with letters</h2>
@if($letters->first())
<table width="75%">
	<tr>
		<td>ID</td>
		<td>Barcode</td>
		<td>Titile</td>
		<td>Call Number</td>
	</tr>
@foreach($letters as $key=>$l)
<tr>
<td>{{$l->id}}</td> <td>{{$l->barcode}}</td> <td> {{$l->title}}</td> <td>{{$l->callno}}</td>
</tr>
@endforeach
</table>
<br>
	<form action="/delete_letters" method="POST">
    {{csrf_field()}}
  <input type="text" name="table_name" value="{{$table_name}}" size="20" autofocus="autofocus" />
  <button type="submit">Delete Rows</button>
</form>

@else
<span>No barcodes with letters found.</span>
@endif

<h2>Duplicate Barcodes</h2>
@if(!empty($duplicates))
<table width="75%">
	<tr>
		<td>ID</td>
		<td>Barcode</td>
		<td>Titile</td>
		<td>Call Number</td>
	</tr>
@foreach($duplicates as $key=>$l)
<tr>
<td>{{$l->id}}</td> <td>{{$l->barcode}}</td> <td> {{$l->title}}</td> <td>{{$l->callno}}</td>
<td>
	<form action="/delete_duplicates" method="POST">
    {{csrf_field()}}
  <input type="hidden" name="table_name" value="{{$table_name}}" />
  <input type="hidden" name="id" value="{{$l->id}}" />
  <button type="submit">Delete This Item</button>
</form>
</td>
</tr>
@endforeach
</table>

@else
<span>No duplicate barcodes found.</span>
@endif
</div>
<br>
<br>
@endsection