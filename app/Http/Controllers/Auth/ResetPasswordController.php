<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function showResetForm(Request $request, $token = null)
    {
        $allowregister = True;
        $file = storage_path('conf/.denyregister');
        if (file_exists($file ))$allowregister = False;

        return view('auth.passwords.reset')->with(
            ['token' => $token, 'username' => $request->username, 'allowregister' => $allowregister]
        );
    }

    protected function rules()
    {
        return [
            'token' => 'required', 
            'username' => 'required',
            'password' => 'required|confirmed|min:6',
        ];
    }

    protected function credentials(Request $request)
    {
        return $request->only(
            'username', 'password', 'password_confirmation', 'token'
        );
    }
    
    protected function sendResetFailedResponse(Request $request, $response)
    {
        return redirect()->back()
                    ->withInput($request->only('username'))
                    ->withErrors(['username' => trans($response)]);
    }


}
