<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Keepvalue;
use App\Config;
use App\Protocol;
use App\Attachment;
use Storage;
use Carbon\Carbon;
use URL;
use Auth;
use Illuminate\Validation\Rule;
use Validator;
use DB;

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

        $fakeloi= Keepvalue::orderBy(DB::raw("MID(`fakelos`,LOCATE('.',`fakelos`)+1,LENGTH(`fakelos`)-(LOCATE('.',`fakelos`)+1))+0<>0 DESC, MID(`fakelos`,LOCATE('.',`fakelos`)+1,LENGTH(`fakelos`)-(LOCATE('.',`fakelos`)+1))+0, `fakelos`"))->select('fakelos', 'describe')->get();

        $config = new Config;
        $newetos = $config->getConfigValueOf('yearInUse')?$config->getConfigValueOf('yearInUse'):Carbon::now()->format('Y');

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
        $newprotocoldate = Carbon::now()->format('d/m/Y');
        $class = 'bg-info';
        $protocoltitle = 'Νέο Πρωτόκολλο';
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
        } 
        $submitVisible = 'active';
        if (Auth::user()->role->role == 'Αναγνώστης') $submitVisible = 'hidden';

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

        $keepval = null;
        if ($protocol->fakelos){
            $keepval = Keepvalue::whereFakelos($protocol->fakelos)->first()->keep;
            if (! $keepval) $keepval = Keepvalue::whereFakelos($protocol->fakelos)->first()->keep_alt;
        }
        
        $config = new Config;
        $allowUserChangeKeepSelect = $config->getConfigValueOf('allowUserChangeKeepSelect');

        $years = Keepvalue::whereNotNull('keep')->select('keep')->distinct()->orderby('keep', 'asc')->get();
        $words = Keepvalue::whereNotNull('keep_alt')->select('keep_alt')->distinct()->orderby('keep_alt', 'asc')->get();
        
        return view('protocol', compact('fakeloi', 'protocol', 'newetos', 'currentEtos', 'newprotocolnum', 'newprotocoldate', 'in_date', 'out_date', 'diekp_date', 'class', 'protocoltitle', 'protocolArrowStep', 'submitVisible','delVisible', 'ipiresiasName', 'readonly', 'years', 'words', 'keepval', 'allowUserChangeKeepSelect'));
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
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,1); 
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13'); // Set a user agent
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HEADER, false);
                $commits = json_decode(curl_exec($curl));
                curl_close($curl);
                
                if ($commits){
                    if(Auth::user()->role_description() == "Διαχειριστής"){
                        $message = 'Έγιναν τροποποιήσεις στον κώδικα του Ηλ.Πρωτοκόλλου. (Github)<br><br>Αν επιθυμείτε ενημερώστε την εγκατάστασή σας.<br><br>Για να μην εμφανίζεται το παρόν μήνυμα καντε κλικ στο menu Διαχείριση->Ενημερώθηκε.';
                    }else{
                        $message = 'Έγιναν τροποποιήσεις στον κώδικα του Ηλ.Πρωτοκόλλου. (Github)<br><br>Ενημερώστε το Διαχειριστή.';
                    }
                    $file = storage_path('conf/.updateCheck');
                    if (file_exists($file )){
                        if ( $commits[0]->sha != file_get_contents($file)){
                                $notification = array(
                                    'message' =>  $message, 
                                    'alert-type' => 'info'
                                    );
                                session()->flash('notification',$notification);
                                $config->setConfigValueOf('needsUpdate', 1);
                            }
                    }else{
                        file_put_contents($file,$commits[0]->sha);
                    }
                }
            }
        }
        return redirect('/home/list');
    }

    public function indexList(){
        $config = new Config;
        $ipiresiasName = $config->getConfigValueOf('ipiresiasName');
        $refreshInterval = $config->getConfigValueOf('minutesRefreshInterval') * 60000;
        $needsUpdate = False;
        if (strpos ( request()->headers->get('referer') , 'login')){
            $needsUpdate = $config->getConfigValueOf('needsUpdate');
        }

        $protocols = Protocol::orderby('etos','desc')->orderby('protocolnum','desc')->paginate($config->getConfigValueOf('showRowsInPage'));
        foreach($protocols as $protocol){
            if($protocol->protocoldate) $protocol->protocoldate = Carbon::createFromFormat('Ymd', $protocol->protocoldate)->format('d/m/Y');
            if($protocol->in_date) $protocol->in_date = Carbon::createFromFormat('Ymd', $protocol->in_date)->format('d/m/Y');
            if($protocol->out_date) $protocol->out_date = Carbon::createFromFormat('Ymd', $protocol->out_date)->format('d/m/Y');
            if($protocol->diekp_date) $protocol->diekp_date = Carbon::createFromFormat('Ymd', $protocol->diekp_date)->format('d/m/Y');
        }
        return view('protocolList', compact('protocols', 'ipiresiasName', 'refreshInterval', 'needsUpdate'));
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
    Protocol::create([
        'user_id' => Auth::user()->id ,
        'protocolnum'=> $newprotocolnum,
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
    }else{ // αλλιώς εισάγω τον Αρ.Πρ που έστειλε η φόρμα
    Protocol::create([
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
    }

    $filescount = 3 * $data['file_inputs_count'];
    $protocol_id = Protocol::max('id');

    for ($i = 1 ; $i < $filescount + 1; $i++){
        if (request()->hasFile("att$i")){
            $file = request()->file("att$i");

            $filename = $file->getClientOriginalName();
            $mimeType = $file->getMimeType();
            $expires = NULL;

            $filenameToStore = request()->protocolnum . '-' . Carbon::createFromFormat('d/m/Y', request()->protocoldate)->format('Ymd') . '_' . $file->getClientOriginalName();
            $dir = '/arxeio/' . request()->fakelos . '/';
            $savedPath = $file->storeas($dir,$filenameToStore);
            if ($data['keep'] and is_numeric($data['keep'])){
             $dt = Carbon::createFromFormat('d/m/Y', request()->protocoldate);
             $dt->addYears($data['keep']);
             $expires = $dt->format('Ymd');
         }

         Attachment::create([
            'protocol_id' => $protocol_id,
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
        if (request()->hasFile("att$i")){
            $file = request()->file("att$i");

            $filename = $file->getClientOriginalName();
            $mimeType = $file->getMimeType();
            $expires = NULL;
            
            $filenameToStore = request()->protocolnum . '-' . Carbon::createFromFormat('d/m/Y', request()->protocoldate)->format('Ymd') . '_' . $file->getClientOriginalName();
            $dir = '/arxeio/' . request()->fakelos . '/';
            $savedPath = $file->storeas($dir,$filenameToStore);
            if ($data['keep'] and is_numeric($data['keep'])){
             $dt = Carbon::createFromFormat('d/m/Y', request()->protocoldate);
             $dt->addYears($data['keep']);
             $expires = $dt->format('Ymd');
         }

         Attachment::create([
            'protocol_id' => $id,
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
    Storage::move($savedPath, $trashPath);
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
    $content = Storage::get($attachment->savedPath);
    return response($content)
    ->header('Content-Type', $attachment->mimeType)
    ->header('Content-Disposition', "filename=" . $attachment->name);
}


public function find(){

    $fields = [
    'fakelos' => 'Φάκελος',
    'thema' => 'Θέμα',
    'in_topos_ekdosis' => 'Τόπος έκδοσης',
    'in_arxi_ekdosis' => 'Αρχή έκδοσης',
    'in_paraliptis' => 'Παραλήπτης',
    'diekperaiosi' => 'Διεκπεραίωση',
    'in_perilipsi' => 'Περιλ. Εισερχ',
    'out_to' => 'Απευθύνεται',
    'out_perilipsi' => 'Περιλ. Εξερχ',
    'keywords' => 'Λέξεις κλειδιά',
    'paratiriseis' => 'Παρατηρήσεις'
    ];

    $config = new Config;
    $searchField1 = $config->getConfigValueOf('searchField1');
    $searchField2 = $config->getConfigValueOf('searchField2');
    $searchField3 = $config->getConfigValueOf('searchField3');
    $ipiresiasName = $config->getConfigValueOf('ipiresiasName');


    return view('find', compact('fields','searchField1', 'searchField2','searchField3', 'ipiresiasName'));
}

public function getFindData(){

    $config = new Config;
    $maxRowsInFindPage = $config->getConfigValueOf('maxRowsInFindPage');

    $wherevalues = [];

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
        $wherevalues[] = [request('searchField1'),'LIKE', '%' . request('searchData1') . '%' ];
    }
    if(request('searchData2')){
        $wherevalues[] = [request('searchField2'),'LIKE', '%' . request('searchData2')  . '%' ];
    }
    if(request('searchData3')){
        $wherevalues[] = [request('searchField3'),'LIKE', '%' . request('searchData3') . '%'  ];
    }
    $foundProtocolsCount = null;
    if (! $wherevalues){
        return;
    }else{
        $foundProtocolsCount = Protocol::where($wherevalues)->count();
        $protocols = Protocol::where($wherevalues)->orderby('protocoldate','desc')->orderby('protocolnum','desc')->take($maxRowsInFindPage)->get();
    }
    foreach($protocols as $protocol){
        if($protocol->protocoldate) $protocol->protocoldate = Carbon::createFromFormat('Ymd', $protocol->protocoldate)->format('d/m/Y');
    }

    $fields = [
    'fakelos' => 'Φάκελος',
    'thema' => 'Θέμα',
    'in_topos_ekdosis' => 'Τόπος έκδοσης',
    'in_arxi_ekdosis' => 'Αρχή έκδοσης',
    'in_paraliptis' => 'Παραλήπτης',
    'diekperaiosi' => 'Διεκπεραίωση',
    'in_perilipsi' => 'Περιλ. Εισερχ',
    'out_to' => 'Απευθύνεται',
    'out_perilipsi' => 'Περιλ. Εξερχ',
    'keywords' => 'Λέξεις κλειδιά',
    'paratiriseis' => 'Παρατηρήσεις'
    ];
    $searchField1 = request('searchField1');
    $searchField2 = request('searchField2');
    $searchField3 = request('searchField3');
    $searchData1 = request('searchData1');
    $searchData2 = request('searchData2');
    $searchData3 = request('searchData3');

    return view('getFindData', compact('protocols', 'foundProtocolsCount' , 'maxRowsInFindPage', 'fields', 'searchField1', 'searchField2', 'searchField3', 'searchData1', 'searchData2', 'searchData3' ));

}

public function printprotocols(){

    $fields = [
    'fakelos' => 'Φάκελος',
    'thema' => 'Θέμα',
    'in_topos_ekdosis' => 'Τόπος έκδοσης',
    'in_arxi_ekdosis' => 'Αρχή έκδοσης',
    'in_paraliptis' => 'Παραλήπτης',
    'diekperaiosi' => 'Διεκπεραίωση',
    'in_perilipsi' => 'Περιλ. Εισερχ',
    'out_to' => 'Απευθύνεται',
    'out_perilipsi' => 'Περιλ. Εξερχ',
    'keywords' => 'Λέξεις κλειδιά',
    'paratiriseis' => 'Παρατηρήσεις'
    ];

    $config = new Config;
    $searchField1 = $config->getConfigValueOf('searchField1');
    $searchField2 = $config->getConfigValueOf('searchField2');
    $searchField3 = $config->getConfigValueOf('searchField3');
    $ipiresiasName = $config->getConfigValueOf('ipiresiasName');

    return view('print', compact('fields','searchField1', 'searchField2','searchField3', 'ipiresiasName'));
}

public function printed(){
    $wherevalues = [];

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
    $config = new Config;
    $ipiresiasName = $config->getConfigValueOf('ipiresiasName');
    $etos = $config->getConfigValueOf('yearInUse');
    $datetime = Carbon::now()->format('d/m/Y H:m:s');

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

}
