@extends('layouts')
@section('content')

<style type="text/css">
.btable {
	display: inline-block;
	width: 100px;
	height: 100px;
	border: 1px solid black;
}
</style>

<?php $x = 1000; ?>

<script type="text/javascript">


	$(document).ready(function (){
		//Goes here on page load
          $( "div#shelf" ).scrollLeft( 23800 );
        });
       
</script>

<script type="text/javascript">


	$(document).ready(function (){
            $("#dest").click(function (){
          $( "div#shelf" ).scrollLeft( 2000 );
        });
        });
</script>

<script type="text/javascript">


	$(document).ready(function (){
            $("#book").click(function (){
          $( "div#shelf" ).scrollLeft( 10000 );
        });
        });
</script>

<br>
<br>

<div id="shelf" style="height:500px;overflow:auto;margin-right:50px;">
	<br>
	<br>

<table  width="23800">
	<tr>
		
		@for($i = 1; $i <= 238; $i++) 

		<td class="btable">{{$i}}</td>

		@endfor

	</tr>
</table>

</div>
<br>
<table width="50%" align="center">
	<tr>
		<td><button id="dest">Show Destination</button></td>
		<td><button id="book">Show Book</button></td>
	</tr>


@endsection