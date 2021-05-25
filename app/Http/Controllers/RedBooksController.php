<?php

namespace App\Http\Controllers;

use App\Demo;
use App\FullShelf;
use App\GulpTest;
use App\RedBook;
use App\FullLister;
use App\Traits\LibraryName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Repositories\Contracts\FullShelfRepository;
use App\Traits\SortTrait;
use App\CustomClasses\Node;
use App\Repositories\Contracts\SortsRepository;

class RedBooksController extends Controller
{
	use LibraryName;
	use SortTrait;

	protected $lis;
	protected $shelf;

	public function __construct(Node $lis, FullShelfRepository $shelf, SortsRepository $sorts) {

		$this->lis = $lis;
		$this->shelf = $shelf;
        $this->sorts = $sorts;

	}

	public function show($barcode=0)
	{

		
			//dd($barcode);
		// See if book is already in sortkey table
    	//$csort = \DB::table('full_sortkeys')->where('barcode',$barcode)->where('user_id',\Auth::id())->count();
		
		//$barcode = Demo::orderBy('position')->where('pulled',0)->take(1)->pluck('barcode')[0];

		$demo_barcode = $barcode;

		$response = $this->api_call($barcode);

		if($response->first() AND $barcode != 0)

			{	

				$book_info = $this->book_info($response,$barcode);

				$title = $book_info[0];
				$callno = $book_info[1];
				$titles = RedBook::titles($title);
				$barcodes = RedBook::barcodes($barcode);
				$positions = RedBook::positions();
				$cpositions = RedBook::cpositions($barcode,$callno);
				$call_numbers= RedBook::call_numbers($callno);

			}

			else

			{
				$titles = Redis::zRangeByScore('titles', 0, 90000000000);
				$barcodes = Redis::zRangeByScore('barcodes', 0, 90000000000);
				$positions = Redis::zRangeByScore('positions', 0, 90000000000);
				$cpositions = Redis::zRangeByScore('cpositions', 0, 90000000000);
				$call_numbers = Redis::zRangeByScore('call_numbers', 0, 90000000000);
			}

			//dd($cpositions);

			$ntitles = Redis::ZCOUNT('cpositions', 0, 90000000000);

			//$books = FullShelf::getOrder(\Auth::id());

			//$gpos = FullShelf::bookPosition($books,$barcode); 

			$ncpositions = Redis::ZCOUNT('cpositions', 0, 90000000000);

			//$match = RedBook::cpos_scan($gpos,$ncpositions);

			$npositions = Redis::ZCOUNT('positions', 0, 90000000000);
			$nbarcodes = Redis::ZCOUNT('barcodes', 0, 90000000000);
			$ncallnumbers = Redis::ZCOUNT('call_numbers', 0, 90000000000);

			$show = 1;
			


		return view('redbooks.show', compact('titles','barcodes','positions','cpositions','call_numbers','demo_barcode',
			'title','ntitles','ntitles','npositions','nbarcodes','ncallnumbers','ncpositions','show'));



	}

	public function clear_demo()

    {
        \DB::table('demos')->update(['pulled' => 0]);
		
		Redis::del('titles');
		Redis::del('barcodes');
		Redis::del('positions');
		Redis::del('cpositions');
		Redis::del('call_numbers');
		//\DB::table('full_sortkeys')->where('user_id', '=', \Auth::id())->delete();
        

        return redirect()->action('RedBooksController@show');
    }

	public function store(Request $request)

	{

		if($request->demo_barcode)
    {


        // Entering demo barcode
        $barcode = $request->demo_barcode;

         \DB::table('demos')
       ->where('barcode', $barcode)
       ->update(['pulled' => 1]);
       

    }

    elseif($request->barcode)
    {
    	$barcode = $request->barcode;
    	//dd($barcode);
    }

    else
    {
    	$barcode = 0;
    }

    return redirect()->action('RedBooksController@show', ['barcode' => $barcode]);

	} // End of store function



	public function api_call($barcode)

	{
		$response = GulpTest::makeResponse($barcode);
			
			//dd($response);
		
		return $response;

	}

	public function book_info($response, $barcode)

	{
		$book_info = GulpTest::itemInfo($response,$barcode);

		return $book_info;
	}


} // End of Controller
