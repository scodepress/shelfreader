@extends('layouts')
@section('title', 'Admin')
@section('content')
<style type="text/css">
	div {
		padding: 15px;
		font-size: 1.1em;
	}
</style>
<br>
<div style="margin-left:150px;">
  <br>
  {{ $page->links() }}
  <br>
<h2>Unfound with titles</h2>
<table width="60%">
@foreach($titled as $key=>$t)
<tr><td>{{$t->name}}</td><td>{{$t->institution}}</td>
  <td><a target="foo"  href="https://cat.libraries.psu.edu:28443/symwsbc/rest/standard/lookupTitleInfo?clientID=BCPAC&itemID={{$t->barcode}}&includeOPACInfo=true&includeItemInfo=true">test</a></td>
  <td>{{$t->created_at}}</td>
  <td>{{$t->barcode}}</td>
</tr>
@endforeach
<tr>
  <td colspan="3">
  
  </td>
</tr>
</table>
<br>

<br>
<h2>Unfound no titles</h2>
<table width="60%">
@foreach($unknown as $key=>$t)
<tr><td>{{$t->name}}</td><td>{{$t->institution}}</td>
  <td>
    <a target="foo" href="https://cat.libraries.psu.edu:28443/symwsbc/rest/standard/lookupTitleInfo?clientID=BCPAC&itemID={{$t->barcode}}&includeOPACInfo=true&includeItemInfo=true">test</a>
  </td>
  <td>{{$t->created_at}}</td>
  <td>{{$t->barcode}}</td>
</tr>
@endforeach
</table>
<br />

<br>

<br>

<table width="98%">
  <tr>
    <td width="60%">
<h2 align="center">Usage: Daily</h2>
<table width="100%">
  <tr>
    <td>Library</td>
    <td>Date</td>
    <td>Scans</td>
    <td>Corrections</td>
    <td>Alerts</td>
    <td>Withdrawn</td>
  </tr>
@foreach($usage as $key=>$k)
<tr><td>{{$k->name}}</td><td>{{$k->date}}</td><td>{{$k->si}}</td>
<td>
  {{\App\Admin::dailyErrors($k->date,$k->id)}}
</td>
<td>
{{\App\Admin::statusAlerts($k->date,$k->id)}}
</td>
<td>
{{\App\Admin::Shadowed($k->date,$k->id)}}
</td>
</tr>
@endforeach
</table>
<br>

</td>
<td width="40%" valign="top">
<h2 align="center">Usage: Overall</h2>
@if($agg->first())
<table width="95%" align="right">
  <tr><td>Name</td><td>Total Scans</td><td>Total Corrections</td></tr>
@foreach($agg as $key=>$k)
<tr><td>{{$k->name}}</td><td>{{$k->num}}</td><td>{{\App\Admin::totalErrors($k->id)}}</td></tr>
@endforeach
<tr>
  <td colspan="3">
 
  </td>
</tr>
</table>
@endif
</td>
</tr>

</table>

<br>
 {{ $page->links() }}
<br>
</div>
<br>
{{-- <div class='tableauPlaceholder' id='viz1554162690023' style='position: relative;'><noscript><a href='#'><img alt=' ' src='https:&#47;&#47;public.tableau.com&#47;static&#47;images&#47;Fa&#47;Fall2018Test&#47;CourseList&#47;1_rss.png' style='border: none' /></a></noscript><object class='tableauViz'  style='display:none;'><param name='host_url' value='https%3A%2F%2Fpublic.tableau.com%2F' /> <param name='embed_code_version' value='1' /> <param name='site_root' value='' /><param name='name' value='Fall2018Test&#47;CourseList' /><param name='tabs' value='no' /><param name='toolbar' value='no' /><param name='static_image' value='https:&#47;&#47;public.tableau.com&#47;static&#47;images&#47;Fa&#47;Fall2018Test&#47;CourseList&#47;1.png' /> <param name='animate_transition' value='yes' /><param name='display_static_image' value='yes' /><param name='display_spinner' value='yes' /><param name='display_overlay' value='yes' /><param name='display_count' value='yes' /></object></div>                <script type='text/javascript'>                    var divElement = document.getElementById('viz1554162690023');                    var vizElement = divElement.getElementsByTagName('object')[0];                    vizElement.style.width='100%';
vizElement.style.height=(divElement.offsetWidth*0.75)+'px';                    var scriptElement = document.createElement('script');                    scriptElement.src = 'https://public.tableau.com/javascripts/api/viz_v1.js';                    vizElement.parentNode.insertBefore(scriptElement, vizElement);                </script>
</div>
</div> --}}
<br>
{{-- @if(!empty($success))
  <div style="margin-left: 200px;margin-right:200px;" class="alert alert-success"> {{ $success }}</div>
@endif
<div align="center">
	<br><br>
<table class="tab" style="cellspacing:15px;width:45%;" align="center">
<tr>
  <td  style="font-size: 1.3em;" colspan="2">
    <h3>Populate a table from CSV (Local only)</h3>
  </td>
</tr>
 
   <tr>
    <td>
 <form action="/store_csv" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <input type="file" class="form-control-file" name="csv" id="exampleInputFile" aria-describedby="fileHelp">
                                <small id="fileHelp" class="form-text text-muted">Upload a CSV file.</small>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control-file" name="table_name" id="table_name" aria-describedby="fileHelp">
                                <small id="fileHelp" class="form-text text-muted">Table name.</small>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>  
    </td>
  </tr>
   @if ( session()->has('message') )
   <tr>
     <td colspan="2" style="width:40%;color:maroon;text-align: center;" class="alert alert-success alert-dismissable">
       {{ session()->get('message') }} </td>
   </tr>
   @endif

<tr>
	<td colspan="3">
<h3>Enter the name of a table to add to the main table:</h3>
</td>
</tr>
<tr>
<td colspan="2">
<form action="/store_data" method="POST">
    {{csrf_field()}}
  <input type="text" name="table_name" size="20" autofocus="autofocus" />
  <td><button type="submit">Load</button></td>
</form>
</td>
</tr>
<tr>
<td colspan="2"><h3>Table tests</h3></td>
	</tr>
<td colspan="2">
<form action="/analyze_table" method="POST">
    {{csrf_field()}}
  <input type="text" name="table_name" size="20" autofocus="autofocus" />
  <td><button type="submit">Test Table</button></td>
</form>
</td>
</tr>
 <tr>
    <td colspan="3"><h3>SQL Table Load</h3></td>
   </tr>
    <tr>
    <td colspan="2">
      <form action="/sql_load" method="POST">
    {{csrf_field()}}
  <input type="file" name="file_name" size="20" autofocus="autofocus" />
  <td><button type="submit">Load File</button></td>
</form>
    </td>
   </tr>
   <tr>
   <td colspan="2"><h3>Dump Table to SQL</h3></td>
  </tr>
<td colspan="2">
<form action="/dump_table" method="POST">
    {{csrf_field()}}
    <label>Table to dump:</label><br>
  <input type="text" name="table_name" size="20" autofocus="autofocus" /><br>
  <label>New table name:</label><br>
  <input type="text" name="new_table_name" size="20" autofocus="autofocus" />
  <td><button type="submit">Dump Table</button></td>
</form>
</td>
</tr>
 <tr>
   <td colspan="2"><h3>Dummmy Test</h3></td>
  </tr>
<td colspan="2">
<form id="test">
    
    <label>Table to dump:</label><br>
  @for($i=1; $i<5; $i++)
  <input class="tst" type="text" name="number
  " value="{{$i}}" /> 
  @endfor
  <td><button type="submit">test</button></td>
</form>
</td>
</tr>
</table>
<div style="width:70%" id="test_div">
  
</div>
<br> --}}


@endsection