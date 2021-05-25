<?php

namespace App\Http\Controllers;

use App;
use App\Models\FullLister;
use App\Models\FullMove;
use App\Models\FullShelf;
use App\Models\FullSortkey;
use App\Models\GulpTest;
use App\Models\Report;
use App\Models\Lis; 
use App\Repositories\Contracts\FullShelfRepository;
use App\Repositories\Contracts\SortsRepository;
use App\Repositories\Eloquent\Criteria\ByUser;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use App\Models\Section;
use App\Models\ShelfError;
use App\Models\Shelfend;
use App\Traits\LibraryName;
use App\Traits\SortTrait;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Auth;

class FullShelvesController extends Controller
{
	use LibraryName;
	use SortTrait;

	protected $lis;
	protected $shelf;
	public $book_shelf;

	public function __construct(Lis $lis, FullShelfRepository $shelf) {

		$this->lis = $lis;
		$this->shelf = $shelf;
        
    }

    public function receiveBarcode(Request $request)
    {
        $barcode = $request->barcode;

        // Identify request is coming from this redirect
        $pst = 1;

        
        return redirect()->action('FullShelvesController@stackBase',['barcode'=>$barcode,'pst'=>1]);
    }

    public function processBarcode($barcode)
    {
        // See if it's a valid barcode


        // See if there are any alerts


        // See if it's a new book or an existing one


        // If it's an existing book, see if it's the book to move


        return redirect()->action('FullShelvesController@stackBase',['barcode'=>$barcode]);
    }

    public function makeCorrection()
    {
        
    }

    public function bookInfo($barcode)

    {
        $binfo = FullShelf::bookInfo($barcode);

        return $binfo;
    }

    

    
    public function insertNewBook($barcode,$book_count)
    {
        $nid = \DB::table('full_shelves')->select('id')->where('user_id', \Auth::id())->count();
        $books = FullShelf::getOrder(Auth::id()); 

        //get response from gulp as array

			//$response = GulpTest::makeResponse($barcode);
			$response = $this->gulpResponse($barcode);
			//dd($response);
			// returns all info for book, false if none 
			//Also inserts info into item_statuses table
			$alt_info = $this->responseInfo($response, $barcode);

			$title = $this->title($response,$barcode);
			$callnum = $this->call_num($response,$barcode);
			$current_location = $this->current_location($response,$barcode);
			$home_location = $this->home_location($response,$barcode);
         
        if($book_count>0)
        {
            // get collection of user's books in correct sort order
            $books = FullShelf::getOrder(\Auth::id());
            
            //Get correct position of this book in array of scanned books
           $gpos = App\FullShelf::bookPosition($books,$barcode);
        

       }

            else

                { $gpos = 1; } // It's the first book 

               
			$full = new FullShelf;

			$full->id = $nid+1;
			$full->user_id = \Auth::id();
			$full->barcode = $barcode;
			$full->title = $title;
			$full->callno = $callnum;
			$full->position = $nid+1;
			$full->cposition = $gpos;

			$full->save();

		//increment cposition of every cposition >= this book's cposition

        FullShelf::where('barcode', '!=', $barcode)->where('cposition','>=', $gpos)
        ->where('user_id', \Auth::id())->increment('cposition', 1);
    }

    

    public function stackBase($barcode=0,$pst=1)
    {
        //dd($barcode);
        
        if($pst == 0) 
        {
            // page was reloaded - return to page without doing anything
            return redirect()->route('stack-view',['barcode'=>$barcode,'pst'=>0]);
        }

        // Tests, 
        $corr = $this->shelf->isCorrection($barcode);

        //dd($corr);

        // See if there are books in the table
        $book_count = $this->shelf->book_count();

        if($barcode == 0 AND $book_count == 0)
        {
            $empty = true;

            return view('fullshelf.stack_view', compact('empty'));

        }
        else
        {
            $empty = false;
            $ordered_books = FullShelf::where('user_id', Auth::id())->orderBy('position')->get();
        }

        // Find out if book is on the shelf
        $check_book = FullShelf::getBooks()->where('barcode',$barcode)->count();

        if($check_book == 0 AND $barcode != 0)
		{
            //check for alerts
            //get response from gulp as array

			//$response = GulpTest::makeResponse($barcode);
			$response = $this->gulpResponse($barcode);
			//dd($response);
			// returns all info for book, false if none 
			//Also inserts info into item_statuses table
            $alt_info = $this->responseInfo($response, $barcode);

            $title = $alt_info[0];
            $callnum = $alt_info[1];

            // Go to method insertNewBook
            $insert = $this->shelf->insert_key($callnum,$barcode,$title);

            $ordered_books = FullShelf::where('user_id', Auth::id())->orderBy('position')->get();
        
        }

      

        // Is this is a spacebar keystroke? (this check should go above to avoid running code unnecessarily)

		if($barcode === '12345') // User signals the end of a shelf

		{
    		// set the last book scanned as a shelfend
			$this->shelfEnd($this->greatestPosition());

            return redirect()->action('stack-view');
            exit;
		}

        //Is this is an alt keystroke
        if($barcode === '123') // User signals the end of a section

		{

			$this->sectionEnd($this->greatestPosition());

            return redirect()->route('stack-view');
            exit;
		}


        $lbook_pos = FullShelf::where('user_id',Auth::id())->orderByDesc('position')->take(1)->pluck('position')[0];

        $bookinfo = $this->bookInfo($barcode);

        $shelf_end = Shelfend::where('user_id',Auth::id())->pluck('position')->toArray();
       
        if($shelf_end)

        {
            array_push($shelf_end, $lbook_pos);

            //dd($shelf_end);

        
            // The position of each section end
            $section_end = Section::where('user_id',Auth::id())->pluck('position')->toArray();
    
            array_push($section_end, $lbook_pos);
    
            // The total number of section ends
            $section_count = count($section_end);
          
            // Count of the shelf ends
            $shelf_count = count($shelf_end);
    
         
            $stack_end = $lbook_pos;
        }
        else
        {
            $shelf_end = [$lbook_pos];
            $section_end =  [$lbook_pos];
            $section_count = 1;
            $shelf_count = 1;
            $stack_end = $lbook_pos;
        }

        $width = $section_count*32;

        $base = [
            'empty' => $empty,
            'book_count' => $book_count,
            'section_end' => $section_end,
            'shelf_end' => $shelf_end,
            'ordered_books' => $ordered_books,
            'stack_end' => $stack_end,
            'lbook_pos' => $lbook_pos,
            'empty' => $empty,
            'section_count' => $section_count,
            'width' => $width
        ];

        //dd($base);
        
        $errors = $this->shelf->has_errors(); 

    
        if( $errors > 0 )

        {
            return $this->stackErrors($base);
            
        
        }   
        
        else

        {
            return $this->stackClean($base);
        }

        
    }

    public function stackClean($base)
    {
        $pos= 0; // Position of book being moved
        $mpos = 0; // Position to move book to
       
        $mbar = 0;
        $dbar = 0;
        $move_binfo = 0;
        $dtitle = 0;
        $dcall = 0;
        $corrections = 0;
        $left = 0;
        $right = 0;

        $green = 0;
        
        // Shelf number of book to be moved
        $shelf_number = 0; 
        $section_number = 0; 

        $dest_shelf = 0;
        $dest_section = 0;


        // Shelf number of destination
        $movepos = 0; 

      
        return view('fullshelf.stack_view', compact('pos','mpos','mbar','dbar','dtitle','dcall','corrections','left','right','green',
                                                    'shelf_number','section_number','dest_shelf','movepos'))->with($base);
    }


    public function stackErrors($base)
    {
    
            $pos= $this->shelf->move_position(); // Position of book being moved
            $mpos = $this->shelf->mpos(); // Position to move book to
           
            $mbar = $this->shelf->next_move()->barcode;
            $dbar = $this->shelf->next_move()->barcode;
            $move_binfo = $this->shelf->book_info($dbar);
            $dtitle = $move_binfo[0]->title;
            $dcall = $move_binfo[0]->callno;
            $corrections = $this->shelf->count_moves();
            $left = App\FullShelf::bLeft($mpos,$pos);
            $right = App\FullShelf::bRight($mpos,$pos);

            if($pos<$mpos) { $green = $mpos; }
            if($pos>$mpos) { $green = $mpos-1; }
            if($pos==$mpos) { $green = $mpos; }

            // if($pos<$mpos) { $green = $mpos; }
            // if($pos>$mpos) { $green = $mpos-1; }
            // if($pos==$mpos) { $green = $mpos; }
            
            // Shelf number of book to be moved
            $shelf_number = App\FullShelf::shelf_number($pos); 
            $section_number = App\FullShelf::section_number($pos); 

            $dest_shelf = App\FullShelf::shelf_number($mpos);
            $dest_section = App\FullShelf::section_number($mpos);


            // Shelf number of destination
            $movepos = App\FullShelf::shelf_number($mpos); 

          
            return view('fullshelf.stack_view', compact('pos','mpos','mbar','dbar','dtitle','dcall','corrections','left','right','green',
                                                        'shelf_number','section_number','dest_shelf','movepos'))->with($base);
    }

    public function show($item_alert=0,$barcode=0,$location=0,$title=0)
    {
        
        $errors = $this->shelf->has_errors(); 

        if($errors>0)
        { 
            $pos= $this->shelf->move_position(); // Position of book being moved
            $mpos = $this->shelf->mpos(); // Position to move book to
           
            $mbar = $this->shelf->next_move()->barcode;
            $dbar = $this->shelf->next_move()->barcode;
            $move_binfo = $this->shelf->book_info($dbar);
            $dtitle = $move_binfo[0]->title;
            $dcall = $move_binfo[0]->callno;
            $corrections = $this->shelf->count_moves();
            $left = App\FullShelf::bLeft($mpos,$pos);
            $right = App\FullShelf::bRight($mpos,$pos);

            if($pos<$mpos) { $green = $mpos; }
            if($pos>$mpos) { $green = $mpos-1; }
            if($pos==$mpos) { $green = $mpos; }

            


            
            // Shelf number of book to be moved
            $shelf_number = App\FullShelf::shelf_number($pos); 
            $section_number = App\FullShelf::section_number($pos); 

            $dest_shelf = App\FullShelf::shelf_number($mpos);
            $dest_section = App\FullShelf::section_number($mpos);


            // Shelf number of destination
            $movepos = App\FullShelf::shelf_number($mpos); 
            

        }
        else

        { 
            $mpos = 'none';
            $mbar = 0; // Barcode of book to be moved
            $dbar = 0; 
            $movepos = 0;
            $maxpos = 0;
            $section_number = 0;
            $shelf_number = 0;
            $con = 0;
            $green = 'none';
            $corrections = $this->shelf->count_moves();
            $left = 0;
            $right = 0;
            $pos= 0; // Position of book being moved
            $dtitle = 0;
            $dcall = 0;
            $dest_shelf = 0;
            $dest_section = 0;


            // Shelf number of destination
            $movepos = FullShelf::shelf_number($mpos);
        }
        
                
        $all_books = FullShelf::getBooks();
        
        $reverse_books = $all_books->reverse();

        // array of shelf end positions
        $bend = $this->shelf->bend();

        // array of section positions
        $send = $this->shelf->send();

        $ind = count($send);

        $bcount = count($all_books);

        // Shelf to scroll to on page load (last shelf)
        if($send)
        {
            $maxpos = FullShelf::shelf_number(max($send));
        }

        else
        {
          $maxpos =   0;
      }

       
    
      return view('fullshelf.show', compact('all_books','bcount','bend','ind','mpos','mbar','green',
        'errors','corrections','dbar','left','right','pos','dtitle','dcall','send','shelf_number','movepos','maxpos',
        'item_alert','barcode','location','title','reverse_books','section_number','dest_shelf','dest_section'));

  }

    public function new_shelf(Request $request)
    {
        
        $this->shelfEnd($this->greatestPosition());
    }

    public function new_section(Request $request)

    {

        $this->sectionEnd($this->greatestPosition());
    }

    public function shelf_size()

    {

    }

    public function delete_book($barcode)

    {
        
        $book_info = $this->shelf->book_info($barcode);

        return view('fullshelf.delete_book', compact('book_info','barcode'));
    }

    public function book_drop(Request $request)

    {
        $barcode = $request['barcode'];
        \DB::table('full_shelves')->where('user_id', '=', \Auth::id())->where('barcode', $barcode)->delete();
        \DB::table('full_sortkeys')->where('user_id', '=', \Auth::id())->where('barcode', $barcode)->delete();

        return redirect()->action('FullShelvesController@show');
    }

    

  public function store(Request $request)
    {
        $barcode = $request->barcode;

        $this->stow($barcode,$request);

        return redirect()->action('FullShelvesController@show');
    }

  public function add_section(Request $request)
  {

    $position = $request->position;

    $section = new Section;
    $section->user_id = \Auth::id();
    $section->position = $position;
    $section->current = 1;

    $section->save();

    return redirect()->route('fullshow');
}

public function add_shelf(Request $request)
{
    $position = $request->position;

    $shelf = new Shelfend;
    $shelf->user_id = \Auth::id();
    $shelf->position = $position;
    $shelf->current = 1;

    $shelf->save();

    return redirect()->route('fullshow');

}


    public function fstruncate()
    {
        \DB::table('full_shelves')->where('user_id', '=', \Auth::id())->delete();
        \DB::table('full_sortkeys')->where('user_id', '=', \Auth::id())->delete();
        \DB::table('full_moves')->where('user_id', '=', \Auth::id())->delete();
        \DB::table('full_listers')->where('user_id', '=', \Auth::id())->delete();
        \DB::table('full_reports')->where('user_id', '=', \Auth::id())->delete();
        \DB::table('sections')->where('user_id', '=', \Auth::id())->delete();
        \DB::table('shelfends')->where('user_id', '=', \Auth::id())->delete();

        $err = "Table is empty.";

        return redirect()->route('stack-view')->with('message', $err);

    }

	public function item_alerts($alert,$title)

	{

		return view('fullshelf.partials._item_alerts', compact('alert','title'));
	}

	public function barcodeNumeric($barcode)
	{
		$status = 1; 

		if(!is_numeric($barcode))

			{ $status = 0; }

		return $status;
	}

	public function barcodeLength($barcode)
	{
		$status = 1; 

		$chars = strlen($barcode);

		if($chars != 12 AND $chars != 14)

			{ $status = 0; }

		return $status;
	}

	public function barcodeEmpty($barcode)

	{
		$status = 1;

		if(empty($barcode))

			{ $status = 0; }

		return $status;
	}

	public function shelfEnd($barcode)

	{
		$barcode = FullShelf::where('user_id', Auth::user()->id)->orderByDesc('position')->take(1)->pluck('barcode')[0];

				$send = new Shelfend;

				$send->user_id = \Auth::id();
				$send->position = $this->shelf->book_count();

				$send->save();
			

			return $send;
	}

	public function sectionEnd($barcode)

	{
            // Get barcode of last book on shelf
			$barcode = FullShelf::where('user_id', Auth::user()->id)->orderByDesc('position')->take(1)->pluck('barcode')[0];

				$send = new Section;

				$send->user_id = \Auth::id();
				$send->position = $this->shelf->book_count();

				$send->save();
			
			return $send;
	}

	public function greatestPosition()
	{
		return $this->shelf->book_count();

	}

    public function webservice_response($barcode)

    {

        return $response;
    }

    public function webservice_error()

    {

        return redirect()->route('fullshelf')->withError($err);
    }

    public function shelve_book($nid,$barcode,$title,$callnum,$gpos)

    {
        $full = new FullShelf;

        $full->id = $nid+1;
        $full->user_id = \Auth::id();
        $full->barcode = $barcode;
        $full->title = $title;
        $full->callno = $callnum;
        $full->position = $nid+1;
        $full->cposition = $gpos;

        return $full->save();
    }

    public function increment_cpositions($gpos)
    {
        // Adjust cpositions in shelf after new book is inserted
        $inc = FullShelf::where('barcode', '!=', $barcode)->where('cposition','>=', $gpos)
        ->where('user_id', \Auth::id())->increment('cposition', 1);

        return $inc;
    }


    public function current_lis(FullShelf $fshelf)
    {
        $gcpositions = FullShelf::getPositions();  

        $lis = $this->lis->seq($gcpositions);

        return $lis;
    }

	public function stow($barcode,$request)

	{
       
		// User signals the end of a shelf
		if($barcode === '12345')

		{
    		// set the last book scanned as a shelfend
			$this->shelfEnd($this->greatestPosition());

			return redirect()->action('FullShelvesController@show');
		}

		if($barcode === '123')

		{

			$this->sectionEnd($this->greatestPosition());

			return redirect()->route('fullshelf');
		}

        $bcount = $this->shelf->book_count();
        $ind = $this->shelf->count_sections();

        // Probably will delete below in new gui
        if($ind === 0 AND $bcount == 0)

        {
            // This is the first book scanned
            //Insert it as a section beginning and a shelfend
            // Need to check to make sure no errors

            $send = new Section;

            $send->user_id = \Auth::id();
            $send->position = 1;

            $send->save();

            $fend = new ShelfEnd;

            $fend->user_id = \Auth::id();
            $fend->position = 1;

            $fend->save();

        }

    	// See if this book is in table
		$check_book = FullShelf::getBooks()->where('barcode',$barcode)->count();

		if($check_book === 0)
		{
    		//get response from gulp as array

			//$response = GulpTest::makeResponse($barcode);
			$response = $this->gulpResponse($barcode);
			//dd($response);
			// returns all info for book, false if none 
			//Also inserts info into item_statuses table
			$alt_info = $this->responseInfo($response, $barcode);
            
			
			//if no info returned, go back to page with alert
			if($alt_info == false) 

			{

				if($request->ajax()){ 
                    //dd($alt_info);
					return response()->json(['alert'=> 'EMPTY_RESPONSE','title'=>'UNKNOWN']);

				}
                //dd($alt_info);
				return redirect()->route('fullshow',['item_alert' => 'EMPTY_RESPONSE','title'=>'UNKNOWN']);
            

			}



			$title = $this->title($response,$barcode);
			$callnum = $this->call_num($response,$barcode);
			$current_location = $this->current_location($response,$barcode);
			$home_location = $this->home_location($response,$barcode);


			// Returns 1 if meets alert criteria
			$alert = GulpTest::alertTest($current_location);

			// Returns 1 if on hold
			$onhold = GulpTest::holdTest($current_location);

			$stitle = str_limit($title,15);

			$message = "$stitle is $current_location. To continue without placing the book on the shelf, scan the next item. To place the book on the shelf, rescan the barcode.";

			if($alert === 1 OR $onhold === 1)

			{
                //dd($current_location);
    		
				$check_shelf = Report::checkFshelf($barcode);



     		// If item in report table but not yet updated
				if($check_shelf === 0)
				{
					\DB::table('full_reports')
                    ->where('user_id', \Auth::id())
                    ->where('barcode', $barcode)
                    ->where('title', $title)
                    ->update(['shelf' => 1]);

                    if($this->shelf->check_preport($barcode) === 0)
                    {
                        $upreport = $this->shelf->insert_preport($barcode,$title,$callnum,$current_location);
                    }
                    

                }

   elseif($check_shelf === 3) // Item not yet in reports table
   {

   	$set_alert = GulpTest::getAlert($title,$current_location);

   	$save_report = FullShelf::storeReport($barcode,$title,$callnum,$current_location);

   	if($onhold === 0)
   	{
   		if($request->ajax()){
            //dd($current_location);
   			return response()->json(['alert'=> $current_location,'title'=>$title]);
   		}

   		return redirect()->route('fullshow', ['item_alert' => $current_location,'barcode' => $barcode,
            'location' => $current_location, 'title' => $title]);
   	}
   	else
   	{
   		if($request->ajax()){ 
   			return response()->json(['alert'=> 'ONHOLD','title'=>$title]);
            //dd($current_location);
   		}

        return redirect()->route('fullshow', ['item_alert' => $current_location,'barcode' => $barcode,
            'location' => $current_location, 'title' => $title]);  

   	}
   }
}

			
        //Send callnumber to makeMask

            $masks = FullShelf::makeMask($callnum);

            $amask = $masks[0];
            $smask = $masks[1];
            $calla = $masks[2];

        // Send masks, etc to pMask
        // $amask,$smask,$callno,$calla
            $pre_sort_key = FullShelf::pMask($amask,$smask,$callnum,$calla);

            $masks = $this->shelf->insertFullKey($amask,$smask,$callnum,$calla,$barcode);

            ###########################################
            //Gets array of books in order by sort key

		    $books = FullShelf::getOrder(\Auth::id()); 

			###########################################		

		 $nid = \DB::table('full_shelves')->select('id')->where('user_id', \Auth::id())->count();
         
        if($nid>0)
        {

         #####################################################
            //Get correct position of this book based on sort order
           $gpos = App\FullShelf::bookPosition($books,$barcode);
         #####################################################

       }

            else

                { $gpos = 1; } // It's the first book 

               
			$full = new FullShelf;

			$full->id = $nid+1;
			$full->user_id = \Auth::id();
			$full->barcode = $barcode;
			$full->title = $title;
			$full->callno = $callnum;
			$full->position = $nid+1;
			$full->cposition = $gpos;

			$full->save();

			//increment cposition of every cposition >= this book's cposition

        FullShelf::where('barcode', '!=', $barcode)->where('cposition','>=', $gpos)
        ->where('user_id', \Auth::id())->increment('cposition', 1);


		$errors = FullShelf::countErrors();
        //dd($errors);

		// if($errors == 0) { // Delete from moves
  //           FullMove('moves')->where('user_id', '=', \Auth::id())->delete(); 
  //           FullLister('listers')->where('user_id', '=', \Auth::id())->delete(); 
  //       }

        if($errors>0) 

        {


                // Delete from moves
            FullMove::where('user_id', '=', \Auth::id())->delete();

                // get collection of all correct positions in order of occurrence on shelf
                $gcpositions = FullShelf::getPositions();  
                //dd($gcpositions);
                //$lis = $this->lis->seq($gcpositions); 
                
                $cgcp = count($gcpositions);
                $lis = $this->lis->LongestIncreasingSubsequence($gcpositions,$cgcp);

                

                        FullLister::where('user_id', '=', \Auth::id())->delete();

                        $linfo = FullShelf::lBooks($lis);

                        //dd($linfo);

                        foreach($linfo as  $l)
                        {
                            //$linfo = \App\Sort::lisBooks($l);

                            $inlist[] = array('user_id' => \Auth::id(), 'barcode' => $l->barcode, 'position' => $l->position,
                            'cposition' => $l->cposition);

                        }

                        \DB::table('full_listers')->insert($inlist);

                            $moves = array_diff($gcpositions,$lis); // Get positions of books not in lis

                            $moves = array_values($moves); // reset keys to start at 0

                            

                            $move_bars = \DB::table('full_shelves')
                            ->select('barcode','position','cposition')
                            ->whereIn('cposition', $moves)
                            ->where('user_id', '=', \Auth::id())
                            ->get();

                            foreach($move_bars as $m)
                            {
                                $mbs[] = array('user_id' => \Auth::id(), 'barcode' => $m->barcode, 'position' => $m->position,
                                'cposition' => $m->cposition);
                            }

                            \DB::table('full_moves')->insert($mbs);

                        }

                       } elseif ($check_book == 1) { // A book was scanned that is already in the table

            $errors = FullShelf::countErrors();

            if($errors>0) {

                $moves = FullShelf::getMoves();
                //dd($moves);
                $tmove = $moves[0]->barcode;

                // Insert book into shelf_errors table
                $shelfee = ShelfError::insertError($barcode);

                if($tmove != $barcode)

                {
                    $binfo = FullShelf::bookInfo($tmove);

                    $dtitle = $binfo[0]->title;
                    // You re-scanned a book but there are no errors
                    $err = "You re-scanned the wrong book. Re-scan $dtitle.";

                    \Session::put('message', $err); 

                    return redirect()->action('FullShelvesController@show');

                    return redirect()->route('fullshow');

                }

                if($tmove == $barcode) 

                {      
                    $dbar = FullShelf::nextMove()->barcode;

                    $pos = FullShelf::where('barcode', $barcode)->where('user_id', \Auth::id())->pluck('position')[0];

                    $binfo = FullShelf::bookInfo($dbar);

                    $dtitle = $binfo[0]->title;

                    $dcall = $binfo[0]->callno;

                //$cpos = $binfo[0]->cposition;

                // $ferror = \App\Sort::firstError();

                    $gcpositions = FullShelf::getPositions();
                    $plist = FullShelf::listersContent();

                    $mov = FullShelf::moveCpos();

                    $mpos = FullShelf::mPos($plist,$mov,$gcpositions);

                    $cpos = $mpos;

                if($pos > $cpos) // Moving left

                {
                    Fullshelf::where('position', '>=', $cpos)->where('user_id', \Auth::id())
                    ->where('position', '<', $pos)->increment('position',1);

                    FullShelf::where('user_id', \Auth::id())
                    ->where('barcode', $dbar)
                    ->update(['position' => $cpos]);

                }

                if($pos < $cpos) // Moving right

                {
                    FullShelf::where('position', '>', $pos)->where('user_id', \Auth::id())
                    ->where('position', '<=', $cpos)->decrement('position', 1);

                    FullShelf::where('user_id', \Auth::id())
                    ->where('barcode', $dbar)
                    ->update(['position' => $cpos]);
                }




                FullMove::where('user_id', \Auth::id())
                ->where('barcode', $dbar)
                    //->take(1)
                ->update(['moved' => 1]);

                $errors = Fullshelf::countErrors();

                if($errors == 0) { // Delete user's entries from moves and listers
                FullMove::where('user_id', '=', \Auth::id())->delete(); 
                FullLister::where('user_id', '=', \Auth::id())->delete(); 

                $current_shelf = $this->shelf->get_shelf_number($pos); // Shelf of book being moved
                $destination_shelf = $this->shelf->get_shelf_number($mpos); // Shelf of destination
                $current_section = $this->shelf->get_shelf_number($pos); // Section of book being moved
                $destination_section = $this->shelf->get_shelf_number($mpos); // Section of destination
            
                if($current_shelf != $destination_shelf)

            {
                // Find out if book is moving left or right

                if($mpos > $pos) // Book is moving right

                {
                    $this->shelf->update_shelfend_right_move($pos,$mpos);
                }

                else

                {
                    // Book is moving left
                    $this->shelf->update_shelfend_left_move($pos,$mpos);
                }

            }

            if($current_section != $destination_section)

            {
                // Find out if book is moving left or right
                if($mpos > $pos) // Book is moving right

                {
                    
                }

                else

                {
                    // Book is moving left
                }

            }


                }

            if($errors >0) {
                     // Check database to see if there are move_bars

                $moves = FullShelf::getMoves();

                if(!$moves->first())

                {
                    FullMove::where('user_id', '=', \Auth::id())->delete(); 
                    FullLister::where('user_id', '=', \Auth::id())->delete();

                //$ferror = \App\Sort::firstError();
                $gcpositions = FullShelf::getPositions(); // get collection of all positions 

                $lis = $this->lis->seq($gcpositions); 

                $linfo = FullShelf::lBooks($lis);

                        foreach($linfo as  $l)
                        {
                            

                            $inlist[] = array('user_id' => \Auth::id(), 'barcode' => $linfo->barcode, 'position' => $linfo->position,
                            'cposition' => $linfo->cposition);

                        }

                        \DB::table('listers')->insert($inlist);


                            $moves = array_diff($gcpositions,$lis); // Get positions of books not in lis
                            $moves = array_values($moves); // reset keys to start at 0

                           $move_bars = \DB::table('full_shelves')
                            ->select('barcode','position','cposition','title')
                            ->whereIn('cposition', $moves)
                            ->where('user_id', '=', \Auth::id())
                            ->get();

                            foreach($move_bars as $m)
                            {
                                $mbs[] = array('user_id' => \Auth::id(), 'barcode' => $m->barcode, 'position' => $m->position,
                                'cposition' => $m->cposition);
                            }

                            \DB::table('moves')->insert($mbs);

                    } 

                }
                return redirect()->action('FullShelvesController@show'); 

            }

                } // End of error condition

                else

                {
                    // You re-scanned a book but there are no errors
                    $err = "You re-scanned a book but there are no errors. Scan the next book.";

                    \Session::put('message', $err); 

                    return redirect()->action('FullShelvesController@show');
                }  

            } // End of $check_book==1 condition




}


}//End of class