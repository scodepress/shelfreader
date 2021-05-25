	@if($reports->first())
	<h2>Alerts</h2>
<table width="70%">
<tr style="font-size: 1.3em;font-weight: bold;">
	<td>Date</td>
	<td>Barcode</td>
	<td>Title</td>
	<td>Call Number</td>
	<td>Location</td>
</tr>
@foreach($reports as $r)
	<tr>
		<td>{{$r->date}}</td>
		<td>{{$r->barcode}}</td>
		<td>{{str_limit($r->title,20,'')}}</td>
		<td>{{$r->callnum}}</td>
		<td>@if($r->location_id === 'SHADOW') <span>SHADOWED OR WITHDRAWN</span> @else {{$r->location_id}} @endif</td>
	</tr>
@endforeach
	@endif
</table>