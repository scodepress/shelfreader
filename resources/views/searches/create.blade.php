@extends('layouts')
@section('title', 'Search')
@section('content')

<h2>Search</h2>

<div align="center">
 <form action="/store_search" method="POST">
    {{csrf_field()}}
  <div><span style="font-size: 1.5em;">Title contains:</span></div>
  <input type="text" name="word" size="20" autofocus="autofocus" />&nbsp;&nbsp;&nbsp;
  <button type="submit">Search</button>
</form>
</div>

@endsection