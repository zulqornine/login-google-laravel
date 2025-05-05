<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

use Illuminate\Support\Str;


class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
{
    try {
        $googleUser = Socialite::driver('google')->stateless()->user();

        // Cari user berdasarkan email
        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            // Jika belum ada, buat user baru
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'password' => bcrypt(Str::random(24)), // password acak
            ]);
        }

        Auth::login($user);
        return redirect()->intended('/dashboard');
    } catch (\Exception $e) {
        return redirect()->route('login')->withErrors(['msg' => 'Login dengan Google gagal.']);
    }
}

}
