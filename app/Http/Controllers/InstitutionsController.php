<?php

namespace App\Http\Controllers;

use App\Imports\ImportInventory;
use App\Models\Institution;
use App\Models\User;
use App\Models\MasterKey;
use App\Models\TempKey;
use App\Traits\LibraryManagementTrait;
use App\Traits\MasterKeyTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;


class InstitutionsController extends Controller
{
	use LibraryManagementTrait;
	use MasterKeyTrait;

    public function index()
	{
		$institutions = Institution::get();
		$users = User::get();
		$inventory_users = $this->inventoryUsers();

		// $p = "LibraryThing is a cataloging and social networking site for book lovers. LibraryThing helps you create and track a library-quality catalog of your media-books (along with movies and music) you own, have read, want to read, etc. Beyond your personal catalog, LibraryThing's social aspects show and aggregate tags, ratings, reviews, and Common Knowledge (facts about a book or author, like series and awards). Contributing your own tags, reviews, etc. improves cataloging data for everyone. LibraryThing connects people based on the books they share.";

		// $totals = $this->splitPea($p);
		// $p = $this->newParagraph($p);

		// ksort($totals);

		return view('institutions.create', compact('institutions','users','inventory_users'));

	}

	public function showUser($user_id)
	{
		$user_info = User::where('id',$user_id)->get();
		return view('institutions.show_user_form', compact('user_info'));
	}

	public function updateUser(Request $request)
	{
		$library_id = $request->library_id;
		$user_id = $request->user_id;
		$privs = $request->privs;

		$up = User::where('id', $user_id)->update(['institution' => $library_id,'privs'=>$privs]);

		return back();
	}

	public function libraryForm()
	{
		$lid = $this->nextLibraryId();

		return view('institutions.show_library_form', compact('lid'));
	}

	public function storeLibrary(Request $request)
	{
		$this->newLibrary($request->all());
		

		return back();

	}

	public function uploadFileForm()
	{
		$inventory_users = $this->inventoryUsers();
		return view('institutions.upload_form', compact('inventory_users'));
	}

	public function storeFile(Request $request)
	{
		$books = Excel::toArray(new ImportInventory(), $request->file('file'));
		$library_id = $request->libraryId;

		$created_array = $this->createInsertArray($books,$library_id);

		$entriesInMasterKeys = MasterKey::where('library_id', $library_id)->count();

		//dd($entriesInMasterKeys);

		if($created_array) {

			$this->createTempKeysTable();
			$isLoaded = $this->insertIntoTempTable($created_array, $library_id);


		}

	
			
			if($entriesInMasterKeys > 0)
			{
				MasterKey::selectIntoTempKeys($library_id);
				$this->deleteMasterKeys($library_id);
			}

			
			MasterKey::selectIntoMasterKeys($library_id);
		
			
			MasterKey::dropTempKeysTable();

			return back();
		}

		public function splitPea($paragraph)
		{
			$words = preg_split('/\s+|-|\(|\)|\.|\,/', $paragraph, -1, PREG_SPLIT_NO_EMPTY);


			foreach ($words as $key=>$w) {

				$a = strlen($w);

				$lengths[] = $a;
			}

			//array_count_values() returns an array using the values of array as keys and their frequency in array as values. 
			return array_count_values($lengths);

	
			
		}

		public function newParagraph($paragraph)
		{
			

			$words = preg_split('/\s+|-|\(|\)|\.|\,/', $paragraph, -1, PREG_SPLIT_NO_EMPTY);

			foreach($words as $key=>$w) {

				$a = strlen($w);

				if($key === 0) { $new_paragraph = "$w $a "; }

				else {
				
				$new_paragraph .= "$w"; 
				$new_paragraph .= " $a "; 
			}
		}

			return $new_paragraph;
		
		
		}

		public function wordCountReport($lengths)
		{
			//array_count_values() returns an array using the values of array as keys and their frequency in array as values.
			
			foreach($lengths as $key=>$l) {

				if($l === 1) {
					"<div>There is one word that has $key letter(s).</div>";
				} else {

					"<div>There are $l words that have $key letter(s).</div>";
				}
			}
		}


}
