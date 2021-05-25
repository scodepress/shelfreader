
<div style="width: 100%;">
 @if($corrections)
@if($corrections >= 5 )
<audio autoplay>
  <source src="/assets/beep-05.wav" type="audio/wav">
</audio>
@endif
@endif

<div align="center">
@if(\Session::has('message'))  
<div  style="width: 65%;background-color: red;color:white;font-size: 1.8em;" class="alert alert-danger">
  {{ \Session::get('message')}} 
  <audio autoplay>
  <source src="/assets/beep-02.wav" type="audio/wav">
</audio>

</div>
@endif
</div>
