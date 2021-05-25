@extends('layouts')
@section('title', 'Tutorial')
@section('content')

<style type="text/css">
	ul li { padding: 5px 0px; }
</style>

<div align="center" style="margin-left: 50px; margin-right:50px; text-align: left;">
<h2>Instructions</h2>
<h3>ShelfReader</h3>
<p style="font-size: 1.4em;">
	<i>Note: To see a screencast of the application in action, click 
		<a href="/video">here</a></i>.
</p>
<div style="font-size: 1.6em;">
	<p>
		ShelfReader is a web application that facilitates the correction of shelving errors in collections using the Library of Congress filing system. It provides a graphical user interface that directs the user in correcting the shelf. Data is input using a barcode scanner, and information pertaining to each book is retrieved via http request from a web service containing the integrated library system (ILS). Because this information is updated in real time, shelfreader is able to provide some additional information about the status of items you scan. 
	</p>
	<p>
		ShelfReader can be used simultaneously by multiple users, using separate user accounts. Please note that two users operating under the same login simultaneously will produce invalid results.
	</p>
	<p>
	 On your first visit to the site, click on the “Register” link in the upper right corner. Fill out the form, selecting the library where you will be using the program and press the “Register” button.
</p>
<h3>Shelf Reading</h3>
<p>
After registering or logging in, you will be taken to the home page. Click on the “Start Shelving” link, or the "Shelving" link in the navigation bar at the top of any page. You can now begin to scan the shelf. 
<ul>
	<li>
Scan the barcodes of the books in the order they are on the shelf, from left to right. As you scan each book, it will appear in the virtual shelf.
</li>
<li>
When an error in the order of the books on the shelf is found, the title of the misplaced book will be blue. 
</li>
<li>
 The location to move the book is indicated by a vertical green line. 
</li>
<li>
You may choose to ignore errors and continue scanning until ready to stop and make corrections.
	
</li>
<li>
When you begin making corrections continue until all errors are resolved. The program uses an algorithm to sort the shelf 
with a minimum number of corrections. The locations it indicates may not be the final correct positions of the book.
</li>

<li>
 Let the application know that a book is being moved by re-scanning the barcode of that book before placing it on the shelf. 
</li>
<li>
	Move the book to its correct position.
	</li>
<li>
When a correction is made, the virtual shelf will be re-ordered to reflect the change.
	</li>
<li>
If there are additional books out of position, the title of the next book to move will be blue.
</li>
<li>
The application will also alert you with an error buzzer when there 5 or more corrections to be made.
</li>
<li>
	The virtual shelf is now scrollable from left to right, so that all the books you have scanned are accessible.
	There is a scrollbar under the shelf that you can use to scroll the shelf to the left to see any books that are
	not currently visible.
</li>
<li>
 If the destination of a misplaced book is not visible on the screen, you do not have to scroll manually to find the book;
 click the "Show Destination" link above the shelf. The shelf will scroll to a position in which the green line is visible.
 If the book with the blue title is not visible, click the "Show Book" link. The shelf will scroll to a position 
 in which that book is visible.
 </li>
<li>
 In practice, in a shelf that is reasonably well shelved, it is probably better to correct each error as it occurs.
</li>
</ul>
</p>
<h3>A screenshot of the application indicating an error and the correct position of the book to be moved:</h3>
<img width="1180px" height="540px" src="/assets/images/scrollable.png" />

<p>
	<h3>Out of Range Books</h3>
<p>
	At times you may scan a book that is far out of range of the section being scanned. If the correct position of the book is far to the left of the current section, the barcode of that book can be re-scanned and it will no longer create errors when scanning is resumed. The book can be set aside for re-shelving.
</p>
<p>
	  If the correct position of the book is far to the right of the section being scanned, it will cause a new error on every subsequent scan. These books can be removed from the scan in the following way: 
	</p>
<ul>
	<li>
Resolve any shelving errors in the current scan by scanning the barcodes of the out of position books and placing the books in the correct position. 
</li>
<li>
The book that is out of range will now be on the right end of the virtual shelf.
</li>
<li>
 Hover the mouse over the title of the book, which is now a link. 
</li>
<li>
 Click on the title and a pop up window will appear, asking if you want to delete the book from the scan. 
</li>
<li>
 Click on “Delete”. The book will be removed and shelving can be resumed. 
</li>
</ul>
</p>
<h3>Shelving Alerts</h3>
<p>
	Shelving alerts are produced by errors in scanning or when no records exist for the scanned bar code. 
</p>
<p>
<ul>
<li>If you re-scan a barcode and there are no errors in the shelf you will see the error “You re-scanned a book but there are no errors. Please scan the next book.”  
</li>
<li>
If you re-scan the barcode of a book that is not the correct book to move, you will see the error “You re-scanned the wrong book, please re-scan <i>&lt;book title&gt;</i> ”. 
</li>
<li>
If you scan a barcode that is not found in the system, you will see the error “There is no record of an item with this barcode. Please scan the next item.”
If this occurs scanning can continue.
</li>
</ul>
</p>
<p>
<h3>Item Status Alerts</h3>
<p>
  Item status alerts indicate that the occurrence of a scanned item on your shelf is unexpected given the location information in the ILL records.
  <br>The following location results will generate a status alert: 
</p>
<p>
<ul>
<li>
 CHECKEDOUT - The item is flagged as checked out to a patron.
</li>
<li>
 ONHOLD - The item has been requested by a patron.
</li>
<li>
	LOST-ASSUM - An item overdue for a predetermined period of time and flagged by an automated process.  
</li>
<li>
	LOST - An overdue item that has been designated as lost by a user.
</li>
<li>
	MISSING - The item has been marked as missing by a user.
</li>
<li>Z-MISSING - The item has been flagged by an automated system process after being missing for a predetermined amount of time.</li>
<li>
	PALCI - An Inter Library Loan item.
</li>
<li>
  INTRANSIT - The item is designated as in transit to a given library.
</li>
<li>LOST-CLAIM - The item is marked as lost and there is an open claim.</li>
<li>WITHDRAWN - The item has been withdrawn from the collection.</li>
<li>CANCELED - The item has been cancelled.</li>

</ul>
</p>

</p>
<h3>Making the Shelf Fit the Page</h3>
	Shelfreader should detect the width of your screen and set the width of your shelf correctly. Please let us know if you are experiencing issues with 
	this.
<h3>Clearing the Shelf</h3>
<p>After logging out of ShelfReader, you are able to log in again and resume scanning a section that you were working on. If at any time you would 
	like to begin shelving in a different section of the library, you will need to clear the previously scanned section.
	There is a button on the top right of the page labeled "Clear all Data". Clicking this button will remove all books scanned by the logged in user.
</p>

<h3>The Stack View</h3>

<p>
The Stack View is a separate version of the shelfreader program. It is an attempt to render the books on the page in the same configuration that they 
are on the actual shelf. The hope is that this will make locating misplaced books and their proper locations on the shelf easier, especially if you 
scan a large number of books before making corrections. At the time of this writing the Stack View is functional but still unfinished.
</p>
<h4 style="font-size: 1.1em;">What you will See</h4>
<p>
Instead of one continuous shelf of books as in the original version, in the Stack View you will see the books seperated into shelves and 
sections as they are on the shelf in front of you. You tell the program to place a book on a new shelf by pressing
the space bar after scanning it. You tell the program to begin a new section by pressing the Alt key after scanning the first book of the new section.
 There are dropdown menus at the top of the page that you can use to designate a book as the first book in a new shelf or section if you forget to do it
  while scanning.
</p>
The images of the books in this version are much smaller than in the original version, and do not show the title of the book. If you need to see the 
title of  particular book, you can mouse over the book, and the title will appear in a tool tip. A number is displayed on each book that indicates its 
 position in that particular shelf.
<p>

	<p>
		When there are corrections to be made in the shelf, the title of the misplaced book will appear above the virtual shelf in its new position,
		flanked by the title of the book to its left, and if applicable, the title of the book on its right.
	</p>


<h3>Contacting Us</h3>
If you encounter any errors, either in the application itself or the way it is ordering books, or have any questions at all, please email us from 
the <a href="/email">Contact Us</a> page.
<br><br>

<br>
</div>
</div>
</div>
<br>
@endsection
