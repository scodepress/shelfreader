@foreach($response as $key=>$j)

<p>{{$response['TitleInfo'][0]['numberOfCallNumbers']}}</p>
<p>{{$response['TitleInfo'][0]['title']}}</p>
@for($i=0; $i<=$response['TitleInfo'][0]['numberOfCallNumbers']-1; $i++)
 @if($j[0]['CallInfo'][$i]['ItemInfo'][0]['itemID'] == $itemID)
 {{$j[0]['CallInfo'][$i]['ItemInfo'][0]['itemID']}}
 <br>
 {{$j[0]['CallInfo'][$i]['ItemInfo'][0]['currentLocationID']}}
 <br>
 {{$j[0]['CallInfo'][$i]['libraryID']}}
  <br>
 {{$j[0]['CallInfo'][$i]['callNumber']}}
 @endif
@endfor

@endforeach
<br>






