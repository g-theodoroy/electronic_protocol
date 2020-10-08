<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Keepvalue;
use App\Config;
use App\Protocol;
use App\Attachment;
use App\User;
use App\Role;
use Storage;
use Carbon\Carbon;
use URL;
use Auth;
use Illuminate\Validation\Rule;
use Validator;
use DB;
use Active;
use Illuminate\Support\Facades\Mail;
use Webklex\IMAP\Facades\Client;

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


    public function index( Protocol $protocol){
        $writers_admins = User::get_writers_and_admins();
        $fakeloi = Keepvalue::orderBy(DB::raw("SUBSTR(`fakelos`,3,LENGTH(`fakelos`)-3)+0<>0 DESC, SUBSTR(`fakelos`,3,LENGTH(`fakelos`)-(3))+0, `fakelos`"))->select('fakelos', 'describe')->get();
        // διαβάζω τις ρυθμίσεις
        $config = new Config;
        $newetos = $config->getConfigValueOf('yearInUse')?$config->getConfigValueOf('yearInUse'):Carbon::now()->format('Y');
        $showUserInfo = $config->getConfigValueOf('showUserInfo');
        $firstProtocolNum = $config->getConfigValueOf('firstProtocolNum');
        $protocolArrowStep = $config->getConfigValueOf('protocolArrowStep');
        $allowWriterUpdateProtocol = $config->getConfigValueOf('allowWriterUpdateProtocol');
        $allowWriterUpdateProtocolTimeInMinutes = $config->getConfigValueOf('allowWriterUpdateProtocolTimeInMinutes');
        $protocolValidate = $config->getConfigValueOf('protocolValidate');
        $diavgeiaUrl = $config->getConfigValueOf('diavgeiaUrl');
        $allowUserChangeKeepSelect = $config->getConfigValueOf('allowUserChangeKeepSelect');
        $allowListValuesMatchingInput = $config->getConfigValueOf('allowListValuesMatchingInput');

        // βρίσκω το νέο αριθμό πρωτοκόλλου
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
              $q->where('role_id', '!=',   Role::whereRole('Αναγνώστης')->first()->id) ;
            })->count();
          // αν είναι πάνω από ένας δεν εμφανίζω τον επόμενο Αρ.Πρωτ.
        $newprotocolnumvisible = 'active';
        if ($activeuserscount > 1 and ! $protocol->id) $newprotocolnumvisible = 'hidden';

        // συμπληρώνω τα σποιχεία αν πρόκειται για επεξεργασία
        $newprotocoldate = Carbon::now()->format('d/m/Y');
        $class = 'bg-info';
        $protocoltitle = 'Νέο Πρωτόκολλο';
        $protocolUser = '';
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

        $time2update = 0;
        $submitVisible = 'active';

        // ΔΙΑΚΑΙΩΜΑΤΑ ΑΠΟΘΗΚΕΥΣΗΣ
        // ΑΠΟΚΡΥΨΗ ΤΟΥ ΚΟΥΜΠΙΟΥ ΑΠΟΘΗΚΕΥΣΗ
        // 1 αν ο χρήστης είναι Αναγνώστης
        if (Auth::user()->role->role == 'Αναγνώστης'){
          $submitVisible = 'hidden';
          $class = 'bg-warning';
          $protocoltitle = 'Πρωτόκολλο';
        }
        // 2 αν ο χρήστης είναι Συγγραφέας ή Αναθέτων
        if(in_array ( Auth::user()->role_description(), [ "Συγγραφέας",  "Αναθέτων"])) {
            // αν είναι παλιό πρωτόκολλο (έχει id) ΕΠΕΞΕΡΓΑΣΙΑ ΠΡΩΤΟΚΟΛΛΟΥ
            if($protocol->id){
              // αν η μεταβλητή είναι 0 ή null δηλαδή δεν επιτρέπεται τροποποίηση από Συγγραφείς
              if ( ! $allowWriterUpdateProtocol){
                $submitVisible = 'hidden';
              // αν η μεταβλητή είναι 1 δηλαδή επιτρέπεται μόνο στον Συγγραφέα που καταχώρισε το Πρ.
              }elseif($allowWriterUpdateProtocol == 1){
                // αν ο Συγγραφέας ΔΕΝ είναι ο ίδιος
                if ($protocol->user_id <> Auth::user()->id ){
                    $submitVisible = 'hidden';
                }else{
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
              // Αν περνώντας τα παραπάνω τεστ μπορεί να τροποιήσει αρχίζω αντίστροφη μέτρηση
              if($submitVisible == 'active'){
                $time2update = $allowWriterUpdateProtocolTimeInMinutes * 60 - (Carbon::now()->getTimestamp() - $protocol->updated_at->getTimestamp());
                $class = 'bg-success';
              }else{
                $class = 'bg-warning';
                $protocoltitle = 'Πρωτόκολλο';
              }
              // Αν το πρωτόκολλο δεν έχει θέμα (είναι δηλαδή κενό) ακυρώνονται όλα τα παραπάνω
              if (! $protocol->thema){
                $submitVisible = 'active';
                $class = 'bg-success';
                $time2update = 0;
              }
              // Αν είναι προς διεκπεραίωση από το χρήστη => επιτρέπεται η επεξεργασία
              if ($protocol->diekperaiosi == Auth::user()->id and ! $protocol->diekp_date){
                $class = 'bg-success';
                $submitVisible = 'active';
              }elseif($protocol->diekperaiosi == Auth::user()->id and $protocol->diekp_date){
                // Αν έχει διεκπεραιωθεί αρχίζω αντίστροφη μέτρηση
                // αν τα λεπτά είναι μεγαλύτερα του 0 τότε ελέγχεται ο χρόνος που πέρασε και μετά κρύβεται το κουμπί
                if ($allowWriterUpdateProtocolTimeInMinutes and $protocol->updated_at->getTimestamp() < Carbon::now()->subMinutes($allowWriterUpdateProtocolTimeInMinutes)->getTimestamp()){
                  $submitVisible = 'hidden';
                }
              }
              // Αν ο χρήστης είναι Αναθέτων και το Πρωτόκολλο δεν έχει ανάτεθεί σε διεκπεραιωτή επιτρέπω τροποποίηση
              if(Auth::user()->role_description() ==  "Αναθέτων") {
                if( ! $protocol->diekperaiosi ){
                  $class = 'bg-success';
                  $submitVisible = 'active';
                }
              }

          }
        }

        // Αν ο χρήστης είναι Διαχειριστής και οι έλεγχοι καταχώρισης είναι ΟΧΙ
        // ανοίγω και πεδία που κανονικά είναι κλειδωμενα (Αρ.Πρωτ, Ημνια , Έτος, ...)
        $readonly = 'readonly';
        $delVisible = 'hidden';
        if(! $protocolValidate){
            if ( Auth::user()->role->role == 'Διαχειριστής'){
                $readonly ='';
                $class = 'bg-danger';
                $delVisible = 'active';
            }
        }

        // Μόνο Διαχειριστής και Αναθέτων μπορούν να αναθέσουν Διεκπεραίωση σε χρήστη
        $forbidenChangeDiekperaiosiSelect = 1;
        if(in_array ( Auth::user()->role_description(), [ "Διαχειριστής",  "Αναθέτων"])) $forbidenChangeDiekperaiosiSelect = null;

        // βρίσκω την τιμή διατήρησης ανάλογα τον φάκελο Φ.
        $keepval = null;
        if ($protocol->fakelos  and Keepvalue::whereFakelos($protocol->fakelos)->first()){
            $keepval = Keepvalue::whereFakelos($protocol->fakelos)->first()->keep;
            if (! $keepval) $keepval = Keepvalue::whereFakelos($protocol->fakelos)->first()->keep_alt;
        }

        // γεμίζω τη λίστα με τη διατήρηση αρχείων
        $years = Keepvalue::whereNotNull('keep')->select('keep')->distinct()->orderby('keep', 'asc')->get();
        $words = Keepvalue::whereNotNull('keep_alt')->select('keep_alt')->distinct()->orderby('keep_alt', 'asc')->get();

        return view('protocol', compact('fakeloi', 'protocol', 'newetos', 'newprotocolnum', 'newprotocoldate', 'in_date', 'out_date', 'diekp_date', 'class', 'protocoltitle', 'protocolArrowStep', 'submitVisible','delVisible', 'readonly', 'years', 'words', 'keepval', 'allowUserChangeKeepSelect', 'diavgeiaUrl', 'activeusers2show', 'showUserInfo' , 'newprotocolnumvisible', 'protocolUser', 'time2update', 'writers_admins', 'forbidenChangeDiekperaiosiSelect', 'allowListValuesMatchingInput'));
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

    public function indexList($filter = null, $userId = null){
        $writers_admins = User::get_writers_and_admins();
        $config = new Config;
        $refreshInterval = $config->getConfigValueOf('minutesRefreshInterval') * 60;
        $needsUpdate = False;
        if (strpos ( request()->headers->get('referer') , 'login')){
            $needsUpdate = $config->getConfigValueOf('needsUpdate');
        }
        $wideListProtocol = $config->getConfigValueOf('wideListProtocol');
        $diavgeiaUrl = $config->getConfigValueOf('diavgeiaUrl');
        $showUserInfo = $config->getConfigValueOf('showUserInfo');

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
        $protocoltitle = 'Πρωτόκολλο';
        $user2show = '';
        $protocols = Protocol::orderby('etos','desc')->orderby('protocolnum','desc');
        if(! $userId ){
          if($filter == 'd'){
            $protocols = $protocols->where('diekperaiosi', Auth::user()->id)->whereNull('diekp_date');
            $protocoltitle = 'Πρωτόκολλο προς Διεκπεραίωση';
          }elseif($filter == 'f'){
            $protocols = $protocols->where('diekperaiosi', Auth::user()->id)->wherenotNull('diekp_date');
            $protocoltitle = 'Πρωτόκολλο Διεκπεραιώθηκε';
          }
        }elseif(User::whereId($userId)->count() and $filter){
            if ($showUserInfo == 1){
              $user2show = User::whereId($userId)->first('username')->username;
            }elseif($showUserInfo == 2){
              $user2show = User::whereId($userId)->first('name')->name;
            }
            if($filter == 'd'){
              $protocols = $protocols->where('diekperaiosi', $userId)->whereNull('diekp_date');
              $protocoltitle = "$user2show, προς Διεκπεραίωση";
            }elseif($filter == 'f'){
              $protocols = $protocols->where('diekperaiosi', $userId)->wherenotNull('diekp_date');
              $protocoltitle = "$user2show, Διεκπεραιώθηκε";
            }else{
              $protocols = $protocols->where('user_id', $userId);
              $protocoltitle = "$user2show, Πρωτόκολλο";
            }
          }else{
          if($filter == 'd'){
            $protocols = $protocols->where('diekperaiosi', '!=', '')->whereNull('diekp_date');
            $protocoltitle = "Όλοι οι χρήστες, προς Διεκπεραίωση";
          }elseif($filter == 'f'){
            $protocols = $protocols->where('diekperaiosi', '!=', '')->wherenotNull('diekp_date');
            $protocoltitle = "Όλοι οι χρήστες, Διεκπεραιώθηκε";
          }
        }
        $protocols = $protocols->paginate($config->getConfigValueOf('showRowsInPage'));
        foreach($protocols as $protocol){
            if($protocol->protocoldate) $protocol->protocoldate = Carbon::createFromFormat('Ymd', $protocol->protocoldate)->format('d/m/Y');
            if($protocol->in_date) $protocol->in_date = Carbon::createFromFormat('Ymd', $protocol->in_date)->format('d/m/Y');
            if($protocol->out_date) $protocol->out_date = Carbon::createFromFormat('Ymd', $protocol->out_date)->format('d/m/Y');
            if($protocol->diekp_date) $protocol->diekp_date = Carbon::createFromFormat('Ymd', $protocol->diekp_date)->format('d/m/Y');
            if($protocol->fakelos and Keepvalue::whereFakelos($protocol->fakelos)->first()) $protocol->describe .= Keepvalue::whereFakelos($protocol->fakelos)->first()->describe;

        }
        return view('protocolList', compact('protocols', 'refreshInterval', 'needsUpdate', 'wideListProtocol', 'diavgeiaUrl', 'activeusers2show', 'writers_admins', 'protocoltitle'));
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
            'thema' => 'nullable|required_with:fakelos,in_num,in_date,in_topos_ekdosis,in_arxi_ekdosis,in_paraliptis,in_perilipsi,diekperaiosi,out_date,diekp_date,sxetiko,out_to,out_perilipsi,keywords,paratiriseis|max:255',
            ],  [
            'thema.required_with' => "Συμπληρώστε το θέμα.<br>&nbsp;",
            ])->validate();

        $this->validate(request(), $notalwaysValidate);
    }


    $validator = Validator::make(request()->all(), [
        'protocoldate' => 'regex:/^\d{2}\/\d{2}\/\d{4}$/',
        'in_date' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
        'out_date' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
        'diekp_date' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',],  [
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
    if ($data['keywords']){
      $keywords = rtrim($data['keywords'], ',');
    }
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

  try{
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
        'keywords' => $keywords,
        'paratiriseis' => $data['paratiriseis']
        ]);
      } catch (\Exception $e) {
          $notification = array(
             'message' => 'Υπήρξε κάποιο πρόβλημα στην καταχώριση του Πρωτοκόλλου<br>Παρακαλώ επαναλάβετε την καταχώριση.',
             'alert-type' => 'error'
             );
          session()->flash('notification',$notification);
          return redirect("home");
        }

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
          // αφαίρεση απαγορευμένων χαρακτήρων από το όνομα του συνημμένου
          $filename = $this->filter_filename($filename, false);

          $mimeType = $file->getMimeType();

          $filenameToStore = request()->protocolnum . '-' . Carbon::createFromFormat('d/m/Y', request()->protocoldate)->format('Ymd') . '_' . $filename;
          $dir = '/arxeio/' . request()->fakelos . '/';
          $savedPath = $file->storeas($dir,$filenameToStore);
        }
          if ($data['keep'] and is_numeric($data['keep'])){
           $dt = Carbon::createFromFormat('d/m/Y', request()->protocoldate);
           $dt->addYears($data['keep']);
           $expires = $dt->format('Ymd');
       }
    try{
       Attachment::create([
          'protocol_id' => $protocol_id,
          'ada' => $data["ada$i"],
          'name' => $filename,
          'mimeType' => $mimeType,
          'savedPath' => $savedPath,
          'keep' => $data['keep'],
          'expires' => $expires,
          ]);
        } catch (\Exception $e) {
        $notification = array(
           'message' => 'Υπήρξε κάποιο πρόβλημα στην καταχώριση των συνημμένων αρχείων<br>Ελέγξτε αν καταχωρίστηκαν όλα σωστά.',
           'alert-type' => 'error'
           );
        session()->flash('notification',$notification);
        return redirect("home/$protocol_id");
      }
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
    //'in_paraliptis' => 'required_with:in_date|max:255',
    'out_date' => 'required_with:out_to,out_perilipsi',
    'out_to' => 'required_with:out_date,out_perilipsi|max:255',
    ];

    if($protocolValidate){
        $validator = Validator::make(request()->all(), [
          'thema' => 'nullable|required_with:fakelos,in_num,in_date,in_topos_ekdosis,in_arxi_ekdosis,in_paraliptis,in_perilipsi,diekperaiosi,out_date,diekp_date,sxetiko,out_to,out_perilipsi,keywords,paratiriseis|max:255',
            ],  [
            'thema.required_with' => "Συμπληρώστε το θέμα.<br>&nbsp;",
            ])->validate();

        $this->validate(request(), $notalwaysValidate);
    }

    $validator = Validator::make(request()->all(), [
        'protocoldate' => 'regex:/^\d{2}\/\d{2}\/\d{4}$/',
        'in_date' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
        'out_date' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
        'diekp_date' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',],  [
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
    if ($data['keywords']){
      $keywords = rtrim($data['keywords'], ',');
    }


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
    try{
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
        'keywords' => $keywords,
        'paratiriseis' => $data['paratiriseis']
        ]);
      } catch (\Exception $e) {
        $notification = array(
           'message' => 'Υπήρξε κάποιο πρόβλημα στην ενημέρωση του Πρωτοκόλλου<br>Παρακαλώ επαναλάβετε την ενημέρωση.',
           'alert-type' => 'error'
           );
        session()->flash('notification',$notification);
        return redirect("home/$protocol_id");
      }


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
            // αφαίρεση απαγορευμένων χαρακτήρων από το όνομα του συνημμένου
            $filename = $this->filter_filename($filename, false);

            $mimeType = $file->getMimeType();

            $filenameToStore = request()->protocolnum . '-' . Carbon::createFromFormat('d/m/Y', request()->protocoldate)->format('Ymd') . '_' . $filename;
            $dir = '/arxeio/' . request()->fakelos . '/';
            $savedPath = $file->storeas($dir,$filenameToStore);
          }
            if ($data['keep'] and is_numeric($data['keep'])){
             $dt = Carbon::createFromFormat('d/m/Y', request()->protocoldate);
             $dt->addYears($data['keep']);
             $expires = $dt->format('Ymd');
         }
        try{
         Attachment::create([
            'protocol_id' => $id,
            'ada' => $data["ada$i"],
            'name' => $filename,
            'mimeType' => $mimeType,
            'savedPath' => $savedPath,
            'keep' => $data['keep'],
            'expires' => $expires,
            ]);
          } catch (\Exception $e) {
          $notification = array(
             'message' => 'Υπήρξε κάποιο πρόβλημα στην καταχώριση των συνημμένων αρχείων<br>Ελέγξτε αν καταχωρίστηκαν όλα σωστά.',
             'alert-type' => 'error'
             );
          session()->flash('notification',$notification);
          return redirect("home/$protocol_id");
     }
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
    $attachmentfields = $this->attachmentfields;
    $config = new Config;
    $searchField1 = $config->getConfigValueOf('searchField1');
    $searchField2 = $config->getConfigValueOf('searchField2');
    $searchField3 = $config->getConfigValueOf('searchField3');

    return view('find', compact('fields', 'attachmentfields', 'searchField1', 'searchField2','searchField3'));
}

public function getFindData(){

    $config = new Config;
    $maxRowsInFindPage = $config->getConfigValueOf('maxRowsInFindPage');

    $fields = array_merge($this->protocolfields,$this->attachmentfields);
    $attachmentfields = $this->attachmentfields;

    $wherevalues = [];
    $whereNullFields = [];
    $whereAttachmentvalues = [];
    $whereNullAttachmentvalues = [];

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
    if(request('searchData1') or request('searchData1chk')){
      if(request('searchData1chk')){
        if( array_key_exists(request('searchField1'), $attachmentfields)){
          $whereNullAttachmentvalues[] = request('searchField1');
        }else{
          $whereNullFields[] = request('searchField1');
        }
      }else{
      if( array_key_exists(request('searchField1'), $attachmentfields)){
        $whereAttachmentvalues[] = [request('searchField1'),'LIKE', '%' . request('searchData1') . '%' ];
      }else{
        $wherevalues[] = [request('searchField1'),'LIKE', '%' . request('searchData1') . '%' ];
      }
    }
    }
    if(request('searchData2') or request('searchData2chk') ){
      if(request('searchData2chk')){
        if( array_key_exists(request('searchField2'), $attachmentfields)){
          $whereNullAttachmentvalues[] = request('searchField2');
        }else{
          $whereNullFields[] = request('searchField2');
        }
      }else{
      if( array_key_exists(request('searchField2'),$attachmentfields)){
        $whereAttachmentvalues[] = [request('searchField2'),'LIKE', '%' . request('searchData2') . '%' ];
      }else{
        $wherevalues[] = [request('searchField2'),'LIKE', '%' . request('searchData2')  . '%' ];
      }
      }
    }
    if(request('searchData3') or request('searchData3chk')){
      if(request('searchData3chk')){
        if( array_key_exists(request('searchField3'), $attachmentfields)){
          $whereNullAttachmentvalues[] = request('searchField3');
        }else{
          $whereNullFields[] = request('searchField3');
        }
      }else{
      if( array_key_exists(request('searchField3'),$attachmentfields)){
        $whereAttachmentvalues[] =  [request('searchField3'),'LIKE', '%' . request('searchData3')  . '%' ];
      }else{
        $wherevalues[] = [request('searchField3'),'LIKE', '%' . request('searchData3') . '%'  ];
      }
      }
    }
    if (! $wherevalues and ! $whereAttachmentvalues and ! $whereNullFields and ! $whereNullAttachmentvalues){
        return;
    }

    $foundProtocolsCount = Null;

    $protocols = Protocol::with('attachments');
    foreach($whereNullFields as $whereNullField){
      $protocols = $protocols->whereNull($whereNullField);
    }
    if ($wherevalues){
      $protocols = $protocols->where($wherevalues);
    }
    foreach($whereNullAttachmentvalues as $whereNullAttachmentvalue){
      $protocols = $protocols->whereHas('attachments', function ($query)  use ($whereNullAttachmentvalue){
           $query->whereNull($whereNullAttachmentvalue );
     });
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

    return view('print');
}

public function printed(){
    $config = new Config;
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

    return view('printed', compact('protocols', 'etos', 'datetime'));
}

public function receipt(Protocol $protocol){
    if($protocol->protocoldate) $protocol->protocoldate = Carbon::createFromFormat('Ymd', $protocol->protocoldate)->format('d/m/Y');
    $config = new Config;
    $datetime = Carbon::now()->format('d/m/Y H:m:s');
    return view('receipt', compact('protocol', 'datetime'));
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
    return view('printAttachments');
}

public function printedAttachments(){
    $config = new Config;
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

    return view('printedAttachments', compact('protocols', 'etos', 'datetime'));
}

public function getEmailNum(){
  $config = new Config;
  $defaultImapEmail = $config->getConfigValueOf('defaultImapEmail');
  // Alternative by using the Facade
  $oClient = Client::account($defaultImapEmail);
  try{
  //Connect to the IMAP Server
  $oClient->connect();
  } catch (\Exception $e) {
  return 0;
  }
  $aMessageNum = $oClient->countMessages();
  return $aMessageNum;
}

public function viewEmails(){

  $config = new Config;
  $allowUserChangeKeepSelect = $config->getConfigValueOf('allowUserChangeKeepSelect');
  $emailNumFetch = $config->getConfigValueOf('emailNumFetch');
  $defaultImapEmail = $config->getConfigValueOf('defaultImapEmail');

  // Alternative by using the Facade
  $oClient = Client::account($defaultImapEmail);
  try{
  //Connect to the IMAP Server
  $oClient->connect();
  } catch (\Exception $e) {
  $notification = array(
  'message' => "Η σύνδεση με τον λογαριασμό email απέτυχε.<br>Ελέγξτε τις ρυθμίσεις.",
  'alert-type' => 'error'
  );
  session()->flash('notification',$notification);
  return back();
  }
  $fakeloi = Keepvalue::orderBy(DB::raw("SUBSTR(`fakelos`,3,LENGTH(`fakelos`)-3)+0<>0 DESC, SUBSTR(`fakelos`,3,LENGTH(`fakelos`)-(3))+0, `fakelos`"))->select('fakelos', 'describe')->get();
  // γεμίζω τη λίστα με τη διατήρηση αρχείων
  $years = Keepvalue::whereNotNull('keep')->select('keep')->distinct()->orderby('keep', 'asc')->get();
  $words = Keepvalue::whereNotNull('keep_alt')->select('keep_alt')->distinct()->orderby('keep_alt', 'asc')->get();

  $aMessageNum = $oClient->countMessages();
  /** @var \Webklex\IMAP\Folder $oFolder */
  $oFolder = $oClient->getFolder('INBOX');
  $aMessage = $oFolder->query()->whereAll()->limit($emailNumFetch)->get();

      return view('viewEmails', compact('aMessage', 'aMessageNum', 'defaultImapEmail', 'fakeloi', 'allowUserChangeKeepSelect', 'years', 'words'));
}

public function viewEmailAttachment( $messageUid, $attachmentKey){
  $config = new Config;
  $defaultImapEmail = $config->getConfigValueOf('defaultImapEmail');
// Alternative by using the Facade
  $oClient = Client::account(  $defaultImapEmail);
  //Connect to the IMAP Server
  try{
  //Connect to the IMAP Server
  $oClient->connect();
  } catch (\Exception $e) {
  $notification = array(
  'message' => "Η σύνδεση με τον λογαριασμό email απέτυχε.<br>Ελέγξτε τις ρυθμίσεις.",
  'alert-type' => 'error'
  );
  session()->flash('notification',$notification);
  return back();
  }
  /** @var \Webklex\IMAP\Folder $oFolder */
  $oFolder = $oClient->getFolder('INBOX');
  $oMessage = $oFolder->getMessage($messageUid, null, null, true, true, false);
  $aAttachment = $oMessage->getAttachments();
  $oAttachment = $aAttachment->get($attachmentKey);
  $content = $oAttachment->getContent();
  return response($content)
  ->header('Content-Type', $oAttachment->getMimeType())
  ->header('Content-Disposition', "filename=" . $oAttachment->getName());
}

public function setEmailRead($messageUid){
  $config = new Config;
  $defaultImapEmail = $config->getConfigValueOf('defaultImapEmail');
// Alternative by using the Facade
  $oClient = Client::account($defaultImapEmail);
  //Connect to the IMAP Server
  try{
  //Connect to the IMAP Server
  $oClient->connect();
  } catch (\Exception $e) {
  $notification = array(
  'message' => "Η σύνδεση με τον λογαριασμό email απέτυχε.<br>Ελέγξτε τις ρυθμίσεις.",
  'alert-type' => 'error'
  );
  session()->flash('notification',$notification);
  return back();
  }

  if(! $oClient->getFolder('INBOX.beenRead')) $oClient->createFolder('INBOX.beenRead');

  /** @var \Webklex\IMAP\Folder $oFolder */
  $oFolder = $oClient->getFolder('INBOX');
  $oMessage = $oFolder->getMessage($messageUid, null, null, false, false, false);
  $oMessage->moveToFolder('INBOX.beenRead');
  $notification = array(
  'message' => "Το μήνυμα μεταφέρθηκε στα Αναγνωσμένα",
  'alert-type' => 'success'
  );
  session()->flash('notification',$notification);
return back();
}

public function storeFromEmail(){

  $data = request()->all();
  $uid = $data['uid'];
  isset($data["fakelos$uid"]) ? $fakelos = $data["fakelos$uid"]:$fakelos = null ;
  isset($data["keep$uid"]) ? $keep = $data["keep$uid"]:$keep = null ;
  $sendReceipt = $data["sendReceipt$uid"];
  $chkboxes = array_filter($data, function($k) use ($uid) {
    return strpos($k, "chk$uid-") !== false;
  }, ARRAY_FILTER_USE_KEY);
  $attachmentKeys = array();
  foreach ($chkboxes as $key => $val){
     $attachmentKeys[] = substr($key, strpos($key, "-")+1);
  }

  $config = new Config;
  $defaultImapEmail = $config->getConfigValueOf('defaultImapEmail');
  // Alternative by using the Facade
  $oClient = Client::account($defaultImapEmail);
  //Connect to the IMAP Server
  try{
  //Connect to the IMAP Server
  $oClient->connect();
  } catch (\Exception $e) {
  $notification = array(
  'message' => "Η σύνδεση με τον λογαριασμό email απέτυχε.<br>Ελέγξτε τις ρυθμίσεις.",
  'alert-type' => 'error'
  );
  session()->flash('notification',$notification);
  return back();
  }
  /** @var \Webklex\IMAP\Folder $oFolder */
  // αν δεν υπάρχει ο φακελος INBOX.inProtocol τον φτιάχνω
  if(! $oClient->getFolder('INBOX.inProtocol')) $oClient->createFolder('INBOX.inProtocol');

  $oFolder = $oClient->getFolder('INBOX');
  $oMessage = $oFolder->getMessage($uid, null, null, true, true, false);


  $thema = $oMessage->getSubject();
  $in_num = Carbon::createFromFormat('Y-m-d H:i:s', $oMessage->getDate())->format('H:i:s');
  $in_date = Carbon::createFromFormat('Y-m-d H:i:s', $oMessage->getDate())->format('Ymd');
  if( $oMessage->getFrom()[0]->personal ) {
    if(mb_detect_encoding($oMessage->getFrom()[0]->personal, 'UTF-8, ISO-8859-7', true)== 'ISO-8859-7') {
        $in_arxi_ekdosis = iconv("ISO-8859-7", "UTF-8//IGNORE", $oMessage->getFrom()[0]->personal);
    }else{
        $in_arxi_ekdosis = $oMessage->getFrom()[0]->personal;
    }
     $in_arxi_ekdosis .= " <";
  }
    $in_arxi_ekdosis .= $oMessage->getFrom()[0]->mail;
  if($oMessage->getFrom()[0]->personal){
    $in_arxi_ekdosis .= ">";
  }
  $in_paraliptis = Auth::user()->name;
  $in_perilipsi = substr($oMessage->getTextBody(), 0, 250);
  $paratiriseis = 'παρελήφθη με email';

  $etos = Carbon::now()->format('Y');

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

    if(Protocol::whereIn_num($in_num)->whereThema($thema)->count()){
      $protocolExists = Protocol::whereIn_num($in_num)->whereThema($thema)->first(['protocolnum', 'etos', 'protocoldate']);
      $message ='Υπάρχει ήδη καταχωρισμένο Πρωτόκολλο από ληφθέν email με ίδιο <strong>Θέμα</strong> και <strong>Ημνία αποστολής</strong> με τα παρακάτω στοιχεία: <br>Αρ Πρωτ: ' . $protocolExists->protocolnum . '<br>Ημνία: ' . Carbon::createFromFormat('Ymd', $protocolExists->protocoldate)->format('d/m/Y') . '<br>Έτος: ' . $protocolExists->etos . '.' ;
      $notification = array(
          'message' =>  $message ,
          'alert-type' => 'warning'
          );
      session()->flash('notification',$notification);
      return back();
    }
    try{
      $protocolCreated = Protocol::create([
    'user_id' => Auth::user()->id ,
    'protocolnum'=> $newprotocolnum,
    'protocoldate'=> Carbon::now()->format('Ymd'),
    'etos' => $etos,
    'fakelos' => $fakelos,
    'thema' => $thema,
    'in_num' => $in_num,
    'in_date' => $in_date,
    'in_topos_ekdosis'=>  null,
    'in_arxi_ekdosis' => $in_arxi_ekdosis,
    'in_paraliptis' => $in_paraliptis,
    'diekperaiosi' => null,
    'in_perilipsi' => $in_perilipsi,
    'out_date' => null,
    'diekp_date' => null,
    'sxetiko' => null,
    'out_to' => null,
    'out_perilipsi' => null,
    'keywords' => null,
    'paratiriseis' => $paratiriseis
    ]);
    } catch (\Exception $e) {
      $notification = array(
         'message' => 'Υπήρξε κάποιο πρόβλημα στην καταχώριση του email στο Πρωτόκολλο<br>Παρακαλώ επαναλάβετε την καταχώριση.',
         'alert-type' => 'error'
         );
      session()->flash('notification',$notification);
      return back();
    }

if ($protocolCreated){
  $message = "Το email καταχωρίστηκε.";
}

$protocol = Protocol::where("etos",$etos)->where('protocolnum', $newprotocolnum)->first();

// αποθηκεύω το email σαν συνημμένο html
$html = view('viewEmail', compact('oMessage'))->render();
$filename = 'email_' . Carbon::createFromFormat('Y-m-d H:i:s', $oMessage->getDate())->format('Ymd_His') . '.html';
$filenameToStore = $protocol->protocolnum . '-' . $protocol->protocoldate . '_' . $filename;
$dir = '/arxeio/emails/';
$savedPath = $dir . $filenameToStore;
Storage::put($savedPath, $html);
$expires = null;
if ($keep and is_numeric($keep)){
 $dt = Carbon::createFromFormat('Ymd', $protocol->protocoldate);
 $dt->addYears($keep);
 $expires = $dt->format('Ymd');
}

try{
Attachment::create([
'protocol_id' => $protocol->id,
'ada' => null,
'name' => $filename,
'mimeType' => 'text/html',
'savedPath' => $savedPath,
'keep' => $keep,
'expires' => $expires,
]);

// αποθηκεύω τα συνημμένα
$aAttachment = $oMessage->getAttachments();
$numCreatedAttachments = 0;
$numMissedAttachments = 0;
foreach($attachmentKeys as $attachmentKey){
      $oAttachment = $aAttachment->get($attachmentKey);
      if(!$oAttachment) {
        $numMissedAttachments++;
        continue;
      }
      $content = $oAttachment->getContent();
      $mimeType = $oAttachment->getMimeType();
      $filename = $oAttachment->getName();

      // αφαίρεση απαγορευμένων χαρακτήρων από το όνομα του συνημμένου
      $filename = $this->filter_filename($filename, false);

      $filenameToStore = $protocol->protocolnum . '-' . $protocol->protocoldate . '_' . $filename;

      $dir = '/arxeio/' . $fakelos . '/';
      $savedPath = $dir . $filenameToStore;
      Storage::put($savedPath, $content);

   $createdAttachment = Attachment::create([
      'protocol_id' => $protocol->id,
      'ada' => null,
      'name' => $filename,
      'mimeType' => $mimeType,
      'savedPath' => $savedPath,
      'keep' => $keep,
      'expires' => $expires,
      ]);
      if($createdAttachment)$numCreatedAttachments++;
 }
} catch (\Exception $e) {
$notification = array(
   'message' => 'Υπήρξε κάποιο πρόβλημα στην καταχώριση των συνημμένων αρχείων<br>Ελέγξτε αν καταχωρίστηκαν όλα σωστά.',
   'alert-type' => 'error'
   );
session()->flash('notification',$notification);
return redirect("home/$protocol_id");
}

if($numCreatedAttachments) $message .= "<br>Εισήχθηκαν $numCreatedAttachments συνημμένα αρχεία.";
if($numMissedAttachments) $message .= "<br><br>Παραλείφθηκαν $numMissedAttachments συνημμένα αρχεία.<br> Δοκιμαστε να τα εισάγετε στο Ηλ. Πρωτόκολλο χειροκίνητα αφού πρώτα τα αποθηκεύσετε στο δίσκο σας";

if($sendReceipt){
  if($protocol->protocoldate) $protocol->protocoldate = Carbon::createFromFormat('Ymd', $protocol->protocoldate)->format('d/m/Y');
  $emaildate = $oMessage->getDate();
  $html = view('receiptEmail', compact('protocol', 'emaildate'))->render();
  Mail::send([], [], function ($message) use ($oMessage, $html)
{
    $message->from(config('mail.from.address'));
    if ($oMessage->getReplyTo()){
      $message->to($oMessage->getReplyTo()[0]->mail);
    }else{
      $message->to($oMessage->getFrom()[0]->mail);
    }
    $message->subject("Καταχώριση email στο Ηλεκτρονικό Πρωτόκολλο.");
    $message->setBody($html,'text/html');
});
if(! count(Mail::failures()))$message .= "<br>Στάλθηκε με email αποδεικτικό καταχώρισης.";
}

// μεταφέρωτο μύνημα στα πρωτοκολλημένα
 $oMessage->moveToFolder('INBOX.inProtocol');

 $alertType ='success';
 if($numMissedAttachments)$alertType ='warning';

 $notification = array(
 'message' => $message,
 'alert-type' => $alertType
 );
 session()->flash('notification',$notification);

return back();
}

public function getValues($term, $field, $id, $divId, $multi){
  $protocols = Protocol::where( $field , 'like', "%" . $term . "%")->distinct($field)->orderby($field)->get($field);
  if (! $protocols ) return;

    if($multi){
      $valuesArray = [];
        foreach($protocols as $protocol) {
          $valuesArray = array_merge($valuesArray, preg_split('/\s*,\s*/', $protocol->$field));
        }
          $collection = collect($valuesArray)->unique()->sortBy('Key')->values()->all();
          foreach ($collection as $value){
            if (mb_stristr($this->removeAccents($value), $this->removeAccents($term))) {
            $output .= '<li><a href="#" onclick="javascript:appendValue(\''.$id.'\',\''.$value.'\',\''.$divId.'\',\''.$multi.'\')">'.e($value).'</a></li>
            ';
          }
        }
    }else{
        foreach($protocols as $protocol) {
            $value = $protocol->$field;
            $output .= '<li><a href="#" onclick="javascript:appendValue(\''.$id.'\',\''.$value.'\',\''.$divId.'\',\''.$multi.'\')">'.e($value).'</a></li>
            ';
        }
    }
  echo $output;
}

/**
 * Replace accented characters with non accented
 *
 * @param $str
 * @return mixed
 * @link http://myshadowself.com/coding/php-function-to-convert-accented-characters-to-their-non-accented-equivalant/
 */
public function removeAccents($str) {
  $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ', 'Ά', 'ά', 'Έ', 'έ', 'Ό', 'ό', 'Ώ', 'ώ', 'Ί', 'ί', 'ϊ', 'ΐ', 'Ύ', 'ύ', 'ϋ', 'ΰ', 'Ή', 'ή');
  $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', 'Α', 'α', 'Ε', 'ε', 'Ο', 'ο', 'Ω', 'ω', 'Ι', 'ι', 'ι', 'ι', 'Υ', 'υ', 'υ', 'υ', 'Η', 'η');
  return str_replace($a, $b, $str);
}

public function filter_filename($filename, $beautify=true) {
    // sanitize filename
    $filename = preg_replace(
        '~
        [<>:"/\\|?*]|            # file system reserved https://en.wikipedia.org/wiki/Filename#Reserved_characters_and_words
        [\x00-\x1F]|             # control characters http://msdn.microsoft.com/en-us/library/windows/desktop/aa365247%28v=vs.85%29.aspx
        [\x7F\xA0\xAD]|          # non-printing characters DEL, NO-BREAK SPACE, SOFT HYPHEN
        [#\[\]@!$&\'()+,;=]|     # URI reserved https://tools.ietf.org/html/rfc3986#section-2.2
        [{}^\~`]                 # URL unsafe characters https://www.ietf.org/rfc/rfc1738.txt
        ~ux',
        '-', $filename);
    // avoids ".", ".." or ".hiddenFiles"
    $filename = ltrim($filename, '.-');
    // optional beautification
    if ($beautify) $filename = $this->beautify_filename($filename);
    // maximize filename length to 255 bytes http://serverfault.com/a/9548/44086
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $filename = mb_strcut(pathinfo($filename, PATHINFO_FILENAME), 0, 255 - ($ext ? strlen($ext) + 1 : 0), mb_detect_encoding($filename)) . ($ext ? '.' . $ext : '');
    return $filename;
}

public function beautify_filename($filename) {
    // reduce consecutive characters
    $filename = preg_replace(array(
        // "file   name.zip" becomes "file-name.zip"
        '/ +/',
        // "file___name.zip" becomes "file-name.zip"
        '/_+/',
        // "file---name.zip" becomes "file-name.zip"
        '/-+/'
    ), '-', $filename);
    $filename = preg_replace(array(
        // "file--.--.-.--name.zip" becomes "file.name.zip"
        '/-*\.-*/',
        // "file...name..zip" becomes "file.name.zip"
        '/\.{2,}/'
    ), '.', $filename);
    // lowercase for windows/unix interoperability http://support.microsoft.com/kb/100625
    $filename = mb_strtolower($filename, mb_detect_encoding($filename));
    // ".file-name.-" becomes "file-name"
    $filename = trim($filename, '.-');
    return $filename;
}

}
