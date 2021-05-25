<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;

class MenusController extends Controller
{
    
    public function admin_menu()

    {
    	$amenu = Menu::adminMenu();

    	return view('menus.admin', compact('amenu'));
    }

    public function store_menu(Request $request)

    {
    	$route_name = $request['route_name'];

    	return redirect()->route($route_name);
    }
}
