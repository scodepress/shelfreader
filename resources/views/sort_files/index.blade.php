@extends('layouts')
@section('title', 'Sort Files')
@section('content')
<div style="margin:50px;">
	
	<h2>Upload an Excel or CSV inventory file and make it sortable by call number.</h2>

	<h3>File Formatting:</h3>
	<ul>
		<li>The upload file must be in CSV or Excel format.</li>
		<li>The first row of the file must consist of one-word column headings. Each column must have a heading</li>
		<li>The first column must contain only the item's call number and have the heading "callnumber".</li>
        <li>The second column must contain only the item's barcode or item id and have the heading "barcode".</li>
        <li>Subsequent columns are allowed and must have column names.</li>
		
	</ul>
	<br>
	<div style="font-size:1.2em">
		The application will create an excel download file sorted by call number, and adds a "position" column to your file. The items can then be re-sorted into call number order using that column if needed.  
	</div>
	<br>
	<div style="width:50%;" >

            <form action="{{ route('store-sort-file') }}" method="POST" enctype="multipart/form-data">

                @csrf
               
             
                <br>
      
                <label>Select the file to sort: (Files must be in csv or excel format.)</label>
                <input type="file" name="file" class="form-control">

                <br>

                <button class="btn btn-success">Import User Data</button>

            </form>

        </div>

</div>



@endsection