<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
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
    protected $redirectTo = '/chkForUpdates';

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
    public function username()
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

    /**
     * GΘ
     * ΕΚΑΝΑ OVERIDE ΓΙΑ ΝΑ ΔΙΑΓΡΑΦΩ ΤΑ MAIL ΣΤΟΝ public/tmp
     * 
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {

        Storage::disk('tmp')->deleteDirectory('u' . Auth()->user()->id);

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/');
    }


}
