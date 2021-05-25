<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\MasterKey;

class MasterKeyOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $callnumber;
    public $library_id;
     
    public function __construct($callnumber,$library_id)
    {
        $this->callnumber = $callnumber;
        $this->library_id = $library_id;
    }

 
    public function handle()
    {
        $newBookPosition = MasterKey::bookPosition($this->library_id,$this->callnumber);

        $setPosition = MasterKey::setPosition($newBookPosition,$this->callnumber,$this->library_id);

        $reorder = MasterKey::reOrder($newBookPosition,$this->callnumber,$this->library_id);


    }
}
