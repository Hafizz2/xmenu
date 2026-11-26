<?php

namespace App\Http\Controllers;

use App\Models\Cafe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'cafe_name' => ['required', 'string', 'max:80'],
            'name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => strtolower($data['email']),
                'phone' => $data['phone'] ?? null,
                'password' => $data['password'],
            ]);
            $user->forceFill(['last_login_at' => now()])->save();
            $this->createCafe($user, $data['cafe_name'], $data['phone'] ?? null);

            return $user;
        });

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('welcome', true);
    }

    public static function createCafe(User $user, string $name, ?string $phone = null): Cafe
    {
        $cafe = new Cafe([
            'name' => $name,
            'slug' => Cafe::uniqueSlug($name),
            'phone' => $phone,
            'tagline' => ['en' => null, 'am' => null],
            'theme' => config('xmenu.theme_defaults'),
        ]);
        $cafe->user_id = $user->id;
        $cafe->status = 'trial';
        $cafe->plan = 'trial';
        $cafe->trial_ends_at = now()->addDays((int) \App\Models\Setting::get('trial_days', config('xmenu.trial_days')));
        $cafe->save();

        for ($d = 0; $d < 7; $d++) {
            $cafe->openingHours()->create(['day_of_week' => $d, 'opens_at' => '07:00', 'closes_at' => '22:00', 'closed' => false]);
        }

        return $cafe;
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        $credentials['email'] = strtolower($credentials['email']);

        // Lock one email for a while after repeated wrong passwords, from any device.
        $key = 'login:'.sha1($credentials['email']);
        if (RateLimiter::tooManyAttempts($key, 6)) {
            $mins = (int) ceil(RateLimiter::availableIn($key) / 60);

            return back()->withErrors(['email' => "Too many tries. For your safety, wait {$mins} minute".($mins > 1 ? 's' : '').' and try again.'])->onlyInput('email');
        }

        if (! Auth::attempt($credentials, $request->boolean('remember', true))) {
            RateLimiter::hit($key, 15 * 60);

            return back()->withErrors(['email' => 'That email and password don’t match.'])->onlyInput('email');
        }
        RateLimiter::clear($key);
        $user = Auth::user();
        if ($user->isSuspended()) {
            Auth::logout();

            return back()->withErrors(['email' => 'This account is suspended. Please contact Xmenu support.'])->onlyInput('email');
        }
        $request->session()->regenerate();
        $user->forceFill(['last_login_at' => now()])->save();

        return redirect()->intended($user->is_admin ? route('admin.index') : route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
