<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    //This method will show user registration form
    public function registration()
    {
        return view('front.account.registration');
    }

    //This method will save user registration data to database
    public function processRegistration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:5|same:confirm_password',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->passes()) {
            $user = new User();

            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password); // Hash the password before saving
            $user->save();

            session()->flash('success', 'Registration successful! ');

            return response()->json([
                'status' => true,
                'errors' => []
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    //This method will show user login form
    public function login()
    {
        return view('front.account.login');
    }

    //This method will authenticate user login credentials
    public function authenticate(Request $request)
    {
        // Authentication logic will go here
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->passes()) {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                // Authentication passed...
                return redirect()->route('account.profile');
            } else {
                return redirect()->route('account.login')
                    ->with('error', 'Invalid credentials. Please try again.');
            }
        } else {
            return redirect()->route('account.login')
                ->withErrors($validator)
                ->withInput($request->only('email')); // Redirect back with validation errors and old input
        }
    }

    //This method will show user profile page
    public function profile()
    {
        $id = Auth::user()->id;
        // dd($id); // Debugging statement to check if the user ID is being retrieved correctly

        // $user = User::where('id', $id)->first();
        $user = User::find($id);
        //dd($user); // Debugging statement to check if the user data is being retrieved correctly

        return view('front.account.profile', [
            'user' => $user
        ]);
    }

    public function updateProfile(Request $request)
    {
        $id = Auth::user()->id;

        // Validation rules for profile update
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:5|max:20',
            'email' => 'required|email|unique:users,email,' . $id . ',id', // Ensure email is unique except for the current user
            'mobile' => 'required|digits:7',
            // 'password' => 'nullable|min:5|same:confirm_password',
            // 'confirm_password' => 'nullable|same:password',
        ]);

        if ($validator->passes()) {
            $user = User::find($id);

            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->designation = $request->designation;

            $user->save();

            session()->flash('success', 'Profile updated successfully!');

            return response()->json([
                'status' => true,
                'errors' => []
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
        public function logout()
    {
        Auth::logout();
        return redirect()->route('account.login');
    }

    public function updateProfilePic(Request $request)
    {
        
        $id = Auth::user()->id;

        $validator = Validator::make($request->all(), [
            'profile_pic' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->passes()) {
            $user = User::find($id);

            // Handle file upload
            if ($request->hasFile('profile_pic')) {
                $file = $request->file('profile_pic');
                $filename = time() . '_' . $file->getClientOriginalName();
                $filePath = public_path('profile_pic/');
                $file->move($filePath, $filename);

                // Update user's profile picture path in the database
                $user->profile_pic = 'profile_pic/' . $filename;
                $user->save();

                session()->flash('success', 'Profile picture updated successfully!');

                return response()->json([
                    'status' => true,
                    'errors' => []
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'errors' => ['profile_pic' => ['No file uploaded.']]
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
        
    }

}
