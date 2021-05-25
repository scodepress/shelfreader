@extends('layouts')
@section('title', 'Email')
@section('content')

<style type="text/css">
	td {
		padding: 10px;
		font-size: 1.3em;
	}
</style>
<div style="margin-left:300px;margin-right:300px;">
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
</div>
<div align="center">
<h2>Email Us</h2>

<table cellspacing="50px">

<form action="/store_mail" method="POST">
    {{csrf_field()}}
    <tr>
    	<td style="width:30px;">From:</td>
    	<td>{{$user_name}}
    		<input type="hidden" name="user_id" value="{{$user_id}}" />
    		<input type="hidden" name="user_name" value="{{$user_name}}" />
    	</td>
    </tr>
    <tr>
  <td>Subject<td> 
<tr>
	<td>
  	<input type="text" name="subject" size="30" autofocus="autofocus" />
  </td>
  </tr>
</tr>
<tr>
  <td>Message</td>
  <tr>
  	<td colspan="2">
  <textarea name="body" rows="8"  cols="100"></textarea>
</td>
<tr>
	<td colspan="2">
  <button type="submit">Send</button>
</td>
</tr>
</form>
</table>
</div>

@endsection