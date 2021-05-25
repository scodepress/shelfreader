	@if($unfound->first())
	<h2>Not Found</h2>
<table width="80%">
<tr style="font-size: 1.3em;font-weight: bold;">
	<td>Date</td>
	<td>Barcode</td>
	<td>Title</td>
</tr>
@foreach($unfound as $u)
	<tr>
		<td>{{$u->date}}</td>
		<td>{{$u->barcode}}</td>
		<td>{{str_limit($u->title,20,'')}}</td>
	</tr>
@endforeach
	@endif
</table>