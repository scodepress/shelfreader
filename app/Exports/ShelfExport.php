<?php

namespace App\Exports;

use App\Models\MasterKey;
use App\Models\Sort;
use App\Traits\MasterKeyTrait;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ShelfExport implements FromCollection, WithHeadings
{
	use MasterKeyTrait;
    


    public function collection()
    {

        return MasterKey::select('position','callno','barcode','title','created_at')
        ->where('library_id',$this->userLibraryId())
        ->orderBy('position')
        ->get();
    }

    public function headings(): array
    {
        return [
            'position','callno','barcode','title','created_at'
        ];

        
    }

}
