<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Support\Facades\RateLimiter; 

class AuthenticatedSessionController extends Controller
{

    /**
     * Display the login view.
     */
    public function create()
    {
        $pieces = [1,2,3,4];
        shuffle($pieces);
        return view('auth.login', ['puzzleOrder' => $pieces]);
    }
    protected function throttleKey(Request $request)
    {
        return strtolower($request->input('name')) . '|' . $request->ip();
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $key = $this->throttleKey($request);
        $maxAttempts = 3;
        $decaySeconds = 120;

        $puzzleOrder = $request->input('puzzle_order');
        if ($puzzleOrder !== '1,2,3,4') {
            RateLimiter::hit($key, $decaySeconds);
            if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                $user = User::where('name', $request->name)->first();
                if ($user) $user->lock();
                return $this->sendLockoutResponse($request, $key);
            }
            throw ValidationException::withMessages([
                'puzzle' => 'Пазл собран неправильно.',
            ]);
        }
        $user = User::where('name', $request->name)->first();
        if ($user && $user->isLocked()) {
            throw ValidationException::withMessages([
                'name' => 'Вы заблокированы. Обратитесь к администратору.',
            ]);
        }
        if (! Auth::attempt($request->only('name', 'password'), $request->filled('remember'))) {
            RateLimiter::hit($key, $decaySeconds);
            if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                if ($user) $user->lock();
                return $this->sendLockoutResponse($request, $key);
            }
            throw ValidationException::withMessages([
                'name' => 'Вы ввели неверный логин или пароль. Пожалуйста проверьте ещё раз введенные данные.',
            ]);
        }
        RateLimiter::clear($key);
        $request->session()->regenerate();
        session()->flash('success', 'Вы успешно авторизовались');
        return redirect()->intended(route('dashboard'));
    }

    protected function sendLockoutResponse(Request $request, string $key)
    {
        $seconds = RateLimiter::availableIn($key);
        throw ValidationException::withMessages([
            'name' => "Слишком много попыток. Аккаунт заблокирован. Обратитесь к администратору.",
        ])->status(429);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}