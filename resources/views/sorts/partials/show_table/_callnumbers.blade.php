@foreach($last_scan as $key=>$l)

	@if($l->position + $con == $cpos)
	<td class="callnum" style="width:10px;"></td>
	@endif

	
	<td class="callnum" cellpadding="0"><span>{{$l->callno}}</span></td>
	
	@endforeach
