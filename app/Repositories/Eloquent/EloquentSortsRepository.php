<?php

namespace App\Repositories\Eloquent;

use App\Models\Preport;
use App\Repositories\Contracts\SortsRepository;
use App\Repositories\RepositoryAbstract;
use App\Models\Sort;

class EloquentSortsRepository extends RepositoryAbstract implements SortsRepository
{

	public function entity()

	{
		return Sort::class;
	}

	public function find($id)

	{
		
	}

	public function check_preport($barcode)
    {
        return \DB::table('preports')
        ->select('barcode')
        ->where('barcode',$barcode)
        ->where('user_id',\Auth::id())
        ->count();

    }

    public function insert_preport($barcode,$title,$callnum,$current_location)

    {
        $preport = new Preport;

        $preport->user_id = \Auth::id();
        $preport->barcode = $barcode;
        $preport->title = $title;
        $preport->callnum = $callnum;
        $preport->location_id = $current_location;

        $preport->save();
    }

	public function insertBook($new_id,$barcode,$callno,$title,$gpos)
{

		$sort = new Sort;

        $sort->id = $new_id;
        $sort->user_id = \Auth::id();
        $sort->barcode = $barcode;
        $sort->title = $title;
        $sort->callno = $callno;
        $sort->position = $new_id;
        $sort->cposition = $gpos;

        $sort->save();
}

public function lastCallnumber()
{
    return \DB::table('sorts')
    ->select('callno')
    ->where('user_id',\Auth::id())
    ->orderByDesc('id')
    ->take(1)
    ->first();
}

}