<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
 
    protected function authenticated(Request $request, $user)
    {
        if($user->verified == false) {
            $this->guard()->logout();
    
            return redirect('/login')->withError(''."Please Your mail for verification and activate your account.");

            
        }
    }

    public function adminLoginView()
    {
        return view('admin.auth.login');
    }

    public function authenticateAdmin(Request $request)
    {

        if (Auth::attempt(['email' => $email, 'password' => $password, 'type' => User::ADMIN_TYPE])) {
            return redirect('security/dashboard');
        }
    }

}
