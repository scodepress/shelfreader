@extends('layouts')
@section('content')


<div align="center">
	<h3>Scan the first book:</h3>
  <form action="/store_sort" method="POST">
    {{csrf_field()}}
  <input type="text" name="barcode" size="20" autofocus="autofocus" />&nbsp;&nbsp;&nbsp;
  <button type="submit">Scan Barcode</button>
</form>
</div>
<br>


@if(isset($mybar))
<?php $e = 1; ?>
<table>
@foreach($shelf as $key=>$s)
 <tr>
<td>{{$e}}. </td>

<td style="font-size:1.5em;">{{$s->title}}</td> <td style="padding:10px;" >{{$s->barcode}}</td>

<td>
	@if($mybar == $s->barcode)
<table width="60%" border="1">
	<tr>
	@if($lbooks->first())	
	@foreach($lbooks as $l)
	<td style="padding:8px;width:10%;" height="200px">{{$l->title}}<br><br> {{$l->callno}}</td>
	@endforeach
	@endif
	<td id="{{$mybar}}" style="padding:8px;width:10%;background-color:#00CC00;">{{$book_info[0]->title}}<br><br> {{$book_info[0]->callno}}</td>
	@if($rbooks->first())
	@foreach($rbooks as $r)
	<td style="padding:8px;width:10%;">{{$r->title}}<br><br> {{$r->callno}}</td>
	@endforeach
	@endif
</tr>
</table>
@endif
</td>
<?php $e++; ?>
</tr>
@endforeach
<br>
<br>
<br>
<script type="text/javascript">
    $(document).ready(function(){

   
var barcode = {!! json_encode($mybar) !!};

            $("#" + barcode).css("background-color", "#00CC00");//green
            
              $('html, body').animate({
         scrollTop: $("#" + barcode).offset().top-425
          }, 0);
          
           
          

});

</script>
@endif
@endsection