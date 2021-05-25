@extends('layouts')
@section('content')

</script>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Hello {{\Auth::user()->name}}</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in! &nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="/sorting">Start Shelving</a>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
