<?php

namespace App\Http\Controllers;

use App\Exports\ShelfExport;
use App\Exports\FullShelfExport;
use App\Exports\VirtualShelfExport;
use App\Models\MasterKey;
use App\Traits\MasterKeyTrait;
use App\Traits\LibraryManagementTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class MasterKeyController extends Controller
{
    use MasterKeyTrait;
    use LibraryManagementTrait;

    public function export() 
    {
        return Excel::download(new ShelfExport, 'inventory.xls');
  
    }

    public function exportShelfList()
    {
        return Excel::download(new VirtualShelfExport, 'shelf_list.xls');
    }

    public function truncate(Request $request)
    {
    	$this->deleteMasterKeys($request->library_id);

    	return back();

    }

    public function masterKeyConfirm($library_id)
    {

    	return view('master_key.master_key_confirm', compact('library_id'));
    }

     public function adminExport(Request $request) 
    {
        $library_id = $request->libraryId;

        return Excel::download(new FullShelfExport($library_id), 'inventory.xls');
  
    }
}
