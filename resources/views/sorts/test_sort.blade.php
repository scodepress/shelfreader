@extends('layouts')
@section('content')

<br>
@foreach(\App\Sort::lastBook() as $b)
{{\App\Sort::sortKey($b->callno)}} 
@endforeach
<br>
<br>

<div style="font-size: 2em;">Call number: <br>{{$mcall_number}}</div>
<br>
<div style="font-size: 2em;">Mask: <br>       {{$smask}}</div>
<br>
<div style="font-size: 2em;">Shelf ID: <br>   {{$shelf_id}}</div>
<br>
<span style="font-size: 2em;">
  Sort Key:
  <br>
{{$cutposition}}
</span>
<br>

<br>

@endsection