@extends('layouts')
@section('title', 'Shelving')
@section('content')

<script type="text/javascript">
  
  $(document).ready(function() {
        var barcode="";

    $(document).keydown(function(e) {

        var code = (e.keyCode ? e.keyCode : e.which);
        if(code==13)// Enter key hit
        {
            
              var mbar = barcode;
              //alert(mbar);
            $.ajax({

           url: "/fullshelf/store",
                  method: 'post',
                  data: {
                     barcode:barcode
                  },

             headers: {
        'X-CSRF-Token': $('meta[name=_token]').attr('content')
    },

           data:{barcode:mbar},

           success: function(data){
                      if(data.alert)
                      {

                        $('#barcode').val('');

                        var uhlert = data.alert;
                        var title = encodeURIComponent(data.title);
                        barcode = '';
                        $( "#item_alert" ).load( '/item_alerts/' + uhlert + '/' + title);
                        
                      }

                      else 

                      {
                        //alert(barcode);
                        location.reload();
                      }
                  }
                    

        }); return false;

        }

        else if(code==9)// Tab key hit
        {
            var mbar = barcode;
            alert(barcode);
            $.ajax({

           type:'POST',

           url:'/fullshelf/store',

             headers: {
        'X-CSRF-Token': $('meta[name=_token]').attr('content')
    },

           data:{barcode:mbar},

           success:function(data){
           
            location.reload();
          
           }

        }); return false;

        }
         else
         {
             barcode=barcode+String.fromCharCode(code);

            
         }
    });

    });

</script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/resources/demos/style.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
  $( function() {
    $( document ).tooltip();
  } );
  </script>
  <style>
  label {
    display: inline-block;
    width: 5em;
  }
  </style>


<div align="center"> 

  <h2>Scan your first book:</h2>

</div>

<table width="50%" align="center">
  <tr>

</td>
<td>
<div align="center"> 

</div>
</td>
<td>
 <form action="/fullshelf/store" method="POST">
    {{csrf_field()}}
  <input  id="barcode" type="text" name="barcode" size="20" autofocus="autofocus" />&nbsp;&nbsp;&nbsp;
  <button type="submit">Scan Barcode</button>
</form>
  </td>
<td>

</td>

<td>
 <form action="/ftruncate" method="POST">
    {{csrf_field()}}
  <input type="hidden" name="truncate"  />&nbsp;&nbsp;&nbsp;
  <button type="submit">Clear Shelves</button>
</form>
</td>
</tr>
</table>
<br>

@include('fullshelf.partials._destination')

<br>

<div align="center">

<div style="margin-left: 100px;">
	{{$bcount}}
	<br>
	<br>
  @if($mcp === 'none')

  @else
	@if(@sections === 1)
	<table width="25%">
		<tr>
      
	@foreach($all_books as $key=>$a)

	@php $e = $key+1; @endphp

		@if($a->position != 1 AND in_array($a->position, $bend)) 
		</tr>
		<tr> 
		<td colspan="{{$e}}" style="height:8px;vertical-align: top;"></td>
		</tr>
		<tr>
		
		@endif
    @if($mbar === $a->barcode)
    <td style="width: 15px; background-color: blue;height:50px;"></td>
    @else
    <td style="text-align:center;width: 15px; background-color: black;height:50px;">
      <span style="color:white">{{$a->position}}</span>
    </td>
    @endif
		@if($mpos === 0)
      <td style="width: 3px; background-color: white;height:50px;"></td>
      <td style="width: 8px; background-color: green;height:50px;"></td>
      <td style="width: 3px; background-color: white;height:50px;"></td>
      @endif
    @if($a->position === $mpos)
    <td style="width: 3px; background-color: white;height:50px;"></td>
    <td style="width: 8px; background-color: green;height:50px;"></td>
    <td style="width: 3px; background-color: white;height:50px;"></td>
    @else
		<td style="width: 3px; background-color: white;height:50px;"></td>
    @endif
	@endforeach
</tr>
</table>
@else
<table width="{{$ind*25}}%">
<tr>
@foreach($sections as $key1=>$s)

<td style="border:3px solid; padding:5px;vertical-align: top;" width="25%">


<table>
		<tr>

			
			@if($loop->last)

	@foreach($all_books->where('position','>=',$mcp) as $shkey1=>$a)
		
	@php $e = $shkey1+1; @endphp

		@if($a->position != 1 AND in_array($a->position, $bend))
		</tr>
		<tr> 
		<td colspan="{{$e}}" style="height:8px;"></td>
		</tr>
		<tr>
		@php  @endphp
		@endif
		@if($mbar === $a->barcode)
    <td style="text-align:center;width: 15px; background-color: blue;height:50px;">
      <span><a style="color:white" title='{{$a->title}} {{$a->callno}}' href="#">{{$a->position}}</a></span>
    </td>
    @else
    <td style="text-align:center;width: 15px; background-color: black;height:50px;">
      <span><a style="color:white" title="{{$a->title}} {{$a->callno}}" href="#">{{$a->position}}</a></span></td>
    @endif
		@if($a->position === $mpos)
    <td style="width: 3px; background-color: white;height:50px;"></td>
    <td style="width: 8px; background-color: green;height:50px;"></td>
    <td style="width: 3px; background-color: white;height:50px;"></td>
    @else
    <td style="width: 3px; background-color: white;height:50px;"></td>
    @endif
	@endforeach
	@break
	@endif



		@foreach($all_books->where('position','>=',$cp[$key1])->where('position','<',$cp[$key1+1]) as $key2=>$a)
		
	@php $e = $key2+1; @endphp

		@if($a->position != 1 AND in_array($a->position, $bend))
		</tr>
		<tr> 
		<td colspan="{{$e}}" style="height:8px;vertical-align: top;"></td>
		</tr>
		<tr>
		@php  @endphp
		@endif
		@if($mbar === $a->barcode)
    <td style="text-align:center;width: 15px; background-color: blue;height:50px;">
      <span><a style="color:white" title="{{$a->title}}" href="#">{{$a->position}}</a></span>
    </td>
    @else
    <td style="width: 15px; background-color: black;height:50px;">
      <span><a style="color:white" title="{{$a->title}}" href="#">{{$a->position}}</a>
      </span></td>
    @endif
		@if($a->position === $mpos)
    <td style="width: 3px; background-color: white;height:50px;"></td>
    <td style="width: 8px; background-color: green;height:50px;"></td>
    <td style="width: 3px; background-color: white;height:50px;"></td>
    @else
    <td style="width: 3px; background-color: white;height:50px;"></td>
    @endif
	@endforeach
	
</tr>
</table>


</td>
@endforeach
</tr>
</table>
</table>
@endif
@endif
</div>
</div>
<br><br>



@endsection