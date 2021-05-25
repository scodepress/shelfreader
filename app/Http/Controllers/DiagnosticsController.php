<?php

namespace App\Http\Controllers;

use App\Models\BadBarcode;
use App\Models\UnprocessedCallnumbers;
use App\Models\Usage;
use App\Models\User;
use App\Models\MasterKey;
use Illuminate\Http\Request;

class DiagnosticsController extends Controller
{
	public function show($user_id=null,$date=null) {

		if($user_id != null && $date != null) {

			$badBarcodes =	BadBarcode::usersBadBarcodes($user_id, $date);

			$failedCallNumbers = UnprocessedCallnumbers::getUnprocessedCallNumbers($user_id,$date);

			$unRecordedScans = Usage::getUsersScans($user_id,$date);
			$name = User::where('id',$user_id)->pluck('name')[0];
			$uniqueUsages = Usage::uniqueScans($user_id,$date);
			$uniqueInventory = MasterKey::uniqueEntries($user_id,$date);
			
			$duplicateScans = Usage::getDuplicateScans($user_id,$date);
		}

		return view('diagnostics.show', compact('badBarcodes','date','failedCallNumbers','unRecordedScans','name','uniqueUsages','uniqueInventory','duplicateScans'));
	} 
}
