<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use JavaScript;

class AuthController extends Controller
{

    /**
     * Renders the signup page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function registerPage(Request $request)
    {

        JavaScript::put(['View' => [
            'Routes' => [
                'submitSignup' => route('auth.register.ajax'),
                'redirectUrl' => route('auth.login')
            ]
        ]]);

        return view('auth.register');
    }


    /**
     * Handles user signup
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxRegister(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Create new user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);


        return redirect()->route('auth.login')->with('success', 'Please log in using your newly added credentials.');
    }

    /**
     * Renders login page or redirects if user is already logged in
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function loginPage(Request $request)
    {
        $redirect = route('projects.all');


        if(Auth::check()) {
            return redirect()->to($redirect);
        }

        JavaScript::put(['View' => [
            'Routes' => [
                'submitLogin' => route('auth.login.ajax'),
                'redirectUrl' => $redirect
            ]
        ]]);
        return view('auth.login');
    }

    /**
     * Logs the user out and redirects to login page
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        Auth::logout();;
        return redirect()->route('auth.login');
    }

    /**
     * Attempts to login a user with provided credentials
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, true)) {

            if(Auth::user()->access_status == 0){
                Auth::logout();
                return new JsonResponse(['error' => 'Sorry, your access to this application is blocked!'],404); 
            }

            $request->session()->regenerate();
            return new JsonResponse();
        }

        return new JsonResponse(['error' => 'Invalid credentials.'],404);
    }
}
