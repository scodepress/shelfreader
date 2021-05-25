@extends('layouts')
@section('title', 'Searches')
@section('content')

<h2>Search Results</h2>

<div align="center">
	<div style="font-size: 1.3em;"> You searched for {{$word}}. There are {{$n}} results.</div>
<table width="50%" style="font-size: 1.3em;">
	<tr><td><b>Title</b></td><td><b>Call Number</b></td></tr>
@foreach($results as $key=>$r)

<tr><td>{{$key+1}}. {{$r->title}}</td><td>{{$r->callno}}</td></tr>

@endforeach

</table>
</div>
<br><br>
@endsection