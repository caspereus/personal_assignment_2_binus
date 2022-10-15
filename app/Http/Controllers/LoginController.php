<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Services\Login\RememberMeExpiration;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    use RememberMeExpiration;

    /**
     * Display login page.
     * 
     * @return Renderable
     */
    public function show()
    {
        return view('auth.login');
    }

    /**
     * Handle account login request
     * 
     * @param LoginRequest $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        try{
            $this->checkTooManyFailedAttempts();
        }catch(Exception $e){
            return redirect()->to('login')->withErrors($e->getMessage());
        }

        $credentials = $request->getCredentials();

        if(!Auth::validate($credentials)){
            RateLimiter::hit($this->throttleKey(), $seconds = 30);
            return redirect()->to('login')->withErrors(trans('auth.failed'));
        }

        RateLimiter::clear($this->throttleKey());

        $user = Auth::getProvider()->retrieveByCredentials($credentials);

        Auth::login($user, $request->get('remember'));

        if($request->get('remember')):
            $this->setRememberMeExpiration($user);
        endif;

        return $this->authenticated($request, $user);
    }

    /**
     * Handle response after user authenticated
     * 
     * @param Request $request
     * @param Auth $user
     * 
     * @return \Illuminate\Http\Response
     */
    protected function authenticated(Request $request, $user) 
    {
        return redirect()->intended();
    }

    public function reloadCaptcha()
    {
        return response()->json(['captcha'=> captcha_img()]);
    }

    public function throttleKey()
    {
        return Str::lower(request('username')) . '|' . request()->ip();
    }

    public function checkTooManyFailedAttempts()
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), $perMinute = 3)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());
        throw new Exception('Terlalu banyak mencoba silahkan coba kembali ' . $seconds . "Detik Lagi");
    }
}
