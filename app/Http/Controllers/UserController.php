<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Role;
use App\Config;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;

class UserController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('web');
        $this->middleware('admin:home');
    }

    /**
     * Show the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {
        $config = new Config;
        $users = User::orderBy('name')->paginate($config->getConfigValueOf('showRowsInPage'));

        foreach ($users as $u){
            $u['role'] = user::find($u['id'])->role->role;
        }

        $roles = Role::all()->sortby('role');

        $submitVisible = 'hidden';
        if (Auth::user()->role->role == 'Διαχειριστής') $submitVisible = 'active';

        return view('users', compact('users', 'user', 'roles', 'submitVisible'));
    }





    /**
     * Add new user.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {

        $this->validate(request(), [
            'name' => 'required|max:255',
            'username' => 'required|max:255|unique:users',
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|confirmed',
            'role_id' => 'required',
        ]);


        $data = request()->all();

        User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'role_id' => $data['role_id'],
            'password' => bcrypt($data['password']),
        ]);
 
         $notification = array(
            'message' => 'Επιτυχημένη καταχώριση.', 
            'alert-type' => 'success'
        );
        session()->flash('notification',$notification);

    return back();
    
    }

    public function update($id)
    {
        $data = request()->all();

        $validatevalues =[
        'name' => 'required|max:255',
        'username' => "required|max:255|unique:users,username,$id,id",
        'email' => 'required|email|max:255',
        'role_id' => 'required' 
        ];
        $updatevalues=[
           'name' => $data['name'],
           'username' => $data['username'],
           'email' => $data['email'] 
           ];

        if (request()->password ){
        $validatevalues['password'] = 'required|min:6|confirmed';
        $updatevalues['password'] = bcrypt($data['password']);
        }


       // βρίσκω πόσοι είναι οι admin
        $user = new User;
        $admin_count = $user ->get_num_of_admins();

        // βρίσκω τον id του admin
        $role = new Role;
        $admin_id = $role->get_admin_id();

        // αν είναι πάνω από 1 admin ενημερώνω
        if ($admin_count >1 ){
        $updatevalues['role_id'] = $data['role_id'];
        }

        // βρίσκω τον role_id του χρήστη που θα ενημέρώθεί
        $old_role_id = $user->find($id)->role_id;
        // αν είναι μονο ένας admin και ο χρήστης
        // που θα ενημερωθεί δεν είναι αυτός ενημερώνω
        if ($admin_count == 1 and $old_role_id != $admin_id){
        $updatevalues['role_id'] = $data['role_id'];
        }

        if ($admin_count == 1 and $old_role_id == $admin_id and $data['role_id'] != $admin_id) {
            $notification = array(
                'message' => 'Πρέπει να υπάρχει τουλάχιστον ένας χρήστης με το Ρόλο <b>\"Διαχειριστής\"</b>', 
                'alert-type' => 'error'
            );
            session()->flash('notification',$notification);
          
            return back();
        }

        $this->validate(request(), $validatevalues);

        User::whereId($id)->update($updatevalues);

        $notification = array(
            'message' => 'Επιτυχημένη ενημέρωση.', 
            'alert-type' => 'success'
        );
        session()->flash('notification',$notification);
      
        return back();
    
    }



    public function delete($user)
    {

        User::destroy ($user);
     
        if (Role::where('role','Διαχειριστής')->has('users')->count() == 0){
            $file = storage_path('conf/.denyregister');
            if (file_exists($file ))unlink($file);
        }

        if (Auth::id() == $user || User::count() == 0 ){
            Auth::logout();
            return redirect('/');
        }

        return redirect('users');
    
    }

}
