@extends('layouts')
@section('title', 'Library Management')
@section('content')
<div style="margin-top: 25px;margin-left:25px;">
	<div>
		<a class="myBtn" href="upload-file-form">Upload a file into inventory</a>
		<br><h3>Download a Library's Inventory</h3>
		
		<form method="POST" action="{{ route('admin-inventory-export') }}">
			{{csrf_field()}}
			<label>Choose the Library:</label>
			<select name="libraryId">
				@foreach($inventory_users as $key=>$user)
				<option value="{{$user->id}}">{{$user->library}}</option>
				@endforeach
			</select>
			&nbsp;&nbsp;&nbsp;&nbsp;
			
			<button type="submit">Download</button> 
			
		</form>
		<h2>Users</h2>
		<table width="50%">
		<tr style="font-weight:bold;">
			<td>ID</td>
			<td>Name</td>
			<td>Email</td>
			<td>Institution</td>
			<td>Privileges</td>
			<td>Update</td>
		</tr>
		@foreach($users as $key=>$u)
		
			<tr>
				<td>{{$u->id}}</td>
				<td>{{$u->name}}</td>
				<td>{{$u->email}}</td>
				<td>{{$u->institution}}</td>
				<td>{{$u->privs}}</td>
				<td><a class="myBtn" href="show_user/{{$u->id}}">Update</a></td>
			</tr>
		
		@endforeach
		</table>
	</div>

	<br>
	<br>
	<div>	
		
		<table width="50%">
			<tr>
				<td><h2>Libraries</h2></td>
				<td><a class="myBtn" href="/admin/add_library_form">Add a Library</a></td>
			</tr>
		<tr style="font-weight:bold;">
			<td>ID</td>
			<td>Library</td>	
		</tr>
		@foreach($institutions as $key=>$i)
		
			<tr>
				<td>{{$i->id}}</td>
				<td>{{$i->library}}</td>
	
			</tr>
		
		@endforeach
		</table>
	</div>

	<br>

	
		<br>
</div>


@endsection