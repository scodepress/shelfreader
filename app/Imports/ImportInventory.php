<?php

namespace App\Imports;


use App\Models\MasterKey;

use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;

  

class ImportInventory implements ToModel

{

    public function model(array $row)

    {

        return new MasterKey([

            'position' => $row[0],
            'library_id' => $row[1], 
            'title' => $row[2],
            'barcode' => $row[3],
            'callno' => $row[4],
            'prefix' => $row[5],
            'tp1' => $row[6],
            'tp2' => $row[7],
            'pre_date' => $row[8],
            'pvn' => $row[9],
            'pvl' => $row[10],
            'cutter' => $row[11],
            'pcd' => $row[12],
            'cutter_date' => $row[13],
            'inline_cutter' => $row[14],
            'inline_cutter_decimal' => $row[15],
            'cutter_date2' => $row[16],
            'cutter2' => $row[17],
            'pcd2' => $row[18],
            'part1' => $row[19],
            'part2' => $row[20],
            'part3' => $row[21],
            'part4' => $row[22],
            'part5' => $row[23],
            'part6' => $row[24],
            'part7' => $row[25]

        ]);

    }

    public function chunkSize(): int
    {
        return 1000;
    }

}