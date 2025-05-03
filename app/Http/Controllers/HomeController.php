<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Redirect based on user role
        if ($user->hasRole('super-admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('hospital-admin')) {
            return redirect()->route('hospital.dashboard');
        } elseif ($user->hasRole('consultant')) {
            return redirect()->route('consultant.dashboard');
        } elseif ($user->hasRole('gp-doctor')) {
            return redirect()->route('doctor.dashboard');
        } elseif ($user->hasRole('booking-agent')) {
            return redirect()->route('booking.dashboard');
        }

        // If user has no specific role, show the default home view
        return view('home');
    }
}
