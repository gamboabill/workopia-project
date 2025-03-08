<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    // @desc Show register form
    // @route Get /register
    public function register(): View
    {
        return view('auth.register');
    }

    // @desc Store user in database
    // @route POST /register
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Hash password
        $validatedData['password'] = Hash::make($validatedData['password']);

        // Create User

        $user = User::create($validatedData);

        return redirect()->route('login')->with('success', 'You are registered and you can now login!');
    }
}
