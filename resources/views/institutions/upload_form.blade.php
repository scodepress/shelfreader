@extends('modals')

@section('content')
 

<div class="container">

    <div>

        <div>

            

        </div>

        <div style="width:50%;" >

            <form action="{{ route('store-file') }}" method="POST" enctype="multipart/form-data">

                @csrf
                <label>
                    Select the Library:
                </label>
                <br>
                <select name="libraryId">
                @foreach($inventory_users as $key=>$user)
                <option value="{{$user->id}}">{{$user->library}}</option>
                @endforeach
                </select>
                <br>
                <br>
                <label>Select a CSV file to upload into the inventory table:</label>
                <input type="file" name="file" class="form-control">

                <br>

                <button class="btn btn-success">Import User Data</button>

            </form>

        </div>

    </div>

</div>

   

@endsection