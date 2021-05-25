{{-- @if($errors>0)
@if(\Auth::id() == 1) {{$mpos}}
@endif --}}



	<span style="color:red;font-size: 1.5em;text-align: left;">Corrections: {{$corrections}}</span>
	@if(\Auth::id()== 1) 
	<span style="color:red;font-size: 1.5em;text-align: left;"> | {{$dbar}} </span>
	 @endif 
