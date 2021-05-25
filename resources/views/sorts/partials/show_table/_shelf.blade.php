@foreach($last_scan as $key=>$l)

		@if($l->position + $con == $cpos)
		{{-- Verical destination stripes --}}
	<td class="callnum" style="width:8px;"></td>
	@endif

	@if($l->barcode == $dbar)
	<td style="color:maroon;font-size: 1.6em; font-weight:bold;text-align: center; display: inline-block; width: 60px;">
	<a style="color:maroon" href="javascript:void(0)" onclick="javascript:destination({{$green_left}})">{{$jump}}</a>

	</td>
	@else
	<td style="font-size: 1.6em; text-align: center; display: inline-block; width: 60px;">
	</td>
		@endif
		@endforeach
	</tr>
<tr>
@foreach($last_scan as $key=>$l)

		@if($l->position + $con == $cpos)
		{{-- Vertical destination stripes --}}
	<td class="callnum" style="width:8px;"></td>
	@endif

		<td style="font-size: 1.6em; text-align: center; display: inline-block; width: 60px;">{{$l->position}}</td>
		@endforeach
	</tr>


	<tr>
		@foreach($last_scan as $key=>$l)

		@if($l->position + $con == $cpos)
	<td class="turn" style="background-image: none; background-color:#00CC00;width: 10px;"></td>
	@endif

		@if($l->cposition == $maxcp AND $l->position == $maxp)

		<td class="turn" ><a class="myBtn" style="text-decoration: none; color:black;" href="/delete_book/{{$l->barcode}}"> {{\Illuminate\Support\Str::limit($l->title, 25,'')}}</a></td>
		@else

		<td class="turn" 
		@if($l->barcode == $dbar) style="color:blue; line-height:50px; font-weight:bold;" @endif
		 @if($l->barcode != $dbar) style="color:black;" @endif>
		 <span>{{\Illuminate\Support\Str::limit($l->title, 25,'')}}</span></td>

		@endif

		@if($loop->last AND $cpos == $mc AND $errors>0)
	 <td class="turn" style="background-image: none; background-color:#00CC00;width: 10px;"></td> 
		@endif

		@endforeach
