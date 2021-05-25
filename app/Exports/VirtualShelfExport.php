<?php

namespace App\Exports;

use App\Models\MasterKey;
use App\Models\Sort;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VirtualShelfExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return DB::table('sorts')
        ->select('cposition','callno','barcode','title','created_at')
        ->where('user_id', Auth::user()->id)
        ->orderBy('cposition')
        ->get();
    }

    public function headings(): array
    {
        return [
            'position','callno','barcode','title','created_at'
        ];

        
    }

}