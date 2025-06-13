<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Orchid\Platform\Models\Role;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        // Validate input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:8',
            'referral_code' => 'nullable|string|max:255',
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // Add other fields as needed
        ]);

        // Optionally handle referral code here

        $defaultRole = Role::where('slug', 'user')->first();
        if ($defaultRole) {
            // Attach the role using the pivot table
            $user->roles()->attach($defaultRole->id);
        }

        // Log the user in
        Auth::login($user);

        // Redirect to dashboard or home
        return redirect()->route('platform.index');
    }
}
