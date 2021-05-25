@extends('layouts')
@section('title', 'Show Full Shelf')
@section('content')

<script type="text/javascript">
  $('body').keyup(function(e){
    // User pressed alt
   if(e.keyCode == 18){

    var task1 = 'new_shelf';

            $.ajax({

           type:'POST',

           url:'/new_section',

             headers: {
        'X-CSRF-Token': $('meta[name=_token]').attr('content')
    },

           data:{task1:task1},

           success:function(data){
           
            location.reload();
          
           }

        }); return false;
       
   }
   if(e.keyCode == 32){

       // user has pressed space key

       var task2 = 'new_section';

       //alert(task2);
       
            $.ajax({

           type:'POST',

           url:'/new_shelf',

             headers: {
        'X-CSRF-Token': $('meta[name=_token]').attr('content')
    },

           data:{task2:task2},

           success:function(data){
           
            location.reload();
          
           }

        }); return false;
       
   }
});
</script>

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

<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="https://jqueryui.com/resources/demos/style.css">
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

  <script type="text/javascript">
  function book(book)
  {
    var book = book;

    $( "div.show_shelf" ).scrollLeft( book );
  }
</script>

<script type="text/javascript">
  function destination(dest)
  {
    var dest = dest;
    $( "div.show_shelf" ).scrollLeft( dest );
  }
</script>

<script type="text/javascript">
  $(document).ready(function (){
          $( "div.show_shelf" ).scrollLeft('<?php echo $maxpos*300; ?>');
        });
</script>

<div align="center"> 

</div>

<table width="80%" align="center">
  <tr>

</td>
<td>

</td>
<td>
 <form action="/fullshelf/store" method="POST">
    {{csrf_field()}}
  <input  id="barcode" type="text" name="barcode" size="20" autofocus="autofocus" />&nbsp;&nbsp;&nbsp;
  <button type="submit">Scan Barcode</button>
</form>
  </td>

<td>
 <form action="/fstruncate" method="POST">
    {{csrf_field()}}
  <input type="hidden" name="truncate"  />&nbsp;&nbsp;&nbsp;
  <button type="submit">Clear Shelves</button>
</form>
</td>
<td>
   <form action="/add_shelf" method="POST">
    {{csrf_field()}}
     <select name="position">
      @foreach($reverse_books as $r)
       <option value="{{$r->position}}">
         {{str_limit($r->title,20,'')}}
       </option>
       @endforeach
     </select>
   <button class="btn btn-primary" id="submit">Add a Shelf</button>
</td>
<td>
   <form action="/add_section" method="POST">
    {{csrf_field()}}
     <select name="position">
      @foreach($reverse_books as $r)
       <option value="{{$r->position}}">
         {{str_limit($r->title,20,'')}}
       </option>
       @endforeach
     </select>
   <button class="btn btn-primary" id="submit">Add a Section</button>
</form>
</td>
</tr>
</table>
<br>

@if($errors > 0)
@include('fullshelf.partials._destination')
@endif
<br>

@if($item_alert !== 0)
@include('fullshelf.partials._error')
@endif
<br>
<div style="margin-left:30px;margin-right:10px;">
  {{-- Begin outer table --}}

<table cellpadding="15px" align="center" width="85%">
  <tr>
<td><a href="#" onclick="javascript:destination('<?php echo $movepos*300; ?>')">Show Destination</a></td>
    <td><a href="#" onclick="javascript:book('<?php echo $section_number*300; ?>')">Show Book</a></td>
  </tr>
</table>
<br><br>
<table align="center" width="80%">
<tr>
    <td align="center" colspan="{{count($send)}}">
      <span style="font-size:2em;">
        @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
      </span>
    </td>
  </tr>
</table>
<div class="show_shelf" 
style="padding-left:10px;border: 0px solid black;height:600px;overflow:auto;margin-left:20px;
         margin-right:20px;">
  
<table style="border:1px solid blue;">
  
  <tr>
  <td valign="top" width="300px;">
  <table style="width:500px;border:5px solid maroon;">
    <tr>
      <td colspan="40" align="center"><span style="padding:10px;font-size:1.5em;">Section 1</span></td>
    </tr>
<tr style="background-color:white;height:5px;"><td colspan="40"></td></tr>
    @php $e = 1; @endphp
    @php $s = 1; @endphp

@foreach($all_books as $key => $a)
@if($errors > 0)

@endif

{{-- Initiate an inner table for each section  Place it inside outer table cell --}}
{{-- get all books where position >= 1 and less than the end of the current section --}}
{{-- $send == array of section end positions --}}
{{-- $bend == array of shelf end positions--}}

@if(in_array($a->position, $send))
{{-- End previous outer table cell, start a new one --}}
@php $e =1; @endphp
@php $s++; @endphp
</table>

</td><td style="padding:10px;vertical-align:top;width:300px;">

<table style="width:700px; border:1px solid yellow">
  <tr><td valign="top" colspan="1000" align="center"><span style="font-size:1.5em;">Section {{$s}}</span></td></tr>
<tr>
@endif

@if(in_array($a->position, $bend))
{{-- End inner table row --}}
@php $e =1; @endphp
</tr>
{{-- Put some white space in between shelves --}}
<tr style="background-color:white;height:5px;"><td colspan="1000"></td></tr>
{{-- Start a new inner table row --}}
<tr>
@endif

@if($loop->first AND $green === 0)
<td style="width: 8px; background-color: green;height:60px;"></td>
    <td style="width: 3px; background-color: white;height:60px;"></td>
@endif

@if($a->cposition == $bcount AND $a->position == $bcount AND $loop->last )
    {{-- Make last book a link for deletion --}}
    <td style="text-align:center;width: 18px;background-image: url('/assets/images/booksmine.png');height:60px;">
      <span>
        <a style="color:white" title="{{$a->title}} {{$a->callno}}"  
          href="#" onclick="javascript:delete_book('<?php echo $a->barcode; ?>')">{{$e}}</a>
        </span>
    </td>
    @break
    @endif

@if($mbar === $a->barcode)
 
    <td style="text-align:center;width: 18px;background-color: blue;height:60px;">
      <span><a style="color:white" title="{{$a->title}} {{$a->callno}}" href="#">{{$e}}</a></span>
    </td>
    @else
    <td style="text-align:center;width: 18px;padding:1.75px;background-image: url('/assets/images/booksmine.png');height:60px;">
      <span><a style="color:white" title="{{$a->title}} {{$a->callno}}" href="#">{{$e}}</a></span></td>
    @endif
    @if($a->position === $green)
    <td style="width: 3px; background-color: white;height:60px;"></td>
    <td style="width: 8px; background-color: green;height:60px;"></td>
    <td style="width: 3px; background-color: white;height:60px;"></td>
    @else
    <td style="width: 3px; background-color: white;height:60px;"></td>
    @endif
    
    @php $e++; @endphp
  
@endforeach

</tr>
</table>
</tr>
</table>
<div id="show_delete"></div>
</div>
</div>
<br>
<br>

<script type="text/javascript">
  function delete_book(barcode)
  {
    var barcode = barcode;

    $('#show_delete').dialog({ autoOpen: false, modal: true,
    resizable: false, draggable: false, width: $(window).width() * 50 /100, 
    height: $(window).height() * 40 /100,
    
  });
  
  $( "#show_delete" ).load( '/delete_full_book/' + barcode,
    function(){$( "#show_delete" ).dialog('open')});
    
  }
</script>

@endsection