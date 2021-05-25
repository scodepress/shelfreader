<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;


class ImpersonateController extends Controller
{
    public function index()

    {

    	$emails = \DB::table('users')->select('email','name')->orderBy('name')->get();

    	return view('admin.impersonate', compact('emails'));
    }

    public function store(Request $request)

    {
    	$this->validate($request, [
    		'email' => 'required|email|exists:users,email'
    	]);

    	$user = User::where('email', $request->email)->first();

    	session()->put('impersonate', $user->id);

    	return redirect('/home');
    }

    public function destroy()

    {
    	session()->forget('impersonate');

    	return redirect('/home');
    }
}
