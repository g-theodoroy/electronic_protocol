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
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('web');
        $this->middleware('admin:keep', ['except' => ['index']]);
    }

    public function index(Keepvalue $keepvalue)
    {
        $keepvalues = Keepvalue::orderBy(DB::raw(
            "SUBSTR(`fakelos`,3,LENGTH(`fakelos`)-(3))+0<>0 DESC,
             SUBSTR(`fakelos`,3,LENGTH(`fakelos`)-(3))+0,
             `fakelos`"
        ))->paginate(Config::getConfigValueOf('showRowsInPage'));

        $submitVisible = 'hidden';

        if (Auth::user()->role->role == 'Διαχειριστής') {
            $submitVisible = 'active';
        }

        return view('keep', compact(
            'keepvalues',
            'keepvalue',
            'submitVisible'
        ));
    }

    public function store()
    {
        $this->validate(request(), [
            'fakelos' => 'required|max:255|unique:keepvalues',
            'keep' => 'nullable|integer',
            'keep_alt' => 'nullable|max:255',
        ]);

        Validator::make(request()->all(), [
            'fakelos' => 'regex:/^Φ\.\w[\w ._-]*$/u',
        ], [
            'fakelos.regex' => "Το όνομα του φακέλου πρέπει να ξεκινάει με κεφαλαίο Φ και τελεία (<b>Φ.</b>) ακολουθούμενο από ένα αριθμό ή γράμμα.<br>Επιτρέπονται γράμματα, αριθμοί και οι χαρακτήρες τελεία, παύλα και κάτω παύλα ( <b>. - _</b> ).<br>&nbsp;",
        ])->validate();

        $data = request()->all();

        Keepvalue::create([
            'fakelos' => $data['fakelos'],
            'keep' => !empty($data['keep']) ? $data['keep'] : null,
            'keep_alt' => !empty($data['keep_alt']) ? $data['keep_alt'] : null,
            'describe' => !empty($data['describe']) ? $data['describe'] : null,
            'remarks' => !empty($data['remarks']) ? $data['remarks'] : null,
        ]);

        session()->flash('notification', [
            'message' => 'Επιτυχημένη καταχώριση.',
            'alert-type' => 'success'
        ]);

        return back();
    }

    public function update($id)
    {
        $validatevalues = [
            'fakelos' => "required|max:255|unique:keepvalues,fakelos,$id,id",
            'keep' => 'nullable|integer',
            'keep_alt' => 'nullable|max:255',
        ];

        $this->validate(request(), $validatevalues);

        Validator::make(request()->all(), [
            'fakelos' => 'regex:/^Φ\.\w[\w ._-]*$/u',
        ], [
            'fakelos.regex' => "Το όνομα του φακέλου πρέπει να ξεκινάει με κεφαλαίο Φ και τελεία (<b>Φ.</b>) ακολουθούμενο από ένα αριθμό ή γράμμα.<br>Επιτρέπονται γράμματα, αριθμοί και οι χαρακτήρες τελεία, παύλα και κάτω παύλα ( <b>. - _</b> ).<br>&nbsp;",
        ])->validate();

        $data = request()->all();

        $updatevalues = [
            'fakelos' => $data['fakelos'],
            'keep' => !empty($data['keep']) ? $data['keep'] : null,
            'keep_alt' => !empty($data['keep_alt']) ? $data['keep_alt'] : null,
            'describe' => !empty($data['describe']) ? $data['describe'] : null,
            'remarks' => !empty($data['remarks']) ? $data['remarks'] : null,
        ];

        Keepvalue::whereId($id)->update($updatevalues);

        session()->flash('notification', [
            'message' => 'Επιτυχημένη ενημέρωση.',
            'alert-type' => 'success'
        ]);

        return back();
    }

    public function delete($id)
    {
        $keepvalue = Keepvalue::findOrFail($id);
        $keepvalue->delete();

        return redirect('keep');
    }
}
