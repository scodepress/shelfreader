<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchesController extends Controller
{
    public function create()

    {
    	

    	return view('searches.create');
    }

    public function store(Request $request)

    {		
    	$word = $request['word'];

    	return redirect()->action('SearchesController@show', ['word' => $word]);
    }

    public function show($word)

    {
    	$main_table = \App\Search::getMain()->main_table; 

    	$results = \App\Search::searchShelfs($word,$main_table);
    	$n = count($results);

    	return view('searches.show', compact('results','n','word'));
    }
}
