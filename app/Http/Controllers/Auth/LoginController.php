<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
    protected $redirectTo = '/home/list';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
    * παρακαπτω το AuthenticatesUsers.php να χρησιμοποιεί username αντί για email
    **/
    protected function username()
    {
        return 'username';
    }

    protected function showLoginForm()
    {
        $allowregister = True;
        $file = storage_path('conf/.denyregister');
        if (file_exists($file ))$allowregister = False;
        return view('auth.login', compact('allowregister'));
    }

}
