@extends('modals')
@section('content')


                <div style="font-family: TimesNewRoman; font-size: 1.5em;">Impersonate a User</div>

             <form action="{{ route('admin.impersonate') }}" method="POST">
             	{{ csrf_field() }}
             	
             	<select name="email">
             		@foreach($emails as $key=>$e)
            		{
             		<option value="{{$e->email}}">{{$e->name}}</option>
             		}
             	@endforeach
             </select>
             	&nbsp;&nbsp;<button type="submit">Impersonate</button>
             </form>
   
<br>
<div style="text-align: center">
	 @impersonating 

        <a style="font-size: 1.6em;font-family: TimesNewRoman;" href="/admin/impersonate/destroy">
        Stop Impersonating</a>
 
      @endimpersonating
</div>
<br><br><br>
@endsection