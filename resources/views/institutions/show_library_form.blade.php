@extends('modals')

@section('content')

<h2>Add a Library</h2>


<form method="POST" action="/admin/add_library">
	{{csrf_field()}}
	<input type="hidden" name="library_id" value="{{$lid}}">
	<label>School Name</label><br>
	<input type="text" name="school_name" size="20">
	<br><br>
	<label>Library Name</label><br>
	<input type="text" name="library_name" size="20">
	<br><br>
	<label>Street Address</label><br>
	<input type="text" name="street" size="20">
	<br><br>
	<label>City</label><br>
	<input type="text" name="city" size="20">
	<br><br>
	<label>State</label><br>
	<input type="text" name="state" size="2">
	<br><br>
	<label>Zip Code</label><br>
	<input type="text" name="zip" size="20">
	<br><br>
	<button type="submit">Add</button> 
	
</form>


@endsection