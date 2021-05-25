@extends('modals')
@section('content')


<table  border="0" style="font-size:1.3em; width: 100%;">
	<tr>
		<td colspan="2"><b>Do you want to delete:</b></td>
	</tr>
	<tr>
		@foreach($book_info as $b)
		<td>{{$b->title}}</td>
		<td>{{$b->callno}}</td>
		@endforeach
	</tr>
</table>
<br>
<div align="center">
  <form id="delete" action="/book_full_drop" method="POST">
    {{csrf_field()}}
  <input type="hidden" name="barcode" value="{{$barcode}}" />&nbsp;&nbsp;&nbsp;
  <button type="submit">Delete</button>
</form>
</div>

@endsection