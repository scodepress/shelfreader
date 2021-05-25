<?php

namespace App\Http\Controllers;

use App\Imports\ImportSortFile;
use App\Models\SortFile;
use App\Traits\SortFileTrait;
use App\Exports\SortFileExport;
use Illuminate\Http\Request;
use Symfony\Component\Console\Input\Input;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;


class SortFileController extends Controller
{
    use SortFileTrait;

    public function index()
    {

    	return view('sort_files.index');
    }

    public function store(Request $request)
    {
    	
    	
    }

     public function import()
    {
        $file = request()->file('file');


        // Get file headings
        $headings = (new HeadingRowImport)->toArray($file);

        //Create db column creation statements for temp table 
        $columns = $this->makeColumnsFromHeadings($headings);

        //Get array of data from data from imported file
        $items = Excel::toArray(new ImportSortFile(), request()->file('file'));


        //Create temporary table to hold data from from the imported file
        $this->makeCreateTableStatement($columns);

        // Create temporary table to hold the sortkeys that correspond to the data in file created in previous step
        $this->tempSortKeys();

        //Create associative array of from file data using headings as keys
        $fileData = $this->makeAssociativeArray($items,$headings);
       
        //Load file data in sort_files temp table
        $table_items = $this->loadSortFilesTable($fileData);


        //Get array of barcodes from $items array
        $barcodes = $this->getBarcodesFromItems($table_items);

        //Use array of items in sort_files table to create an array of sort keys for each entry
        $sort_keys = $this->createItemSortKeys($table_items);

        //Populate sortKey array with data for insertion into temp_sort_keys table
        $sortKeys = $this->createSortKeysArray($sort_keys,$barcodes);

        // Insert $sortKeys into temp_sort_Keys
        $this->insertKeysInTempSortKey($sortKeys);

        $orderedRows = $this->getOrderedData();

        $orderedRowsArray = $this->arrayFromCollection($orderedRows);

        $this->reloadSortFilesTable($orderedRowsArray);

        return Excel::download(new SortFileExport($headings), 'orderedFile.xls');

    }
}
