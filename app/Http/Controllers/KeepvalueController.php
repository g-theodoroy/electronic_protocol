<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Validator;
use App\Config;
use App\Keepvalue;
use Illuminate\Http\Request;

class KeepvalueController extends Controller
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
        $this->middleware('admin:keep', ['except' => ['index']]);
    }



    /**
     * Show the keepvalue.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Keepvalue $keepvalue)
    {
        $keepvalues = Keepvalue::orderBy(DB::raw("SUBSTR(`fakelos`,3,LENGTH(`fakelos`)-(3))+0<>0 DESC, SUBSTR(`fakelos`,3,LENGTH(`fakelos`)-(3))+0, `fakelos`"))->paginate(Config::getConfigValueOf('showRowsInPage'));

        $submitVisible = 'hidden';
        if (Auth::user()->role->role == 'Διαχειριστής') $submitVisible = 'active';

        return view('keep', compact('keepvalues', 'keepvalue', 'submitVisible'));
    }

    /**
     * Add new keepvalue.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {

        $this->validate(request(), [
            'fakelos' => 'required|max:255|unique:keepvalues',
            'keep' => 'nullable|integer',
        ]); // 'describe' => 'required',
        // 'keep' => 'nullable|integer|required_without:keep_alt',
        // 'keep_alt' => 'nullable|max:255|required_without:keep',

        $validator = Validator::make(request()->all(), [
            'fakelos' => 'regex:/^Φ\.\w[\w ._-]*$/u',
        ],  [
            'fakelos.regex' => "Το όνομα του φακέλου πρέπει να ξεκινάει με κεφαλαίο Φ και τελεία (<b>Φ.</b>) ακολουθούμενο από ένα αριθμό ή γράμμα.<br>Επιτρέπονται γράμματα, αριθμοί και οι χαρακτήρες τελεία, παύλα και κάτω παύλα ( <b>. - _</b> ).<br>&nbsp;",
        ])->validate();

        $data = request()->all();
        if (!$data['keep']) $data['keep'] = null;
        if (!$data['keep_alt']) $data['keep_alt'] = null;
        if (!$data['describe']) $data['describe'] = null;
        if (!$data['remarks']) $data['remarks'] = null;

        Keepvalue::create([
            'fakelos' => $data['fakelos'],
            'keep' => $data['keep'],
            'keep_alt' => $data['keep_alt'],
            'describe' => $data['describe'],
            'remarks' => $data['remarks'],
        ]);

        $notification = array(
            'message' => 'Επιτυχημένη καταχώριση.',
            'alert-type' => 'success'
        );
        session()->flash('notification', $notification);

        return back();
    }

    public function update($id)
    {
        $data = request()->all();
        if (!$data['keep']) $data['keep'] = null;
        if (!$data['keep_alt']) $data['keep_alt'] = null;
        if (!$data['describe']) $data['describe'] = null;
        if (!$data['remarks']) $data['remarks'] = null;

        $validatevalues = [
            'fakelos' => "required|max:255|unique:keepvalues,fakelos,$id,id",
            'keep' => 'nullable|integer',
        ]; // 'describe' => 'required',
        // 'keep' => 'nullable|integer|required_without:keep_alt',
        // 'keep_alt' => 'nullable|max:255|required_without:keep',

        $validator = Validator::make(request()->all(), [
            'fakelos' => 'regex:/^Φ\.\w[\w ._-]*$/u',
        ],  [
            'fakelos.regex' => "Το όνομα του φακέλου πρέπει να ξεκινάει με κεφαλαίο Φ και τελεία (<b>Φ.</b>) ακολουθούμενο από ένα αριθμό ή γράμμα.<br>Επιτρέπονται γράμματα, αριθμοί και οι χαρακτήρες τελεία, παύλα και κάτω παύλα ( <b>. - _</b> ).<br>&nbsp;",
        ])->validate();

        $updatevalues = [
            'fakelos' => $data['fakelos'],
            'keep' => $data['keep'],
            'keep_alt' => $data['keep_alt'],
            'describe' => $data['describe'],
            'remarks' => $data['remarks'],
        ];

        $this->validate(request(), $validatevalues);
        Keepvalue::whereId($id)->update($updatevalues);

        $notification = array(
            'message' => 'Επιτυχημένη ενημέρωση.',
            'alert-type' => 'success'
        );
        session()->flash('notification', $notification);

        return back();
    }



    public function delete($id)
    {
        keepvalue::destroy($id);
        return redirect('keep');
    }
}
