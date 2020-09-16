<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Validation\ValidationException;

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
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);
        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request))
        {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }
        if ($this->attemptLogin($request))
        {
            $allowedStatus = ['A'];
            $userStaus     = \Auth::user()->user_status;
            if (!in_array($userStaus, $allowedStatus, TRUE))
            {
                // Logged user activity
                $this->userActivityLog(\Auth::user(), 'tried_at');
                \Auth::logout();
                return $this->userStatusRestric($userStaus);
            }
            // logged user successful login 
            $this->userActivityLog(\Auth::user());
            return $this->sendLoginResponse($request);
        }
        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }
    
    protected function userStatusRestric($status)
    {
        throw ValidationException::withMessages([
            $this->username() => ['User is Blocked' ],
        ]);
    }

    protected function userActivityLog($logData, $activityType = 'login_at')
    {
        $log            = [
            'userName' => $logData->name,
            'user_id'  => $logData->id,
            'email'    => $logData->email
        ];
        $activity       = ($activityType) ?: 'login_at';
        $log[$activity] = date('Y-m-d H:i:s');
        \Log::info($log);
    }

}
