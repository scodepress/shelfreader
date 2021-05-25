@extends('layouts')
@section('content')
<br>
<br>
<br>
<h1>Thun Library</h1>
<br>
<a href="/shelfs/create" class="myBtn" >New Section</a>
<br>
<br>
<div align="center">
  <form>
  <input id="bcode" type="text" name="barcode" size="20" autofocus="autofocus" />&nbsp;&nbsp;&nbsp;
<button type="button"></button>
</form>
</div>
<br>


<br>
<table align="center" width="90%" border="0">

        @foreach($section as $key=>$s)
        <tr id="{{$s->barcode}}" >

                <td width="5%">{{$key+1}}</td>
                <td width="60%"><span style="font-size: 1.3em;">{{$s->title}}</td>
                <td width="10%"><span style="font-size: 1.3em;">{{$s->barcode}}</span></td>
                <td><span style="font-size: 1.3em;">{{$s->callno}}</span></td>
        </tr>

        @endforeach

</table>
<br>

@endsection

