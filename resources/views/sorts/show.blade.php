@extends('layouts')
@section('title', 'Shelving')
@section('content')

@if($item_alert == 'post')

<script type="text/javascript">
  
  $(document).ready(function() {
        var mbar = "";
          var mbar = "<?php echo $barcode; ?>";
            $.ajax({
           type:'POST',
           url:'/store_sort',
             headers: {
        'X-CSRF-Token': $('meta[name=_token]').attr('content')
    },
           data:{barcode:mbar},
           success:function(data){
           
          location.reload();
          
           }
        }); return false;
        });
</script>

@endif

<script type="text/javascript">
  
  $(document).ready(function() {
        var barcode="";
    $(document).keydown(function(e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if(code==13)// Enter key hit
        {
            
            var mbar = barcode;
             // alert(barcode);
            $.ajax({
           url: "/store_sort",
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
                        var uhlert = data.alert;
                        var title = encodeURIComponent(data.title);
                        barcode = '';
                        $( "#item_alert" ).load( '/item_alerts/' + uhlert + '/' + title);
                        
	    		$('#barcode').val('');
                      }
                      else 
                      {
                        location.reload();
                      }
                  }
                    
	    });
	  
	    return false;
        }
        else if(code==9)// Tab key hit
        {
            var mbar = barcode;
            alert(barcode);
            $.ajax({
           type:'POST',
           url:'/store_sort',
             headers: {
        'X-CSRF-Token': $('meta[name=_token]').attr('content')
    },
           data:{barcode:mbar},
           success:function(data){
           
            location.reload();
          
           }
	    }); 
	    
	    $('#barcode').val('');
	    return false;
        }
         else
         {
             barcode=barcode+String.fromCharCode(code);
            
         }
    });
    });
</script>

 

@if($item_alert == 1)

<audio autoplay>
  <source src="/assets/beep-05.wav" type="audio/wav">
</audio>
<script type="text/javascript">
  $(document).ready(function(){
    var barcode = "<?php echo $barcode; ?>";
    var location = "<?php echo $location; ?>";
    var ptitle = "<?php echo $title; ?>";
    var title = encodeURI(ptitle);
    $( "#item_alert" ).load( '/sorts/' + barcode);
  });
</script> 
@endif 
@if(!\Session::has('div_width'))

{{\Session::get('div_width')}} 
<span style="color:red;">Calculating the shelf width...</span>
@endif
@if(!\Session::has('div_width') OR \Session::get('div_width') === 0 )

<script type="text/javascript">
  setTimeout(function(){ 
   $(document).ready(function(){
  var w = $('div.show_table').width(); 
  $.ajax({
           url: "/set_width",
                  method: 'post',
                  data: {
                     width:w
                  },
             headers: {
        'X-CSRF-Token': $('meta[name=_token]').attr('content')
    },
           data:{width:w},
           success: function(data){
                      
                        //load form instead of this
                        location.reload();
                      }
        });
        });
   }, 2000);
</script>

@endif

@if(isset($mybar)) 

<script type="text/javascript">
    $(document).ready(function(){
        var barcode = "<?php echo $mybar; ?>";
          $( ".show_table" ).load( '/sorts/' + barcode);
        }); 
</script> 

<div>
<table style="margin-top:15px;" width="90%" align="center">
	<tr>
		<td>
<h2 style="color:#2E6F9E">Penn State Libraries</h2>
</td>

<td>

{{-- @if($errors>0)
@if(\Auth::id() == 1) {{$mpos}}
@endif --}}



	<span style="color:red;font-size: 1.5em;text-align: left;">Corrections: {{$corrections}}</span>
	@if(\Auth::id()== 1) 
	<span style="color:red;font-size: 1.5em;text-align: left;"> | {{$dbar}} </span>
	 @endif 
</td>
<td>
 <form action="/store_sort" method="post">
  {{csrf_field()}}
  <input id="barcode" type="text" name="barcode" size="20" autofocus="autofocus" />&nbsp;&nbsp;&nbsp;
   <button id="submit" class="btn btn-success">Submit</button>
</form>
  </td>

  <td>

	@if($userPrivs == 2 OR $userPrivs == 1)
    <a href="/master_keys/export_shelf">Export Shelf</a>
	@endif
  </td>

<td>
 <form action="/sorts/truncate" method="POST">
    {{csrf_field()}}
  <input type="hidden" name="barcode"  />&nbsp;&nbsp;&nbsp;
  <button type="submit">Clear All Data</button>
</form>
</td>
</tr>
</table>

<div width="80%" align="center"> 

</div>
</div>
 @if(isset($err))
  <span style="color:red; font-size: 2.3em;"> {{$err}} {{$dbar}} </span>
  @endif

 {{--  @if(\Session::has('error') )
  <div  style="margin-right:150px; width: 65%;background-color: red;color:white;font-size: 1.8em;" class="alert alert-danger">
  {{ \Session::get('error')}}
  </div>
  @endif --}}

  @if(\Session::has('message') AND \Session::get('message') != 'Table is empty.') 
  <div align="center">
<div  style="margin-right:150px; width: 65%;background-color: red;color:white;font-size: 1.8em;" class="alert alert-danger">
  {{ \Session::get('message')}} 
  <audio autoplay>
  <source src="/assets/beep-02.wav" type="audio/wav">
</audio>
</div>
</div>
@endif
@include('layouts.partials.alerts._alerts')


<div id="item_alert"></div>

<div class="show_table" id="sbar" style="width:100%;height:900px;overflow:auto;margin-left:20px;margin-right:20px;">
  
</div>


@else

<div align="center"> 

  <h2>Scan your first book:</h2>

</div>
<div align="center">

 <form action="/store_sort" method="POST">
    {{csrf_field()}}
  <input type="text" name="barcode" size="20" autofocus="autofocus" />&nbsp;&nbsp;&nbsp;
  <button type="submit">Scan Barcode</button>
</form>
</div>
@endif


  <script type="text/javascript">
         jQuery(document).ready(function(){
            jQuery('#submit').click(function(e){
               e.preventDefault();
               var barcode = jQuery('#barcode').val();
               //alert(barcode);
               
               jQuery.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                  }
              });
               jQuery.ajax({
                  url: "/store_sort",
                  method: 'post',
                  data: {
                     barcode:barcode
                  },
                  success: function(data){
                      if(data.alert)
                      {
                        $('#barcode').val('');
                        barcode = '';
                        var uhlert = data.alert;
                        var title = encodeURIComponent(data.title);
                        $( "#item_alert" ).load( '/item_alerts/' + uhlert + '/' + title);
                      }
                      else 
                      {
                        location.reload();
                      }
                  }
                    
                  });
               });
            });
</script>
<script type="text/javascript">
  function destination(dest)
  {
    var dest = dest;
    $( "div.show_table" ).scrollLeft( dest );
  }
</script>


	@endsection

	
<style>
	#sbar::-webkit-scrollbar {
  display: none;
 -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}
</style>
