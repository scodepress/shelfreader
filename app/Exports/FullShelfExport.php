<?php

namespace App\Exports;

use App\Models\MasterKey;
use App\Models\Sort;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FullShelfExport implements FromCollection
{
    public $library_id;

    public function __construct($library_id) {

        $this->library_id = $library_id;
    }

    public function collection()
    {
        return MasterKey::select(
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
            'part7')
        ->where('library_id',$this->library_id)
        ->orderBy('position')
        ->get();
    }

}
