<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReferencesController extends Controller
{
    public function show()

    {

    	$books = \App\Reference::getBooks();

    	$subs = \App\Reference::getSubs();

    	return view('references.show', compact('books','subs')); 
    }
}
