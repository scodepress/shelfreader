<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Callnumber;

class SortFile extends Model
{
    use HasFactory;

    public static function processCallnumber($callnumber)
    {
    
    
    
    }

    public static function createMirrorFile()
        {
            
            DB::unprepared(
            DB::raw("
            CREATE TEMPORARY TABLE temp_keys (
              id int(11) NOT NULL,
              callno varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
              title text COLLATE utf8mb4_unicode_ci NOT NULL,
              barcode varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
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

        

        public static function createTempSort()
        {
            
            DB::unprepared(
            DB::raw("
            CREATE TEMPORARY TABLE temp_sort (
              id int(11) NOT NULL,
              callno varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
              title text COLLATE utf8mb4_unicode_ci NOT NULL,
              barcode varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
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
}
