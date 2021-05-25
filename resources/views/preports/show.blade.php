@extends('layouts')
@section('title', 'My Reports')
@section('content')

<div style="margin-left:150px;margin-top:100px;">
  <br>
  {{ $page->links() }}
  <br>
	@include('preports.partials._alerts')
	
	<br>
	<br>

	@include('preports.partials._not_found')

	<br>
	<br>
	<table width="98%">
		<tr>
			<td width="60%">

				@include('preports.partials._daily_usage')

			</td>
			<td width="40%" valign="top">

				@include('preports.partials._total_usage')

			</td>
		</tr>
	</table>

	<br>
	<br>
	  <br>
  {{ $page->links() }}
  <br>
</div>
<br>
<br>
@endsection