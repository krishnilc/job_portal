<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    //This method will show user registration form
    public function registration()
    {
        return view('front.account.registration');
    }

    //This method will handle user registration form submission
    public function registrationSubmit(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'confirm_password' => 'required|same:password'
        ]);
       
        return redirect()->route('account.registration')->with('success', 'Registration successful!');
    }

    //This method will show user login form
    public function login()
    {
        return view('front.account.login');
    }
}
