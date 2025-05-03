<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
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
    protected $redirectTo = RouteServiceProvider::HOME;

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
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
            'role' => 'required|string',
        ]);
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Update last login timestamp
        $user->last_login_at = now();
        $user->save();

        // Verify that the user has the selected role
        $selectedRole = $request->input('role');
        if (!$user->hasRole($selectedRole)) {
            // Log the user out
            $this->guard()->logout();
            $request->session()->invalidate();
            
            // Throw a validation error
            throw ValidationException::withMessages([
                'role' => ['You do not have access to this role.'],
            ]);
        }

        // Redirect based on selected role
        switch ($selectedRole) {
            case 'super-admin':
                return redirect()->route('admin.dashboard');
            case 'hospital-admin':
                return redirect()->route('hospital.dashboard');
            case 'consultant':
                return redirect()->route('consultant.dashboard');
            case 'gp-doctor':
                return redirect()->route('doctor.dashboard');
            case 'booking-agent':
                return redirect()->route('booking.dashboard');
            default:
                return redirect($this->redirectTo);
        }
    }
}
