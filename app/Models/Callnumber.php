<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\SortsRepository;
use App\Models\Sort;

class Callnumber extends Model
{

    public static function secondCutter($smask,$amask,$calla)
    {
        $offs = self::firstCutter($smask);
        
        if($offs !== false) 
        {
             foreach($smask as $key=>$s)
        {
            
            $cut2pos = strpos($smask, "DA",$offs);
            if($cut2pos !== false)
            {
                // extract 2nd cutter and key
                 foreach($smask as $key=>$s)

        {
            if(ctype_alpha($s))
            {
                $subclass[]= $s;
            }

            else

            {
                break;
            }
        }


                $subclasskey[] = $key;

        return array($subclass,$subclasskey);

            }

            else

            {
              return 0;  
            }
        }

        }

        else 
        {
            return 0;
        }
       
    }


public static function count_cutters($smask)
{

    return substr_count($smask, '.A');

}

public static function count_spaces($smask)
{

    return substr_count($smask, '_');

}


public static function pre_cutter_space($cutterpos,$pspace)
{
    // if call number has no space, return 0
    if($pspace === false) { return 0; }

    // If callnumber contains a space and it comes before the cutter, return 1
    if($pspace<$cutterpos)
    {
        return 1;
    }
    else
    {
        return 0;
    }
}

public static function last_cutter($smask)
{

    $inline_lcut = strrpos($smask, 'IDA',0)+1; 
    $freestanding_lcut = strrpos($smask, '_DA',0)+1; 

    if($inline_lcut != false AND $freestanding_lcut != false)

    {

        if($inline_lcut > $freestanding_lcut)

        {
            return $inline_lcut;
        }

        else

        {
           return $freestanding_lcut;
        }

    }

    elseif($freestanding_lcut == false AND $inline_lcut != false)

    {
        return $inline_lcut;
    }

    else

    {
        return 0;
    }
}

public static function start_specification($amask,$smask)
{
    $lcut = self::last_cutter($smask);
    
    // Find the first space after the last cutter
    if($lcut != 0) {

        for($i=$lcut; $i>=$lcut; $i++)

        {
            if($i == count($amask)-1) { return -1; }
            if($amask[$i] == '_') 

            {
    
                break;
            }


        }

        return $i;

    }

    else

    {
        // Find first space
        $first_space = strpos($smask, '_');

        if($first_space != false)

        {
            return $first_space;
        }

        else

        {
            return 0;
        }

    }

}

    public static function amask($callno)
    {    

        for($i = 0; $i < strlen($callno); $i++) // Get existing pattern in Ascii Decimal form
        {


            if (ord($callno[$i]) >= 48 AND ord($callno[$i]) <= 57)  { $amask[] = "I";  }
            if (ord($callno[$i]) >= 65 AND ord($callno[$i]) <= 90)  { $amask[] = "A";  }
            if (ord($callno[$i]) >= 97 AND ord($callno[$i]) <= 122)  { $amask[] = "a"; }
            if (ord($callno[$i]) === 46)  { $amask[] = "D"; }
            if (ord($callno[$i]) === 45)  { $amask[] = "-"; }
            if (ord($callno[$i]) === 47)  { $amask[] = "/"; }
            if (ord($callno[$i]) === 44)  { $amask[] = ","; }
        if (ord($callno[$i]) === 32)  { $amask[] = "_"; } // This is a space

    }

    return $amask;
    
}

public static function smask($callno)
{
    $amask = self::amask($callno);
    $smask = implode("",$amask); // Returns mask as string with no spaces 

    return $smask;
}



public static function calla($callno)
{
    for($i = 0; $i < strlen($callno); $i++) // Get existing pattern in Ascii Decimal form
    {


        if (ord($callno[$i]) >= 48 AND ord($callno[$i]) <= 57)  { $calla[] = $callno[$i]; }
        if (ord($callno[$i]) >= 65 AND ord($callno[$i]) <= 90)  { $calla[] = $callno[$i]; }
        if (ord($callno[$i]) >= 97 AND ord($callno[$i]) <= 122)  { $calla[] = $callno[$i]; }
        if (ord($callno[$i]) === 46)  { $calla[] = $callno[$i]; }
        if (ord($callno[$i]) === 45)  { $calla[] = $callno[$i]; }
        if (ord($callno[$i]) === 44)  { $calla[] = $callno[$i]; }
        if (ord($callno[$i]) === 47)  { $calla[] = $callno[$i]; }
        if (ord($callno[$i]) === 32)  {  $calla[] = $callno[$i]; } // This is a space

    }

    return $calla;
}

public static function cutterCount($smask)
{
    return substr_count($smask, 'DA');
}

public static function firstCutterPos($smask)
{
    return strpos($smask, 'DA');
}

// Finds position of second cutter only in case of cutter date following 1st cutter
public static function lastCutterPos($smask)
{
    return strrpos($smask, 'DA');
}

public static function throwCutter2($smask,$amask,$calla)
{
    $cutter_count = self::cutterCount($smask,$amask);
    $lpos = self::lastCutterPos($smask);
    //dd($lpos);

    $lcut = "";

    if($cutter_count==2){
        foreach($amask as $key=>$a)
        {
            if($key <= $lpos) { continue; }
            if($a != 'A' ) { break; }
            if($key >= $lpos)
            {
                
                $lcut .= $calla[$key];
            }
        }
    }

    return $lcut;
}

public static function make_key($callno)
{
    $amask = self::amask($callno);
    $smask = self::smask($callno);// Returns mask as string with underscores in place of spaces 
    $calla = self::calla($callno);

    $subclass = self::subclass($amask,$calla);

    $caption_integer = self::caption_integer($amask,$smask,$calla,$callno);

    $caption_decimal = self::caption_decimal($smask,$callno);

    $cut1pos = strpos($smask, "DA");

    if($cut1pos === false)
    {
        $caption_date = 0;
        $caption_ordinal_ind = 0;
        $caption_ordinal_num = 0;
        $cutter1 = 0;
        $cutter1_decimal = 0;
        $inline_cutter = null;
        $inline_cutter = 0;
        $inline_cutter_decimal = 0;
        $cutter_date = "";
        $cutter_date2 = "";
        $cutter2 = 0;
        $cutter2_decimal = 0;
        

    }

    else

    {
            // Is there a space before the first cutter
            //Find first space
        $first_space = strpos($smask, '_');

        $capspace = self::pre_cutter_space($cut1pos,$first_space);
        //dd($capspace);
            if($capspace == 1) // Space comes before cutter
            {
                // Returns date if found
                $caption_date = self::caption_date($smask,$calla,$cut1pos,$first_space);
                //method on line 341
            }

            else
            {
                $caption_date = 0;
            }

            $caption_ordinal = self::caption_ordinal($calla,$first_space,$cut1pos);

            //dd($caption_ordinal);
            //Extract number and letters
            $str = $caption_ordinal;
            //dd($str);
            $len = strlen($str);

            if($caption_ordinal != 0)

            {
                $capdigit = null;
                $capletter = null;

                for( $i = 0; $i<$len; $i++)

                {
                    if(ctype_digit($str[$i]))
                    {
                        $capdigit .= $str[$i]; 
                    }

                    else
                    {
                        $capletter .= $str[$i];
                    }
                }
            }

            else
            {
                $capdigit = 0;
                $capletter = 0;
            }

            $caption_ordinal_num = $capdigit;

            $caption_ordinal_ind = $capletter;

            $cutter1 = self::cutter1($amask,$calla,$cut1pos);

            $cutter1_decimal = self::cutter1_decimal($amask,$calla,$cut1pos);

            $cdate_pos = strpos($smask, '_IIIIDA', $cut1pos);

            // This checks for the existence of a space and date pattern after the 1st cutter
            $cdate_pos = strpos($smask, '_IIIIDA', $cut1pos);

            //dd($cdate_pos);

            if($cdate_pos !== false) 
            {
                $cutter_date = self::cutter_date($amask,$calla,$smask,$cdate_pos);
            }

            else
            {
               $cutter_date = 3000;
            }
            
            //Position of the end of the first cutter, including decimal
            $cut_one_end = self::cutOneEnd($amask,$calla,$cut1pos);

            if($cdate_pos > $cut_one_end)
            {
                $cutter_date2 = $cutter_date;
                $cutter_date = 3000;
                $fcutter_date = $cutter_date2;
            }
            else {
                $cutter_date2 = 3000;
                $cutter_date = $cutter_date;
                $fcutter_date = $cutter_date;
            }
            //dd($fcutter_date);
            //dd($cutter_date2);
            // Below returns position of second cutter in the case of DA cutter pattern following cutter date, else returns false 
            // Does not tell if this is the second or third cutter overall
            //$second_cutter = self::secondCutterPos($smask);
            $inline_cutter = null;
            $inline_cutter = self::inline_cutter($amask,$calla,$cut1pos,$cutter_date);

            //dd($inline_cutter);

            // Need ending position of inline cutter
            $inline_cutter_end = self::inlineEnd($amask,$calla,$cut1pos,$cutter_date);

            //dd($inline_cutter_end);

            $dcutter = self::throwCutter2($smask,$amask,$calla);

            // Position of last cutter
            $last_cdate_pos = self::lastCutterPos($smask);

            
            

            //dd($inline_cutter);
            // Make sure inline cutter decimal starts after 1st cutter date  (if there is one)

            if(self::cutterCount($smask) < 2)
            {
                // Max is one decimal cutter and 1 inline cutter
                $cutter2 = 0;
            }
            $inline_cutter_decimal = self::inline_cutter_decimal($amask,$calla,$cut1pos,$cutter_date);
           
            //dd($inline_cutter_decimal);
            //dd($cutter_date);
            $pcutter = self::postDateCutter($amask,$calla,$cut1pos,$fcutter_date,$cdate_pos);

           
            //dd($cutter_date);
            ###########################################################
            # if($cutter_date) check for inline cutter - should be two decimal cutters - if no inline cutter, 
            # 2nd decimal cutter goes in inline column,
            # use postDateCutter($amask,$calla,$cut1pos,$cutter_date,$cdate_pos) to get cutter letters
            # if(!$cutter_date) should only be 1 decimal cutter - do normal processing (original)
           

            if($cdate_pos != false AND self::cutterCount($smask)==2 AND $cdate_pos < $inline_cutter_end)
            {
                #cutter date is between first and second cutter
                $cutter2_pos = strpos($smask, 'DA', $cdate_pos);
                $inline_cutter = null;
                $inline_cutter = self::postDateCutter($amask,$calla,$cut1pos,$fcutter_date,$cdate_pos);
                //dd($inline_cutter);
                //$cutter2 = self::cutter2($amask,$smask,$calla,$callno,$cutter2_pos);
                $cutter2 = self::cutter2($amask,$smask,$calla,$callno,$cutter2_pos);
                $cutter2_decimal = 0;
            }

            elseif($cdate_pos != false AND self::cutterCount($smask)==2 AND $cdate_pos > $inline_cutter_end)
            {
                $cutter2_pos = strpos($smask, 'DA', $cdate_pos);
                $inline_cutter = null;
                //$inline_cutter = self::postDateCutter($amask,$calla,$cut1pos,$fcutter_date,$cdate_pos);
                $inline_cutter = self::inline_cutter($amask,$calla,$cut1pos,$fcutter_date);
                //dd($inline_cutter);
                $cutter2 = self::cutter2($amask,$smask,$calla,$callno,$cutter2_pos);
                $cutter2_decimal_pos = strlen($cutter2) + $cutter2_pos+1;
                $cutter2_decimal = self::cutter2_decimal($amask,$smask,$calla,$callno,$cutter2_decimal_pos);
                //dd($cutter2_decimal);
                //dd($cutter2);
                $cutter2 = self::cutter2($amask,$smask,$calla,$callno,$cutter2_pos);
            }

            else
            {
               $cutter2_pos = false;
               $cutter2_decimal = 0;
               $cutter2 = 0;
            }

            
            if($cutter2_pos != false AND self::cutterCount($smask)==3)

            { 
                $cutter2_pos = $cdate_pos +6; //includes space and decimal
                
                //$cutter2 = self::cutter2($amask,$smask,$calla,$callno,$cutter2_pos);

                $cutter2_decimal_pos = strlen($cutter2) + $cutter2_pos;

                $spec_start = self::start_specification($amask,$smask);

            if($spec_start > $cutter2_decimal_pos)
            {

            $cutter2_decimal = self::cutter2_decimal($amask,$smask,$calla,$callno,$cutter2_decimal_pos);

            }

            else

            {
              //$cutter2_decimal =  0;
            }

            }

            else 

            {
                //$cutter2 = 0;
                //$cutter2_decimal = 0;
            }

    }
            
            $spec_start = self::start_specification($amask,$smask);

            //dd($spec_start);

            if($spec_start == 0) 
                { 
                    // There's no specification
                    $specification = 0;
                }

                else

            {
                $specification = self::specification($amask,$smask,$calla,$spec_start);
            }

            if($spec_start == -1) { $specification = 0; }

            //dd($cutter_date);

return "$subclass*$caption_integer*$caption_decimal*$caption_date*$caption_ordinal_num*$caption_ordinal_ind*$cutter1*$cutter1_decimal*$cutter_date*$inline_cutter*$inline_cutter_decimal*$cutter_date2*$cutter2*$cutter2_decimal*$specification";

    }



public static function subclass($amask,$calla)
    {
        $subclass=null;                             

        foreach($amask as $key=>$a)

        {
            if($key > 2) {break;}
            if($a == 'A')
            {
                $subclass .= $calla[$key];
            }

        }


        return $subclass;
    
    }

    public static function caption_integer($amask,$smask,$calla,$callno)
    {

        $lim = strlen($smask)-1;
        $flen = strlen(self::subclass($amask,$calla));
        $offs = $flen-1;
        $caption_integer = null;

        for($i=$offs; $i<=$lim; $i++)

        {
            if($smask[$i] == '_') {break;}
            if($smask[$i] == 'D') {break;}
            if($i > $lim) { break; }
            if($smask[$i] == 'I')

            {
                $caption_integer .= $callno[$i];
            }
        }

        //dd($caption_integer);

        return $caption_integer;
    
    }

    public static function caption_decimal($smask,$callno)
    {
        // Check for I.I pattern
        $cap_dec = strpos($smask,'IDI');

        if($cap_dec != false)
        {
            $offs = $cap_dec+2;
        

        $lim = strlen($smask)-1;

        $caption_decimal = null;

        for($i=$offs; $i<=$lim; $i++)

        {
            if($smask[$i] == '_') {break;}
            if($smask[$i] == 'A') {break;}
            if($smask[$i] == 'D') {continue;}
            if($i > $lim) { break; }
            if($smask[$i] == 'I')

            {
                $caption_decimal .= $callno[$i];
            }
        }

        return $caption_decimal;

    }

    else

    {
        return 0;
    }
    
    }


    public static function caption_date($smask,$calla,$cut1pos,$first_space)

    {
        // Start at the position after 1st space
        $bspace = $first_space+1;
        //dd($cut1pos);
        $cp_date = null;

        //
        for($i = $bspace; $i<=$cut1pos; $i++)

        {
            //dd($calla[$i] );
            if($smask[$i] == 'I')
                {
                    $cp_date .= $calla[$i];
                }
                else
                {
                    break;
                }
        }

        //dd(strlen($cp_date));

        if(strlen($cp_date)==4 AND is_numeric($cp_date))
        {

            if($cp_date[0] == 1 OR $cp_date[0] == 2)

            {
                return $cp_date;
            }
            else
            {
                return 0;
            }

        }

        else

        {
            return 0;
        }
    
    }

    public static function caption_ordinal($calla,$first_space,$cut1pos)

    {
        $bspace = $first_space+1;
        //dd($cut1pos);
        $cp_ordinal = null;

        for($i=$bspace; $i<=$cut1pos-1; $i++)

        {

                {
                    $cp_ordinal .= $calla[$i];
                }
        }

        if(isset($cp_ordinal) AND !is_numeric($cp_ordinal))
        

            {
                return $cp_ordinal;
            }

        else

        {
            return 0;
        }
    
    }


    public static function cutter1($amask,$calla,$cut1pos)
    {
    
        foreach($amask as $key=>$a)
        {
            if($key>$cut1pos)
            {
                if($a != 'A') {break;}
              
                 $cutter1[] = $calla[$key];
                
            }
        }


        if(isset($cutter1))
        {
            return implode("",$cutter1);
        }

        else
        {
            return 0;
        }
    
    }

    
    public static function cutter1_decimal($amask,$calla,$cut1pos)
    {
        $cd = self::cutter1($amask,$calla,$cut1pos);
        $cdl = strlen($cd);
        $start_point = $cut1pos+$cdl;
        //dd($amask);
        $cutter1d = null;
        foreach($amask as $key=>$a)
        {
            if($key > $start_point)
            {
                if($a == '_') {break;}
                if($a == 'A') {break;}
                if($a == 'I') 
                    {
                        $cutter1d .= $calla[$key];
                    }
            }
        }

        //dd($cutter1d);

        if(isset($cutter1d))

            { 
                return $cutter1d; 
            }

            else

            {
                return 0;
            }
    
    }

    public static function cutOneEnd($amask,$calla,$cut1pos)
    {
        // Returns position of the end of the first cutter including decimal
        $cd = self::cutter1_decimal($amask,$calla,$cut1pos);
        $cdl = strlen($cd);
        return $cut1pos+$cdl+3;
    }

    public static function inlineEnd($amask,$calla,$cut1pos,$cutter_date)
    {
        // Returns position of the end of the inline cutter including decimal
        $cd = self::inline_cutter_decimal($amask,$calla,$cut1pos,$cutter_date);
        $cdl = strlen($cd);
        return $cut1pos+$cdl+3;
    }

    public static function inline_cutter($amask,$calla,$cut1pos,$cutter_date)
    {
        //This is no cutter date condition, or cutter date is after inline cutter
        //For starting position here need to know the length of the cutter1 decimal

        $cd = self::cutter1_decimal($amask,$calla,$cut1pos,$cutter_date);
        $cdl = strlen($cd);
        $start_point = $cut1pos+$cdl+1;
        $icutter = null;
        foreach($amask as $key=>$a)
        {
            if($key > $start_point)
            {
                if($a == '_') {break;}
                if($a == 'A') 
                    {
                        $icutter .= $calla[$key];
                    }
            }
        }

        if(isset($icutter))

            { 
                return $icutter; 
            }

            else

            {
                return 0;
            }
    
    }

    public static function postDateCutter($amask,$calla,$cut1pos,$cutter_date,$cdate_pos)
    {
        //For starting position here need to know the length of the cutter1 decimal
     
        //If there is a cutter date, make sure you are beyond it
        if($cutter_date != 3000)
        {
            $skip = $cdate_pos+5;
        }
        else {
            $skip = 0;
        }
        //dd($cutter_date);
        //$cd = self::cutter1_decimal($amask,$calla,$cut1pos,$cutter_date);
        //$cdl = strlen($cd);
        $start_point = $skip;
        $icutter = null;
        foreach($amask as $key=>$a)
        {
            if($key > $start_point)
            {
                if($a == '_') {break;}
                if($a == 'A') 
                    {
                        $icutter .= $calla[$key];
                    }
            }
        }
        //dd($start_point);
        if(isset($icutter))

            { 
                return $icutter; 
            }

            else

            {
                return 0;
            }
    
    }


    public static function inline_cutter_decimal($amask,$calla,$cut1pos,$cutter_date)
    {
        //For starting position here need to know the length of the inline cutter

        //If there is a cutter date, make sure you are beyond it
        if($cutter_date != 3000)
        {
            $skip = 5;
        }
        else {
            $skip = 0;
        }
        $cd = strlen(self::cutter1_decimal($amask,$calla,$cut1pos));
        $ic = strlen(self::inline_cutter($amask,$calla,$cut1pos,$cutter_date));
        $cdl = $cd+$ic;
        $start_point = $cut1pos+$cdl+$skip;
        $icutterd = null;
        foreach($amask as $key=>$a)
        {
            if($key > $start_point)
            {
                if($a == '_') {break;}
                if($a == 'I') 
                    {
                        $icutterd .= $calla[$key];
                    }
            }
        }

        //dd($icutterd);

        if(isset($icutterd))

            { 
                return $icutterd; 
            }

            else

            {
                return 0;
            }
    
    }


    public static function cutter_date($amask,$calla,$smask,$cdate_pos)
    {
        
        
        //dd($cdate);

        if($cdate_pos != false)
        {
            foreach($amask as $key=>$a)
            {

                if($key<=$cdate_pos) {continue;}
                if($a == '_') {break;}
                if($a == 'D') {break;}
                $cutter_date[] = $calla[$key];
                
            }
            //dd($cutter_date);
             return implode("", $cutter_date);
        } 

        else

        {
            return 0;
        }
    
    }


    public static function cutter2($amask,$smask,$calla,$callno,$cutter2_pos)
    {
        //dd($cutter2_pos);
        foreach($amask as $key=>$a)
        {
            if($key<$cutter2_pos) { continue; }
            if($a == 'D') { continue; }
            //if($a == '_') { break; }
            if($a == 'I') { break; }
            $cutter2[] = $calla[$key];
        }

        if(isset($cutter2))

        {
            
            return implode("", $cutter2);
            //dd($b);
        }

        else

        {
            return 0;
        }
    
    }

    public static function cutter2_decimal($amask,$smask,$calla,$callno,$cutter2_decimal_pos)
    {
    
        foreach($amask as $key=>$a)
        {
            if($key < $cutter2_decimal_pos) { continue; }
            if($a == '_') { break; }
            $cutter2_decimal[] = $calla[$key];
        }

        if(isset($cutter2_decimal))

        {
            return implode("", $cutter2_decimal);
        }

        else

        {
            return 0;
        }

    
    }

    public static function specification($amask,$smask,$calla,$spec_start)
    {
        $spec = array();

        //dd($spec_start);

        foreach($amask as $key=>$a)

        {   
           
            if($key<=$spec_start) {continue;}
            if(end($spec) != '*' AND $a == "_") { $spec[]='*'; continue; }
            if($a == "-") { $spec[]='*'; continue; }
            if($a == ",") { $spec[]='*'; continue; }
            if($a == "/") { $spec[]='*'; continue; }
            if($a != 'I' AND $a != 'A') { continue; }

            if($a == 'I') {  $atype = 1; }
            if($a == 'A') {  $atype = 2; }

            if(isset($btype) AND $btype != $atype) {$spec[]='*';}

                $spec[] = $calla[$key];

            if($a == 'I') {  $btype = 1; }
            if($a == 'A') { $btype = 2; }
            

        }

        
        $specks = implode("",$spec);

        $specs = explode('*',$specks);

        $specification = null;

        foreach($specs as $key=>$s)

        {

            if(is_numeric($s))

            {
                $specification .= self::leadingZeros($s);

            }

            else

            {
               $specification .= $s; 
            }
        }

        //dd($specification);
        return $specification;
    
    }

    public static function leadingZeros($string)

    {
      $c = strlen($string);
      $missing = 7-$c;

      if($missing > 0) {

         for ($i=0; $i < $missing; $i++) 

         { 

            $zeros[] = 0; 

        } 

        $lzeros = implode("", $zeros);

        return "$lzeros$string";         
    }

    else

    {
        return "$string";
    } 

}

    

} // End of class
