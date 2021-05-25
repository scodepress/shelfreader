<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Stack;


class InsertIntoStacks implements ShouldQueue
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
        $present = Stack::where('barcode',$this->barcode)->count();

        if($present === 0)
        {
            $stack = new Stack;

            $stack->barcode = $this->barcode;
            $stack->call_number = $this->callnum;
            $stack->title = $this->title;
            $stack->current_location = $this->current_location;
            $stack->home_location = $this->home_location;

            $stack->save();
        }

    }
}
