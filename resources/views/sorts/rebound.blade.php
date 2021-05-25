@extends('layouts')
@section('content')
@if(\App\Sort::getGid()->first()) 
<script type="text/javascript">
    $(document).ready(function(){

    	var barcode = {!! json_encode(\App\Sort::getGid()[0]->barcode) !!};

$( "#show_table" ).load( '/sorts/' + barcode);
   


      $('#c_code').val('');  

            $('html, body').animate({
         scrollTop: $("#mid").offset().top-565
          }, 0);  

});

</script>
@endif
<h1>Thun Library</h1>
<br>
<audio autoplay>
  <source src="/assets/beep-05.wav" type="audio/wav">
</audio>
<div align="center">
<table width="70%" align="center">
	<tr>
		<td>
<h1>Thun Library</h1>
</td>
<td>
<div align="center">
  <form action="/store_sort" method="POST">
    {{csrf_field()}}
  <input type="text" name="barcode" size="20" autofocus="autofocus" />&nbsp;&nbsp;&nbsp;
  <button type="submit">Scan Barcode</button>
</form>
</div>
</td>
<td>
 <form action="/sorts/truncate" method="POST">
    {{csrf_field()}}
  <input type="hidden" name="barcode"  />&nbsp;&nbsp;&nbsp;
  <button type="submit">Empty Table</button>
</form>
</td>
</tr>
</table>
</div>
<div id="show_table"></div>
<span style="color:red; font-size: 2.3em;"> {{$error}} </span>

<br>
@endsection
