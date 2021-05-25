@extends('modals')
@section('content')

	@include('sorts.partials.show_table._errors')
</div>
<table width="50%" align="center">
  <tr>
  </tr>
</table>

<script type="text/javascript">
$(document).ready(function (){
	$( "div.show_table" ).scrollLeft('<?php echo $tpix; ?>');
});
</script>
<div class="show_table" style="width:100%;height:900px;overflow:auto;margin-left:20px;margin-right:20px;margin-top: 0px;" id="sbar">

<table style="margin-top: 0px;" width="900000px" border="0">


		<tr>

			@include('sorts.partials.show_table._shelf')

		</tr>



		<tr>


			@include('sorts.partials.show_table._callnumbers')

		</tr>

	</table>
</table>
</div>
</div>


<style>
	#sbar::-webkit-scrollbar {
  display: none;
 -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}
</style>
@endsection
