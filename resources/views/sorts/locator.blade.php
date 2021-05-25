@extends('modals')
@section('content')
<script type="text/javascript">
    $(document).ready(function(){

   
var barcode = {!! json_encode($mybar) !!};

      $('#c_code').val('');  

            $('html, body').animate({
         scrollTop: $("#mid").offset().top-565
          }, 0);  

});

</script>
<table border="1" style="font-size: 2em;">
	<tr style="border:solid 3px blue;">
	@if($lbooks->first())	
	@foreach($lbooks as $l)
	<td align="center" style="padding:8px;width:10%;" height="200px">{{$l->title}}<br><br> {{$l->callno}}</td>
	@endforeach
	@endif
	<td align="center" id="{{$mybar}}" style="padding:8px;width:10%;background-color:#00CC00; color:maroon;">
		{{$book_info[0]->title}}<br><br> {{$book_info[0]->callno}}
	</td>
	
	@if($rbooks->first())
	@foreach($rbooks as $r)
	<td align="center" style="border:solid 3px blue;padding:8px;width:10%;">{{$r->title}}<br><br> {{$r->callno}}</td>
	@endforeach
	@endif
</tr>

<table border="1" style="font-size: 2em;">
	<tr style="border:solid 3px blue;">
	@if($cleft->first())	
	@foreach($lbooks as $l)
	<td align="center" style="padding:8px;width:10%;" height="200px">{{$l->title}}<br><br> {{$l->callno}}</td>
	@endforeach
	@endif
	<td align="center" id="{{$mybar}}" style="padding:8px;width:10%;background-color:#00CC00; color:maroon;">
		{{$book_info[0]->title}}<br><br> {{$book_info[0]->callno}}
	</td>
	
	@if($cright->first())
	@foreach($rbooks as $r)
	<td align="center" style="border:solid 3px blue;padding:8px;width:10%;">{{$r->title}}<br><br> {{$r->callno}}</td>
	@endforeach
	@endif
</tr>

</table>

@endsection

