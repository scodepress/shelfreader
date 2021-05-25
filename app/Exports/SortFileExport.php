<?php

namespace App\Exports;

use App\Models\SortFile;
use App\Models\TempSortKey;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SortFileExport implements FromCollection, WithHeadings
{
    protected $headings;

    public function __construct($headings) {
     $this->headings = $headings;
   
 }
    
    public function collection()
    {
        return SortFile::get();
    }

    public function headings(): array
    {
        $headers = $this->headings[0][0];
        foreach($headers as $key=>$h){
        if($key === 0) { $cols[] = 'position'; }
        $cols[] = $h;
        }

        return $cols;
    }

}