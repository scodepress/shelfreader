<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\User;
use App\Menu;
use Illuminate\Support\Facades\Route;

class MenuComposer
{
    /**
     * The user repository implementation.
     *
     * @var UserRepository
     */
    protected $users;

    /**
     * Create a new profile composer.
     *
     * @param  UserRepository  $users
     * @return void
     */
    public function __construct(User $users)
    {
        // Dependencies automatically resolved by service container...
        $this->users = $users;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {

        
        $amenu = \App\Menu::adminMenu();

        $view->with('amenu', $amenu);
    }
}