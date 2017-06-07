<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'username' => 'required|max:255|unique:users',
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $file = storage_path('conf/.denyregister');
        file_put_contents($file,"Η ύπαρξη του αρχείου .denyregister ελέγχει
τη δυνατότητα να γίνεται register νέων χρηστών.

Με την εγγραφή του πρώτου Διαχειριστή δημιουργείται το αρχείο από τον 
app/Http/Controllers/Auth/RegisterController.php.

Αν διαγραφούν όλοι οι Διαχειριστές το παρόν αρχείο διαγράφεται και
απελευθερώνεται από το app/Http/Controllers/UsersController.php.

Αν δεν μπορείτε να κάνετε register διαγράψτε το αρχείο.");
        return User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    protected function showRegistrationForm()
    {
        $allowregister = True;
        $file = storage_path('conf/.denyregister');
        if (file_exists($file ))$allowregister = False;
        return view('auth.register', compact('allowregister'));
    }

}
