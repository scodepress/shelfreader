<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use App\Models\Institution;
use App\Models\User;
use App\Models\SortFile;
use App\Models\MasterKey;
use App\Models\TempSortKey;
use App\Imports\ImportSortFile;
use App\Models\Callnumber;
use Illuminate\Support\Facades\DB;

trait SortFileTrait {

	public function getKeys($callnumber)
	{
		$pre_sort_key = Callnumber::make_key($callnumber);

    	return explode("*", $pre_sort_key);
 }

	public static function saveKeys()
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
            

            $test->save();


	}

	public function makeColumnsFromHeadings(array $headings)
        {
        	$columns = '';
        	foreach($headings as $key=>$he) {

        		foreach($he as $key2=>$hd) {

        		foreach($hd as $key3=>$h) {	
              $column_count = $key3+1;
              if($key3 === 0) {

        		$columns .= "callnumber varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,";

          } elseif($key3 === 1) {

            $columns .= "barcode varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,";

          } else {

            $columns .= "column_$column_count varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,";

          }

        	}
        	}
        }
        
         	return $columns;
        }

  public function dropTable($table_name)
  {
        DB::unprepared(
        DB::raw(" DROP TABLE IF EXISTS $table_name;")
        );
     
  }

   public function makeCreateTableStatement($columns)
   {    
       
            DB::unprepared(
                DB::raw("
                    DROP TABLE IF EXISTS sort_files;
                ")
                );
          
            DB::unprepared(
            DB::raw("
            CREATE TEMPORARY TABLE sort_files (
              position int(11) NOT NULL,
              $columns
              created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            
            ALTER TABLE sort_files
              ADD PRIMARY KEY (position);

            ALTER TABLE sort_files
              MODIFY position int(11) NOT NULL AUTO_INCREMENT;
            COMMIT;

            ")
                );

            
   
   }  

   public function tempSortKeys()
   {
    DB::unprepared(
                DB::raw("
                    DROP TABLE IF EXISTS temp_sort_keys ;
                ")
                );

        DB::unprepared(
            DB::raw("
            CREATE TEMPORARY TABLE temp_sort_keys (
              position int(11) NOT NULL,
              barcode varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
              prefix varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
              tp1 int(11) NOT NULL,
              tp2 decimal(15,15) NOT NULL,
              pre_date varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
              pvn int(11) DEFAULT NULL,
              pvl varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              cutter varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
              pcd decimal(15,15) NOT NULL,
              cutter_date int(11) NULL DEFAULT NULL,
              inline_cutter varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
              inline_cutter_decimal decimal(15,15) NOT NULL,
              cutter_date2 int(11)  NULL DEFAULT NULL,
              cutter2 varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
              pcd2 decimal(15,15) NOT NULL,
              part1 varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            ALTER TABLE temp_sort_keys
              ADD PRIMARY KEY (position);

            ALTER TABLE temp_sort_keys
              MODIFY position int(11) NOT NULL AUTO_INCREMENT;
            COMMIT;

            ")
                );
   }

 
   public function createAssociativeDataArray($file)
   {
      ini_set('auto_detect_line_endings', true);

            $file = fopen($file, 'r') or die('Unable to open file!');

            $fileData = array();
            $header = null;

            while(($row = fgetcsv($file)) !== false){
                if($header === null){
                    $header = $row;
                    continue;
                }

                $newRow = array();
                for($i = 0; $i<count($row); $i++){

                    $newRow[$header[$i]] = $row[$i];
                    
                    
                }

                

                $fileData[] = $newRow;
            }

    
            fclose($file);

      return $fileData;
   }

   public function getBarcodesFromItems($items)
   {
       foreach($items as $key=>$i) {
      
         $barcodes[] = $i->barcode;
   
     }

            return $barcodes;
   }

   

   public function createItemSortKeys($items)
   {
        foreach($items as $key=>$i) {

            $sort_keys[] = self::getKeys($i->callnumber); 
          }

     

        return $sort_keys;    

   }

   
   public function sortColumnArray()
   {
        $sort_columns = [
                'prefix',
                'tp1',
                'tp2',
                'pre_date',
                'pvn',
                'pvl',
                'cutter',
                'pcd',
                'cutter_date',
                'inline_cutter',
                'inline_cutter_decimal',
                'cutter_date2',
                'cutter2',
                'pcd2',
                'part1'
                ];

                return $sort_columns;
   }

   public function createSortKeysArray($sort_keys,$barcodes)
   {    
           $sort_columns = self::sortColumnArray();

            foreach($sort_keys as $key1=>$sort_keys) {
             
                foreach($sort_keys as $key=>$s) {

                    if($sort_columns[$key] === 'tp2' || $sort_columns[$key] === 'pcd'  
                        || $sort_columns[$key] === 'inline_cutter_decimal' || $sort_columns[$key] === 'pcd2' )
                        { $s = ".$s"; }
                    if($key === 0) {
                      $newRow['barcode'] = $barcodes[$key1];  
                    }
                $newRow[$sort_columns[$key]] = $s;
            
                }

                $sortKeys[] = $newRow;
            }


        return $sortKeys;

    }

    public function makeAssociativeArray($arr,$headings)
    {
        $headers = $headings[0][0];

        $arr = $arr[0];


        foreach($arr as $key1=>$sort_keys) {

          if($key1 === 0) { continue; }
             
                foreach($headers as $key=>$h){
                  if($key === 0) {
                  $newRow['callnumber'] = $sort_keys[$key]; 
                  } elseif($key === 1 ) {
                   $newRow['barcode'] = $sort_keys[$key]; 
                  } else {
                    $colnumber = $key+1;
                    $colname = "Column_$colnumber";
                   $newRow[$colname] = $sort_keys[$key]; 
                  }
                }
                
            
                $fileData[] = $newRow;

                
            }


        return $fileData;
    }

    public function arrayFromCollection($orderedRows)
    {
      foreach($orderedRows as $key=>$or) {

        foreach($or as $akey => $o) { 

          $newRow[$akey] = $o;
        }

          $insertArray[] = $newRow;
      }

        return $insertArray;
    }

    

    public function insertKeysInTempSortKey($sortKeys)
    {

      DB::table('temp_sort_keys')->insert($sortKeys);
    }

    public function loadSortFilesTable($fileData)
   {
      DB::table('sort_files')->truncate();
      DB::table('sort_files')->insert($fileData);

        $items = SortFile::all();

        return $items;    
   }


   public function reloadSortFilesTable($orderedRows)
    {
      //dd($orderedRows);
        DB::table('sort_files')->truncate();
        DB::table('sort_files')->insert($orderedRows);
    }

    public function getOrderedData()
    { 
      return DB::table('temp_sort_keys as t')
        ->join('sort_files as s','s.barcode','t.barcode')
        ->select('s.*') // Select all columns from sort_files table
            ->orderBy('t.prefix')
            ->orderBy('t.tp1')
            ->orderBy('t.tp2')
            ->orderBy('t.pre_date')
            ->orderBy('t.pvn')
            ->orderBy('t.pvl')
            ->orderBy('t.cutter')
            ->orderBy('t.pcd')
            ->orderBy('t.cutter_date')
            ->orderBy('t.inline_cutter')
            ->orderBy('t.inline_cutter_decimal')
            ->orderBy('t.cutter2')
            ->orderBy('t.pcd2')
            ->orderBy("t.part1")
            ->get();
    }



}
