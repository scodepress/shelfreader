<?php

namespace App\Repositories\Eloquent;

use App\Models\FullPreport;
use App\Models\FullShelf;
use App\Models\FullSort;
use App\Models\FullSortkey;
use App\Repositories\Contracts\FullShelfRepository;
use App\Repositories\RepositoryAbstract;
use App\Models\Section;
use App\Models\Shelfend;
use App\Models\Sort;

class EloquentFullShelfRepository extends RepositoryAbstract implements FullShelfRepository
{

	public function entity()

	{
		return FullShelf::class;
	}

    

	public function allLive()

	{
		return $this->entity->get();
	}

	public function find($id)

	{
		return $this->entity->get();
	}

	public function get_sections()
    {
        return Section::all()->where('user_id',\Auth::id())->sortBy('position');
    }

	public function count_sections()
    {
        return count($this->get_sections());
    }

    public function update_shelfend_left_move($position,$destination) // param is position of book that is moving left

    {
        // Increment any shelfends < this book's original position and right of its new position
        Shelfend::where('position', '<', $position)->where('position', '>', $destination)->increment('position',1);

        // Decrememnt any shelfend >= book's original position
        Shelfend::where('position', '>', $position)->increment('position',1);
    }

    public function update_shelfend_right_move($position,$destination) // param is position of book that is moving right

    {
        // Increment any shelfends > book's new position
        Shelfend::where('position', '>', $destination)->increment('position',1);

        // Decrememnt any shelfend right of this book's original position AND left of its new position
        Shelfend::where('position', '>', $position)->where('position', '<', $destination)->decrement('position',1);

    }

    public function move_is_shelfend()

    {
    	// Special case if book to be moved is shelfend and moving to new shelf
    }

    public function get_shelf_number($position)

    {
    	// Find which shelf a book is on -- count the number of shelf ends <= to position 
    	// Works for current position and destination position

    	return \DB::table('shelfends')
    	->select('id')
    	->where('position', '<', $position)
    	->where('user_id',\Auth::id())
    	->count();

    }

    public function get_section_number($position)

    {
    	// Find which shelf a book is on -- count the number of shelf ends <= to position 
    	// Works for current position and destination position

    	return \DB::table('sections')
    	->select('id')
    	->where('position', '<', $position)
    	->where('user_id',\Auth::id())
    	->count();

    }

    public function bend()
    {
        return Shelfend::where('user_id',\Auth::id())->where('position','>',1)->pluck('position')->toArray();
    }

    public function send()
    {
        return Section::all()->where('user_id',\Auth::id())->where('position','>',1)->sortBy('position')->pluck('position')->toArray();
    }

    public function check_preport($barcode)
    {
        return \DB::table('full_preports')
        ->select('barcode')
        ->where('barcode',$barcode)
        ->where('user_id',\Auth::id())
        ->count();

    }

    public function insert_preport($barcode,$title,$callnum,$current_location)

    {
        $preport = new FullPreport;

        $preport->user_id = \Auth::id();
        $preport->barcode = $barcode;
        $preport->title = $title;
        $preport->callnum = $callnum;
        $preport->location_id = $current_location;

        $preport->save();
    }

    public function insertFullKey($amask,$smask,$callno,$calla,$barcode)
{
    $pre_sort_key = Sort::pMask($amask,$smask,$callno,$calla);

            $sort_key = explode("*", $pre_sort_key);

            $prefix = trim($sort_key[0]);
            $tp1 = trim($sort_key[1]);
            $tp2 = trim($sort_key[2]);
            $pre_date = trim($sort_key[3]);
            $pvn = trim($sort_key[4]);
            $pvl = trim($sort_key[5]);
            $cutter = trim($sort_key[6]);
            $pcd = trim($sort_key[7]);
            $cutter2 = trim($sort_key[8]);
            $pcd2 = trim($sort_key[9]);
            $part1 = trim($sort_key[10]);
            $part2 = trim($sort_key[11]);
            $part3 = trim($sort_key[12]);
            $part4 = trim($sort_key[13]);
            $part5 = trim($sort_key[14]);
            $part6 = trim($sort_key[15]);
            $part7 = trim($sort_key[16]);


        // Insert this into sortkeys table

            $sort = new FullSortkey;

            $sort->user_id = \Auth::id(); 
            $sort->barcode = $barcode;
            $sort->callno = $callno;
            $sort->prefix = $prefix;
            $sort->tp1 = $tp1;
            $sort->tp2 = $tp2;
            $sort->pre_date = $pre_date;
            $sort->pvn = $pvn;
            $sort->pvl = $pvl;
            $sort->cutter = $cutter;
            $sort->pcd = $pcd;
            $sort->cutter2 = $cutter2;
            $sort->pcd2 = $pcd2;
            $sort->part1 = $part1;
            $sort->part2 = $part2;
            $sort->part3 = $part3;
            $sort->part4 = $part4;
            $sort->part5 = $part5;
            $sort->part6 = $part6;
            $sort->part7 = $part7;

            $sort->save();

}
   
}