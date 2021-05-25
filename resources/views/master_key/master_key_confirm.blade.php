@extends('modals')

@section('content')
<table  border="0" style="font-size:1.3em; width: 100%;">
	
	<tr>
		<td><span style="color:red;">Clearing the table will delete the records of all items in the inventory.</span></td>
	</tr>
	<tr>
		<td><span style="font-weight:bold;">To cancel this action, click "Close" at the bottom of this dialog.</span></td>
	</tr>
</table>
<br>
<div align="center">
  <form id="delete" action="/clear_masterkey" method="POST">
    {{csrf_field()}}
  <input type="hidden" name="library_id" value="{{$library_id}}" />&nbsp;&nbsp;&nbsp;
  <button type="submit">Clear the Inventory Table</button>
</form>
</div>
@endsection