<?php

namespace App\Models;

use App\Models\FullReport;
use App\Models\FullShelf;
use App\Models\Shelfend;
use App\Models\Sort;
use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\Model;

class FullShelf extends Model
{
    public function user()

    {
        return $this->BelongsTo(User::class);
    }

    public function fshelf()
    {
        return $this->BelongsTo(FullSortkey::class,'user_id');
    }
    
    public static function getBooks()
    {
    
       return FullShelf::all()->where('user_id',\Auth::id())->sortBy('position');
    
    }

    public static function getShelfends()
    {
    
       return Shelfend::all()->where('user_id',\Auth::id());
    
    }

    public static function shelf_number($position)
    {
    
        return \DB::table('shelfends')
        ->select('position')
        ->where('position','<=',$position)
        ->where('user_id',Auth::id())
        //->where('position', '!=', 1)
        ->orderByDesc('position')
        ->count();
    
    }

    public static function section_number($position)
    {
    
        return \DB::table('sections')
        ->select('position')
        ->where('position','<=',$position)
        ->where('user_id',Auth::id())
        ->orderByDesc('position')
        ->count();
    
    }

    public static function getShelfs($user_id)
    {
    	
          return \DB::table('full_sortkeys')
        ->join('full_shelves','full_shelves.barcode', '=', 'full_sortkeys.barcode')
        ->select('full_shelves.barcode','full_shelves.callno','full_shelves.title')
        ->where('full_shelves.user_id', '=', $user_id)
        ->where('sortkeys.user_id', '=', $user_id)
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
            ->orderBy('full_shelves.created_at')
            ->get();
    }

    public static function getMoves()

    {
        return \DB::table('full_moves')
        ->select('barcode')
        ->where('moved', 0)
        ->where('user_id',Auth::id())
        ->orderBy('cposition')
        ->get();     
    }

    public static function moveCpos()

    {
        return \DB::table('full_moves')
        ->select('cposition')
        ->where('moved',0)
        ->where('user_id',Auth::id())
        ->orderBy('cposition')
        ->take(1)
        ->first()->cposition;
    }

     public static function getEmail($user_id)

    {
        return \DB::table('users')
        ->select('email')
        ->where('id', $user_id)
        ->first();
    }

    public static function nextMove()
    {
        return \DB::table('full_moves as f')
        ->join('full_shelves as s', 'f.barcode', '=', 'f.barcode')
        ->selectRaw("f.barcode")
        ->where('f.moved', 0)
        ->where('f.user_id',Auth::id())
        ->where('s.user_id',Auth::id())
        ->orderBy('f.cposition')
        ->first();

    }

    public static function upMove($cpos)
    {
        return \DB::table('full_shelves')
        ->where('cposition', $cpos)
        ->where('user_id',Auth::id())
        ->pluck('position')[0];
        

    }


    public static function getLis()

    {
        $listers = \DB::table('full_listers')
        ->select('cposition')
        ->where('user_id', '=',Auth::id())
        ->orderBy('cposition')
        ->get();

        foreach($listers as $l)

          {  $lis[] = $l->cposition; }

      return $lis;

    }

    public static function listersContent()
    {
    
       return FullLister::where('user_id', \Auth::id())->orderBy('cposition')->pluck('cposition')->toArray();    
    
    }

    public static function storeReport($barcode,$title,$callnum,$current_location)
    {
    
        $report = new FullReport;

        $report->user_id = \Auth::id();
        $report->barcode = $barcode;
        $report->title = $title;
        $report->callnum = $callnum;
        $report->location_id = $current_location;
        $report->shelf = 0;

        $report->save();
    
    }

    public static function mPos($plis,$move,$gcp)

    {  
         $mgc = max($gcp);
         $mnc = min($gcp);

        foreach($plis as $p)
                            { $lis[] = $p; }


            $mpos = null;
            foreach($lis as $key=>$l)

            {
               if($move < $l)

               {

                 //Get current position of book with cposition of $l
        $cup = \DB::table('full_shelves')->where('cposition', $l)->where('user_id',\Auth::user()->id)->pluck('position')[0];
        $mp = \DB::table('full_shelves')->where('cposition', $move)->where('user_id',\Auth::user()->id)->pluck('position')[0];
            if($mp > $cup) { return $cup; } //book is moving from right of $l to position of $l 
             else // book is moving from left of $l to left adjacent of $l
         { $mpos = $cup-1; if($mpos<$mnc) { return $move; } else { return $mpos; }  }
                 
               }
            
            }

            if($mpos == null)

            {
                foreach($lis as $key=>$l)

                {
                    if($move>$l)

                    {   

                    $cup = \DB::table('full_shelves')->where('cposition', $l)->where('user_id',\Auth::user()->id)->pluck('position')[0];
                        $mpos = $cup+($mgc-$cup); 
                    }
                }

                return $mpos;

            }
 
    }


    public static function firstError()

    {
        $first = \DB::table('full_shelves')
        ->select('position')
        ->where('user_id', '=',Auth::id())
        ->where('position', '!=', 'cposition')
        ->orderBy('position')
        ->take(1)
        ->first();

        return $first->position;
    }

    public static function getPositions()

    {
        
      $cpositions = \DB::table('full_shelves')
        ->select('cposition')
        ->where('user_id', '=',Auth::id())
        ->orderBy('position')
        ->get();

        foreach($cpositions as $p)

        {
            $cposition[] = $p->cposition;
        }

        return $cposition;  
    

    }

    public static function lBooks($cpos)
    {
    
        return \DB::table('full_shelves')
        ->select('position','title','barcode','cposition')
        ->whereIn('cposition', $cpos)
        ->where('user_id', '=',Auth::id())
        ->get();
    
    }

    public static function lisBooks($cpos)

    {
        return \DB::table('full_shelves')
        ->select('position','title','barcode','cposition')
        ->where('cposition', $cpos)
        ->where('user_id', '=',Auth::id())
        ->first();
    }


    public static function bLeft($mpos,$pos)

    {   
        if($pos>$mpos) {
        return \DB::table('full_shelves')
        ->select('title','callno','cposition','position')
        ->where('position', $mpos)
        ->where('user_id', '=',Auth::id())
        ->get();
    }
    else
    {
     return \DB::table('full_shelves')
        ->select('title','callno','cposition','position')
        ->where('position', $mpos)
        ->where('user_id', '=',Auth::id())
        ->get();  
    }
    }

    public static function bRight($mpos,$pos)

    {
     if($pos>$mpos) {
        return \DB::table('full_shelves')
        ->select('title','callno','cposition','position')
        ->where('position', $mpos+1)
        ->where('user_id', '=',Auth::id())
        ->get();
        }

         else
    {
     return \DB::table('full_shelves')
        ->select('title','callno','cposition','position')
        ->where('position', $mpos+1)
        ->where('user_id', '=',Auth::id())
        ->get();  
    }
    }

    public static function getMc()

    {
        return \DB::table('full_shelves')
        ->select('cposition')
        ->where('user_id',\Auth::user()->id)
        ->orderByDesc('cposition')
        ->first()->cposition;
    }


    public static function cRight($pos)

    {
        return \DB::table('full_shelves') 
        ->select('title')
        ->where('position', '>', $pos)
        ->where('user_id', '=',Auth::id())
        ->orderBy('position')
        ->limit(4)
        ->get();
    }

     public static function cLeft($pos) 
    {
        $books = \DB::table('full_shelves') 
        ->select('title', 'callno','shelf_id')
        ->where('position', '<', $pos)
        ->where('user_id', '=',Auth::id())
        ->orderByDesc('position')
        ->limit(4)
        ->get();

        $skoobs = $books->reverse();
        return $skoobs;
    }

    public static function getLast($shelf_size,$scan_size)

    { 
        if($scan_size > $shelf_size) {
        $no_show = $scan_size - $shelf_size;
        $last = \DB::table('full_shelves')
        ->select('id','barcode','title','callno','position','cposition')
        ->where('user_id', '=',Auth::id())
        ->orderBy('position')
        ->skip($no_show)
        ->take($shelf_size)
        ->get();

        return $last;

    }

    else 

    {
         $last = \DB::table('full_shelves')
        ->select('id','barcode','title','callno','position','cposition')
        ->where('user_id', '=',Auth::id())
        ->orderBy('position')
        ->get();

        return $last;
    }

    }

    public static function getAll()

    {
        return \DB::table('full_shelves')
        ->select('id','barcode','title','callno','position','cposition')
        ->where('user_id', '=',Auth::id())
        ->orderBy('position')
        ->get();
    }


     public static function newLast()

    {
        return \DB::table('full_shelves')
        ->select('id','barcode','title','callno','cposition','position')
        ->where('user_id', '=',Auth::id())
        ->orderByDesc('position')
        ->limit(36)
        ->get();

         
    }

    public static function getDimensions()

    {
        $divwidth = \DB::table('dimensions')
        ->select('divwidth')
        ->where('user_id',Auth::id())
        ->get();
        
        return $divwidth[0]->divwidth;

    }

      public static function newCorrect()

    {
        return \DB::table('full_shelves')
        ->select('id','barcode','title','callno','cposition','position')
        ->where('user_id', '=',Auth::id())
        ->orderByDesc('cposition')
        ->limit(36)
        ->get();

         
    }


    public static function countBook($barcode)
    {
        return \DB::table('full_shelves')
        ->select('barcode')
        ->where('user_id', '=',Auth::id())
        ->where('barcode', $barcode)
        ->count();
    }

    public static function countMains($barcode)
    {
        return \DB::table('mains')
        ->select('barcode')
        ->where('user_id', '=',Auth::id())
        ->where('barcode', $barcode)
        ->count();
    }

    public static function countBooks()
    {
        return \DB::table('full_shelves')
        ->select('barcode')
        ->where('user_id', '=',Auth::id())
        ->count();
    }

    public static function offSite($barcode)

    {
        return \DB::table('main')
        ->select('title','callno','lib_name')
        ->where('barcode', $barcode)
        ->get();
    }

   

    public static function getMain()

    {
        return \DB::table('institutions')
        ->join('users', 'users.institution', '=', 'institutions.id')
        ->select('main_table')
        ->where('users.id', '=',Auth::id())
        ->first();
    }

    public static function libraryInfo()

    {
        return \DB::table('institutions')
        ->join('users', 'users.institution', '=', 'institutions.id')
        ->select('main_table','library','institutions.institution')
        ->where('users.id', '=',Auth::id())
        ->get();
    }

    public static function getCallnumber($barcode,$main_table)

    {
        return \DB::table($main_table)
        ->select('title','callno')
        ->where('barcode', $barcode)
        ->get();

    }

    public static function bookInfo($barcode)

    {
        return \DB::table('full_shelves')
        ->select('title','callno','barcode','cposition','position')
        ->where('barcode', $barcode)
        ->where('user_id', '=',Auth::id())
        ->get();
    }

   

        // Get array of books in correct order from sortkeys table

        public static function getOrder($user_id)

        {
            return \DB::table('full_sortkeys')
            ->select('barcode','callno')
            ->where('user_id', '=', $user_id)
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
            ->orderBy('created_at')
            ->get();

        }

        public static function getBorder(array $abarcodes)

        {
            return \DB::table('full_sortkeys')
            ->whereIn('barcode', $abarcodes)
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
            ->orderBy('created_at')
            ->get();

        }


        public static function bookPosition($books,$barcode)

        {
      
            foreach($books as $key=>$b)

            {
                if($b->barcode == $barcode) { break; }
            }

            return $key+1;

        }

        public static function getTest() 

        {
            return \DB::table('testkeys') 
            ->select('*')
            ->where('user_id',Auth::id())
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
            ->orderBy('created_at')
            ->get();

        }

        public static function getSortkeys() 

        {
            return \DB::table('full_sortkeys') 
            ->select('*')
            ->where('user_id',Auth::id())
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
            ->orderBy('created_at')
            ->get();

        }

        public static function getLid()

        {
            return \DB::table('testkeys')
            ->select('id')
            ->where('user_id',Auth::id())
            ->orderByDesc('id')
            ->take(1)
            ->first();
        }

         public static function getSlid()

        {
            return \DB::table('full_sortkeys')
            ->select('id')
            ->where('user_id',Auth::id())
            ->orderByDesc('id')
            ->take(1)
            ->first();
        }



        public static function Position($barcode)

    {

        return \DB::table('full_shelves') 
        ->select('position')
        ->where('barcode', $barcode)
        ->where('user_id', '=',Auth::id())
        ->first();
        
        }

    public static function scanPosition($barcode)
    {
        return \DB::table('full_shelves')
        ->select('id')
        ->where('barcode', $barcode)
        ->where('user_id', '=',Auth::id())
        ->first();
    }

    public static function getGid()

    {
        return \DB::table('full_shelves')
        ->select('id','barcode')
        ->where('user_id', '=',Auth::id())
        ->orderByDesc('id')
        ->limit(1)
        ->get();
    }

    public static function getDiff($barcode)
    {
        return \DB::table('full_shelves')
        ->selectRaw("position-cposition as move")
        ->where('barcode',$barcode)
        ->where('user_id', '=',Auth::id())
        ->first();
    }

    public static function getLastbar($id)

    {
        return \DB::table('full_shelves')
        ->select('barcode')
        ->where('id', '=', $id)
        ->where('user_id', '=',Auth::id())
        ->first();
    }



  
     public static function sortDisplacement()
    {
         return \DB::table('full_shelves as s1')
    ->join('full_shelves AS s2', 's2.barcode', '=', 's1.barcode')
    ->selectRaw("abs(s2.position - s1.cposition) as displacement, s1.barcode")
    ->whereRaw('s2.position != s1.cposition')
    ->where('s2.user_id', '=',Auth::id())
    ->where('s1.user_id', '=',Auth::id())
    ->groupBy('s1.barcode','displacement','s2.id','s1.id')
    ->orderByDesc('displacement')
    ->orderBy('s2.id')
    ->take(1)
    ->get();

    }

    public static function shelfSize()

    {
        $size = \DB::table('shelfsizes')
        ->select('shelf_size')
        ->where('user_id',Auth::id())
        ->get();

        if($size->first()) 
            { $size = $size[0]->shelf_size; return $size;}

            else

                {$size = 19; return $size;}
    }

    public static function countSize()

    {
        return \DB::table('shelfsizes')
        ->select('id')
        ->where('user_id',Auth::id())
        ->count();
    }

    public static function countErrors()

    {
       
       return \DB::table('full_shelves as s1')
    ->join('full_shelves AS s2', 's2.barcode', '=', 's1.barcode')
    ->selectRaw("s2.id")
    ->whereRaw('s2.position != s2.cposition')
    ->where('s2.user_id', '=',Auth::id())
    ->where('s1.user_id', '=',Auth::id())
    ->count();
    }

    public static function countCorrections()

    {
       
       return \DB::table('full_shelves as s1')
    ->join('full_shelves AS s2', 's2.barcode', '=', 's1.barcode')
    ->selectRaw("s2.id")
    ->whereRaw("(s2.position - s2.cposition)>1")
    ->where('s2.user_id', '=',Auth::id())
    ->where('s1.user_id', '=',Auth::id())
    ->count();
    }


    public static function pdiff($barcode)

    {
        return \DB::table('full_shelves')
        ->selectRaw("(position-cposition) as pdiff")
        ->where('barcode', '=', $barcode)
        ->where('user_id', '=',Auth::id())
        ->first();
    } 


    public static function lastBook()

    {
        return \DB::table('full_shelves')
        ->select('barcode','title','callno','position')
        ->where('user_id', '=',Auth::id())
        ->orderByDesc('id')
        ->take(1)
        ->get();
    }

    public static function fBook()
    {
        
    
    
    }

    public static function lastShelfend()

    {
        return \DB::table('shelfends')
        ->select('position')
        ->where('user_id', '=',Auth::id())
        ->get();
    }

    public static function saveTable()

    {
        // Copies all unique barcodes not in destination table to destination table
       $query = "INSERT INTO main_stacks
SELECT null,s.barcode,s.title,s.callno,null,null
FROM full_shelves s 
       LEFT JOIN main_stacks m ON (m.barcode = s.barcode)
WHERE m.barcode IS NULL";

return \DB::connection()->getpdo()->exec($query);
    }

####################################################################################################

    // Call number processing functions

     public static function leadingZeros($string)

     {
      $c = strlen($string);
        $missing = 6-$c;

        if($missing > 0) {

       for ($i=0; $i < $missing; $i++) 

            { 

            $zeros[] = 0; 

            }           
       }

       $lzeros = implode("", $zeros);

       return "$lzeros$string";

     }

     public static function isDate($string)

     {
        if(strlen($string) == 5 OR strlen($string) == 4)

            {
                if(is_numeric($string[0])
                    AND
                    is_numeric($string[1])
                    AND
                    is_numeric($string[2])
                    AND
                    is_numeric($string[3])
                    AND
                    $string[0]<3
                    AND
                    $string[0]>0
            )
                { $status = 1; }

                else
                    { $status = 0; }
        }

            else

                { $status = 0; }

            

            return $status;
     }

     // Check string occurring after space and before cutter for volume number

     public static function isVnum($str)

     {
        //Explode the string into individual characters
        $star = preg_split('//', $str, -1, PREG_SPLIT_NO_EMPTY);

        $type = null;
        foreach($star as $key=>$s)

        {   
            if(is_numeric($s)) {$type = "n";} else {$type = "l";}

            if($type != null AND isset($ltype) AND $type != $ltype)

                { $starr[] = "/"; }

                $starr[] = $s; 
   
            if(is_numeric($s)) {$ltype = "n";} else {$ltype = "l";}
        }

        $string = implode("", $starr);
        $strings = explode("/", $string);

        if(is_numeric($strings[0]) AND !is_numeric($strings[1])) 

        { return $string; }

            else

                { return 0; }
     }

     public static function dateAbbr($string)

     {
        if($string == "JAN" OR
        $string == "FEB" OR
        $string == "MAR" OR
        $string == "APR" OR
        $string == "MAY" OR
        $string == "JUNE" OR
        $string == "JUN" OR
        $string == "JULY" OR
        $string == "JUL" OR
        $string == "AUG" OR
        $string == "SEPT" OR
        $string == "SEP" OR
        $string == "OCT" OR
        $string == "NOV" OR
        $string == "DEC")
        {
        if($string == "JAN") {return "01";}
        if($string == "FEB") {return "02";}
        if($string == "MAR") {return "03";}
        if($string == "APR") {return "04";}
        if($string == "MAY") {return "05";}
        if($string == "JUNE") {return "06";}
        if($string == "JUN") {return "06";}
        if($string == "JULY") {return "07";}
        if($string == "JUL") {return "07";}
        if($string == "AUG") {return "08";}
        if($string == "SEPT") {return "09";}
        if($string == "SEP") {return "09";}
        if($string == "OCT") {return "10";}
        if($string == "NOV") {return "11";}
        if($string == "DEC") {return "12";}
        }
    else
        { return 0; }
     
    }


    public static function makeMask($callno) // Find type of each character in callno string and return template
    {
        for($i = 0; $i < strlen($callno); $i++) // Get existing pattern in Ascii Decimal form
    {
        

        if (ord($callno[$i]) >= 48 AND ord($callno[$i]) <= 57)  { $amask[] = "I"; $calla[] = $callno[$i]; }
        if (ord($callno[$i]) >= 65 AND ord($callno[$i]) <= 90)  { $amask[] = "A"; $calla[] = $callno[$i]; }
        if (ord($callno[$i]) >= 97 AND ord($callno[$i]) <= 122)  { $amask[] = "a"; $calla[] = $callno[$i]; }
        if (ord($callno[$i]) === 46)  { $amask[] = "D"; $calla[] = $callno[$i]; }
        if (ord($callno[$i]) === 45)  { $amask[] = "H"; $calla[] = $callno[$i]; }
        if (ord($callno[$i]) === 32)  { $amask[] = "_"; $calla[] = $callno[$i]; } // This is a space

    }


    $amask[] = "~"; // put a marker at the end of the array
    $smask = implode("",$amask); // Returns string with no spaces 
    
    $masks = array($amask,$smask,$calla);

    return $masks;

    }

    public static function pMask($amask,$smask,$callno,$calla)
    {   

        $str = $smask;

        $pos = (strpos($str, "DA")); // Gives position of decimal in cutter counting from 0
        $bd = $pos-1; //gives position right before cutter counting from  0 - usually last character in precutter string
        $endofcutter = ($pos+1); // In the array, will be the key of the letter that follows the decimal in the cutter
        $pnum = (strpos($str, "I")); // Gives position of 1st number in callno string
        $preflength = $pos-1; //Total length of prefix including spaces
        $volume = (strpos($callno, "V.")); // Find volume section if any
        $space = (strpos($str, "_")); //gives position of 1st space counting from 0
        $xmask = str_replace("_", " ", $smask); // Replace "_" is $smask with spaces

        $pvn = null;
        $pvl = null;

        ########################################################################################################################

        // Make separate section for call numbers with no cutter

        if ($pos === false) {

            //Count number of spaces in $amask
                $cspace = 0;
                foreach($amask as $key => $a)
                {
                    if($a == "_") { $cspace++; $skey = $key; }
                }

            // Get prefix

        //Find out how many numbers are in prefix
        $prenums = 0; // initiate variable

        foreach($amask as $key=>$a)
        {   
            if($a == 'D') { break; }
            if($amask[$key] == 'I')
            { $prenums++; }
            if($key == $preflength ) { break; }
        }


        foreach($amask as $key=>$a)
        {   
            if($a == 'D') { break; }
            if($amask[$key] == 'A') {

                $prefix[] = $calla[$key];

            } 


            if($key == 3) { break; }

            
        }



        $prefix = implode("", $prefix);


        

        // Construct 6 character number section after prefix

        // First get the array before any precutter decimal points, however many there are
        foreach($amask as $key=>$a)

        {
            // stop when you hit end of string
            if($a == '~') { break; } 
            if($a == '_') { break; } //Stop at space

            // Stop if there is a decimal
            if($amask[$key] == 'D') { break; }
            if($amask[$key] == 'I') { $section2b[] = $calla[$key]; }
                

        }

        $off = 0;
        $d = 0;
        
         foreach($amask as $key=>$a)

        {
            // stop when you hit end of string
            if($a == '~') { break; } 

            //count number of decimals before cutter
            if($amask[$key] == 'D') { $d++; }

            if($a=="_") { break; }

            if($amask[$key] == 'I' AND $d>0) {  //Start constructing after 1st precutter decimal


             { $section2c[]=$calla[$key]; }
             
             }   

        }

        
        if(isset($section2b)) {
        $cnum = count($section2b);

        $missing = 6-$cnum; // 6 to allow for the place the decimal point takes

         // Add 0's if less than 5 characters

        if($missing > 0) {

       for ($i=0; $i < $missing; $i++) { 

            $section2a[] = 0; }           
       }
   }
       // So far $section2a is the leading 0's before any integers

       // merge with the integers that occur before any precutter decimal

       if( isset($section2a) AND isset($section2b) ) {
       $section2a = array_merge($section2a,$section2b);

       $section2a = implode("", $section2a);

        } else { $section2a = "";}

       if(isset($section2c)) { $section2c = implode("", $section2c); } else {$section2c = "";}

       $cutter = ""; $post_cutter = ""; $cutter2 = ""; $post_cutter2 = ""; 

       
       $pre_marker = 0; //For no cutter pre_date will fall in line 2 section
       $pre_date = ""; //For no cutter pre_date will fall in line 2 section
   }



    else {  // Resume after topline processing done

##################################################################################################################
         
         //Count number of spaces in $amask
                $cspace = 0;
                foreach($amask as $key => $a)
                {
                    if($a == "_") { $cspace++; $skey = $key; }
                }


        // Find out if topline contains a space and a date

        if($space !== false AND $space < $pos ) //If true there is a space before the cutter

            { 

                foreach($amask as $key=>$a)

                {
                    if($key<=$space) { continue; }
                    if($key == $pos) { break; }
                    $pre1_date[] = $calla[$key];
                }

               
                
            
                if(isset($pre1_date)) 

                    { 
                         
                        $pre1_date = implode("", $pre1_date);  

                        $status = Sort::isDate($pre1_date);

                        if($status == 1) 
                            { $pre_date = $pre1_date; $pre_marker = 1; }
                        else
                        { $pre_date = ""; $pre_marker = 0; 

                        //See if it's a volume number
                        $vstatus = Sort::isVnum($pre1_date);

                        if($vstatus != 0)

                            { 
                                $pvn1 = explode("/",$vstatus); 
                               
                                $pvn = Sort::leadingZeros($pvn1[0]);

                                $pvl = $pvn1[1];
                            }

                        }
                    }

                    else
                        { $pre_date = ""; $pre_marker = 0; }
           
             }
        else
            { $pre_date=""; $pre_marker = 0; }

         // Get prefix

        //Find out how many numbers are in prefix
        $prenums = 0; // initiate variable

        foreach($amask as $key=>$a)
        {
            if($amask[$key] == 'I')
            { $prenums++; }
            if($key == $preflength ) { break; }
        }
 

        foreach($amask as $key=>$a)
        {   
            if($a == 'D') { break; }
            if($amask[$key] == 'A') {

                $prefix[] = $calla[$key];

            } 


            if($key == 3) { break; }

            
        }



        $prefix = implode("", $prefix);

        ################################################################################

        // Construct 6 character number section after prefix

        // First get the array before any precutter decimal points, however many there are
        foreach($amask as $key=>$a)

        {
            // stop when you hit cutter
            if($key == $pos) { break; } // stop when you hit cutter
            if($a == "_") { break; }
            // Stop if there is a decimal
            if($amask[$key] == 'D') { break; }
            if($amask[$key] == 'I') { $section2b[] = $calla[$key]; }
                

        }

 #####################################################################################################
            // Get only part after pre-cutter decimal. This part is separate section
        // First find the position of the pre-cutter decimal


       
       if ($pos !== false) {

        $off = 0;
        $d = 0;
         foreach($amask as $key=>$a)

        {
            // stop when you hit cutter
            if($key == $pos) { break; } 

            if($a == "_") { break; }

            //count number of decimals before cutter
            if($amask[$key] == 'D') { $d++; }

            if($amask[$key] == 'I' AND $d>0) {  //Start constructing after 1st precutter decimal

             { $section2c[]=$calla[$key]; }
             
             }   

        }

        // $section2c is an array of post-decimal integers
        // $secton2b is an array of pre-decimal integers
        // $section2a is array of zeros to pad $section2b
        // $section 2c is array_merge of $section2a and section2b



        if(isset($section2b)) {
        $cnum = count($section2b);

        $missing = 6-$cnum; // 6 to allow for the place the decimal point takes

         // Add 0's if less than 5 characters

        if($missing > 0) {

       for ($i=0; $i < $missing; $i++) { 

            $section2a[] = 0; }           
       }


      $section2a = array_merge($section2a,$section2b);
      $section2a = implode("", $section2a);

   }

   else 

    { 
        $section2b = ""; $section2a = "";  

    }


    if(isset($section2c))

    {
        $section2c = implode("", $section2c);
    }

    else

        { $section2c = ""; }

     }  

#############################################################
        // Get cutter
        $i =0;
        foreach($amask as $key=>$a) 
         {
            if($key >= $pos)


                {   
                    if($a == 'D') { continue; }
                    if($a == 'I' OR $a == '_') {break;}
                    $cutterp[] = $calla[$key]; 
                }
          
         }

         $lcut = $key-1; // One less than last iteration because loop has iterated an additional time before break

         $cutter = implode("", $cutterp);

###################################################################################################

// Beginning of Post-Cutter 

        // Get second cutter if it exists

         foreach($amask as $key=>$a) 

         {  
            if($key > $lcut)  {  // last character in first cutter
            if($a == '_') { break; }

                if($a == 'A') { 

                $cutter2letters[] = $calla[$key]; $cutter2_position = $key; 
                
                }
            }
         }

         if(isset($cutter2letters)) { $cutter2 = implode("", $cutter2letters); }
         
         else
            { $cutter2 ="";}


         // Get first post cutter or pcd
         foreach($amask as $key=>$a) { 

            if($key > $lcut)  {

                // Stop if you hit beginning of second cutter or end
                if($a == '_' OR $a == "A") { break; }

                if($amask[$key] == 'I') {

                if($calla[$key] == 0) {$postcutter[] = 'a';}
                if($calla[$key] == 1) {$postcutter[] = 'b';}
                if($calla[$key] == 2) {$postcutter[] = 'c';}
                if($calla[$key] == 3) {$postcutter[] = 'd';}
                if($calla[$key] == 4) {$postcutter[] = 'e';}
                if($calla[$key] == 5) {$postcutter[] = 'f';}
                if($calla[$key] == 6) {$postcutter[] = 'g';}
                if($calla[$key] == 7) {$postcutter[] = 'h';}
                if($calla[$key] == 8) {$postcutter[] = 'i';}
                if($calla[$key] == 9) {$postcutter[] = 'j';}
        }
       
        }

    }

        $post_cutter = implode("", $postcutter); //post_cutter string


        // Get second post cutter numbers, converted to lower case letters

        if(isset($cutter2) AND $cutter2 !=="") {

          foreach($amask as $key=>$a)

            {
                
                if($key > $cutter2_position)

                {
                     if($a == '_') { break; }

                if($amask[$key] == 'I') {

                if($calla[$key] == 0) {$postcutter2[] = 'a';}
                if($calla[$key] == 1) {$postcutter2[] = 'b';}
                if($calla[$key] == 2) {$postcutter2[] = 'c';}
                if($calla[$key] == 3) {$postcutter2[] = 'd';}
                if($calla[$key] == 4) {$postcutter2[] = 'e';}
                if($calla[$key] == 5) {$postcutter2[] = 'f';}
                if($calla[$key] == 6) {$postcutter2[] = 'g';}
                if($calla[$key] == 7) {$postcutter2[] = 'h';}
                if($calla[$key] == 8) {$postcutter2[] = 'i';}
                if($calla[$key] == 9) {$postcutter2[] = 'j';}
        }  

                }


            }
                

        }

        if(isset($postcutter2)) { $post_cutter2 = implode("", $postcutter2); }

        else

          { $post_cutter2 = ""; }

      
      
##################################################################################################################

      } // End of no cutter section

      // Identify index volumes


      // Split call number at topline boundary

      if($space !== false) {
      if($pre_marker == 0) //There is no pre_date
        { 
            // Split the call number in 2 parts at the first space if there is no pre_date space
            $call = (explode(' ', $callno, 2)); $call = $call[1]; 

        } 


      if(isset($call)) {



        // Create array from characters in last part of call number
      $string = preg_split('//', $call, -1, PREG_SPLIT_NO_EMPTY);

      
      // Find out if there is date followed by letter


      if($pre_marker == 0 AND $pvn == null) // If there's no pre_date split $smask in 2 parts at 1st space
      { 
        
       $dx = (explode('_', $smask, 2));  $dm = $dx[1];  // Split the call number template in 2 parts at the first space

      } 

      
        elseif($pre_marker != 0 OR $pvn !== null AND $cspace >= 2)

        {

         preg_match('/^([^ ]+ +[^ ]+) +(.*)$/', $xmask, $matches); $dm=$matches[2];  //Split $xmask in 2 at second space

        }



        else

        {
          // account for $pvn with no cutter here    
        }

        //dd($matches[2]);

        $hyphen = (strpos($dm, "H")); //position of the hyphen in the last part of call number
        
        $bhyphen = (strpos($smask, "H")); // positon of hyphen in the string as a whole 

        if( $volume !== false AND $hyphen !== false ) // Have to check by !== false because if true, then = an integer, not true

      {
        // Look for IHI pattern in $smask

        $vindex = (strpos($smask, "IHI")); 

        if($vindex !== false)

        {
            foreach($amask as $key=>$a) 
            {
                if($key<=$bhyphen) { continue; }
                if($a=="_") { break; }
                $index_end = $key;
                if($a=="~") { break; }

                $index_values[] = $calla[$key]; // Get the actual values in the index string
                
            }

        }



      }

      if(isset($index_values))

      { 
        $letter = "A";
        $index = implode("", $index_values); 
        $index = Sort::leadingZeros($index);
        $zindex = "$index$letter"; 
       
      } 

      else

        { $zindex = 0; }



      $dp = (strpos($dm, "IIIIA")); // Find pattern of date with letter
      $vol2 = (strpos($dm, "V."));

      if($dp !== FALSE)

      {
        
            if($string[$dp] == 1 OR $string[$dp] == 2) { $tdwl = 1; }

            if(isset($tdwl)) {
            if( $tdwl == 1) { 

                $p1=$string[$dp];
                $p2=$string[$dp+1];
                $p3=$string[$dp+2];
                $p4=$string[$dp+3];
                $p5=$string[$dp+4];

                $dwl = "$p1$p2$p3$p4$p5"; }
            }
                else

                    { $dwl = ""; }
        
      }


      //Create an array, replace spaces and number-letter transitions with delimiter /
      //dd($index_values);
        $past = null;
      foreach($string as $key=>$s)

      { 
       
        if($zindex !== 0 AND $key>$vol2) 

        {      
            if($s == "~") { break; } // Break if you hit end of string
            if($key >$vol2 AND $key<$hyphen) { continue; } //skip everything from the decimal point to the hyphen
            if($s == " ") { $temp[] = "/"; continue; }
            if($key == $hyphen)

            { $temp[] = "/$zindex"; continue; }
            
            if($key>$hyphen AND $key<=$hyphen+count($index_values)) { continue; }

        }


        if($dp !== false AND $key == $dp) // get pre_date

           { 
                $temp[] = 0;
                $temp[] = 0;

                for($i=0; $i<=4; $i++)

                    { $temp[] = $string[$dp+$i]; }

           }

           

           if($dp !== false)
           { 

            if($key >= $dp AND $key <= $dp+4) { continue; }

            }

        if($s == " ") { $temp[]="/"; continue; } // Replace each space with a delimiter
        if($s == ".") { continue; } // Remove decimal points

        if(is_numeric($s) === TRUE) {$type1 = 1;} else {$type1 = 2;}

        if(isset($type2) AND $type2 != $type1 AND end($temp) != "/") { $temp[] = "/"; }

        $temp[]=$s;

        $type2 = $type1;
     
      }

  
      

      if($pvn !== null) {
       
           unset($temp);
           $temp = array();
            foreach($amask as $key=>$s) {
            if($key > $skey ) // $skey is position of 2nd space
            {
                if($s == "~") { break; }
             $temp[] = $calla[$key]; 
            }
           }
             }

             $temp = implode("",$temp); // Convert array to sting
             $parts = explode("/", $temp); // Create an array of strings using delimiter



    if(isset($parts[0])) { $part1 = trim($parts[0]); } else {$part1="";}
    if(isset($parts[1])) { $part2 = trim($parts[1]); } else {$part2="";}
    if(isset($parts[2])) { $part3 = trim($parts[2]); } else {$part3="";} 
    if(isset($parts[3])) { $part4 = trim($parts[3]); } else {$part4="";}
    if(isset($parts[4])) { $part5 = trim($parts[4]); } else {$part5="";}
    if(isset($parts[5])) { $part6 = trim($parts[5]); } else {$part6="";}
    if(isset($parts[6])) { $part7 = trim($parts[6]); } else {$part7="";}

    if($part1) {
    if(is_numeric($part1) === TRUE AND strlen($part1) < 6) 
    {
        $part1 = Sort::leadingZeros($part1);
      
    }

    $abstat = (Sort::dateAbbr($part1));

    if($abstat !== 0) $part1 = $abstat;

    }

    // Normalize any numbers to 6 places, adding leading zeros

    if($part2) {
    if(is_numeric($part2) === TRUE AND strlen($part2) < 6) 
    {
        $part2 = Sort::leadingZeros($part2);

    }


    $abstat = (Sort::dateAbbr($part2));

    if($abstat !== 0) $part2 = $abstat;

    }

    if($part3) {
    if(is_numeric($part3) === TRUE AND strlen($part3) < 6) 
    {
        $part3 = Sort::leadingZeros($part3);
      
    }

    $abstat = (Sort::dateAbbr($part3));

    if($abstat !== 0) $part3 = $abstat;

    }

    if($part4) {
    if(is_numeric($part4) === TRUE AND strlen($part4) < 6) 
    {
        $part4 = Sort::leadingZeros($part4);
      
    }

    $abstat = (Sort::dateAbbr($part4));

    if($abstat !== 0) $part4 = $abstat;

    }

    if($part5) {
    if(is_numeric($part5) === TRUE AND strlen($part5) < 6) 
    {
        $part5 = Sort::leadingZeros($part5);
      
    }

    $abstat = (Sort::dateAbbr($part5));

    if($abstat !== 0) $part5 = $abstat;

    }


    if($part6) {
    if(is_numeric($part6) === TRUE AND strlen($part6) < 6) 
    {
        $part6 = Sort::leadingZeros($part6);
      
    }

    $abstat = (Sort::dateAbbr($part6));

    if($abstat !== 0) $part6 = $abstat;

    }

    if($part7) {
    if(is_numeric($part7) === TRUE AND strlen($part7) < 6) 
    {
        $part7 = Sort::leadingZeros($part7);
      
    }

    $abstat = (Sort::dateAbbr($part7));

    if($abstat !== 0) $part7 = $abstat;

    }

}

else

{

    $part1="";
    $part2="";
    $part3="";
    $part4="";
    $part5="";
    $part6="";
    $part7="";
}

}

else

{

    $part1="";
    $part2="";
    $part3="";
    $part4="";
    $part5="";
    $part6="";
    $part7="";

}

   ######################################
if($pos === false) {


    $cdate = Sort::isDate(ltrim($part1,0));
   
    if($part2 == "" AND isset($part1))
    {    
        $part1 = ltrim($part1,0); 
        $pre_date = "$part1";  $part1 = "";
    }

    if($part3 == "" AND $part2 != "")

    {
        $part1 = ltrim($part1,0);
        $part2 = ltrim($part2,0);
        $testp = "$part1$part2";
        $vstatus = Sort::isVnum($testp);
        if($vstatus != 0) {
        $pvn = $part1;
        $pvl = $part2;
        $part1 = "";
        $part2 = "";
    }
    }
}

    ######################################


###################################################################################################################
       
        
        if(isset($section2c))
        { $sort_key = "$prefix/$section2a/$section2c/$pre_date/$pvn/$pvl/$cutter/$post_cutter/$cutter2/$post_cutter2/$part1/
    $part2/$part3/$part4/$part5/$part6/$part7"; }

        else
            { $sort_key = "$prefix/$section2a/0/$pre_date/$pvn/$pvl/$cutter/$post_cutter/$cutter2/post_cutter2/$part1/$part2/part3/
        $part4/$part5/$part6/$part7"; }

       
        return $sort_key;

    }





}






