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

           url: "/receive-barcode",
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

           url:'/receive-barcode',

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

<table width="80%" align="center">
  <tr>

</td>
<td>

</td>
<td>
 <form action="/receive-barcode" method="POST">
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

</tr>
</table>
<br>


@if($empty === false)

<script type="text/javascript">
  $(document).ready(function (){
          $( "div.show_shelf" ).scrollLeft('<?php echo $lbook_pos*300; ?>');
        });
</script>

<div align="center"> 

</div>



<br>


<style>
.stack {
  display: flex;
  width: {{$width}}%;
  flex-direction: row;
  /* background-color: DodgerBlue; */
  margin-left: 5px;
}

.section {
  width: 30%;
  height:100%;
  margin-top: 5px;
  text-align: center;
}

.shelf {
  /* background-color: #f1f1f1; */
  width: 100%;
  height:20%;
  margin-top: 5px;
  margin-left: 5px;
}

.book {
  background-color: #cc9900;
  width: 14px;
  height:45px;
  border: 1px solid black;
  font-size: .9em;

}

.green_line {
  background-color: green;
  width: 3px;
  height:45px;
}

.blue_book {
  background-color: blue;
  width: 12px;
  height:45px;
}


</style>

{{$corrections}} {{$dbar}} {{$mpos}}

<h2>First Section</h2>

<div class="stack">
  @foreach($section_end as $section_key=>$se)

  <!-- Get the beginning and ending positions of each section as it loops through -->
  @if($section_key == 0)

  <?php $brange = 0; $trange = $se; ?>

  @else

  <?php $brange = $section_end[$section_key-1]; $trange = $se; ?>

  @endif

<div class="section">
  <div class="shelf">
  @foreach($ordered_books->where('position','<=',$trange)->where('position', '>', $brange) as $bkey=>$b)
  @if($b->position <= $shelf_end[0])
  @if($mpos == 1 AND $bkey ==0)
  <div class="green_line">
  </div>
  @endif
  @if($mbar == $b->barcode)
  <div class="blue_book">
  <span><a style="color:white" title="{{$b->title}} {{$b->callno}}" href="#">{{$b->position}}</a></span>
  </div>
  @else
  <div class="book">
  <span><a style="color:white" title="{{$b->title}} {{$b->callno}}" href="#">{{$b->position}}</a></span>
  </div>
  @endif
  
  @if($b->position == $green)
  <div class="green_line">
  </div>
  @endif

  @endif
  @endforeach
 



<!-- Middle Shelves -->

  
  @foreach($shelf_end as $skey=>$s)
  @if($skey>0)
  <div class="shelf">
  @foreach($ordered_books->where('position','<=',$trange)->where('position', '>', $brange) as $bkey=>$b)
  @if( in_array($b->position-1, $shelf_end) OR $bkey == 0) 
  <?php $tcount = 1; ?>
  @endif
  @if($skey == 0) <?php $offset = 0; ?> @else <?php $offset = $skey-1; ?> @endif
  @if($b->position > $shelf_end[$offset] AND $b->position < $s+1)
  <div class="book">
  <span><a style="color:white" title="{{$b->title}} {{$b->callno}}" href="#">{{$tcount}}</a></span>
  </div>
  @endif
  @if($b->position == $green)
  <div class="green_line">
  </div>
  @endif
  <?php $tcount++; ?>
  @endforeach
  

  @endif
  </div>
  @endforeach


  <!-- Last Shelf -->

  

  @foreach($ordered_books->where('position','<=',$trange)->where('position', '>', $brange) as $bkey=>$b)
  @if($b->position > end($shelf_end))
  @if($b->position == end($shelf_end)+1)
  @if($b->position == $green)
  <div class="green_line">
  </div>
  @endif
  <div class="shelf">
    @endif
    <div class="book">
    <span><a style="color:white" title="{{$b->title}} {{$b->callno}}" href="#">{{$tcount}}</a></span>
    </div>
    @if($b->position == end($ordered_books))
  
  </div>

  @endif
  @endif
  @endforeach
  
  

  </div>

@endforeach
</div> <!-- End of Stack -->

@endif
<br><br>
@endsection
