<form action="{{ route('menu_admin') }}" method="post">
	{{csrf_field()}}
<select name="route_name">
	<option value="" selected="selected">Admin Links</option>

	<option><a href="/search">Search</option>
	<option><a href="/test_keys">Test Call Numbers</option>
	<option><a href="/store_data">Store Data</option>
	<option><a href="/sql_load">Load Table</option>
	<option><a href="/library_management">Manage Libraries</option>
	<option><a href="/php_info">PhpInfo</option>
	<option><a href="logs">Logs</option>
	
</select>
&nbsp;
<button>Go</button>
</form>

