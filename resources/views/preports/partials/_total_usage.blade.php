<h2 align="center">Total Usage</h2>
<table width="90%" align="right">
	<tr style="font-size: 1.3em;font-weight: bold;">
		<td valign="top">Scans</td>
		<td>Corrections*</td>
	<td>Alerts*</td>
	<td>Not Found*</td>
	<td>Error Rate*</td>
	</tr>
@foreach($agg as $key=>$k)
<tr>
	<td>{{$k->num}}</td><td>{{\DB::table('shelf_errors')->select('id')->where('user_id',$k->id)->count()}}</td>
	<td>
	{{\DB::table('preports')->select('id')->where('user_id',$k->id)->count()}}
</td>
<td>
{{\DB::table('shadows')->select('id')->where('user_id',$k->id)->count()}}
</td>
<td>{{ $erate }}%</td>
</tr>
@endforeach
<tr>
	<td colspan="4"> <br>
	<b>* Tabulated since 3-20-2019</b>
</td>
</tr>
</table>