<?php

namespace App\Http\Controllers;

use App\Models\Sort;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmailsController extends Controller
{

	public function create()
	{
		// Get user id and name
		$user_id = Auth::user()->id;
		$user_name = Auth::user()->name;

		return view('emails.create', compact('user_id','user_name'));
	}

	public function store_mail(Request $request)

	{
		$admin_email = DB::table('users')->where('id', 1)->pluck('email')[0];

		$this->validate($request, [

            'subject' => 'required',
            'body' => 'required',
        ]);

		$user_id = $request['user_id'];
		$user_name = $request['user_name'];
		$subject = $request['subject'];
		$content = $request['body'];
		$sender = Sort::getEmail($user_id)->email;
		$emails = [$admin_email];

		Mail::send('emails.send', ['title' => $subject,'content' => $content], function ($message) use ($subject,$sender,$content,$emails,$user_name)
        {
        	$message->setBody($content);

            $message->from($sender,  $user_name);
            
            $message->to($emails); 

            $message->subject($subject);


        });

		return redirect()->action('EmailsController@landing', ['title' => $subject, 'content' => $content]);
    }

    public function landing($title,$content)

    {

    	return view('emails.landing', compact('title','content'));
    }

		
	}

    
