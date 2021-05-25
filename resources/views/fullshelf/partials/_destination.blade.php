@if($errors>0)
@if(\Auth::id() == 1) Mpos: {{$mpos}}@endif
<div style="margin-left:25px;margin-right:75px;">
<table width="100%">
	<tr>
		<td width="20%">
<span style="color:red;font-size: 1.5em;text-align: left;">@if($corrections) Corrections: {{$corrections}}@if(\Auth::id()== 1)  | {{$dbar}} @endif @endif



</span></td>

	@foreach($left as $key=>$t)
	<td align="center" valign="top" style="text-align:center;background-color:#f4a460;border: 2px solid white;padding:5px;">
		
			<div>Section {{$dest_section}} Shelf {{$dest_shelf}}  Position {{$mpos}}</div>

			<span style="font-size: 1.2em;">{{$t->position}}. {{str_limit($t->title,26,'')}} </span>
			<br>
			<span style="font-size: 1.1em;">{{$t->callno}}</span>
			 
	</td>
	@endforeach
	<td align="center" valign="top" style="color:blue;font-weight:bold;background-color:#00CC00;text-align:center;border: 2px solid white;padding:5px;">
		
			<span style="font-size: 1.2em;">

				<div>Section {{$section_number}} Shelf {{$shelf_number}} Book {{$pos}}</div>
			
				{{$pos}}. {{str_limit($dtitle,26,'')}} 
			</span>
			<br>
			
			<span style="font-size: 1.1em;">{{$dcall}}</span><br>
		
			 </td>
	
	@foreach($right as $key=>$t)
	<td align="center" valign="top" style="background-color:#f4a460;border: 2px solid white;padding:5px;">
		<span style="font-size: 1.2em;">
			
				{{$t->position}}. {{str_limit($t->title,26,'')}} 
			</span>
			<br>
			<span style="font-size: 1.1em;">{{$t->callno}}</span>
			 
	</td>	
	@endforeach	

</tr>
<span id="mid"></span>
</table>
@endif

</div>