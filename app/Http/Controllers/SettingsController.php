<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // If no user is authenticated, get the first user for demo purposes
        if (!$user) {
            $user = User::first();
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
        
        return view('content.settings.index', compact('user'));
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
            'notifications' => 'nullable|boolean',
            'email_notifications' => 'nullable|boolean',
            'dark_mode' => 'nullable|boolean',
            'language' => 'nullable|string|in:en,id',
            'timezone' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // For demo purposes, we'll just show success message
        // In real application, you would save these settings to database
        
        return redirect()->back()
            ->with('success', 'Settings berhasil diperbarui!');
    }
}
