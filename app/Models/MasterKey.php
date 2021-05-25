<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use app\Models\MasterKey;

class MasterKey extends Model
{
    use HasFactory;

      public static function getOrder($library_id)

        {
            return \DB::table('master_keys')
            ->select('barcode','callno')
            ->where('library_id', '=', $library_id)
            ->orderBy('prefix')
            ->orderBy('tp1')
            ->orderBy('tp2')
            ->orderBy('pre_date')
            ->orderBy('pvn')
            ->orderBy('pvl')
            ->orderBy('cutter')
            ->orderBy('pcd')
            ->orderBy('cutter_date')
            ->orderBy('inline_cutter')
            ->orderBy('inline_cutter_decimal')
            ->orderBy('cutter2')
            ->orderBy('pcd2')
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

        public static function getFullShelfOrder($library_id)

        {
            return \DB::table('master_keys')
            ->select('*')
            ->where('library_id', '=', $library_id)
            ->orderBy('prefix')
            ->orderBy('tp1')
            ->orderBy('tp2')
            ->orderBy('pre_date')
            ->orderBy('pvn')
            ->orderBy('pvl')
            ->orderBy('cutter')
            ->orderBy('pcd')
            ->orderBy('cutter_date')
            ->orderBy('inline_cutter')
            ->orderBy('inline_cutter_decimal')
            ->orderBy('cutter2')
            ->orderBy('pcd2')
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

        public static function bookPosition($library_id,$callnumber)

        {

        	$books = self::getOrder($library_id);

            if(count($books))

            foreach($books as $key=>$b)

            {
                if($b->callno == $callnumber) { break; }
            }

            return $key+1;

        }

        public static function setPosition($newBookPosition,$callnumber,$library_id)
        {
        	MasterKey::where('callno', $callnumber)
        	->where('library_id', $library_id)
        	->update(['position' => $newBookPosition]);
        }

        public static function reOrder($newBookPosition,$callnumber,$library_id)
        {
        	MasterKey::where('callno', '!=', $callnumber)
        	->where('position','>=', $newBookPosition)
        	->where('library_id', $library_id)
        	->increment('position', 1);
        }

        public static function createTempKeys()
        {
            $drop = self::dropTempKeysTable();
            
            DB::unprepared(
            DB::raw("
            CREATE TEMPORARY TABLE temp_keys (
              id int(11) NOT NULL,
              position bigint(20) NOT NULL,
              library_id bigint(11) NOT NULL,
              title text COLLATE utf8mb4_unicode_ci NOT NULL,
              barcode varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
              callno varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
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
              part1 varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
              part2 varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
              part3 varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
              part4 varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
              part5 varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
              part6 varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
              part7 varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
              created_at timestamp NULL DEFAULT NULL,
              updated_at timestamp NULL DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            
            ALTER TABLE temp_keys
              ADD PRIMARY KEY (id);

            ALTER TABLE temp_keys
              MODIFY id int(11) NOT NULL AUTO_INCREMENT;
            COMMIT;

            ")
                );
                
        
        }

        public static function dropTempKeysTable()
        {
        
            DB::unprepared(
                DB::raw("
                    DROP TABLE IF EXISTS temp_keys ;
                ")
                );
        
        }

        public static function insertIntoTempKeys($contents_of_file)
        {
            DB::table('temp_keys')
            ->insert($contents_of_file);
        
        }

        public static function libraryMasterKeys($library_id)
        {
        
            return MasterKey::where('library_id', $library_id)
            ->select(  
                        'position',
                        'library_id',
                        'title',
                        'barcode',
                        'callno',
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
                        'part1',
                        'part2',
                        'part3',
                        'part4',
                        'part5',
                        'part6',
                        'part6',
                        'part7'
                    )->get()->toArray();
        
        }

        public static function libraryTempKeys($library_id)
        {
            $mkeys = DB::table('temp_keys')
            ->distinct()
            ->select(
                        'position',
                        'library_id',
                        'title',
                        'barcode',
                        'callno',
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
                        'part1',
                        'part2',
                        'part3',
                        'part4',
                        'part5',
                        'part6',
                        'part6',
                        'part7',
                        'created_at'
                        )
                        ->orderBy('prefix')
                        ->orderBy('tp1')
                        ->orderBy('tp2')
                        ->orderBy('pre_date')
                        ->orderBy('pvn')
                        ->orderBy('pvl')
                        ->orderBy('cutter')
                        ->orderBy('pcd')
                        ->orderBy('cutter_date')
                        ->orderBy('inline_cutter')
                        ->orderBy('inline_cutter_decimal')
                        ->orderBy('cutter2')
                        ->orderBy('pcd2')
                        ->orderBy("part1")
                        ->orderBy("part2")
                        ->orderBy("part3")
                        ->orderBy("part4")
                        ->orderBy("part5")
                        ->orderBy("part6")
                        ->orderBy("part7")
                        ->orderBy('created_at')
                        ->where('library_id', $library_id)
                        ->get();

                foreach($mkeys as $key=>$m) {
                
                    $arr[] = [
                         
                        'position' => $key+1,
                        'library_id' => $library_id,
                        'title' => $m->title,
                        'barcode' => $m->barcode,
                        'callno' => $m->callno,
                        'prefix' => $m->prefix,
                        'tp1' => $m->tp1,
                        'tp2' => $m->tp2,
                        'pre_date' => $m->pre_date,
                        'pvn' => $m->pvn,
                        'pvl' => $m->pvl,
                        'cutter' => $m->cutter,
                        'pcd' => $m->pcd,
                        'cutter_date' => $m->cutter_date,
                        'inline_cutter' => $m->inline_cutter,
                        'inline_cutter_decimal' => $m->inline_cutter_decimal,
                        'cutter_date2' => $m->cutter_date2,
                        'cutter2' => $m->cutter2,
                        'pcd2' => $m->pcd2,
                        'part1' => $m->part1,
                        'part2' => $m->part2,
                        'part3' => $m->part3,
                        'part4' => $m->part4,
                        'part5' => $m->part5,
                        'part6' => $m->part6,
                        'part7' => $m->part7
                    ];
            }

            return $arr;
        
        }

        public static function selectIntoTempKeys($library_id)
        {
            $libraryKeys = self::libraryMasterKeys($library_id);

            DB::table('temp_keys')->insert($libraryKeys);
         
        }

        public static function selectIntoMasterKeys($library_id)
        {
            $tempKeys = self::libraryTempKeys($library_id);


            DB::table('master_keys')->insert($tempKeys);         
        }

	public static function uniqueEntries($user_id,$date) {

		return DB::table('master_keys')
			->select('barcode')
			->whereDate('created_at',$date)
			->where('user_id', $user_id)
			->distinct('barcode')
			->count();
	}
}
