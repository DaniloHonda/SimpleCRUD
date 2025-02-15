<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function login(Request $request) {
        $incomingFields = $request->validate([
            'loginname' => 'required',
            'loginpassword' => 'required'
        ]);
    
        if (Auth::attempt([
            'name' => $incomingFields['loginname'], 
            'password' => $incomingFields['loginpassword']
        ])) {
            $request->session()->regenerate();
            return redirect('/');
        }
    
        return back()->withErrors([
            'loginname' => 'Invalid credentials'
        ]);
    }

    public function logout() {
        Auth::logout();
        return redirect('/');
    }

    public function register(Request $request) {
        $incomingFields = $request->validate([
            'name' => ['required', Rule::unique('users', 'name')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required']
        ]);

        $incomingFields['password'] = bcrypt($incomingFields['password']);
        $user = User::create($incomingFields);
        Auth::login($user);

        return redirect('/');
    }
}
