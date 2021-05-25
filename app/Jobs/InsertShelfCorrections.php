<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\ShelfError;

class InsertShelfCorrections implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user_id;
    public $barcode;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_id,$barcode)
    {
        $this->user_id =$user_id;
        $this->barcode =$barcode;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $shelf = new ShelfError;

        $shelf->user_id = $this->user_id;
        $shelf->date = date('Y-m-d');
        $shelf->barcode = $this->barcode;
        
        $shelf->save();

    }
}
