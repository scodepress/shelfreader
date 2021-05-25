@extends('modals')

@section('content')


<h2>Assign {{$user_info[0]->name}} to a library:</h2>

<form method="POST" action="/admin/update_user">
	{{csrf_field()}}
	<label>Enter the Library ID:</label>
	<input type="text" name="library_id" size="3">
	&nbsp;&nbsp;&nbsp;&nbsp;
	<label>Assign Privilege Level:</label>
	<input type="text" name="privs" size="3">
	<input type="hidden" name="user_id" value="{{$user_info[0]->id}}">
     &nbsp;&nbsp;&nbsp;
	<button type="submit">Update</button> 
	
</form>


@endsection