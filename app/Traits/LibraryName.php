<?php

namespace App\Traits;

trait LibraryName {
	
	public function library()

	{
		return \DB::table('institutions')
		->select('institution','library')
		->where('id', \Auth::user()->institution)
		->get();
	}

	public function uid()
	{
		return \Auth::user()->id;
	}
	
}