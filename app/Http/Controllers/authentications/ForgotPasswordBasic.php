<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Mail\PasswordResetMail;

class ForgotPasswordBasic extends Controller
{
  public function index()
  {
    return view('content.authentications.auth-forgot-password-basic');
  }

  public function store(Request $request)
  {
    // Validate the email
    $validator = Validator::make($request->all(), [
      'email' => 'required|email|exists:users,email',
    ], [
      'email.exists' => 'Email tidak ditemukan dalam sistem kami.',
    ]);

    if ($validator->fails()) {
      return redirect()->back()
        ->withErrors($validator)
        ->withInput();
    }

    $user = User::where('email', $request->email)->first();
    
    if ($user) {
      // Generate a secure token
      $token = bin2hex(random_bytes(32));
      
      // Store the token in the password_reset_tokens table
      DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $request->email],
        [
          'email' => $request->email,
          'token' => Hash::make($token),
          'created_at' => now()
        ]
      );

      // Send email with reset link
      try {
        Mail::to($user->email)->send(new PasswordResetMail($token, $user->email, $user));
        
        return redirect()->back()
          ->with('success', 'Link reset password telah dikirim ke email Anda. Silakan cek email Anda untuk melanjutkan proses reset password.');
      } catch (\Exception $e) {
        return redirect()->back()
          ->with('error', 'Gagal mengirim email. Silakan coba lagi. Error: ' . $e->getMessage());
      }
    }

    return redirect()->back()
      ->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
  }

  public function showResetForm($token, Request $request)
  {
    $email = $request->query('email');
    
    // Verify token exists and is valid
    $resetRecord = DB::table('password_reset_tokens')
      ->where('email', $email)
      ->first();
    
    if (!$resetRecord || !Hash::check($token, $resetRecord->token)) {
      return redirect()->route('auth-reset-password-basic')
        ->with('error', 'Token reset password tidak valid atau sudah kedaluwarsa.');
    }
    
    // Check if token is expired (60 minutes)
    if (now()->diffInMinutes($resetRecord->created_at) > 60) {
      return redirect()->route('auth-reset-password-basic')
        ->with('error', 'Token reset password sudah kedaluwarsa. Silakan request ulang.');
    }
    
    return view('content.authentications.auth-reset-password', compact('token', 'email'));
  }

  public function resetPassword(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'email' => 'required|email|exists:users,email',
      'token' => 'required|string',
      'password' => 'required|string|min:8|confirmed',
    ]);

    if ($validator->fails()) {
      return redirect()->back()
        ->withErrors($validator)
        ->withInput();
    }

    // Verify token
    $resetRecord = DB::table('password_reset_tokens')
      ->where('email', $request->email)
      ->first();
    
    if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
      return redirect()->back()
        ->with('error', 'Token reset password tidak valid.');
    }
    
    // Check if token is expired
    if (now()->diffInMinutes($resetRecord->created_at) > 60) {
      return redirect()->back()
        ->with('error', 'Token reset password sudah kedaluwarsa.');
    }

    // Update user password
    $user = User::where('email', $request->email)->first();
    $user->password = Hash::make($request->password);
    $user->save();

    // Delete the reset token
    DB::table('password_reset_tokens')->where('email', $request->email)->delete();

    return redirect()->route('auth-login-basic')
      ->with('success', 'Password berhasil direset. Silakan login dengan password baru Anda.');
  }
}
