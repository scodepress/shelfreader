<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;

class ShadowsController extends Controller
{
    public function show($barcode)

    {

    	$unknown = App\Shadow::getUnknown($barcode);
    	$titled = App\Shadow::getTitled($barcode);

    	
    }
}
