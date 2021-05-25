  $( function() {
    $( ".datepicker" ).datepicker({
      dateFormat: "yy-mm-dd"
      });
  } );


$(document).ready(function(){
   
$('.myBtn').on('click', function(e){
  e.preventDefault();
  $('#myModal').modal('show').find('.modal-body').load($(this).attr('href'));
});

   $("#myModal").on('hidden.bs.modal', function () {
            // $(this).data('bs.modal', null);
             $(this).modal('hide');
    });
 
});

  $(document).ready(function(){
   
    $('#test').submit(function(e){
      
      e.preventDefault();
      alert($('#test').serializeArray());
      $.ajax({
        type: 'POST',
        url: '/test',
        data: $('#test').serialize(),
        headers: {
          'X-CSRF-Token' : $('meta[name=_token]').attr('content')
        },

        success:function(data){
         alert(data);
          //$( "#test_div" ).load( '/faq');
        }
      });

    });
  });

  $(document).ready(function(){

    $("#bcode").keypress(function(event){
    if (event.which == '10' || event.which == '13') {
        event.preventDefault();
    }


        var barcode = $("#bcode").val().slice(4);
        
        $.ajax({

           type:'POST',

           url:'/bookid',

             headers: {
        'X-CSRF-Token': $('meta[name=_token]').attr('content')
    },

           data:{barcode:barcode},

           success:function(data){
            $("#" + barcode).css("background-color", "#00CC00");//green
            
              $('html, body').animate({
         scrollTop: $("#" + barcode).offset().top-825
          }, 0);
          
           $('#bcode').val('');
           }

        }); 

});

  });


   $(document).ready(function(){

    $("#r_code").one('input', function(event){
   

        event.preventDefault();
  
        var barcode = $("#r_code").val();
        var terminus = $("#terminus").val();

        //alert(terminus);

    
        
        $.ajax({

           type:'POST',

           url:'/store_rows',

             headers: {
        'X-CSRF-Token': $('meta[name=_token]').attr('content')
    },

           data:{barcode:barcode,terminus:terminus}

         })

        .done(function( data ) {
     $( "#row_switch" ).load( '/row_switch');
      })
    //   .fail(function( jqXHR, textStatus, errorThrown) {
     
    // }

});

});  

    $(document).ready(function(){

    $("#e_code").one('input', function(event){
   

        event.preventDefault();
  
        var barcode = $("#e_code").val();
        var terminus = $("#eterminus").val();

        //alert(terminus);

    
        
        $.ajax({

           type:'POST',

           url:'/store_rows',

             headers: {
        'X-CSRF-Token': $('meta[name=_token]').attr('content')
    },

           data:{barcode:barcode,terminus:terminus}

         })

        .done(function( data ) {
        window.location = "/create_rows";
      })
    //   .fail(function( jqXHR, textStatus, errorThrown) {
     
    // }

});

}); 

 $(document).ready(function(){

    $("#s1_code").one('input', function(event){
   

        event.preventDefault();
  
        var barcode = $("#r_code").val();
        var terminus = $("#terminus").val();

        //alert(terminus);

    
        
        $.ajax({

           type:'POST',

           url:'/store_section',

             headers: {
        'X-CSRF-Token': $('meta[name=_token]').attr('content')
    },

           data:{barcode:barcode,terminus:terminus}

         })

        .done(function( data ) {
     $( "#section_switch" ).load( '/section_switch');
      })
    //   .fail(function( jqXHR, textStatus, errorThrown) {
     
    // }

});

}); 

 $(document).ready(function(){

    $("#s2_code").one('input', function(event){
   

        event.preventDefault();
  
        var barcode = $("#e_code").val();
        var terminus = $("#eterminus").val();

        //alert(terminus);

    
        
        $.ajax({

           type:'POST',

           url:'/store_section',

             headers: {
        'X-CSRF-Token': $('meta[name=_token]').attr('content')
    },

           data:{barcode:barcode,terminus:terminus}

         })

        .done(function( data ) {
        window.location = "/create_section";
      })
    //   .fail(function( jqXHR, textStatus, errorThrown) {
     
    // }

});

}); 
 
 $(document).ready(function(){

   function reg_lib() { 
    alert('reg_lib');
   }

 });

 function locationDialog(item_alert,location) {

  var item_alert = item_alert;

  var location = location;

  $('#item_alert').dialog({ autoOpen: false, modal: true,
    resizable: false, draggable: false, width: $(window).width() * 50 /100, 
    height: $(window).height() * 70 /100,
    
  });
  
  $( "#item_alert" ).load( '/item_alert/' + item_alert + '/' + location,
    function(){$( "#item_alert" ).dialog('open')});
  
  
}