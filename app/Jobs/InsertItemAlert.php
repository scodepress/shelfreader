<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Auth;

use App\Models\ItemAlert;

use App\Models\User;

class InsertItemAlert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $barcode;
    public $callnum;
    public $title;
    public $current_location;
    public $home_location;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($barcode,$callnum,$title,$current_location,$home_location)
    {
        $this->barcode = $barcode;
        $this->callnum = $callnum;
        $this->title = $title;
        $this->current_location = $current_location;
        $this->home_location = $home_location;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $present = ItemAlert::where('barcode',$this->barcode)->where('user_id',Auth::user()->id)->count();

        if($present === 0)
        {
            $alert = new ItemAlert;

            $alert->user_id = Auth::user()->id;
            $alert->barcode = $this->barcode;
            $alert->call_number = $this->callnum;
            $alert->title = $this->title;
            $alert->current_location = $this->current_location;
            $alert->home_location = $this->home_location;

            $alert->save();
        }
    }
}
