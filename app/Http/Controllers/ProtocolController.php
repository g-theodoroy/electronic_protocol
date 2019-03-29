<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Keepvalue;
use App\Config;
use App\Protocol;
use App\Attachment;
use App\User;
use Storage;
use Carbon\Carbon;
use URL;
use Auth;
use Illuminate\Validation\Rule;
use Validator;
use DB;
use Active;

/*

            $table->increments('id');
            $table->integer('user_id')->unsigned()->required();
            $table->integer('protocolnum')->required()->unsigned()->index();
            $table->integer('protocoldate')->required()->unsigned();
            $table->integer('etos')->unsigned()->required();
            $table->string('fakelos')->nullable();
            $table->string('thema')->required();
            $table->string('in_num')->nullable();
            $table->integer('in_date')->unsigned()->nullable();
            $table->string('in_topos_ekdosis')->nullable();
            $table->string('in_arxi_ekdosis')->nullable();
            $table->string('in_paraliptis')->nullable();
            $table->string('diekperaiosi')->nullable();
            $table->string('in_perilipsi')->nullable();
            $table->integer('out_date')->unsigned()->nullable();
            $table->string('out_to')->nullable();
            $table->string('out_perilipsi')->nullable();
            $table->string('keywords')->nullable();
            $table->string('paratiriseis')->nullable();
            $table->string('flags')->nullable();
            $table->timestamps();


*/

class ProtocolController extends Controller
            {

     protected  $protocolfields = [
    'fakelos' => 'Φάκελος',
    'thema' => 'Θέμα',
    'in_num' => 'Αριθ. Εισερχ.',
    'in_topos_ekdosis' => 'Τόπος έκδοσης',
    'in_arxi_ekdosis' => 'Αρχή έκδοσης',
    'in_paraliptis' => 'Παραλήπτης',
    'in_perilipsi' => 'Περιλ. Εισερχ',
    'diekperaiosi' => 'Διεκπεραίωση',
    'sxetiko' => 'Σχετικοί Αριθ.',
    'out_to' => 'Απευθύνεται',
    'out_perilipsi' => 'Περιλ. Εξερχ',
    'keywords' => 'Λέξεις κλειδιά',
    'paratiriseis' => 'Παρατηρήσεις'
    ];
    protected  $attachmentfields = [
    'name' => 'Όνομα συνημμένου',
    'ada' => 'ΑΔΑ'
    ];


     /**
     * Create a new controller instance.
     *
     * @return void
     */
     public function __construct()
     {
        $this->middleware('auth');
        $this->middleware('web');
        $this->middleware('writer:home/list', ['except' => ['index', 'indexList', 'getFileInputs', 'gotonum', 'download', 'find', 'getFindData', 'printprotocols', 'printed', 'about']]);
    }

    public function getTitleColorStyle(){
        $config = new Config;
        $titleColor = $config->getConfigValueOf('titleColor');
        $titleColorStyle = '';
        if($titleColor) $titleColorStyle = "style='background:" . $titleColor . "'" ;
        return $titleColorStyle;
    }

    public function index( Protocol $protocol){

        $fakeloi= Keepvalue::orderBy(DB::raw("SUBSTR(`fakelos`,3,LENGTH(`fakelos`)-3)+0<>0 DESC, SUBSTR(`fakelos`,3,LENGTH(`fakelos`)-(3))+0, `fakelos`"))->select('fakelos', 'describe')->get();

        $config = new Config;
        $newetos = $config->getConfigValueOf('yearInUse')?$config->getConfigValueOf('yearInUse'):Carbon::now()->format('Y');
        $titleColorStyle = $this->getTitleColorStyle() ;
        $showUserInfo = $config->getConfigValueOf('showUserInfo');

        $firstProtocolNum = $config->getConfigValueOf('firstProtocolNum');
        if (Protocol::all()->count()){
            if ($config->getConfigValueOf('yearInUse')){
                $newprotocolnum = Protocol::whereEtos($newetos)->max('protocolnum') ? Protocol::whereEtos($newetos)->max('protocolnum') + 1 : 1 ;
            }else{
                $newprotocolnum = Protocol::all() -> last() -> protocolnum ? Protocol::all() -> last() -> protocolnum + 1 : 1 ;
            }
        }else{
            if($firstProtocolNum){
                $newprotocolnum = $firstProtocolNum;
            }else{
                $newprotocolnum = 1;
            }
        }

        // βρίσκω τους όλους ενεργούς χρήστες
        $activeusers = Active::users()->mostRecent()->get();
        $activeusers2show = [];
        foreach($activeusers as $actuser){
          if ($showUserInfo == 1){
            $activeusers2show[] = $actuser['user']['username'];
          }elseif($showUserInfo == 2){
            $activeusers2show[] = $actuser['user']['name'];
          }
        }
        // μετράω μόνο τους Διαχειριστές και Συγγραφείς που έχουν δικαίωμα να γράψουν
        $activeuserscount = Active::users()->whereHas('user', function($q) {
              $q->where('role_id', 1)->orWhere('role_id', 2) ;
            })->count();
          // αν είναι πάνω από ένας δεν εμφανίζω τον επόμενο Αρ.Πρωτ.
        $newprotocolnumvisible = 'active';
        if ($activeuserscount > 1 and ! $protocol->id) $newprotocolnumvisible = 'hidden';

        $newprotocoldate = Carbon::now()->format('d/m/Y');
        $class = 'bg-info';
        $protocoltitle = 'Νέο Πρωτόκολλο';
        $protocolUser = '';
        $protocolArrowStep = $config->getConfigValueOf('protocolArrowStep');
        if($protocol->etos) $newetos = $protocol->etos;
        if($protocol->protocolnum) $newprotocolnum = $protocol->protocolnum;
        if($protocol->protocoldate) $newprotocoldate = Carbon::createFromFormat('Ymd', $protocol->protocoldate)->format('d/m/Y');
        $in_date = null;
        if($protocol->in_date)$in_date = Carbon::createFromFormat('Ymd', $protocol->in_date)->format('d/m/Y');
        $out_date = null;
        if($protocol->out_date)$out_date = Carbon::createFromFormat('Ymd', $protocol->out_date)->format('d/m/Y');
        $diekp_date = null;
        if($protocol->diekp_date)$diekp_date = Carbon::createFromFormat('Ymd', $protocol->diekp_date)->format('d/m/Y');
        if($protocol->protocolnum){
            $class = 'bg-success';
            $protocoltitle = 'Επεξεργασία Πρωτοκόλλου';
            $protocolUser = User::whereId($protocol->user_id)->first();
        }

        $allowWriterUpdateProtocol = $config->getConfigValueOf('allowWriterUpdateProtocol');
        $allowWriterUpdateProtocolTimeInMinutes = $config->getConfigValueOf('allowWriterUpdateProtocolTimeInMinutes');

        $submitVisible = 'active';
        // ΑΠΟΚΡΥΨΗ ΤΟΥ ΚΟΥΜΠΙΟΥ ΑΠΟΘΗΚΕΥΣΗ
        // 1 αν ο χρήστης είναι Αναγνώστης
        if (Auth::user()->role->role == 'Αναγνώστης') $submitVisible = 'hidden';
        // 2 αν ο χρήστης είναι Συγγραφέας
        if (Auth::user()->role->role == 'Συγγραφέας') {
            // αν είναι παλιό πρωτόκολλο (έχει id) ΕΠΕΞΕΡΓΑΣΙΑ ΠΡΩΤΟΚΟΛΛΟΥ
            if($protocol->id){
              // αν η μεταβλητή είναι 0 ή null δηλαδή δεν επιτρέπεται τροποποίηση από Συγγραφείς
              if ( ! $allowWriterUpdateProtocol){
                $submitVisible = 'hidden';
              // αν η μεταβλητή είναι 1 δηλαδή επιτρέπεται μόνο στον Συγγραφέα που καταχώρισε το Πρ.
              }elseif($allowWriterUpdateProtocol == 1){
                // αν ο Συγγραφέας ΔΕΝ είναι ο ίδιος
                if ($protocol->user_id !== Auth::user()->id ){
                    $submitVisible = 'hidden';
                }else{
                  //return Carbon::now()->subMinutes($allowWriterUpdateProtocolTimeInMinutes)->getTimestamp() - $protocol->updated_at->getTimestamp() ;
                  // αν τα λεπτά είναι μεγαλύτερα του 0 τότε ελέγχεται ο χρόνος που πέρασε και μετά κρύβεται το κουμπί
                  if ($allowWriterUpdateProtocolTimeInMinutes){
                    if ($protocol->updated_at->getTimestamp() < Carbon::now()->subMinutes($allowWriterUpdateProtocolTimeInMinutes)->getTimestamp()){
                    $submitVisible = 'hidden';
                  }
                }
              }
              // αν η μεταβλητή είναι 2 δηλαδή επιτρέπεται σε κάθε Συγγραφέα να τροποποιήσει
              }else{
                // αν τα λεπτά είναι μεγαλύτερα του 0 τότε ελέγχεται ο χρόνος που πέρασε και μετά κρύβεται το κουμπί
                if ($allowWriterUpdateProtocolTimeInMinutes and $protocol->updated_at->getTimestamp() < Carbon::now()->subMinutes($allowWriterUpdateProtocolTimeInMinutes)->getTimestamp()){
                  $submitVisible = 'hidden';
                }
              }
              // Αν το πρωτόκολλο δεν έχει θέμα (είναι δηλαδή κενό) ακυρώνονται όλα τα παραπάνω
              if (! $protocol->thema)$submitVisible = 'active';
            }
        }

        $readonly = 'readonly';
        $delVisible = 'hidden';
        $protocolValidate = $config->getConfigValueOf('protocolValidate');
        if(! $protocolValidate){
            if ( Auth::user()->role->role == 'Διαχειριστής'){
                $readonly ='';
                $class = 'bg-danger';
                $delVisible = 'active';
            }
        }

        $ipiresiasName = $config->getConfigValueOf('ipiresiasName');
        $diavgeiaUrl = $config->getConfigValueOf('diavgeiaUrl');

        $keepval = null;
        if ($protocol->fakelos  and Keepvalue::whereFakelos($protocol->fakelos)->first()){
            $keepval = Keepvalue::whereFakelos($protocol->fakelos)->first()->keep;
            if (! $keepval) $keepval = Keepvalue::whereFakelos($protocol->fakelos)->first()->keep_alt;
        }

        $allowUserChangeKeepSelect = $config->getConfigValueOf('allowUserChangeKeepSelect');

        $years = Keepvalue::whereNotNull('keep')->select('keep')->distinct()->orderby('keep', 'asc')->get();
        $words = Keepvalue::whereNotNull('keep_alt')->select('keep_alt')->distinct()->orderby('keep_alt', 'asc')->get();

        return view('protocol', compact('fakeloi', 'protocol', 'newetos', 'newprotocolnum', 'newprotocoldate', 'in_date', 'out_date', 'diekp_date', 'class', 'protocoltitle', 'protocolArrowStep', 'submitVisible','delVisible', 'ipiresiasName', 'readonly', 'years', 'words', 'keepval', 'allowUserChangeKeepSelect', 'titleColorStyle', 'diavgeiaUrl', 'activeusers2show', 'showUserInfo' , 'newprotocolnumvisible', 'protocolUser'));
    }

    public function chkForUpdates(){
        // μόνο όταν γίνεται login
        // έλεγχος εάν έχουν γίνει αλλαγές στο github
        // και ενημέρωση του Χρήστη αν η ρύθμιση updatesAutoCheck = 1
        $config = new Config;
        $updatesAutoCheck = $config->getConfigValueOf('updatesAutoCheck');
        if($updatesAutoCheck){
            if (strpos ( request()->headers->get('referer') , 'login')){
                $url = 'https://api.github.com/repos/g-theodoroy/electronic_protocol/commits';
                $opts = ['http' => ['method' => 'GET', 'header' => ['User-Agent: PHP']]];
                $context = stream_context_create($opts);
                $json = file_get_contents($url, false, $context);
                $commits = json_decode($json, true);

                if ($commits){
                    if(Auth::user()->role_description() == "Διαχειριστής"){
                        $message = 'Έγιναν τροποποιήσεις στον κώδικα του Ηλ.Πρωτοκόλλου στο Github.<br><br>Αν επιθυμείτε <a href=\"https://github.com/g-theodoroy/electronic_protocol/commits/master\" target=\"_blank\"><u> εξετάστε τον κώδικα</u></a> και ενημερώστε την εγκατάστασή σας.<br><br>Για να μην εμφανίζεται το παρόν μήνυμα καντε κλικ στο menu Διαχείριση->Ενημερώθηκε.';
                    }else{
                        $message = 'Έγιναν τροποποιήσεις στον κώδικα του Ηλ.Πρωτοκόλλου στο Github.<br><br>Ενημερώστε το Διαχειριστή.';
                    }
                    $file = storage_path('conf/.updateCheck');
                    if (file_exists($file )){
                        if ( $commits[0]['sha'] != file_get_contents($file)){
                                $notification = array(
                                    'message' =>  $message,
                                    'alert-type' => 'info'
                                    );
                                session()->flash('notification',$notification);
                                $config->setConfigValueOf('needsUpdate', 1);
                            }
                    }else{
                        file_put_contents($file,$commits[0]['sha']);
                    }
                }
            }
        }
        return redirect('/home/list');
    }

    public function indexList(){
        $config = new Config;
        $ipiresiasName = $config->getConfigValueOf('ipiresiasName');
        $refreshInterval = $config->getConfigValueOf('minutesRefreshInterval') * 60;
        $needsUpdate = False;
        if (strpos ( request()->headers->get('referer') , 'login')){
            $needsUpdate = $config->getConfigValueOf('needsUpdate');
        }
        $wideListProtocol = $config->getConfigValueOf('wideListProtocol');
        $diavgeiaUrl = $config->getConfigValueOf('diavgeiaUrl');
        $titleColorStyle = $this->getTitleColorStyle() ;

        $protocols = Protocol::orderby('etos','desc')->orderby('protocolnum','desc')->paginate($config->getConfigValueOf('showRowsInPage'));
        foreach($protocols as $protocol){
            if($protocol->protocoldate) $protocol->protocoldate = Carbon::createFromFormat('Ymd', $protocol->protocoldate)->format('d/m/Y');
            if($protocol->in_date) $protocol->in_date = Carbon::createFromFormat('Ymd', $protocol->in_date)->format('d/m/Y');
            if($protocol->out_date) $protocol->out_date = Carbon::createFromFormat('Ymd', $protocol->out_date)->format('d/m/Y');
            if($protocol->diekp_date) $protocol->diekp_date = Carbon::createFromFormat('Ymd', $protocol->diekp_date)->format('d/m/Y');
            if($protocol->fakelos and Keepvalue::whereFakelos($protocol->fakelos)->first()) $protocol->describe .= Keepvalue::whereFakelos($protocol->fakelos)->first()->describe;

        }
        return view('protocolList', compact('protocols', 'ipiresiasName', 'refreshInterval', 'needsUpdate', 'wideListProtocol', 'titleColorStyle', 'diavgeiaUrl' ));
    }


    public function getKeep4Fakelos($fakelos){
        $keepval = null;
        if ($fakelos){
            $keepval = Keepvalue::whereFakelos($fakelos)->first()->keep;
            if (! $keepval) $keepval = Keepvalue::whereFakelos($fakelos)->first()->keep_alt;
        }
        return $keepval;
    }


    public function getFileInputs($num){
        $data = request()->all();
        return view('getFileInputs', compact('num', 'data'));
    }



    public function store(){

      $data = request()->all();

      $config = new Config;

      $protocolValidate = $config->getConfigValueOf('protocolValidate');
      $etos = request('etos');
      $currentEtos = Carbon::now()->format('Y');
      $safeNewProtocolNum = $config->getConfigValueOf('safeNewProtocolNum');

      // βρίσκω το νέο Αρ.Πρωτ στην εισαγωγή δεδομένων
      $firstProtocolNum = $config->getConfigValueOf('firstProtocolNum');
      if (Protocol::all()->count()){
          if ($config->getConfigValueOf('yearInUse')){
            $newprotocolnum = Protocol::whereEtos($etos)->max('protocolnum') ? Protocol::whereEtos($etos)->max('protocolnum') + 1 : 1 ;
            }else{
            $newprotocolnum = Protocol::all() -> last() -> protocolnum ? Protocol::all() -> last() -> protocolnum + 1 : 1 ;
            }
        }else{
            if($firstProtocolNum){
                $newprotocolnum = $firstProtocolNum;
            }else{
                $newprotocolnum = 1;
            }
        }
        // Αν η ρύθμιση λέι ΝΑΙ σε ασφαλή Αρ.Πρωτ δεν ελέγχω το νέο Αρ.Πρ που μόλις έφτιαξα
      if($safeNewProtocolNum){
          $mustValidate =[
          'etos' => 'required|integer|digits:4',
          'protocoldate' => 'required',
          ];
      }else{
        // αλλιώς ελέγχω
          $mustValidate =[
          'protocolnum' => "required|integer|unique:protocols,protocolnum,NULL,id,etos,$etos",
          'etos' => 'required|integer|digits:4',
          'protocoldate' => 'required',
          ];
      }
      $this->validate(request(), $mustValidate);

      if($protocolValidate){
        $validator = Validator::make(request()->all(), [
            'etos' => "in:$currentEtos",],  [
            'etos.in' => "Δεν μπορείτε να καταχωρίσετε Νέο Πρωτόκολλο στο παρελθόν έτος $etos.<br><br>Αλλάξτε στις ρυθμίσεις εφαρμογής το ''Ενεργό έτος πρωτοκόλλησης'' είτε σε:<br>-> <b>$currentEtos</b> για να ξεκινήσετε από το 1<br>-> <b>κενό</b> για τον επόμενο Αρ.Πρωτ.<br>&nbsp;",
            ])->validate();
    }

    $notalwaysValidate = [
    'in_date' => 'required_with:in_num,in_topos_ekdosis,in_arxi_ekdosis',
    'in_topos_ekdosis' => 'required_with:in_date,in_arxi_ekdosis|max:255',
    'in_arxi_ekdosis' => 'required_with:in_date,in_topos_ekdosis|max:255',
    'in_paraliptis' => 'required_with:in_date|max:255',
    'out_date' => 'required_with:out_to,out_perilipsi',
    'out_to' => 'required_with:out_date,out_perilipsi|max:255',
    ];

    if($protocolValidate){
        $validator = Validator::make(request()->all(), [
            'thema' => 'required_with:fakelos,in_num,in_date,in_topos_ekdosis,in_arxi_ekdosis,in_paraliptis,in_perilipsi,diekperaiosi,out_date,diekp_date,sxetiko,out_to,out_perilipsi,keywords,paratiriseis|max:255',
            ],  [
            'thema.required_with' => "Συμπληρώστε το θέμα.<br>&nbsp;",
            ])->validate();

        $this->validate(request(), $notalwaysValidate);
    }


    $validator = Validator::make(request()->all(), [
        'protocoldate' => 'regex:/^\d{2}\/\d{2}\/\d{4}$/',
        'in_date' => 'regex:/^\d{2}\/\d{2}\/\d{4}$/',
        'out_date' => 'regex:/^\d{2}\/\d{2}\/\d{4}$/',
        'diekp_date' => 'regex:/^\d{2}\/\d{2}\/\d{4}$/',],  [
        'protocoldate.regex' => "Η ημερομηνία πρέπει να έχει τη μορφή 'ηη/μμ/εεεε'.<br>&nbsp;",
        'in_date.regex' => "Η ημερομηνία πρέπει να έχει τη μορφή 'ηη/μμ/εεεε'.<br>&nbsp;",
        'out_date.regex' => "Η ημερομηνία πρέπει να έχει τη μορφή 'ηη/μμ/εεεε'.<br>&nbsp;",
        'diekp_date.regex' => "Η ημερομηνία πρέπει να έχει τη μορφή 'ηη/μμ/εεεε'.<br>&nbsp;",
        ])->validate();

    $in_date = null;
    $out_date = null;
    $diekp_date = null;
    if($data['in_date']) $in_date = Carbon::createFromFormat('d/m/Y', $data['in_date'])->format('Ymd');
    if($data['out_date']) $out_date = Carbon::createFromFormat('d/m/Y', $data['out_date'])->format('Ymd');
    if($data['diekp_date']) $diekp_date = Carbon::createFromFormat('d/m/Y', $data['diekp_date'])->format('Ymd');

    $in_chkdata = $data['in_chk'];
    if ($in_chkdata == '1' ){
        $validator = Validator::make(request()->all(), [
            'in_num' => "unique:protocols,in_num,NULL,id,in_date,$in_date",],  [
            'in_num.unique' => "<center><h4>Ενημέρωση ...</h4><hr></center>Υπάρχει καταχωρημένο πρωτόκολλο με ίδιο<br><br>Αριθμό Εισερχομένου και<br>Ημ/νία Εισερχομένου<br><br>Θέλετε ωστόσο να προχωρήσετε;<br>&nbsp;",
            ])->validate();
    }

    if($safeNewProtocolNum){ // αν η ρύθμιση Ασφαλής νέος Αρ.Πρ είναι ΝΑΙ
        // εισαγωγή της εγγραφής με το νέο Αρ.Πρ που μόλις έφτιαξα
        $protocolNewNum = $newprotocolnum;
      }else{
        $protocolNewNum = $data['protocolnum'];
      }

    Protocol::create([
        'user_id' => Auth::user()->id ,
        'protocolnum'=> $protocolNewNum,
        'protocoldate'=> Carbon::createFromFormat('d/m/Y', $data['protocoldate'])->format('Ymd'),
        'etos' => $data['etos'],
        'fakelos' => $data['fakelos'],
        'thema' => $data['thema'],
        'in_num' => $data['in_num'],
        'in_date' => $in_date,
        'in_topos_ekdosis'=>  $data['in_topos_ekdosis'],
        'in_arxi_ekdosis' => $data['in_arxi_ekdosis'],
        'in_paraliptis' => $data['in_paraliptis'],
        'diekperaiosi' => $data['diekperaiosi'],
        'in_perilipsi' => $data['in_perilipsi'],
        'out_date' => $out_date,
        'diekp_date' => $diekp_date,
        'sxetiko' => $data['sxetiko'],
        'out_to' => $data['out_to'],
        'out_perilipsi' => $data['out_perilipsi'],
        'keywords' => $data['keywords'],
        'paratiriseis' => $data['paratiriseis']
        ]);

    $filescount = 3 * $data['file_inputs_count'];
    $protocol_id = Protocol::where("etos",$data['etos'])->where('protocolnum', $protocolNewNum)->first()->id ;

    for ($i = 1 ; $i < $filescount + 1; $i++){
      if ($data["ada$i"] or request()->hasFile("att$i")){
        $filename = NULL;
        $mimeType = NULL;
        $savedPath = NULL;
        $expires = NULL;
        if (request()->hasFile("att$i")){
          $file = request()->file("att$i");

          $filename = $file->getClientOriginalName();
          $mimeType = $file->getMimeType();

          $filenameToStore = request()->protocolnum . '-' . Carbon::createFromFormat('d/m/Y', request()->protocoldate)->format('Ymd') . '_' . $file->getClientOriginalName();
          $dir = '/arxeio/' . request()->fakelos . '/';
          $savedPath = $file->storeas($dir,$filenameToStore);
        }
          if ($data['keep'] and is_numeric($data['keep'])){
           $dt = Carbon::createFromFormat('d/m/Y', request()->protocoldate);
           $dt->addYears($data['keep']);
           $expires = $dt->format('Ymd');
       }

       Attachment::create([
          'protocol_id' => $protocol_id,
          'ada' => $data["ada$i"],
          'name' => $filename,
          'mimeType' => $mimeType,
          'savedPath' => $savedPath,
          'keep' => $data['keep'],
          'expires' => $expires,
          ]);
     }
 }

 $notification = array(
    'message' => 'Επιτυχημένη καταχώριση.',
    'alert-type' => 'success'
    );
 session()->flash('notification',$notification);

 return redirect("home/$protocol_id");
}


public function update(Protocol $protocol){

    $data = request()->all();

    $id = $protocol->id;
    $oldFakelos = $protocol->fakelos;
    $oldIn_num = $protocol->in_num;
    $oldIn_date = $protocol->in_date;
    $etos = request('etos');

    $config = new Config;
    $protocolValidate = $config->getConfigValueOf('protocolValidate');


    $mustValidate =[
    'protocolnum' => "required|integer|unique:protocols,protocolnum,$id,id,etos,$etos",
    'etos' => 'required|integer|digits:4',
    'protocoldate' => 'required',
    ];
    $this->validate(request(), $mustValidate);

    $notalwaysValidate = [
    'in_date' => 'required_with:in_num,in_topos_ekdosis,in_arxi_ekdosis',
    'in_topos_ekdosis' => 'required_with:in_date,in_arxi_ekdosis|max:255',
    'in_arxi_ekdosis' => 'required_with:in_date,in_topos_ekdosis|max:255',
    'in_paraliptis' => 'required_with:in_date|max:255',
    'out_date' => 'required_with:out_to,out_perilipsi',
    'out_to' => 'required_with:out_date,out_perilipsi|max:255',
    ];

    if($protocolValidate){
        $validator = Validator::make(request()->all(), [
            'thema' => 'required_with:fakelos,in_num,in_date,in_topos_ekdosis,in_arxi_ekdosis,in_paraliptis,in_perilipsi,diekperaiosi,out_date,diekp_date,sxetiko,out_to,out_perilipsi,keywords,paratiriseis|max:255',
            ],  [
            'thema.required_with' => "Συμπληρώστε το θέμα.<br>&nbsp;",
            ])->validate();

        $this->validate(request(), $notalwaysValidate);
    }

    $validator = Validator::make(request()->all(), [
        'protocoldate' => 'regex:/^\d{2}\/\d{2}\/\d{4}$/',
        'in_date' => 'regex:/^\d{2}\/\d{2}\/\d{4}$/',
        'out_date' => 'regex:/^\d{2}\/\d{2}\/\d{4}$/',
        'diekp_date' => 'regex:/^\d{2}\/\d{2}\/\d{4}$/',],  [
        'protocoldate.regex' => "Η ημερομηνία πρέπει να έχει τη μορφή 'ηη/μμ/εεεε'.<br>&nbsp;",
        'in_date.regex' => "Η ημερομηνία πρέπει να έχει τη μορφή 'ηη/μμ/εεεε'.<br>&nbsp;",
        'out_date.regex' => "Η ημερομηνία πρέπει να έχει τη μορφή 'ηη/μμ/εεεε'.<br>&nbsp;",
        'diekp_date.regex' => "Η ημερομηνία πρέπει να έχει τη μορφή 'ηη/μμ/εεεε'.<br>&nbsp;",
        ])->validate();

    $in_date = null;
    $out_date = null;
    $diekp_date = null;
    if($data['in_date']) $in_date = Carbon::createFromFormat('d/m/Y', $data['in_date'])->format('Ymd');
    if($data['out_date']) $out_date = Carbon::createFromFormat('d/m/Y', $data['out_date'])->format('Ymd');
    if($data['diekp_date']) $diekp_date = Carbon::createFromFormat('d/m/Y', $data['diekp_date'])->format('Ymd');


    $in_chkdata = $data['in_chk'];
    if($data['in_num'] == $oldIn_num and $in_date == $oldIn_date )$in_chkdata = '0';

    if ($in_chkdata == '1' ){
        $validator = Validator::make(request()->all(), [
            'in_num' => "unique:protocols,in_num,NULL,id,in_date,$in_date",],  [
            'in_num.unique' => "<center><h4>Ενημέρωση ...</h4><hr></center>Υπάρχει καταχωρημένο πρωτόκολλο με ίδιο<br><br>-> Αριθμό Εισερχομένου και<br>-> Ημ/νία Εισερχομένου<br><br>Θέλετε ωστόσο να προχωρήσετε;<br>&nbsp;",
            ])->validate();
    }

    if($protocol->attachments()->count()){
        if($data['fakelos'] !== $oldFakelos){

            $validator = Validator::make(request()->all(), [
                'fakelos' => "required|in:$oldFakelos",],  [
                'fakelos.required' => "Δεν μπορείτε να αλλάξετε τον Φάκελλο πρωτοκόλλου με συνημμένα αρχεία.<br>Για να επιτευχθεί αυτό πρέπει πρώτα να διαγράψετε τα συνημμένα αρχεία.",
                'fakelos.in' => "Δεν μπορείτε να αλλάξετε τον Φάκελλο πρωτοκόλλου με συνημμένα αρχεία.<br>Για να επιτευχθεί αυτό πρέπει πρώτα να διαγράψετε τα συνημμένα αρχεία.",
                ])->validate();

        }
    }

    Protocol::whereId($id)->update([
        'user_id' => Auth::user()->id ,
        'protocolnum'=> $data['protocolnum'],
        'protocoldate'=> Carbon::createFromFormat('d/m/Y', $data['protocoldate'])->format('Ymd'),
        'etos' => $data['etos'],
        'fakelos' => $data['fakelos'],
        'thema' => $data['thema'],
        'in_num' => $data['in_num'],
        'in_date' => $in_date,
        'in_topos_ekdosis'=>  $data['in_topos_ekdosis'],
        'in_arxi_ekdosis' => $data['in_arxi_ekdosis'],
        'in_paraliptis' => $data['in_paraliptis'],
        'diekperaiosi' => $data['diekperaiosi'],
        'in_perilipsi' => $data['in_perilipsi'],
        'out_date' => $out_date,
        'diekp_date' => $diekp_date,
        'sxetiko' => $data['sxetiko'],
        'out_to' => $data['out_to'],
        'out_perilipsi' => $data['out_perilipsi'],
        'keywords' => $data['keywords'],
        'paratiriseis' => $data['paratiriseis']
        ]);


    $filescount = 3 * $data['file_inputs_count'];

    for ($i = 1 ; $i < $filescount + 1; $i++){
        if ($data["ada$i"] or request()->hasFile("att$i")){
          $filename = NULL;
          $mimeType = NULL;
          $savedPath = NULL;
          $expires = NULL;
          if (request()->hasFile("att$i")){
            $file = request()->file("att$i");

            $filename = $file->getClientOriginalName();
            $mimeType = $file->getMimeType();

            $filenameToStore = request()->protocolnum . '-' . Carbon::createFromFormat('d/m/Y', request()->protocoldate)->format('Ymd') . '_' . $file->getClientOriginalName();
            $dir = '/arxeio/' . request()->fakelos . '/';
            $savedPath = $file->storeas($dir,$filenameToStore);
          }
            if ($data['keep'] and is_numeric($data['keep'])){
             $dt = Carbon::createFromFormat('d/m/Y', request()->protocoldate);
             $dt->addYears($data['keep']);
             $expires = $dt->format('Ymd');
         }

         Attachment::create([
            'protocol_id' => $id,
            'ada' => $data["ada$i"],
            'name' => $filename,
            'mimeType' => $mimeType,
            'savedPath' => $savedPath,
            'keep' => $data['keep'],
            'expires' => $expires,
            ]);
     }
 }

 $notification = array(
    'message' => 'Επιτυχημένη ενημέρωση.',
    'alert-type' => 'success'
    );
 session()->flash('notification',$notification);

 return back();
}

public function delprotocol(Protocol $protocol){
    $protocolnum = $protocol->protocolnum;
    $newprotocolnum = $protocolnum + 1;
    $etos = $protocol->etos;
    if ($protocol->attachments->count()){
        $notification = array(
            'message' => 'Δεν μπορώ να διαγράψω πρωτόκολλο με συνημμένα.',
            'alert-type' => 'error'
            );
        session()->flash('notification',$notification);

        return back();
    }
    Protocol::destroy($protocol->id);

    $notification = array(
        'message' => "Διαγράφηκε το Πρωτόκολλο με αριθμό $protocolnum για το έτος $etos",
        'alert-type' => 'success'
        );
    session()->flash('notification',$notification);

    return redirect("home");
}


public function attachDelete (Attachment $attachment){
    $protocol = $attachment->protocol;
    $savedPath = $attachment->savedPath;
    $trashPath = str_replace('arxeio', 'trash', $savedPath);
    if(Storage::exists($attachment->savedPath)){
        Storage::move($savedPath, $trashPath);
    }
    $attachment->delete();
    return view('getArxeia', compact('protocol'));
}

public function gotonum( $etos, $protocolnum){
    if(request('find')){
        if (Protocol::whereEtos($etos)->where('protocolnum', $protocolnum)->count()){
            $protocol_id = Protocol::whereEtos($etos)->where('protocolnum', $protocolnum)->first()->id ;
            return redirect("home/$protocol_id");
        }
    }else{
        if ($protocolnum <= 0){
            $etos--;
            if (Protocol::whereEtos($etos)->count()){
                $protocol_id = Protocol::whereEtos($etos)->get()->last()->id ;
                return redirect("home/$protocol_id");
            }
            $etos++;
        }else{
            if (Protocol::whereEtos($etos)->where('protocolnum', $protocolnum)->count()){
                $protocol_id = Protocol::whereEtos($etos)->where('protocolnum', $protocolnum)->first()->id ;
                return redirect("home/$protocol_id");
            }
        }

        if ($protocolnum > Protocol::whereEtos($etos)->max('protocolnum') ){
            if ($etos == Protocol::max('etos')){
                return redirect("home");
            }else{
                $etos++;
                if (Protocol::whereEtos($etos)->count()){
                    $protocol_id = Protocol::whereEtos($etos)->get()->first()->id ;
                    return redirect("home/$protocol_id");
                }
                $etos--;
            }
        }
    }

    $notification = array(
        'message' => "Δεν βρέθηκε Πρωτόκολλο με Αριθμό $protocolnum για το έτος $etos.",
        'alert-type' => 'warning'
        );
    session()->flash('notification',$notification);

    return back();
}

public function download(Attachment $attachment){
    if(Storage::exists($attachment->savedPath)){
        $content = Storage::get($attachment->savedPath);
        return response($content)
        ->header('Content-Type', $attachment->mimeType)
        ->header('Content-Disposition', "filename=" . $attachment->name);
    }
    $message = 'Το αρχείο<center>' . $attachment->name . '</center><br>που επιλέξατε δεν υπάρχει!<br>Πιθανόν να έχει διαγραφεί από άλλο εξωτερικό πρόγραμμα!';
    $notification = array(
        'message' => $message,
        'alert-type' => 'info'
    );
    session()->flash('notification',$notification);
    return back();
}


public function find(){

    $fields = array_merge($this->protocolfields,$this->attachmentfields);
    $config = new Config;
    $searchField1 = $config->getConfigValueOf('searchField1');
    $searchField2 = $config->getConfigValueOf('searchField2');
    $searchField3 = $config->getConfigValueOf('searchField3');
    $ipiresiasName = $config->getConfigValueOf('ipiresiasName');
    $titleColorStyle = $this->getTitleColorStyle() ;


    return view('find', compact('fields','searchField1', 'searchField2','searchField3', 'ipiresiasName', 'titleColorStyle'));
}

public function getFindData(){

    $config = new Config;
    $maxRowsInFindPage = $config->getConfigValueOf('maxRowsInFindPage');

    $fields = array_merge($this->protocolfields,$this->attachmentfields);
    $attachmentfields = $this->attachmentfields;

    $wherevalues = [];
    $whereAttachmentvalues = [];

    if(request('aponum')){
        $wherevalues[] = ['protocolnum', '>',request('aponum')-1 ];
    }
    if(request('eosnum')){
        $wherevalues[] = ['protocolnum', '<',request('eosnum')+1 ];
    }
    if(request('etosForMany')){
        $wherevalues[] = ['etos', request('etosForMany')];
    }

    if(request('apoProtocolDate')){
        $wherevalues[] = ['protocoldate', '>=', Carbon::createFromFormat('d/m/Y', request('apoProtocolDate'))->format('Ymd')];
    }
    if(request('eosProtocolDate')){
        $wherevalues[] = ['protocoldate', '<=', Carbon::createFromFormat('d/m/Y', request('eosProtocolDate'))->format('Ymd')];
    }
    if(request('apoEiserxDate')){
        $wherevalues[] = ['in_date', '>=', Carbon::createFromFormat('d/m/Y', request('apoEiserxDate'))->format('Ymd')];
    }
    if(request('eosEiserxDate')){
        $wherevalues[] = ['in_date', '<=', Carbon::createFromFormat('d/m/Y', request('eosEiserxDate'))->format('Ymd')];
    }
    if(request('apoExerxDate')){
        $wherevalues[] = ['out_date', '>=', Carbon::createFromFormat('d/m/Y', request('apoExerxDate'))->format('Ymd')];
    }
    if(request('eosExerxDate')){
        $wherevalues[] = ['out_date', '<=', Carbon::createFromFormat('d/m/Y', request('eosExerxDate'))->format('Ymd')];
    }
    if(request('searchData1')){
      if( array_key_exists(request('searchField1'), $attachmentfields)){
        $whereAttachmentvalues[] = [request('searchField1'),'LIKE', '%' . request('searchData1') . '%' ];
      }else{
        $wherevalues[] = [request('searchField1'),'LIKE', '%' . request('searchData1') . '%' ];
      }
    }
    if(request('searchData2')){
      if( array_key_exists(request('searchField2'),$attachmentfields)){
        $whereAttachmentvalues[] = [request('searchField2'),'LIKE', '%' . request('searchData2') . '%' ];
      }else{
        $wherevalues[] = [request('searchField2'),'LIKE', '%' . request('searchData2')  . '%' ];
      }
    }
    if(request('searchData3')){
      if( array_key_exists(request('searchField3'),$attachmentfields)){
        $whereAttachmentvalues[] =  [request('searchField3'),'LIKE', '%' . request('searchData3')  . '%' ];
      }else{
        $wherevalues[] = [request('searchField3'),'LIKE', '%' . request('searchData3') . '%'  ];
      }
    }
    if (! $wherevalues and ! $whereAttachmentvalues){
        return;
    }

    $foundProtocolsCount = Null;

    $protocols = Protocol::with('attachments');
    if ($wherevalues){
      $protocols = $protocols->where($wherevalues);
    }
    if ($whereAttachmentvalues){
      $protocols = $protocols->whereHas('attachments', function ($query)  use ($whereAttachmentvalues){
           $query->where($whereAttachmentvalues );
     });
   }
    $protocols = $protocols->orderby('protocoldate','desc')->orderby('protocolnum','desc')->take($maxRowsInFindPage);
    $protocols = $protocols->get();
    $foundProtocolsCount = $protocols->count();

    foreach($protocols as $protocol){
        if($protocol->protocoldate) $protocol->protocoldate = Carbon::createFromFormat('Ymd', $protocol->protocoldate)->format('d/m/Y');
    }

    $searchField1 = request('searchField1');
    $searchField2 = request('searchField2');
    $searchField3 = request('searchField3');
    $searchData1 = request('searchData1');
    $searchData2 = request('searchData2');
    $searchData3 = request('searchData3');

    return view('getFindData', compact('protocols', 'foundProtocolsCount' , 'maxRowsInFindPage', 'fields', 'attachmentfields', 'searchField1', 'searchField2', 'searchField3', 'searchData1', 'searchData2', 'searchData3' ));
}

public function printprotocols(){

    $config = new Config;
    $ipiresiasName = $config->getConfigValueOf('ipiresiasName');
    $titleColorStyle = $this->getTitleColorStyle() ;

    return view('print', compact('ipiresiasName', 'titleColorStyle'));
}

public function printed(){
    $config = new Config;
    $ipiresiasName = $config->getConfigValueOf('ipiresiasName');
    $etos = $config->getConfigValueOf('yearInUse');
    $datetime = Carbon::now()->format('d/m/Y H:m:s');

    $wherevalues = [];

    if(request('aponum')){
        $wherevalues[] = ['protocolnum', '>',request('aponum')-1 ];
    }
    if(request('eosnum')){
        $wherevalues[] = ['protocolnum', '<',request('eosnum')+1 ];
    }
    if(request('etosForMany')){
        $wherevalues[] = ['etos', request('etosForMany')];
        $etos = request('etosForMany');
    }
    if(request('apoProtocolDate')){
        $wherevalues[] = ['protocoldate', '>=', Carbon::createFromFormat('d/m/Y', request('apoProtocolDate'))->format('Ymd')];
    }
    if(request('eosProtocolDate')){
        $wherevalues[] = ['protocoldate', '<=', Carbon::createFromFormat('d/m/Y', request('eosProtocolDate'))->format('Ymd')];
    }
    $foundProtocolsCount = null;
    if (! $wherevalues){
        return back();
    }else{
        $foundProtocolsCount = Protocol::where($wherevalues)->count();
        $protocols = Protocol::where($wherevalues)->orderby('protocolnum','asc')->get();
    }
    foreach($protocols as $protocol){
        if($protocol->protocoldate) $protocol->protocoldate = Carbon::createFromFormat('Ymd', $protocol->protocoldate)->format('d/m/Y');
        if($protocol->in_date) $protocol->in_date = Carbon::createFromFormat('Ymd', $protocol->in_date)->format('d/m/Y');
        if($protocol->out_date) $protocol->out_date = Carbon::createFromFormat('Ymd', $protocol->out_date)->format('d/m/Y');
        if($protocol->diekp_date) $protocol->diekp_date = Carbon::createFromFormat('Ymd', $protocol->diekp_date)->format('d/m/Y');
    }

    return view('printed', compact('protocols', 'ipiresiasName' , 'etos', 'datetime'));
}

public function receipt(Protocol $protocol){
    if($protocol->protocoldate) $protocol->protocoldate = Carbon::createFromFormat('Ymd', $protocol->protocoldate)->format('d/m/Y');
    $config = new Config;
    $ipiresiasName = $config->getConfigValueOf('ipiresiasName');
    $datetime = Carbon::now()->format('d/m/Y H:m:s');
    return view('receipt', compact('protocol', 'ipiresiasName', 'datetime'));
}

public function about(){
    return view('about');
}

public function updated(){
    $file = storage_path('conf/.updateCheck');
    unlink($file);
    $config = new Config;
    $config->setConfigValueOf('needsUpdate', 0);
    return redirect("home/list");
}

public function printAttachments(){

    $config = new Config;
    $ipiresiasName = $config->getConfigValueOf('ipiresiasName');
    $titleColorStyle = $this->getTitleColorStyle() ;

    return view('printAttachments', compact('ipiresiasName', 'titleColorStyle'));
}

public function printedAttachments(){
    $config = new Config;
    $ipiresiasName = $config->getConfigValueOf('ipiresiasName');
    $etos = $config->getConfigValueOf('yearInUse');
    $datetime = Carbon::now()->format('d/m/Y H:m:s');

    $wherevalues = [];

    if(request('aponum')){
        $wherevalues[] = ['protocolnum', '>',request('aponum')-1 ];
    }
    if(request('eosnum')){
        $wherevalues[] = ['protocolnum', '<',request('eosnum')+1 ];
    }
    if(request('etosForMany')){
        $wherevalues[] = ['etos', request('etosForMany')];
        $etos = request('etosForMany');
}
    if(request('apoProtocolDate')){
        $wherevalues[] = ['protocoldate', '>=', Carbon::createFromFormat('d/m/Y', request('apoProtocolDate'))->format('Ymd')];
    }
    if(request('eosProtocolDate')){
        $wherevalues[] = ['protocoldate', '<=', Carbon::createFromFormat('d/m/Y', request('eosProtocolDate'))->format('Ymd')];
    }

    $foundProtocolsCount = null;
    if (! $wherevalues){
        return back();
    }else{
        $protocols = Protocol::has('attachments')->where($wherevalues)->orderby('protocolnum','asc')->get();
    }
    foreach($protocols as $protocol){
        if($protocol->protocoldate) $protocol->protocoldate = Carbon::createFromFormat('Ymd', $protocol->protocoldate)->format('d/m/Y');
        if($protocol->in_date) $protocol->in_date = Carbon::createFromFormat('Ymd', $protocol->in_date)->format('d/m/Y');
        if($protocol->out_date) $protocol->out_date = Carbon::createFromFormat('Ymd', $protocol->out_date)->format('d/m/Y');
    }

    return view('printedAttachments', compact('protocols', 'ipiresiasName' , 'etos', 'datetime'));
}

}
