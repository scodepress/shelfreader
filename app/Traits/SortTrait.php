<?php

namespace App\Traits;

use App\Models\FullShelf;
use App\Models\GulpTest;
use App\Models\Report;
use App\Models\Callnumber;

trait SortTrait
{

	public function barcodeNumeric($barcode)
	{
		$status = 1; 

		if(!is_numeric($barcode))

			{ $status = 0; }

		return $status;
	}

	public function barcodeLength($barcode)
	{
		$status = 1; 

		$chars = strlen($barcode);

		if($chars != 12 AND $chars != 14)

			{ $status = 0; }

		return $status;
	}

	public function barcodeEmpty($barcode)

	{
		$status = 1;

		if(empty($barcode))

			{ $status = 0; }

		return $status;
	}

	public function gulpResponse($barcode)
	{

		$response = GulpTest::makeResponse($barcode);

		return $response;

	}

	public function responseInfo($response,$barcode)
	{
		return GulpTest::itemInfo($response,$barcode);
	}

	public function title($response,$barcode)
	{
		return self::responseInfo($response,$barcode)[0];

	}

	public function call_num($response,$barcode)
	{
		return self::responseInfo($response,$barcode)[1];

	}

	public function home_location($response,$barcode)
	{
		return self::responseInfo($response,$barcode)[3];

	}

	public function current_location($response,$barcode)
	{
		return self::responseInfo($response,$barcode)[4];

	}

	public function updateReport($barcode)
	{
		$shelf = Report::where('user_id',\Auth::id())->where('barcode',$barcode);
		$shelf->shelf = 1;
		$shelf->save();
	}

	public function insertReport($barcode)
	{

		$upreport = self::updateReport('barcode');

		$nid = \DB::table($this->table)->select('id')->where('user_id',  \Auth::id())->count();

		$books = FullShelf::getOrder(\Auth::id()); 

		$gpos = FullShelf::bookPosition($books,$barcode);

		$full = new FullShelf;

		$full->id = $nid+1;
		$full->user_id = \Auth::id();
		$full->barcode = $barcode;
		$full->title = $title;
		$full->callno = $callnum;
		$full->position = $nid+1;
		$full->cposition = $gpos;

		$full->save();

	}

	public function sortKeyInfo($callnumber)
	{
		return Callnumber::make_key($callno);
	}
	
}