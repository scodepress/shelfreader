<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    public static function adminMenu()
    {
    	foreach( \Route::getRoutes() as $route){
            $data = explode('@', $route->getName());
           
            if($route->getPrefix() ===  "/admin" && in_array('GET', $route->methods())) 
            {
                $menu[] = $route->getName();
            }
        }

        return $menu;
    }
}
