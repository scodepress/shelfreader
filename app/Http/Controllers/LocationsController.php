<?php

namespace App\Http\Controllers;

use App;
use App\CustomClasses\Node;
use App\FullLister;
use App\FullMove;
use App\FullShelf;
use App\FullSortkey;
use App\GulpTest;
use App\Report;
use App\Repositories\Contracts\FullShelfRepository;
use App\Repositories\Contracts\SortsRepository;
use App\Repositories\Eloquent\Criteria\ByUser;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use App\Section;
use App\ShelfError;
use App\Shelfend;
use App\Traits\LibraryName;
use App\Traits\SortTrait;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Auth;
use App\Location;

class LocationsController extends Controller
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
    
    public function index()
    {
        
        return view('locations.index');
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

    public function show($barcode=0,$position=0)
    {
        
        $errors = $this->shelf->has_errors(); 
        	if($position == 0)
        	{
        		$mbar = 0;
        	}
        	else

        	{
        		$mbar = $barcode;
        	}

            $mpos = 'none';
            
            $movepos = 0;
            $maxpos = 0;
            $section_number = 0;
            $shelf_number = 0;

  

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
            $maxpos = App\FullShelf::shelf_number(max($send));
        }

        else
        {
          $maxpos =   0;
      }

       $corrections = 0;
       $left = 0;

      return view('locations.show', compact('all_books','bcount','bend','mpos','mbar','errors','corrections','dbar',
        'mvp','left','right','pos','dtitle','dcall','send','shelf_number','maxpos','barcode','title','tpix','reverse_books','section_number'));

  }

  public function store(Request $request)
  {
    $barcode = $request->barcode;

    if(is_numeric($barcode))
    {
      $barcode = $barcode;
    }
  	

  	return redirect()->action('LocationsController@prep', ['barcode'=>$barcode]);
  }

  public function prep($barcode)
  {
  
  	$check = Location::checkBook($barcode);


  	if($check > 0)

  	{
  		$position = Location::bookPosition($barcode);
  	}

  	else

  	{
  		$position = 0;
  	}


  	return redirect()->action('LocationsController@show', ['barcode'=>$barcode,'position'=>$position]);
  
  }
    
}
