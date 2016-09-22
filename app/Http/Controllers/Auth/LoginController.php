<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;
use App\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function signinWithGoogle()
    {
        $profile = Socialite::driver('google')->user();
        $user = User::ofEmail($profile->getEmail())->first();

        if ($user) {
            if ($user->withoutGoogle()) {
                $this->saveGoogleProfile($user, $profile);
            }

            auth()->login($user, true);

            return redirect()->intended('/');
        }

        return abort(403);
    }

    protected function saveGoogleProfile(user $user, $profile)
    {
        $user->google_id = $profile->getId();
        $user->avatar_url = $profile->getAvatar();

        if ($pos = strrpos($user->avatar_url, '?')) {
            $user->avatar_url = substr($user->avatar_url, 0, $pos);
        }

        return $user->save();
    }
}
