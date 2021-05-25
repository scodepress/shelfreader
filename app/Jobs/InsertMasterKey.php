<?php

namespace App\Jobs;

use App\Models\MasterKey;
use App\Traits\MasterKeyTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class InsertMasterKey implements ShouldQueue
{
    use MasterKeyTrait;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    	public $user_id;
        public $callnumber;
        public $barcode;
        public $title;
        public $pre_sort_key;
        public $library_id;
        


    public function __construct($user_id,$callnumber,$barcode,$title,$pre_sort_key,$library_id)
    {
        $this->user_id = $user_id;
        $this->callnumber = $callnumber;
        $this->barcode = $barcode;
        $this->title = $title;
        $this->library_id = $title;
        $this->pre_sort_key = $pre_sort_key;
        $this->library_id = $library_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sort_key = explode("*", $this->pre_sort_key);

        $position = MasterKey::where('library_id', $this->library_id)->count() + 1;

        foreach($sort_key as $key=>$p) {

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

        }

         $sort = new MasterKey;

            $sort->position = $position;
            $sort->user_id = $this->user_id;
            $sort->library_id = $this->library_id;
            $sort->title = $this->title;
            $sort->barcode = $this->barcode;
            $sort->callno = $this->callnumber; 
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
        
    }
}
