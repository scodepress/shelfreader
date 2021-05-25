<!DOCTYPE html>
<html>
<head>
  <title>ShelfReader - @yield('title')</title>
  <meta http-equiv="X-UA-Compatible" content="IE-edge">
 <meta name="viewport" content="width = device-with, initial-scale = 1">
  <meta name="_token" content="{{ csrf_token() }}">
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.7/js/tether.min.js" integrity="sha512-X7kCKQJMwapt5FCOl2+ilyuHJp+6ISxFTVrx+nkrhgplZozodT9taV2GuGHxBgKKpOJZ4je77OuPooJg9FJLvw==" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" crossorigin="anonymous"></script>

<!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" crossorigin="anonymous">

<script src="/js/layouts.js"></script> 
 <link rel="stylesheet" href="/css/custom/library.css">


<style>
.stand {

      transform: rotate(90deg);
  transform-origin: left top 0;

      float: left;
    }
    
</style>
<style type="text/css">
.tab {
    border-collapse: collapse;
    width: 50%;
}

.tdat {
    text-align: left;
    padding: 8px;
}

</style>
 
</head>
<body style="margin-right: auto; margin-left: auto; width: 100%;">


</div>

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #2E6F9E;color: white;">
    <div class="navbar-nav">

  <a class="navbar-brand" href="#"><span style="font-size: 20px;">{{ auth()->user()->name }}</span></a>
	<a class="nav-item nav-link" href="/sorting"><span style="font-size: 18px;">Shelving</span></a>

	<a  class="nav-item nav-link" href="{{ url('/tutorial') }}"><span style="font-size: 18px;">Instructions</span></a>

	<a class="nav-item nav-link" href="{{ url('/video') }}"><span style="font-size: 18px;">Video Tutorial</span></a>

	<a class="nav-item nav-link" href="{{ url('/faq') }}"><span style="font-size: 18px;">FAQ</span></a>
	<a class="nav-item nav-link" href="{{ url('/myreports') }}"><span style="font-size: 18px;">My Reports</span></a>
	<a class="nav-item nav-link" href="{{ url('/sort-file') }}"><span style="font-size: 18px;">Sort an Excel File</span></a>
	<a class="nav-item nav-link" href="{{ url('/email') }}"><span style="font-size: 18px;">Contact Us</span></a>
	@impersonating
	<a class="nav-link myBtn" style="color:white;"  href="{{ route('admin.impersonate') }}"><span style="font-size: 18px;">Stop/Change Impersonation</a>

      @endimpersonating

       @if(\Auth::user()->privs == 1 )
	<a class="nav-link myBtn" style="color:white;" href="{{ route('admin.impersonate') }}"><span style="font-size: 18px;">Impersonate</a>

      @endif
      <a class="nav-item nav-link" href="/logout"><span style="font-size: 18px;">Logout</a>

   @if(\Auth::user()->privs == 1 )
  <div align="right">
   <span style="color:black; font-size: 18px;" class="nav-link">@include('menus.admin')</span>
 </div>
 @endif
    </div>
  </div>
</nav>
</div>




   <div id="myModal" class="modal" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
    
      <div class="modal-body mx-auto mw-800">
        <p>Some text in the modal.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<div style="margin:0px;">
  
@yield('content')
</div>

 <div style="font-size: 1.3em;" align="center">

 @include('footer')

    </div>
</body>
</html>






