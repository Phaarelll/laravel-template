<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // If no user is authenticated, create a dummy user for demo purposes
        if (!$user) {
            $user = User::first(); // Get the first user from database
            if (!$user) {
                // Create a demo user if none exists
                $user = new User();
                $user->id = 1;
                $user->name = 'Demo User';
                $user->email = 'demo@example.com';
                $user->usertype = 'user';
                $user->created_at = now();
                $user->updated_at = now();
                $user->email_verified_at = null;
            }
        }
        
        return view('content.profile.my-profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        // If no user is authenticated, get the first user for demo purposes
        if (!$user) {
            $user = User::first();
            if (!$user) {
                return redirect()->back()
                    ->with('error', 'User tidak ditemukan.');
            }
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check current password if new password is provided
        if ($request->filled('new_password')) {
            if (!$request->filled('current_password') || !Hash::check($request->current_password, $user->password)) {
                return redirect()->back()
                    ->with('error', 'Password saat ini tidak benar.')
                    ->withInput();
            }
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo && file_exists(storage_path('app/public/profile_photos/' . $user->profile_photo))) {
                unlink(storage_path('app/public/profile_photos/' . $user->profile_photo));
            }
            
            // Store new photo
            $photo = $request->file('profile_photo');
            $photoName = time() . '_' . $user->id . '.' . $photo->getClientOriginalExtension();
            $photo->storeAs('public/profile_photos', $photoName);
            $user->profile_photo = $photoName;
        }

        // Update user data
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return redirect()->back()
            ->with('success', 'Profile berhasil diperbarui!');
    }
}
