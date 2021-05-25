<?php

namespace App\Repositories;
use Illuminate\Support\Facades\Auth;

use App\Models\Callnumber;
use App\Models\Subsequence;
use App\Models\Lis;
use App\Models\FullPreport;
use App\Models\FullReport;
use App\Models\FullShelf;
use App\Models\Preport;
use App\Models\Report;
use App\Repositories\Contracts\RepositoryInterface;
use App\Repositories\Criteria\CriteriaInterface;
use App\Repositories\Exceptions\NoEntityDefined;
use App\Models\Sort;
use App\Models\Testkey;
use App\Models\Sortkey;
use App\Models\FullSortkey;
use App\Models\Usage;
use App\Models\ShelfError;
use App\Jobs\InsertShelfCorrections;

use App\Traits\SortTrait;
use App\Traits\MasterKeyTrait;

abstract class RepositoryAbstract implements RepositoryInterface, CriteriaInterface
{
	protected $entity;
    protected $lis;


	public function __construct(Lis $lis)

	{
		$this->entity = $this->resolveEntity();

        // The array returned by Subsequence class
        $this->lis = $lis;

        // Set table names according to model
        $this->shelf = $this->shelf();
        $this->sortkey = $this->sortkey();
        $this->moves = $this->moves();
        $this->reports = $this->reports();
        $this->preports = $this->preports();
        $this->subsequences = $this->subsequences();
        $this->error_count = $this->error_count();
        $this->keyModel = $this->keyModel();
	}

	protected function resolveEntity()

	{
        // Determines which model the request is coming from


		if(!method_exists($this, 'entity'))

		{
			throw new NoEntityDefined();
			
		}


		return app()->make($this->entity());

	}

    public function shelf()
    {
        if($this->entity() === 'App\Models\FullShelf')

        {
            $shelf = 'full_shelves';
           
        }

        else
        {
            $shelf = 'sorts';
          
          
        }

        return $shelf;
    }

    public function keyModel()
    {
        if($this->entity() === 'App\Models\FullShelf')

        {
            return "App\Models\FullShelf";
           
        }

        else
        {
            return "App\Models\Sortkey";
          
          
        }

      
    }

    public function sortkey()
    {
        if($this->entity() === 'App\Models\FullShelf')

        {
            $sortkey = 'full_sortkeys';
            
        }

        else 

        {
          $sortkey = 'sortkeys';  
        }

        return $sortkey;
    }

        public function moves()
    {
        if($this->entity() === 'App\Models\FullShelf')

        {
            return 'full_moves';
            
        }

        else 

        {
          return 'moves';  
        }

        
    }

     public function subsequences()
    {
        if($this->entity() === 'App\Models\FullShelf')

        {
            return 'full_listers';
            
        }

        else 

        {
          return 'subsequences';  
        }

        
    }

    public function reports()
    {
        if($this->entity() === 'App\Models\FullShelf')

        {
            return 'full_reports';
            
        }

        else 

        {
          return 'reports';  
        }

        
    }

    public function preports()
    {
        if($this->entity() === 'App\Models\FullShelf')

        {
            return 'full_preports';
            
        }

        else 

        {
          return 'preports';  
        }

        
    }

    public function error_count()
    {
        if($this->entity() === 'App\Models\FullShelf')

        {
            return 'full_errors';
            
        }

        else 

        {
          return 'sort_errors';  
        }

        
    }


	public function all()
	
	{
		return $this->entity->get();
	}

	public function withCriteria(...$criteria)

	{
		$criteria = array_flatten($criteria);
		
		foreach ($criteria as $key => $criterion) {
			
			$this->entity = $criterion->apply($this->entity);
		}

		return $this;
	}


	  public function findWhere($column, $value)
    {
        return $this->entity->where($column, $value)->get();
    }

    public function findWhereFirst($column, $value)
    {
        $model = $this->entity->where($column, $value)->first();

        if (!$model) {
            throw (new ModelNotFoundException)->setModel(
                get_class($this->entity->getModel())
            );
        }

        return $model;
    }

    public function paginate($perPage = 10)
    {
        return $this->entity->paginate($perPage);
    }

    public function create(array $properties)
    {
        return $this->entity->create($properties);
    }

    public function update($id, array $properties)
    {
        return $this->find($id)->update($properties);
    }

    public function delete($id)
    {
        return $this->find($id)->delete();
    }

    
  	public function orderedShelf()

	{
       
		return \DB::table("$this->sortkeys as sk")
        ->join("$this->sorts as s",'s.barcode', '=', 'sk.barcode')
        ->select('s.barcode','s.callno','s.title')
        ->where('s.user_id', '=', \Auth::id())
        ->where('sk.user_id', '=', \Auth::id())
        ->orderBy('prefix')
            ->orderBy('tp1')
            ->orderBy('tp2')
            ->orderBy('pre_date')
            ->orderBy('pvn')
            ->orderBy('pvl')
            ->orderBy('cutter')
            ->orderByRaw("binary pcd")
            ->orderBy('cutter2')
            ->orderByRaw("binary pcd2")
            ->orderBy("part1")
            ->orderBy("part2")
            ->orderBy("part3")
            ->orderBy("part4")
            ->orderBy("part5")
            ->orderBy("part6")
            ->orderBy("part7")
            ->orderBy('s.created_at')
            ->get();
	}


    public function mpos()

    {  
        /*
        |--------------------------------------------------------------------------
        | Move position method
        |--------------------------------------------------------------------------
        |
        | This method finds the destination position of the currently moving item
        |
        */

        // Set of correct positions ordered by position
        $gcp = $this->gcpositions(); 

        // all cpositions in subsequences table ordered by position
        $plis = $this->subsequence_cpositions();

        // Gets cposition of next book to be moved
        $move = $this->move_cposition();

        $mgc = max($gcp);
        $mnc = min($gcp);

        $mpos = null;
        foreach($plis as $key=>$l)

        {
            // cposition of moving book is less than the current element in lis 
         if($move < $l)

         {

            //Get current position of book with cposition of $l
            $cup = \DB::table($this->shelf)
            ->where('cposition', $l)
            ->where('user_id',\Auth::user()->id)
            ->pluck('position')[0];

            $mp = \DB::table($this->shelf)
            ->where('cposition', $move)
            ->where('user_id',\Auth::user()->id)
            ->pluck('position')[0];

            if($mp > $cup) { return $cup; } //book is moving from right of $l to position of $l 

             else // book is moving from left of $l to left adjacent of $l

             { 
                $mpos = $cup-1; 
                if($mpos<$mnc) { return $move; } 
                else 
                { return $mpos; }  }

         }

     }



     if($mpos == null)  // Cposition of book is greater than greatest lis element

     {
        foreach($plis as $key=>$l)

        {
            if($move>$l)

            {   

                $cup = \DB::table($this->shelf)
                ->where('cposition', $l)
                ->where('user_id',\Auth::user()->id)
                ->pluck('position')[0];

                $mpos = $cup+($mgc-$cup); 
            }
        }

        return $mpos;

    }

}


    public function gcpositions()

    {
        // Gets complete array of correct positions of books on the shelf in order of their position
        return \DB::table($this->shelf)
        ->where('user_id', '=', Auth::id())
        ->orderBy('position')
        ->pluck('cposition')
        ->toArray();
    }

    public function subsequence_cpositions()

    {
        // Gets all cpositions in subsequence table ordered by cposition
        return \DB::table($this->subsequences)
        ->select('cposition')
        ->where('user_id', \Auth::id())
        ->orderBy('cposition')
        ->pluck('cposition')
        ->toArray();
    }

    public function move_cposition()

    {
        // Gets cposition of next book to be moved
        $mc = \DB::table($this->moves())
        ->select('cposition')
        ->where('user_id', \Auth::id())
        ->where('moved', 0)
        ->orderBy('cposition')
        ->take(1)
        ->first();

        if($mc) {return $mc->cposition;}
        else
            { return 0; }

    }

    public function move_position()

    {

        // Gets position of next book to be moved
        $next_move = \DB::table($this->moves())
        ->select('position')
        ->where('moved',0)
        ->where('user_id', \Auth::id())
        ->orderBy('cposition')
        ->take(1)
        ->first();

        if($next_move)
        {
            return $next_move->position;
        }

        else

        {
                $empty_moves = $this->empty_moves();



                $empty_subsequence = $this->empty_subsequence();

                        //Fill moves and subsequence
                $insert_moves = $this->insert_moves();



                $insert_subsequence = $this->insert_subsequence();

            // Gets position of next book to be moved
            return \DB::table($this->moves())
            ->select('position')
            ->where('moved',0)
            ->where('user_id', \Auth::id())
            ->orderBy('cposition')
            ->take(1)
            ->first()->position;
        }

    }

    public function count_moves()

    {
        // Count number of books to be moved
        return \DB::table($this->moves())
        ->select('id')
        ->where('moved',0)
        ->where('user_id', \Auth::id())
        ->count();
    }

   

    public function get_lis()
    {
        //get lis and convert to array
        $gcp = $this->gcpositions();
        $n = count($gcp);
        $lis = $this->lis->LongestIncreasingSubsequence($gcp, $n);
        return $lis; 

    }

    public function lBooks()
    {
        // Gets info for array of books in the LIS

        $cpos = $this->get_lis();
    
        return \DB::table($this->shelf)
        ->select('position','title','barcode','cposition')
        ->whereIn('cposition', $cpos)
        ->where('user_id', '=',Auth::id())
        ->get();
    
    }

    public function sub_insert_array()
    {
        // Create insert array for subsequence table
        $linfo = $this->lBooks();

        foreach($linfo as  $l)
        {
            //$linfo = \App\Sort::lisBooks($l);

            $inlist[] = array('user_id' => \Auth::id(), 'barcode' => $l->barcode, 'position' => $l->position,
                'cposition' => $l->cposition);

        }

        return $inlist;
    }

    public function insert_subsequence()
    {
        $listers = $this->sub_insert_array();
        $linsert = \DB::table($this->subsequences)->insert($listers);

        return $linsert;
    }

    public function get_moves_array()
    {
        // Get diff of array of all books and LIS -- i.e. the array of books to be moved
        $gcp = $this->gcpositions();
        $cgcp = count($gcp);
        $lis = $this->lis->LongestIncreasingSubsequence($gcp,$cgcp);

        

        $moves1 = array_diff($gcp,$lis); // Get positions of books not in lis
        $moves = array_values($moves1);  // reset keys to start at 0

        return $moves; 

    }



    public function insert_moves()
    {
        $moves = $this->get_moves_array();

       
        // This initiates a new move array after the table has been truncated
        $move_out = $this->empty_moves();

        //dd($moves);

        // Use the moves array of cpositions generated in get_moves to get info from sorts table
        $move_bars = \DB::table($this->shelf)
        ->select('barcode','position','cposition')
        ->whereIn('cposition', $moves)
        ->where('user_id', '=', Auth::id())
        ->get();

        // Make associative array
        foreach($move_bars as $m)
        {
            $mbs[] = array('user_id' => Auth::id(), 'barcode' => $m->barcode, 'position' => $m->position,
                'cposition' => $m->cposition);
        }

        //Insert array into tmoves table

        $move_inserts = \DB::table($this->moves)->insert($mbs);

        return  $move_inserts;

    }

    public function next_move()
    {
        $mpos = $this->mpos();
        return \DB::table("$this->moves as m")
        ->join("$this->shelf as s", 's.barcode', '=', 'm.barcode')
        ->selectRaw("s.barcode")
        ->where('m.user_id',Auth::id())
        ->where('s.user_id',Auth::id())
        ->where('m.moved', '!=', 1)
        ->orderBy('s.cposition')
        ->first();
    
    }

    public function empty_subsequence()
    {
        //Truncate listers table
        $csub = \DB::table($this->subsequences)->where('user_id',Auth::user()->id)->delete();
        return $csub;
    }

    public function empty_moves()
    {
        //Truncate moves table
        $cmoves = \DB::table($this->moves)->where('user_id',Auth::id())->delete();

        return $cmoves;
    }

    public function book_count()
    {
        //Get count of books on shelf
        $gcp = $this->gcpositions();
        return count($gcp);
    }

    public function shelf_correction()
    {
        // Stuff that happens when the book to be moved is rescanned

    }

    public function has_errors()
    {
        //See if there are errors in the shelf
        
        return \DB::table("$this->shelf as s1")
        ->join("$this->shelf AS s2", 's2.barcode', '=', 's1.barcode')
        ->selectRaw("s2.id")
        ->whereRaw('s2.position != s2.cposition')
        ->where('s2.user_id', '=', \Auth::id())
        ->where('s1.user_id', '=', \Auth::id())
        ->count();
    }

    public function insert_report($barcode,$title,$callnum,$location)
    {
        if($this->reports === 'reports')
        { $report = new Report; }

        else 

            { $report = new FullReport; }

        $report->user_id = \Auth::id();
        $report->barcode = $barcode;
        $report->title = $title;
        $report->callnum = $callnum;
        $report->location_id = $location;
        $report->shelf = 0;

        $report->save();

    }

    public function on_shelf($barcode)

    {
        $shelf = \DB::table($this->reports)
        ->select('shelf')
        ->where('barcode',$barcode)
        ->where('user_id',\Auth::id())
        ->get();

        if($shelf->first())
        {
            $shelf = $shelf[0]->shelf;
            
            return $shelf;
        }

        else 

            { return 3; }

    }

    

    public function response_check_najax($item_response)
{

}

public function response_check_ajax($current_location,$title)
{
    
    return response()->json(['alert'=> $current_location,'title'=>$title]);
}

public function item_response_test($item_response)
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

public function getMask($callno)
{
    return Sort::makeMask($callno);
}

public function insert_key($callno,$barcode,$title,$pre_sort_key)
 {
    

    $sort_key = explode("*", $pre_sort_key);

            $prefix = trim($sort_key[0]);
            $tp1 = trim($sort_key[1]);
            $tp2 = trim($sort_key[2]);
            $pre_date = trim($sort_key[3]);
            $pvn = trim($sort_key[4]);
            $pvl = trim($sort_key[5]);
            $cutter = trim($sort_key[6]);
            $pcd = trim($sort_key[7]);
            $cutter_date = trim($sort_key[8]);
            $inline_cutter = trim($sort_key[9]);
            $inline_cutter_decimal = trim($sort_key[10]);
            $cutter_date2 = trim($sort_key[11]);
            $cutter2 = trim($sort_key[12]);
            $pcd2 = trim($sort_key[13]);
            $part1 = trim($sort_key[14]);
            $part2 = 0;
            $part3 = 0;
            $part4 = 0;
            $part5 = 0;
            $part6 = 0;
            $part7 = 0;


        // Insert this into sortkeys table

            $sort = new $this->keyModel;

            $sort->user_id = \Auth::id(); 
            $sort->barcode = $barcode;
            $sort->callno = $callno;
            $sort->prefix = $prefix;
            $sort->tp1 = $tp1;
            $sort->tp2 = ".$tp2";
            $sort->pre_date = $pre_date;
            $sort->pvn = $pvn;
            $sort->pvl = $pvl;
            $sort->cutter = $cutter;
            $sort->pcd = ".$pcd";
            $cutter_date = $cutter_date;
            $sort->inline_cutter = $inline_cutter;
            $sort->inline_cutter_decimal = ".$inline_cutter_decimal";   
            $sort->cutter_date2;   
            $sort->cutter2 = $cutter2;
            $sort->pcd2 = ".$pcd2";
            $sort->part1 = $part1;
            $sort->part2 = $part2;
            $sort->part3 = $part3;
            $sort->part4 = $part4;
            $sort->part5 = $part5;
            $sort->part6 = $part6;
            $sort->part7 = $part7;

            $sort->save();

           $insert_book = $this->insertBook2($barcode,$callno,$title);
    }

    public function insertBook2($barcode,$callnum,$title)
{

   
        $ordered_books = $this->entity::getOrder(Auth::id());

        $gpos = $this->entity::bookPosition($ordered_books,$barcode);
        $new_id = $this->shelfCount()+1;
     
        $sort = new $this->entity;

        $sort->id = $new_id;
        $sort->user_id = \Auth::id();
        $sort->barcode = $barcode;
        $sort->title = $title;
        $sort->callno = $callnum;
        $sort->position = $new_id;
        $sort->cposition = $gpos;

        $sort->save();

        $post_insert = $this->postInsert($gpos,$barcode,$callnum);
}

    public function shelfCount()
    {
        return \DB::table($this->shelf)
        ->select('barcode')
        ->where('user_id', Auth::id())
        ->count();
    }


    public function postInsert($gpos,$barcode,$callno)
    {
            // update cpositions

        $ModelT = $this->entity;

        $ModelT::where('barcode', '!=', $barcode)
        ->where('cposition','>=', $gpos)
        ->where('user_id', Auth::id())
        ->increment('cposition', 1);

        $errors = $this->countErrors();

        //dd($errors);

        if($errors > 0)

            {
                //Truncate moves and subsequence  
                //$empty_moves = $this->empty_moves();



                $empty_subsequence = $this->empty_subsequence();

                        //Fill moves and subsequence
                $insert_moves = $this->insert_moves();



                $insert_subsequence = $this->insert_subsequence();

            }


        }



    public function countBook($barcode)
    {
        return \DB::table($this->shelf)
        ->select('barcode')
        ->where('user_id', '=',Auth::id())
        ->where('barcode', $barcode)
        ->count();
    }


    public function isCorrection($barcode)
    {
        //Checks to see if book scanned is already in DB

        $bc = $this->countBook($barcode);

        // See if there are shelf errors

        $error_count = $this->countErrors();

	$numberOfBooksInTheMovesTable = $this->count_moves();

        // See if this  particular book is in moves table
        $moves_count = $this->moves_count($barcode);


        if($bc == 0) 
        {
            // Book is not on shelf
            return 0;
        }
      

        if($error_count == 0 AND $bc > 0)
            {
                // Error you re-scanned book but no errors
            
                return 1;
            }

        if($error_count > 0 AND $moves_count == 0 AND $numberOfBooksInTheMovesTable>0)
         
         { 
            // Error you rescanned the wrong book

            return 2;

         }

         if($bc > 0 AND $error_count > 0 AND $moves_count > 0)

         {
            // Scan is shelf correction

            // Enter correction in error count table

            $user_id = Auth::id();
      
            dispatch(new InsertShelfCorrections($user_id,$barcode));

            // Get lis from subsequence
            $lis = $this->get_lis();

            $mpos = $this->mpos();

            $mbar = $this->next_move();

            //dd($mbar);

            if($mbar == $barcode)

            {

                return 3;
            }

            else

            {

                $book_info = $this->book_info($barcode);

                $pos = $book_info[0]->position;
                $cpos = $book_info[0]->cposition;

            if($mpos>$pos)

            {
                $book_right = $this->book_right($pos,$mpos,$barcode);
                $update_moves = $this->updateMoves($barcode);
            }

            else

            {
               $book_left = $this->book_left($pos,$mpos,$barcode); 
               $update_moves = $this->updateMoves($barcode);
            }

            //Check for remaining errors

                $remaining_moves = $this->count_moves();

            //If no errors, truncate moves and subsequence

                $ecount = $this->countErrors();

            if($remaining_moves == 0 AND $ecount == 0)

            {

                $empty_subsequence = $this->empty_subsequence();
                $empty_moves = $this->empty_moves();
            }


        }

            return 4;
        }
        
             
    }

    public function updateMoves($dbar)
    {
        \DB::table($this->moves)
                ->where('user_id', Auth::id())
                ->where('barcode', $dbar)
                ->delete();
    
    }

     public function book_info($barcode)
    {
        return \DB::table($this->shelf)
        ->select('title','callno','barcode','cposition','position')
        ->where('barcode', $barcode)
        ->where('user_id', '=', Auth::id())
        ->get();
    }


    public function book_right($pos,$mpos,$dbar)
    {
        // Increment positions of books on shelf when book is moving right
        $this->entity()::where('position', '>', $pos)
        ->where('user_id', \Auth::id())
        ->where('position', '<=', $mpos)
        ->decrement('position', 1);

        // Change the position of the moved book to it's new position
        $this->entity()::where('user_id', \Auth::id())
        ->where('barcode', $dbar)
        ->update(['position' => $mpos]);

        $user_id = Auth::id();

        \DB::statement("Update $this->moves m 
        inner join $this->shelf s 
        on s.barcode = m.barcode 
        set m.position = s.position 
        where s.user_id = $user_id"); 

    }

    public function book_left($pos,$mpos,$dbar)
    {
        $user_id = Auth::id();
        // Increment positions of books on shelf when book is moving left
        $this->entity()::where('position', '>=', $mpos)->where('user_id', \Auth::id())
        ->where('position', '<', $pos)->increment('position',1);
        
        // Change the position of the moved book to it's new position
        $this->entity()::where('user_id', \Auth::id())
        ->where('barcode', $dbar)
        ->update(['position' => $mpos]);
            
        \DB::statement("Update $this->moves m 
        inner join $this->shelf s 
        on s.barcode = m.barcode 
        set m.position = s.position 
        where s.user_id = $user_id");

    }

    public function countCorrections()
    {
        
        return $this->countMoves();
    }

    public function countMoves()
    {
        return \DB::table($this->moves)
        ->select('barcode')
        ->where('user_id', Auth::id())
        ->count();
    }

 public function countErrors()

 {

    return \DB::table("$this->shelf as s1")
     ->join("$this->shelf AS s2", 's2.barcode', '=', 's1.barcode')
     ->selectRaw("s2.id")
     ->whereRaw('s2.position != s2.cposition')
     ->where('s2.user_id', '=', Auth::id())
     ->where('s1.user_id', '=', Auth::id())
     ->count();

 }

 public function errorTable()
 {
        
     return \DB::table('sort_errors')
     ->select('errors')
     ->first()->errors;
 
 }

 public function moves_count($barcode)
 {
    return \DB::table($this->moves)
    ->select('id')
    ->where('barcode',$barcode)
    ->where('user_id', Auth::user()->id)
    ->count();
 
 }

 public function update_error_count($new_error_count)
 {
    $new_errors = \DB::table($this->error_count)
    ->update(['errors' => $new_error_count]);

    return $new_errors;
 
 }

 public function insert_test_key($callno)
 {
    $pre_sort_key = Callnumber::make_key($callno);

    $sort_key = explode("*", $pre_sort_key);

    //dd($sort_key);

            $prefix = trim($sort_key[0]);
            $tp1 = trim($sort_key[1]);
            $tp2 = trim($sort_key[2]);
            $pre_date = trim($sort_key[3]);
            $pvn = trim($sort_key[4]);
            $pvl = trim($sort_key[5]);
            $cutter = trim($sort_key[6]);
            $pcd = trim($sort_key[7]);
            $cutter_date = trim($sort_key[8]);
            $inline_cutter = trim($sort_key[9]);
            $inline_cutter_decimal = trim($sort_key[10]);
            $cutter_date2 = trim($sort_key[11]);
            $cutter2 = trim($sort_key[12]);
            $pcd2 = trim($sort_key[13]);
            $part1 = trim($sort_key[14]);
            $part2 = 0;
            $part3 = 0;
            $part4 = 0;
            $part5 = 0;
            $part6 = 0;
            $part7 = 0;

        // Insert this into testkeys table

            $test = new Testkey;

            $test->user_id = \Auth::id(); 
            $test->callno = $callno;
            $test->prefix = $prefix;
            $test->tp1 = $tp1;
            $test->tp2 = ".$tp2";
            $test->pre_date = $pre_date;
            $test->pvn = $pvn;
            $test->pvl = $pvl;
            $test->cutter = $cutter;
            $test->pcd = ".$pcd";
            $test->cutter_date = $cutter_date;
            $test->inline_cutter = $inline_cutter;
            $test->inline_cutter_decimal = ".$inline_cutter_decimal";
            $test->cutter_date2 = $cutter_date2;
            $test->cutter2 = $cutter2;
            $test->pcd2 = ".$pcd2";
            $test->part1 = $part1;
            $test->part2 = $part2;
            $test->part3 = $part3;
            $test->part4 = $part4;
            $test->part5 = $part5;
            $test->part6 = $part6;
            $test->part7 = $part7;

            $test->save();

 }

public function bookShelf()

    { 

        return \DB::table($this->shelf)
        ->select('id','barcode','title','callno','position','cposition')
        ->where('user_id', '=',Auth::id())
        ->orderBy('position')
        ->get();

    }

public function scan_count($barcode)
{
   $usage = new Usage;

   $usage->user_id = \Auth::id();
   $usage->date = date('Y-m-d');
   $usage->barcode = $barcode;
   $usage->save();
}

public function update_report($barcode,$title)
{
    \DB::table($this->reports)
    ->where('user_id', \Auth::id())
    ->where('barcode', $barcode)
    ->where('title', $title)
    ->update(['shelf' => 1]);
}

} 
