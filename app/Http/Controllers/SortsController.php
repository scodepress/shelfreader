<?php

namespace App\Http\Controllers;

use App\Models\UnprocessedCallnumbers;
use App\CustomClasses\Node;
use App\Jobs\InsertItemAlert;
use App\Models\Callnumber;
use App\Models\GulpTest;
use App\Models\Lis;
use App\Models\Lister;
use App\Models\Move;
use App\Models\Report;
use App\Models\BadBarcode;
use App\Models\ShelfError;
use App\Models\Sort;
use App\Models\User;
use App\Models\Subsequence;
use App\Repositories\Contracts\SortsRepository;
use App\Traits\LibraryName;
use App\Traits\MasterKeyTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;


class SortsController extends Controller
{	
	use LibraryName;
	use MasterKeyTrait;

	protected $lis;
	protected $sorts;

	public function __construct(Subsequence $lis, SortsRepository $sorts, User $user) {
		$this->lis = $lis;
		$this->sorts = $sorts;

	}

	public function scroll()
	{

		return view('sorts.scroll');
	}

	public function faq()

	{

		return view('sorts.faq');
	}

	public function test_order($callno)
	{    $barcode = '000065142622';
	$subb = $this->sorts->insert_key($callno,$barcode);

	$subclass = Callnumber::make_key($callno);
	//$amask = Callnumber::aMask($callno);

	return view('sorts.test_order', compact('subclass'));
	}

	public function truncate()
	{
		// Insert Into Main Table
		//$saved = Sort::saveTable();
		\DB::table('sorts')->where('user_id', '=', \Auth::id())->delete();
		\DB::table('sortkeys')->where('user_id', '=', \Auth::id())->delete();
		\DB::table('moves')->where('user_id', '=', \Auth::id())->delete();
		\DB::table('subsequences')->where('user_id', '=', \Auth::id())->delete();
		\DB::table('item_statuses')->where('user_id', '=', \Auth::id())->delete();
		\DB::table('reports')->where('user_id', '=', \Auth::id())->delete();
		\DB::table('sort_errors')->update(['errors' => 0]);


		return redirect()->route('sorting');
	}

	public function ftruncate()
	{
		\DB::table('sorts')->where('user_id', '=', \Auth::id())->delete();
		\DB::table('sortkeys')->where('user_id', '=', \Auth::id())->delete();
		\DB::table('moves')->where('user_id', '=', \Auth::id())->delete();
		\DB::table('listers')->where('user_id', '=', \Auth::id())->delete();
		\DB::table('shelfends')->where('user_id', '=', \Auth::id())->delete();

		$err = "Table is empty.";

		return redirect()->route('fshow')->with('message', $err);
	}

	public function item_alerts($alert,$title)

	{

		return view('sorts.item_alerts', compact('alert','title'));
	}

	public function barcode_check_ajax($status,$barcode)

	{
		//Throw any alerts barcode sent by ajax request causes
		if($status === 1)

		{
			return response()->json(['alert'=> 'NON_NUMERIC','title'=>$barcode]);
		}

		if($status === 2)

		{

			return response()->json(['alert'=> 'LENGTH','title'=>$barcode]);
		}

		if($status === 3)

		{

			return response()->json(['alert'=> 'EMPTY_BARCODE','title'=>$barcode]);

		}

		if($status === 0)

		{

			return null;

		}


	}



	public function barcode_check_najax($status)

	{
		//Throw any error barcode sent by nonajax request causes
		if($status === 1)

		{
			return redirect()->action('SortsController@show', ['barcode' => 0])->withError('Non-numeric barcode entered.');
		}

		if($status === 2)

		{

			return redirect()->action('SortsController@show', ['barcode' => 0])->withError('Barcode is incorrect length.');
		}

		if($status === 3)

		{

			return redirect()->action('SortsController@show', ['barcode' => 0])->withError('Empty barcode entered.');
		}

		if($this->barcodeLength($barcode) === 0)

		{

			return null;
		}



	}

	public function barcode_test($barcode)
	{
		$status = 0;

		if(!is_numeric($barcode))

		{ $status = 1; return $status; }


		$chars = strlen($barcode);

		if($chars != 12 AND $chars != 14)

		{ $status = 2; return $status; }


		if(empty($barcode))

		{ $status = 3; return $status; }

		return $status;


	}


	public function bad_barcode_test($barcode)
	{
		$status = 0;

		if(!is_numeric($barcode))

		{ $status = 1; return $status; }


		$chars = strlen($barcode);

		if($chars > 28)

		{ $status = 2; return $status; }


		if(empty($barcode))

		{ $status = 3; return $status; }

		return $status;


	}


	public function set_width(Request $request)

	{
		//Sets the width for the shelf from jquery width measurement

		\Session::forget('div_width');
		\Session::put('div_width', $request->width); 

	}

	public function item_info($response,$barcode)
	{

		$item_info = GulpTest::itemInfo($response,$barcode);

		if($item_info == false) 

		{

			return 0;

		}

		else

		{
			$title = $item_info[0];
			$callnum = $item_info[1];
			$home_location = $item_info[3];
			$current_location = $item_info[4];

			return array($title,$callnum,$current_location,$home_location);
		}


	}

	public function return_alert($alert)
	{
		return response()->json($alert);
	}

	public function onHold($barcode,$title,$current_location,$callnum)
	{
		$shelf = Report::checkShelf($barcode);

		if($shelf === 0)
		{
			\DB::table('reports')
				->where('user_id', \Auth::id())
				->where('barcode', $barcode)
				->where('title', $title)
				->update(['shelf' => 1]);

			//Insert sortkey into sortkey table and get the book's correct position
			$insert_key = $this->sorts->insert_key($callnum,$barcode,$title);

			return redirect()->action('SortsController@show', ['barcode' => 0]);

		}

		if($shelf === 3)
		{
			$save_report = Report::store($barcode,$title,$callnum,$current_location,0);

		}


		return $this->sorts->response_check_ajax('ONHOLD',$title); 

	}

	public function onAlert($barcode,$title,$current_location,$callnum,$request)
	{

		$shelf = Report::checkShelf($barcode);

		// Item in report table but not yet updated
		if($shelf === 0)
		{
			// this update means book will remain on shelf
			$this->sorts->update_report($barcode,$title);

			//Insert sortkey into sortkey table and get the book's correct position
			$pre_sort_key = Callnumber::make_key($callnum);
			$insert_key = $this->sorts->insert_key($callnum,$barcode,$title,$pre_sort_key );
		}

		elseif($shelf === 3) // Item not yet in reports table
		{

			$save_report = Report::store($barcode,$title,$callnum,$current_location,0);

			if($request->ajax())
			{ 
				// return $this->sorts->response_check_ajax($current_location,$title); 

				$book_name = \Illuminate\Support\Str::limit($title,15);
				$title = "$book_name is designated $current_location by the system.";

				return response()->json(['alert'=> 'InventoryAlert','title'=>$title]);


			}

			else

			{

			}
		}
	}

	public function store(Request $request)
	{


		// Forget error session - may not need any more
		\Session::forget('message'); 


		$barcode = $request['barcode']; 

		// Check barcode - If error send back to page with alert

		$bcheck = $this->barcode_test($barcode);

		if($bcheck > 0)

		{

			$bad_bcheck = $this->bad_barcode_test($barcode);

			if($bad_bcheck === 0) {
				$barcode = $barcode;

				$bb = new BadBarcode;

				$bb->user_id = \Auth::id();
				$bb->barcode = $barcode;
				$bb->save();

				return $this->barcode_check_ajax($bcheck,$barcode);
			}
			if($bad_bcheck === 1) {
				$barcode = 'NON_NUMERIC';
				$bb = new BadBarcode;

				$bb->user_id = \Auth::id();
				$bb->barcode = $barcode;
				$bb->save();
				return $this->barcode_check_ajax($bcheck,$barcode);
			}
			if($bad_bcheck === 2) {
				$barcode = 'Too_Long';
				$bb = new BadBarcode;

				$bb->user_id = \Auth::id();
				$bb->barcode = $barcode;
				$bb->save();
				return $this->barcode_check_ajax($bcheck,$barcode);
			}
			if($bad_bcheck === 3) {
				$barcode = 'Empty';
				$bb = new BadBarcode;

				$bb->user_id = \Auth::id();
				$bb->barcode = $barcode;
				$bb->save();
				return $this->barcode_check_ajax($bcheck,$barcode);
			}


			

		}

		// If we've got this far barcode is ok


		// make entry for user in usage table

		$this->sorts->scan_count($barcode);


		$corr = $this->sorts->isCorrection($barcode);

		if( $corr == 0 )
		{
			// Scan is not a correction, continue processing api response


			$response = GulpTest::makeResponse($barcode);


			$item_info = $this->item_info($response,$barcode);

			if($item_info == 0)

			{
				return response()->json(['alert'=> 'EMPTY_RESPONSE','title'=>'UNKNOWN']);
			}

			else

			{
				$title = $item_info[0];
				$callnum = $item_info[1];
				$current_location = $item_info[2];
				$home_location = $item_info[3];
			}


			// Returns 1 if on hold
			$onhold = GulpTest::holdTest($current_location);

			if($onhold === 1)

			{
				$this->dispatch(new InsertItemAlert($barcode,$callnum,$title,$current_location,$home_location));
				return $this->onHold($barcode,$title,$current_location,$callnum);
			}


			$alert = GulpTest::alertTest($current_location);

			if($alert === 1)

			{

				return $this->onAlert($barcode,$title,$current_location,$callnum,$request);


			}


			$pre_sort_key = Callnumber::make_key($callnum);

			//Insert sortkey into sortkey table if not in master_keys table and get the book's correct position
			try {

				$insert_key = $this->sorts->insert_key($callnum,$barcode,$title,$pre_sort_key);

			} catch(\Illuminate\Database\QueryException $ex) {

				$userIDN = \Auth::id();
				$data[] = array('user_id' => $userIDN, 'callnumber' => $callnum, 'barcode' => $barcode);

				$uc = new UnprocessedCallnumbers;

				$uc->user_id =  \Auth::id();
				$uc->callnumber = $callnum;
				$uc->barcode = $barcode;

				$uc->save();

				return response()->json(['alert'=> 'CALLNUMBERERROR','title'=>$callnum]);
			}





			// Send as flag for whether or not enter info in master_keys table
			if($this->userPrivs() === '2' || $this->userPrivs() === '1') {


				$master_key_count = $this->masterKeyCount($barcode,$this->userLibraryId());

				if($master_key_count === 0) {

					$user_id =  \Auth::id();
					$this->runMasterKeyJob($user_id,$callnum,$barcode,$title,$pre_sort_key,$this->userLibraryId());
				}

			}



		}

		elseif($corr == 1)
		{
			$title = "You re-scanned a book but there are no errors.";

			return $this->sorts->response_check_ajax('NoMshelf',$title); 
			// 	return response()->json(['alert'=> 'CALLNUMBERERROR','title'=>$callnum]);
		}

		elseif($corr == 2)
		{
			// Get title of nextMove
			$nbar = $this->sorts->next_move()->barcode;
			$title = Sort::where('barcode',$nbar)->pluck('title')[0];

			return $this->sorts->response_check_ajax('WrongBook',$title);
		}

		elseif($corr == 3)
		{
			return $this->sorts->response_check_ajax('ONHOLD','Sartorial');
		}

		return redirect()->action('SortsController@show');

	}  // End of store method

	###########################################################################################

	public function clear_demo()

	{
		\DB::table('demos')->update(['pulled' => 0]);

		\DB::table('sorts')->where('user_id', '=', \Auth::id())->delete();
		\DB::table('sortkeys')->where('user_id', '=', \Auth::id())->delete();
		\DB::table('moves')->where('user_id', '=', \Auth::id())->delete();
		\DB::table('listers')->where('user_id', '=', \Auth::id())->delete();
		\DB::table('item_statuses')->where('user_id', '=', \Auth::id())->delete();
		\DB::table('reports')->where('user_id', '=', \Auth::id())->delete();
		//\DB::table('mains')->where('user_id', '=', \Auth::id())->delete();

		return redirect()->action('SortsController@show', ['barcode' => 0]);
	}


	public function show($item_alert=0,$barcode=0,$location=0,$title=0)

	{

		if($item_alert == 'post')
		{
			$shelf = Report::checkShelf($barcode);
			if($shelf !=0) {$item_alert = false;}
		}

		$userPrivs = $this->userPrivs();

		$corrections = $this->sorts->countCorrections();
		if($corrections>0) {

			$move_bar = $this->sorts->next_move();
			$dbar = $move_bar->barcode;
		} else {
			$dbar = 0;
		}

		$mybar = Sort::lastBook();

		if($mybar->first()) {

			$mybar = $mybar[0]->barcode;


			$shelf = Sort::getShelfs(\Auth::id());

			$book_count = $shelf->count();

			$shelf_size = Sort::shelfSize();
			//dd($shelf_size);

			if($book_count > $shelf_size)
			{
				$tpix = ($book_count-$shelf_size)*61;

			}

			else

			{$tpix = 0;}

			$count_size = Sort::countSize();  

			$lib_name = "Penn State Libraries"; // This is from trait LibraryName

			return view('sorts.show', compact('mybar','shelf','shelf_size','count_size','lib_name','item_alert',
				'location','barcode','title','tpix','userPrivs','corrections','dbar')); 

		}

		else


		{
			$corrections = $this->sorts->countCorrections();
			$mybar = 0;
			//$main_table = Sort::getMain()->main_table;

			$shelf = Sort::getShelfs(\Auth::id());

			$book_count = $shelf->count();

			$shelf_size = Sort::shelfSize();

			if($book_count > $shelf_size)
			{
				$tpix = ($book_count-$shelf_size)*61;

			}

			else

			{$tpix = 0;}

			//$tend = ($book_count-$cpos)*61;

			$count_size = Sort::countSize();

			$lib_name = "Penn State Libraries"; // This is from trait LibraryName

			$dbar = 0;

			return view('sorts.show', compact('mybar','shelf','shelf_size','count_size','lib_name','item_alert','location','barcode','title','tpix','userPrivs','corrections','dbar')); 
		}


	}

	public function item_alert($barcode,$location,$title)
	{
		return view('sorts.item_alert', compact('barcode','location','title'));
	}


	public function show_table(Request $request, $barcode,$item_alert=0,$location=0,$title=0)

	{   
		$bcheck = $this->barcode_test($barcode);

		if($bcheck > 0)

		{
			exit;
		}

		// Bookshelf in current positions. Returns 'id','barcode','title','callno','position','cposition'
		$bookshelf = $this->sorts->bookShelf();

		// Are there books out of place
		$errors = $this->sorts->countErrors();

		$userPrivs = $this->userPrivs();

		if($item_alert == 'post')
		{
			$shelf = Report::checkShelf($barcode);
			if($shelf !=0) {$item_alert = false;}
		}

		if($errors > 0)

		{

			// How many moves to correct shelf
			$corrections = $this->sorts->countCorrections();


			// What is its position on the shelf
			$current_position = $this->sorts->move_position();

			//Where is it moving
			$destination_position = $this->sorts->mpos();

			//What's the barcode of the next book to move
			$move_bar = $this->sorts->next_move();
			$dbar = $move_bar->barcode;

			// See if this barcode is the one to move



			//What direction is it moving
			if($current_position > $destination_position) 

			{ 
				$direction = 'left'; 
			}

			else

			{
				$direction = 'right'; 
			}

			$move_length = abs($current_position-$destination_position);

			$mybar = $barcode;
			$last_scan = $bookshelf;

			$mc = Sort::getMc(); 

			$shelf_size = round($request->session()->get('div_width')/65);

			$books = count($bookshelf);

			if(count($bookshelf) > $shelf_size)
			{
				$tpix = ($books-$shelf_size)*61;

			}

			else

			{
				$tpix = 0;
			}

			$cpos = $destination_position;

			$jump = $move_length;
			if($direction == 'left') {  $jump="<$jump"; } else { $jump = "$jump>"; }
			$pos = $current_position;
			//dd($cpos);
			if( $pos>$cpos) { $con = 0; } 
			// Moving a book to the right of its current position
			if ($pos<$cpos) { $con = -1; }
			if ($pos==$cpos) { $con = 0; }



			$green_left = ($cpos-3)*61;

			$green_right = ($cpos-$shelf_size)*61;

			$blue_left = ($pos-3)*61;

			$blue_right = ($pos-$shelf_size)*61;
			$book_count = Sort::countBooks();

			$maxcp = Sort::where('user_id', \Auth::id())->max('cposition');
			$maxp = Sort::where('user_id', \Auth::id())->max('position');

			return view('sorts.show_table', 
				compact('mybar','last_scan','errors','dbar','mc','tpix','direction','cpos',
				'corrections','maxp','maxcp','green_left','green_right','blue_left',
				'blue_right','jump','con','userPrivs','item_alert','book_count'));

		} // End of errors = true condition

		#####################################################################################################

		// Bookshelf in current positions. Returns 'id','barcode','title','callno','position','cposition'
		$bookshelf = $this->sorts->bookShelf();

		$corrections = $this->sorts->countCorrections();

		//What's the barcode of the next book to move
		$move_bar = 0;
		$dbar = 0;

		// See if this barcode is the one to move

		// What is its position on the shelf
		$current_position = 0;

		//Where is it moving
		$destination_position = 0;

		//correct order of books including this one
		//$books = Sort::getOrder(\Auth::id());

		// Returns 'title','callno','barcode','cposition','position'
		$book_info = $this->sorts->book_info($barcode); 

		//Is the book in sorts table
		$check_book = $this->sorts->countBook($barcode);

		$shelf_size = Sort::shelfSize();

		$book_count = count($bookshelf); 

		$last_scan = $bookshelf; 


		$errors = Sort::countErrors();

		$mpos = "none";



		$book_count = Sort::countBooks();

		$dbar = 0;

		$con = 100; 
		$corrections = 0;


		//Accounts for change in shelf position of intervening books
		$mybar = $barcode;



		$direction = 'none';


		$shelf_size = round($request->session()->get('div_width')/65);



		if($book_count > $shelf_size)
		{
			$tpix = ($book_count-$shelf_size)*61;

		}

		else

		{
			$tpix = 0;
		}


		$green_left = 0;

		$green_right = 0;

		$blue_left = 0;

		$blue_right = 0;


		$maxcp = Sort::where('user_id', \Auth::id())->max('cposition');
		$maxp = Sort::where('user_id', \Auth::id())->max('position');

		if($book_count>1)
		{

			$mc = Sort::getMc(); //Greatest cposition

		}

		else

		{
			$mc = 1; //Greatest cposition

		}


		$jump = 0;
		$con = 100;
		$cpos = 0;



		return view('sorts.show_table', 
			compact('mybar','last_scan','errors','dbar','mc','tpix','direction','cpos','corrections','mpos','maxp','maxcp','green_left','green_right','blue_left','blue_right','jump','con',
			'userPrivs','item_alert','book_count'));
	}


	public function save_table(Request $request) 
	{

		$save = Sort::saveTable();

		$code = Sort::lastBook();
		$barcode = $code[0]->barcode;

		return redirect()->action('SortsController@show', ['barcode' => 0]);
	}

	public function highlight(Request $request)

	{

	}



	public function delete_book($barcode)

	{
		$book_info = Sort::bookInfo($barcode);

		return view('sorts.delete_book', compact('book_info','barcode'));
	}

	public function book_drop(Request $request)

	{
		$barcode = $request['barcode'];
		\DB::table('sorts')->where('user_id', '=', \Auth::id())->where('barcode', $barcode)->delete();
		\DB::table('sortkeys')->where('user_id', '=', \Auth::id())->where('barcode', $barcode)->delete();

		return redirect()->action('SortsController@show');
	}



	public function testkeys()
	{   

		return view('sorts.testkeys');

	}


	public function store_test_keys(Request $request) 

	{
		$callno = strtoupper($request['callno']);


		$this->sorts->insert_test_key($callno);


		return redirect()->action('SortsController@testkeys');

	}

	public function store_shelf_size(Request $request)

	{
		\DB::table('shelfsizes')->where('user_id', '=', \Auth::id())->delete();

		$shelf_size = $request['shelf_size'];

		$size = new Shelfsize;

		$size->shelf_size = $shelf_size;
		$size->user_id = \Auth::id();

		$size->save();

		//$barcode = Sort::lastBook()->pluck('barcode')[0];

		return redirect()->action('SortsController@show', ['barcode' => 0]);

	}

	public function truncate_test()

	{
		\DB::table('testkeys')->where('user_id', '=', \Auth::id())->delete();

		return redirect()->action('SortsController@testkeys');

	}

	public function video()

	{

		return view('sorts.video');
	}

	public function tutorial()

	{

		return view('sorts.tutorial');
	}







	public static function test()
	{

	}

}

