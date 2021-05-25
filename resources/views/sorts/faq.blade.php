@extends('layouts')
@section('title', 'FAQ')
@section('content')
<h2 align="center">Frequently Asked Questions</h2>
<div style="width: 70%; margin-left:15%;">

<ol style="font-size: 1.5em;">
<li>
		<span style="color:red;font-weight: bold;">Q. </span> <b>Will the program identify missing books?</b>
		<br>
		<span style="color:blue;font-weight: bold;">A.</span> 
		Since we began using the ILS web service as a data source for ShelfReader, we have immediate feedback about the status of the items 
		you scan. ShelfReader alerts the user when a scanned item should not currently be on your shelf according to the ILS records.
		For example, if a book was returned to the shelf without being discharged, an alert will appear telling you the item is checked out.
	</li>
	<br>
	<li>
		<span style="color:red;font-weight: bold;">Q. </span> <b>What does it mean when a book is "Shadowed"?</b>
		<br>
		<span style="color:blue;font-weight: bold;">A. </span>
		<span>
			Shadowed items remain on file but are not displayed in search results because they are not available for circulation. Items
			may be shadowed at the title level, in which case no information for any copies of the book are is available, or they may be shadowed at the 
			copy level, in which case information for sorting the book can be obtained from a copy.
		</span>
	</li>
	<br>
	<li>
		<span style="color:red;font-weight: bold;">Q. </span><b>Why does the barcode scanner not beep when I scan a barcode?</b>
		<br>
		<span style="color:blue;font-weight: bold;">A.</span> This has to do with the settings of the barcode scanner itself. Check the documentation 
		for your scanner to turn the sound on, or increase the volume.

	</li>
	
</ol>
</div>
<br><br><br>
@endsection