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
use App\Exports\ProtocolExport;
use Maatwebsite\Excel\Facades\Excel;

use ZBateson\MailMimeParser\Message;
use ZBateson\MailMimeParser\MailMimeParser;
use ZBateson\MailMimeParser\Header\HeaderConsts;

//use Illuminate\Support\Facades\Log;
//Log::info('test');

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
    protected $protocolfields = [
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
    protected $attachmentfields = [
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
        $this->middleware('writer:home/list', ['except' => ['index', 'indexList', 'getFileInputs', 'gotonum', 'download', 'find', 'getFindData', 'printprotocols', 'printed', 'about', 'setDiekpDate']]);
        $this->middleware('email', ['only' => ['viewEmails', 'setEmailRead', 'storeFromEmail']]);
        $this->middleware('limitList', ['only' => ['printprotocols', 'printAttachments']]);
    }


    public function index(Protocol $protocol)
    {
        // αν δεν είναι νέο πρωτόκολλο και οι συγγραφείς περιορίζονται στη λίστα διεκπεραιωσης
        if ($this->limitProtocolAccessList()) {
            // αν δεν είναι νέο πρωτόκολλο και οι συγγραφείς περιορίζονται στη λίστα διεκπεραιωσης και δεν έχει καταχωριστεί από το χρήστη
            if ($protocol->id && strpos($protocol->diekperaiosi  . ',' , 'd' . Auth::user()->id . ',' ) === false && strpos($protocol->diekperaiosi . ',' , 'e' . Auth::user()->id . ',' ) === false && $protocol->user_id !== Auth::user()->id){
                return abort(404);
            }
        }
         // βρίσκω τους Συγγραφείς, Αναθέτοντες και Διαχειριστές
        $writers_admins = User::get_writers_and_admins();
        // παίρνω τους Φακέλους με συγκεκριμμένη ταξινόμηση
        $fakeloi = Keepvalue::orderBy(DB::raw("SUBSTR(`fakelos`,3,LENGTH(`fakelos`)-3)+0<>0 DESC, SUBSTR(`fakelos`,3,LENGTH(`fakelos`)-(3))+0, `fakelos`"))->select('fakelos', 'describe')->get();

        // διαβάζω τις ρυθμίσεις
        $newetos = Config::getConfigValueOf('yearInUse') ? Config::getConfigValueOf('yearInUse') : Carbon::now()->format('Y');
        $showUserInfo = Config::getConfigValueOf('showUserInfo');
        $firstProtocolNum = Config::getConfigValueOf('firstProtocolNum');
        $protocolArrowStep = Config::getConfigValueOf('protocolArrowStep');
        $allowWriterUpdateProtocol = Config::getConfigValueOf('allowWriterUpdateProtocol');
        $allowWriterUpdateProtocolTimeInMinutes = Config::getConfigValueOf('allowWriterUpdateProtocolTimeInMinutes') ? Config::getConfigValueOf('allowWriterUpdateProtocolTimeInMinutes'): 0 ;
        $protocolValidate = Config::getConfigValueOf('protocolValidate');
        $diavgeiaUrl = Config::getConfigValueOf('diavgeiaUrl');
        $allowUserChangeKeepSelect = Config::getConfigValueOf('allowUserChangeKeepSelect');
        $allowListValuesMatchingInput = Config::getConfigValueOf('allowListValuesMatchingInput');

        // βρίσκω το νέο αριθμό πρωτοκόλλου
        if (Protocol::count()) {
            if (Config::getConfigValueOf('yearInUse')) {
                $newprotocolnum = Protocol::whereEtos($newetos)->max('protocolnum') ? Protocol::whereEtos($newetos)->max('protocolnum') + 1 : 1;
            } else {
                $newprotocolnum = Protocol::last()->protocolnum ? Protocol::last()->protocolnum + 1 : 1;
            }
        } else {
            if ($firstProtocolNum) {
                $newprotocolnum = $firstProtocolNum;
            } else {
                $newprotocolnum = 1;
            }
        }

        // βρίσκω τους όλους ενεργούς χρήστες
        $activeusers = Active::users()->mostRecent()->get();
        $activeusers2show = [];
        foreach ($activeusers as $actuser) {
            if ($showUserInfo == 1) {
                $activeusers2show[] = $actuser['user']['username'];
            } elseif ($showUserInfo == 2) {
                $activeusers2show[] = $actuser['user']['name'];
            }
        }
        // μετράω μόνο τους Διαχειριστές και Συγγραφείς που έχουν δικαίωμα να γράψουν
        $activeuserscount = Active::users()->whereHas('user', function ($q) {
            $q->where('role_id', '!=', Role::whereRole('Αναγνώστης')->first()->id);
        })->count();
        // αν είναι πάνω από ένας δεν εμφανίζω τον επόμενο Αρ.Πρωτ.
        $newprotocolnumvisible = 'active';
        if ($activeuserscount > 1 and !$protocol->id) {
            $newprotocolnumvisible = 'hidden';
        }

        // συμπληρώνω τα στοιχεία αν πρόκειται για νέο πρωτόκολλο
        // ή επεξεργασία πρωτοκόλλου
        $newprotocoldate = Carbon::now()->format('d/m/Y');
        $class = 'bg-info';
        $protocoltitle = 'Νέο Πρωτόκολλο';
        $protocolUser = '';
        if ($protocol->etos) {
            $newetos = $protocol->etos;
        }
        if ($protocol->protocolnum) {
            $newprotocolnum = $protocol->protocolnum;
        }
        if ($protocol->protocoldate) {
            $newprotocoldate = Carbon::createFromFormat('Ymd', $protocol->protocoldate)->format('d/m/Y');
        }
        $in_date = null;
        if ($protocol->in_date) {
            $in_date = Carbon::createFromFormat('Ymd', $protocol->in_date)->format('d/m/Y');
        }
        $out_date = null;
        if ($protocol->out_date) {
            $out_date = Carbon::createFromFormat('Ymd', $protocol->out_date)->format('d/m/Y');
        }
        $diekp_date = null;
        if ($protocol->diekp_date) {
            $diekp_date = Carbon::createFromFormat('Ymd', $protocol->diekp_date)->format('d/m/Y');
        }
        if ($protocol->protocolnum) {
            $class = 'bg-success';
            $protocoltitle = 'Επεξεργασία Πρωτοκόλλου';
            $protocolUser = User::whereId($protocol->user_id)->first();
        }
        // η μεταβλητή $time2update ορίζει πόσο χρόνο σε λεπτά μπορεί να επεξεργαστεί
        // ένα πρωτόκολλο ο χρήστης. Το 0 σημαίνει χωρίς περιορισμό
        // αρχικοποίηση μεταβλητής για απεριόριστο χρόνο
        $time2update = 0;
        // έλεγχος τί έχει συμπληρωθεί
        //$headWithData = false;
        //if($protocol->fakelos || $protocol->thema) $headWithData = true;
        // εισερχόμενο πρωτόκολλο
        $inWithData = false;
        if($protocol->in_num || $protocol->in_date || $protocol->in_topos_ekdosis || $protocol->in_arxi_ekdosis || $protocol->in_paraliptis || $protocol->in_perilipsi) $inWithData = true;
        // εξερχόμενο πρωτόκολλο
        $outWithData = false;
        if ($protocol->out_to || $protocol->out_date || $protocol->out_perilipsi) $outWithData = true;
        // έλεγχος αν επιτρέπεται η ενημέρωση
        // αν δεν έχει περάσει ο χρόνος που ορίζεται στις ρυθμίσεις
        // η μεταβλητή γίνεται true = επεξεργασία
        // μόνο σε ενημέρωση και όχι σε νέο πρωτόκολλο
        if ($protocol->id){
            // αν ο χρόνος που πέρασε από την πρώτη καταχώριση είναι μέσα στα όρια επιτρέπεται η επεξεργασία
            $inTimeFirstCommit = false;
            if ($protocol->created_at->getTimestamp() > Carbon::now()->subMinutes($allowWriterUpdateProtocolTimeInMinutes)->getTimestamp()) $inTimeFirstCommit = true;
            // αν ο χρόνος που πέρασε από την την τελευταία ενημέρωση είναι μέσα στα όρια επιτρέπεται η επεξεργασία
            $inTimeLastEdit = false;
            if ($protocol->updated_at->getTimestamp() > Carbon::now()->subMinutes($allowWriterUpdateProtocolTimeInMinutes)->getTimestamp()) $inTimeLastEdit = true;
        }

        // έλεγχος των κουμπιών που θα φαίνονται ή θα κρύβονται ανάλογα με το ρόλο του χρήστη
        // αρχικές ρυθμίσεις -> όλα επιτρέπονται
        $submitVisible = 'active';
        $readerVisible = 'hidden';
        $headReadonly = '';
        $inReadonly = '';
        $outReadonly = '';
        $diekpDateReadonly = '';

        // ΔΙΚΑΙΩΜΑΤΑ ΑΠΟΘΗΚΕΥΣΗΣ
        // ΑΠΟΚΡΥΨΗ ΤΟΥ ΚΟΥΜΠΙΟΥ ΑΠΟΘΗΚΕΥΣΗ
        // 1 αν ο χρήστης είναι Αναγνώστης μπορεί μόνο να ενημερώσει την ημνια διεκπεραίωσης και τίποτε άλλο
        if (Auth::user()->role->role == 'Αναγνώστης') {
            $submitVisible = 'hidden';
            $headReadonly = 'readonly';
            $inReadonly = 'readonly';
            $outReadonly = 'readonly';
            $diekpDateReadonly = 'readonly';
            $class = 'bg-warning';
            $protocoltitle = 'Πρωτόκολλο';
            if ($protocol->diekperaiosi && strpos($protocol->diekperaiosi  . ',' , 'd' . Auth::user()->id . ',' ) !== false) {
                $readerVisible = 'active';
                $diekpDateReadonly = '';
                if ($allowWriterUpdateProtocolTimeInMinutes && $protocol->user_id == Auth::user()->id) {
                    // αν έχει περάσει ο χρόνος κρύβεται
                    if (! $inTimeLastEdit) {
                        $readerVisible = 'hidden';
                        $diekpDateReadonly = 'readonly';
                    }
                }
                if ($readerVisible == 'active' && $protocol->diekp_date) {
                    $time2update = $allowWriterUpdateProtocolTimeInMinutes * 60 - (Carbon::now()->getTimestamp() - $protocol->updated_at->getTimestamp());
                }
            }
        }
        // 2 αν ο χρήστης είναι Συγγραφέας ή Αναθέτων
        if (in_array(Auth::user()->role_description(), ["Συγγραφέας",  "Αναθέτων"])) {
            // αν είναι παλιό πρωτόκολλο (έχει id) ΕΠΕΞΕΡΓΑΣΙΑ ΠΡΩΤΟΚΟΛΛΟΥ
            if ($protocol->id) {
                // αν η μεταβλητή είναι 0 ή null δηλαδή δεν επιτρέπεται τροποποίηση από Συγγραφείς
                if (!$allowWriterUpdateProtocol) {
                    $submitVisible = 'hidden';
                    $headReadonly = 'readonly';
                    $inReadonly = 'readonly';
                    $outReadonly = 'readonly';
                    $diekpDateReadonly = 'readonly';
                    // αν η μεταβλητή είναι 1 δηλαδή επιτρέπεται μόνο στον Συγγραφέα που καταχώρισε το Πρ.
                } elseif ($allowWriterUpdateProtocol == 1) {
                    // αν ο Συγγραφέας ΔΕΝ είναι ο ίδιος ΚΡΥΒΕΤΑΙ
                    if ($protocol->user_id <> Auth::user()->id) {
                        $submitVisible = 'hidden';
                        $headReadonly = 'readonly';
                        $inReadonly = 'readonly';
                        $outReadonly = 'readonly';
                        $diekpDateReadonly = 'readonly';
                    } else {
                        // αν τα λεπτά είναι μεγαλύτερα του 0 τότε ελέγχεται ο χρόνος που πέρασε και μετά κρύβεται το κουμπί
                        if ($allowWriterUpdateProtocolTimeInMinutes) {
                            // εκτός ορίων Κλειστα όλα
                            if (! $inTimeFirstCommit && ! $inTimeLastEdit) {
                                $submitVisible = 'hidden';
                                $headReadonly = 'readonly';
                                $inReadonly = 'readonly';
                                $outReadonly = 'readonly';
                                $diekpDateReadonly = 'readonly';
                            }
                            // εκτός από την αρχική καταχώριση - εντός από τελευταία ενημέρωση
                            // ανοιχτά σε επεξεργασία μόνο τα εξερχόμενα
                            if(!$inTimeFirstCommit && $inTimeLastEdit){
                                // έχει δεδομένα εισερχόμενα και εξερχόμενα -> ανοιχτά μόνο εξερχόμενα
                                if ($inWithData && $outWithData) {
                                    $headReadonly = 'readonly';
                                    $inReadonly = 'readonly';
                                }
                                // έχει δεδομένα εξερχόμενα -> ανοιχτά φάκελος-θέμα και εξερχόμενα
                                if (!$inWithData && $outWithData) {
                                    $inReadonly = 'readonly';
                                }
                            }
                        }
                    }
                    // αν η μεταβλητή είναι 2 δηλαδή επιτρέπεται σε κάθε Συγγραφέα να τροποποιήσει
                } else {
                    // αν τα λεπτά είναι μεγαλύτερα του 0 τότε ελέγχεται ο χρόνος που πέρασε και μετά κρύβεται το κουμπί
                     if ($allowWriterUpdateProtocolTimeInMinutes) {
                            // εκτός ορίων Κλειστα όλα
                        if (!$inTimeFirstCommit && !$inTimeLastEdit) {
                            $submitVisible = 'hidden';
                            $headReadonly = 'readonly';
                            $inReadonly = 'readonly';
                            $outReadonly = 'readonly';
                            $diekpDateReadonly = 'readonly';
                        }
                        if (!$inTimeFirstCommit && $inTimeLastEdit) {
                            // έχει δεδομένα εισερχόμενα και εξερχόμενα -> ανοιχτά μόνο εξερχόμενα
                            if ( $inWithData && $outWithData) {
                                $headReadonly = 'readonly';
                                $inReadonly = 'readonly';
                            }
                            // έχει δεδομένα εξερχόμενα -> ανοιχτά φάκελος-θέμα και εξερχόμενα
                            if (! $inWithData && $outWithData) {
                                $inReadonly = 'readonly';
                            }
                        }
                    }
                }
                // Αν περνώντας τα παραπάνω τεστ μπορεί να τροποιήσει αρχίζω αντίστροφη μέτρηση
                if ($submitVisible == 'active') {
                    $time2update = $allowWriterUpdateProtocolTimeInMinutes * 60 - (Carbon::now()->getTimestamp() - $protocol->updated_at->getTimestamp());
                    $class = 'bg-success';
                } else {
                    $class = 'bg-warning';
                    $protocoltitle = 'Πρωτόκολλο';
                }
                // Αν το πρωτόκολλο δεν έχει θέμα (είναι δηλαδή κενό) ακυρώνονται όλα τα παραπάνω
                if (!$protocol->thema) {
                    $submitVisible = 'active';
                    $headReadonly = '';
                    $inReadonly = '';
                    $outReadonly = '';
                    $diekpDateReadonly = '';
                    $class = 'bg-success';
                    $time2update = 0;
                }
                // Αν είναι προς διεκπεραίωση από το χρήστη => επιτρέπεται η επεξεργασία μόνο εξερχομένου
                if (strpos($protocol->diekperaiosi  . ',' , 'd' . Auth::user()->id . ',' ) !== false and !$protocol->diekp_date) {
                    $class = 'bg-success';
                    $submitVisible = 'active';
                    $headReadonly = 'readonly';
                    $inReadonly = 'readonly';
                    $outReadonly = '';
                    $diekpDateReadonly = '';
                    $time2update = 0;
                } elseif (strpos($protocol->diekperaiosi  . ',' , 'd' . Auth::user()->id . ',' ) !== false and $protocol->diekp_date) {
                    // Αν έχει διεκπεραιωθεί αρχίζω αντίστροφη μέτρηση
                    // αν τα λεπτά είναι μεγαλύτερα του 0 τότε ελέγχεται ο χρόνος που πέρασε και μετά κρύβεται το κουμπί
                    if ($allowWriterUpdateProtocolTimeInMinutes and ! $inTimeLastEdit) {
                        $submitVisible = 'hidden';
                        $headReadonly = 'readonly';
                        $inReadonly = 'readonly';
                        $outReadonly = 'readonly';
                        $diekpDateReadonly = 'readonly';
                    }
                }
                /*
                // αν δεν έχει συμπληρωμένα εξερχόμενα παραμένει ανοιχτό μόνο για εξερχόμενα
                if(! $outWithData && ! $inTimeFirstCommit && ! $inTimeLastEdit){
                    $submitVisible = '';
                    $headReadonly = 'readonly';
                    $inReadonly = 'readonly';
                    $outReadonly = '';
                    $diekpDateReadonly = 'readonly';
                    $time2update = 0;
                }
                */
                 // αν δεν έχει οριστεί χρόνος που μπορεί ο συγγραφέας να επεξεργαστεί
                // τότε η επεξεργασία επιτρέπεται και ανοίγουν όλα
                if(! $allowWriterUpdateProtocolTimeInMinutes){
                    $class = 'bg-success';
                    $submitVisible = 'active';
                    $headReadonly = '';
                    $inReadonly = '';
                    $outReadonly = '';
                    $diekpDateReadonly = '';
                    $time2update = 0;
                }
            }
        }

        // Αν ο χρήστης είναι Διαχειριστής και οι έλεγχοι καταχώρισης είναι ΟΧΙ
        // ανοίγω και πεδία που κανονικά είναι κλειδωμενα (Αρ.Πρωτ, Ημνια , Έτος, ...)
        $readonly = 'readonly';
        $delVisible = 'hidden';
        if (!$protocolValidate) {
            if (Auth::user()->role->role == 'Διαχειριστής') {
                $readonly = '';
                $class = 'bg-danger';
                $delVisible = 'active';
            }
        }

        // Μόνο Διαχειριστής και Αναθέτων μπορούν να αναθέσουν Διεκπεραίωση σε χρήστη
        $forbidenChangeDiekperaiosiSelect = 1;
        if (in_array(Auth::user()->role_description(), ["Διαχειριστής",  "Αναθέτων"])) {
            $forbidenChangeDiekperaiosiSelect = null;
        }

        // βρίσκω την τιμή διατήρησης ανάλογα τον φάκελο Φ.
        $keepval = null;
        if ($protocol->fakelos  and Keepvalue::whereFakelos($protocol->fakelos)->first()) {
            $keepval = Keepvalue::whereFakelos($protocol->fakelos)->first()->keep;
            if (!$keepval) {
                $keepval = Keepvalue::whereFakelos($protocol->fakelos)->first()->keep_alt;
            }
        }

        // γεμίζω τη λίστα με τη διατήρηση αρχείων
        $years = Keepvalue::whereNotNull('keep')->select('keep')->distinct()->orderby('keep', 'asc')->get();
        $words = Keepvalue::whereNotNull('keep_alt')->select('keep_alt')->distinct()->orderby('keep_alt', 'asc')->get();

        return view('protocol', compact('fakeloi', 'protocol', 'newetos', 'newprotocolnum', 'newprotocoldate', 'in_date', 'out_date', 'diekp_date', 'class', 'protocoltitle', 'protocolArrowStep', 'submitVisible', 'delVisible', 'readonly', 'readerVisible', 'years', 'words', 'keepval', 'allowUserChangeKeepSelect', 'diavgeiaUrl', 'activeusers2show', 'showUserInfo', 'newprotocolnumvisible', 'protocolUser', 'time2update', 'writers_admins', 'forbidenChangeDiekperaiosiSelect', 'allowListValuesMatchingInput','headReadonly', 'inReadonly', 'outReadonly', 'diekpDateReadonly'));
    }

    public function chkForUpdates()
    {
        // και ενημέρωση του Χρήστη αν η ρύθμιση επιτρέπεται ο έλεγχος για ενημερώσεις updatesAutoCheck = 1
        $updatesAutoCheck = Config::getConfigValueOf('updatesAutoCheck');
        if ($updatesAutoCheck) {
            // μόνο όταν γίνεται login
            if (strpos(request()->headers->get('referer'), 'login')) {
                try {
                    // έλεγχος εάν έχουν γίνει αλλαγές στο github
                    $url = 'https://api.github.com/repos/g-theodoroy/electronic_protocol/commits';
                    $opts = ['http' => ['method' => 'GET', 'header' => ['User-Agent: PHP']]];
                    $context = stream_context_create($opts);
                    $json = file_get_contents($url, false, $context);
                    $commits = json_decode($json, true);
                } catch (\Throwable $e) {
                    report($e);
                    $commits = null;
                }
                // εάν υπάρχουν commits
                if ($commits) {
                    if (Auth::user()->role_description() == "Διαχειριστής") {
                        $message = 'Έγιναν τροποποιήσεις στον κώδικα του Ηλ.Πρωτοκόλλου στο Github.<br><br>Αν επιθυμείτε <a href=\"https://github.com/g-theodoroy/electronic_protocol/commits/master\" target=\"_blank\"><u> εξετάστε τον κώδικα</u></a> και ενημερώστε την εγκατάστασή σας.<br><br>Για να μην εμφανίζεται το παρόν μήνυμα καντε κλικ στο menu Διαχείριση->Ενημερώθηκε.';
                    } else {
                        $message = 'Έγιναν τροποποιήσεις στον κώδικα του Ηλ.Πρωτοκόλλου στο Github.<br><br>Ενημερώστε το Διαχειριστή.';
                    }
                    // διαβάζω από το αρχείο .updateCheck το id του τελευταίου αποθηκευμένου commit
                    $file = storage_path('conf/.updateCheck');
                    if (file_exists($file)) {
                        // αν διαφέρει με το id του τελευταίου commit στο github
                        // στέλνω ειδοποίηση για την υπάρχουσα ενημέρωση
                        if ($commits[0]['sha'] != file_get_contents($file)) {
                            $notification = array(
                                'message' =>  $message,
                                'alert-type' => 'info'
                            );
                            session()->flash('notification', $notification);
                            // αλλάζω τη μετσβλητή needsUpdate σε true(1)
                            Config::setConfigValueOf('needsUpdate', 1);
                        }
                    } else {
                        // αν δεν υπάρχει το αρχείο .updateCheck το
                        // δημιουργώ και γράφω το id του τελευταίου commit
                        file_put_contents($file, $commits[0]['sha']);
                    }
                }
            }
        }
        return redirect('/home/list');
    }

    public function indexList($filter = null, $userId = null)
    {
        // βρίσκω τους Συγγραφείς, Αναθέτοντες και Διαχειριστές
        $writers_admins = User::get_writers_and_admins();
        // διαβάζω από τις ρυθμίσεις τα λεπτά για την αυτόματη ανανέωση
        $refreshInterval = Config::getConfigValueOf('minutesRefreshInterval') * 60;
        // διαβάζω από τις ρυθμίσεις αν πρέπει να ειδοποιήσω για ενημερώσεις
        $needsUpdate = false;
        if (strpos(request()->headers->get('referer'), 'login')) {
            $needsUpdate = Config::getConfigValueOf('needsUpdate');
        }
        $diavgeiaUrl = Config::getConfigValueOf('diavgeiaUrl');
        $showUserInfo = Config::getConfigValueOf('showUserInfo');

        // βρίσκω τους όλους ενεργούς χρήστες
        $activeusers = Active::users()->mostRecent()->get();
        $activeusers2show = [];
        foreach ($activeusers as $actuser) {
            if ($showUserInfo == 1) {
                $activeusers2show[] = $actuser['user']['username'];
            } elseif ($showUserInfo == 2) {
                $activeusers2show[] = $actuser['user']['name'];
            }
        }
        // Παίρνω τα Πρωτόκολλα που θα εμφανίσω
        // όλα ή του χρήστη
        // ή με βάση το φίλτρο
        //      d => προς Διεκπεραίωση
        //      f => Διεκπεραιώθηκαν
        $protocoltitle = 'Πρωτόκολλο';
        $user2show = '';
        $protocols = Protocol::orderby('etos', 'desc')->orderby('protocolnum', 'desc');
        if (!$userId) {
            // φιλτραρω τα πρωτόκολλα για το χρήστη
            if ($this->limitProtocolAccessList()) {
                $protocols = $protocols->where(function ($query) {
                    $query->where(DB::raw("CONCAT(`diekperaiosi`, ',')"), 'like', "%" . 'd' . Auth::user()->id . ",%")->orWhere(DB::raw("CONCAT(`diekperaiosi`, ',')"), 'like', "%" . 'e' . Auth::user()->id . ",%")->orWhere('user_id', Auth::user()->id);
                });
            }
            if ($filter == 'd') {
                $protocols = $protocols->where(DB::raw("CONCAT(`diekperaiosi`, ',')"), 'like', "%" . 'd' . Auth::user()->id . ",%")->whereNull('diekp_date');
                $protocoltitle = 'Πρωτόκολλο προς Διεκπεραίωση';
            } elseif ($filter == 'f') {
                $protocols = $protocols->where(DB::raw("CONCAT(`diekperaiosi`, ',')"), 'like', "%" . 'd' . Auth::user()->id . ",%")->wherenotNull('diekp_date');
                $protocoltitle = 'Πρωτόκολλο Διεκπεραιώθηκε';
            }
        } elseif (User::whereId($userId)->count() and $filter) {
            if ($showUserInfo == 1) {
                $user2show = User::whereId($userId)->first('username')->username;
            } elseif ($showUserInfo == 2) {
                $user2show = User::whereId($userId)->first('name')->name;
            }
            if ($filter == 'd') {
                $protocols = $protocols->where(DB::raw("CONCAT(`diekperaiosi`, ',')"), 'like', "%" . 'd' . $userId . ",%")->whereNull('diekp_date');
                $protocoltitle = "$user2show, προς Διεκπεραίωση";
            } elseif ($filter == 'f') {
                $protocols = $protocols->where(DB::raw("CONCAT(`diekperaiosi`, ',')"), 'like', "%" . 'd' . $userId . ",%")->wherenotNull('diekp_date');
                $protocoltitle = "$user2show, Διεκπεραιώθηκε";
            } else {
                $protocols = $protocols->where('user_id', $userId);
                $protocoltitle = "$user2show, Πρωτόκολλο";
            }
        } else {
            if ($filter == 'd') {
                $protocols = $protocols->where(DB::raw("CONCAT(`diekperaiosi`, ',')"), 'like', "%" . 'd' . "%" )->whereNull('diekp_date');
                $protocoltitle = "Όλοι οι χρήστες, προς Διεκπεραίωση";
            } elseif ($filter == 'f') {
                $protocols = $protocols->where(DB::raw("CONCAT(`diekperaiosi`, ',')"), 'like', "%" . 'd' . "%" )->wherenotNull('diekp_date');
                $protocoltitle = "Όλοι οι χρήστες, Διεκπεραιώθηκε";
            }
        }
        // παίρνω τα πρωτόκολλα σε σελίδες με αριθμό πρωτοκόλλων σύμφωνα με τις ρυθμίσεις
        $protocols = $protocols->paginate(Config::getConfigValueOf('showRowsInPage'));
        // αλλάζω τη μορφή στις ημερομηνίες και παίρνω την περιγραφή του φακέλου Φ.
        foreach ($protocols as $protocol) {
            if ($protocol->protocoldate) {
                $protocol->protocoldate = Carbon::createFromFormat('Ymd', $protocol->protocoldate)->format('d/m/Y');
            }
            if ($protocol->in_date) {
                $protocol->in_date = Carbon::createFromFormat('Ymd', $protocol->in_date)->format('d/m/Y');
            }
            if ($protocol->out_date) {
                $protocol->out_date = Carbon::createFromFormat('Ymd', $protocol->out_date)->format('d/m/Y');
            }
            if ($protocol->diekp_date) {
                $protocol->diekp_date = Carbon::createFromFormat('Ymd', $protocol->diekp_date)->format('d/m/Y');
            }
            if ($protocol->fakelos and Keepvalue::whereFakelos($protocol->fakelos)->first()) {
                $protocol->describe .= Keepvalue::whereFakelos($protocol->fakelos)->first()->describe;
            }
            if ($protocol->sxetiko) {
                $sxetiko = explode(', ', $protocol->sxetiko);
                $pattern = "/^\d+\/\d{4}/i";
                $links = array();
                foreach ($sxetiko as $sxet) {
                    if (preg_match($pattern, $sxet)) {
                        $data = explode('/', $sxet);
                        $href = URL::to('/goto') . "/" .  $data[1] . "/" . $data[0] . "?find=1";
                        $links[] = '<a href="' . $href . '">' . $sxet . '</a>';
                    } else {
                        $links[] = $sxet;
                    }
                    $protocol->sxetiko = join(', ', $links);
                }
            }
        }
        return view('protocolList', compact('protocols', 'refreshInterval', 'needsUpdate', 'diavgeiaUrl', 'activeusers2show', 'writers_admins', 'protocoltitle'));
    }


    public function getKeep4Fakelos($fakelos)
    {
        $keepval = null;
        if ($fakelos) {
            $keepval = Keepvalue::whereFakelos($fakelos)->first()->keep;
            if (!$keepval) {
                $keepval = Keepvalue::whereFakelos($fakelos)->first()->keep_alt;
            }
        }
        return $keepval;
    }


    public function getFileInputs($num)
    {
        $data = request()->all();
        return view('getFileInputs', compact('num', 'data'));
    }



    public function store()
    {
        // παίρνω τα δεδομένα της φορμας
        $data = request()->all();

        // το id του Διεκπεραιωτή (άν έχει σταλεί) για αποστολή email
        $sendEmailTo = request('sendEmailTo');
        // το email (αν υπάρχει) στο οποίο θα σταλεί απόδειξη παραλαβής
        $reply_to_email = request('reply_to_email');

        // διαβάζω ρυθμίσεις
        $protocolValidate = Config::getConfigValueOf('protocolValidate');
        $etos = request('etos');
        $currentEtos = Carbon::now()->format('Y');
        $safeNewProtocolNum = Config::getConfigValueOf('safeNewProtocolNum');
        $sendEmailOnDiekperaiosiChange = Config::getConfigValueOf('sendEmailOnDiekperaiosiChange');

        // βρίσκω το νέο Αρ.Πρωτ στην εισαγωγή δεδομένων
        $firstProtocolNum = Config::getConfigValueOf('firstProtocolNum');
        if (Protocol::count()) {
            if (Config::getConfigValueOf('yearInUse')) {
                $newprotocolnum = Protocol::whereEtos($etos)->max('protocolnum') ? Protocol::whereEtos($etos)->max('protocolnum') + 1 : 1;
            } else {
                $newprotocolnum = Protocol::last()->protocolnum ? Protocol::last()->protocolnum + 1 : 1;
            }
        } else {
            if ($firstProtocolNum) {
                $newprotocolnum = $firstProtocolNum;
            } else {
                $newprotocolnum = 1;
            }
        }
        // Αν η ρύθμιση λέι ΝΑΙ σε ασφαλή Αρ.Πρωτ δεν ελέγχω το νέο Αρ.Πρ που μόλις έφτιαξα
        // γιατί θα δοθεί αυτόματα από το σύστημα ο πρώτος διαθέσιμος
        if ($safeNewProtocolNum) {
            $mustValidate = [
                'etos' => 'required|integer|digits:4',
                'protocoldate' => 'required',
            ];
        } else {
            // αλλιώς ελέγχω ότι σε καθεστώς πολλών χρηστών δεν έχει ήδη ληφθεί από άλλο χρήστη
            $mustValidate = [
                'protocolnum' => "required|integer|unique:protocols,protocolnum,NULL,id,etos,$etos",
                'etos' => 'required|integer|digits:4',
                'protocoldate' => 'required',
            ];
        }
        // εφαρμογή του validation (αυτό γίνεται πάντα)
        $this->validate(request(), $mustValidate);

        // ελέγχεται η μορφή των ημερομηνιών
        $validator = Validator::make(request()->all(), [
            'protocoldate' => 'regex:/^\d{2}\/\d{2}\/\d{4}$/',
            'in_date' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
            'out_date' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
            'diekp_date' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
        ], [
            'protocoldate.regex' => "Η ημερομηνία πρέπει να έχει τη μορφή 'ηη/μμ/εεεε'.<br>&nbsp;",
            'in_date.regex' => "Η ημερομηνία πρέπει να έχει τη μορφή 'ηη/μμ/εεεε'.<br>&nbsp;",
            'out_date.regex' => "Η ημερομηνία πρέπει να έχει τη μορφή 'ηη/μμ/εεεε'.<br>&nbsp;",
            'diekp_date.regex' => "Η ημερομηνία πρέπει να έχει τη μορφή 'ηη/μμ/εεεε'.<br>&nbsp;",
        ])->validate();

        // παίρνω τις ρυθμίσεις [Έλεγχοι & περιορισμοί κατά την καταχώριση]
        // αν είναι true (1)
        if ($protocolValidate) {

            // έλεγχος για το έτος πρωτοκόλλησης (αλλαγή χρόνου)
            $validator = Validator::make(request()->all(), [
                'etos' => "in:$currentEtos",
            ], [
                'etos.in' => "Δεν μπορείτε να καταχωρίσετε Νέο Πρωτόκολλο στο παρελθόν έτος $etos.<br><br>Αλλάξτε στις ρυθμίσεις εφαρμογής το ''Ενεργό έτος πρωτοκόλλησης'' είτε σε:<br>-> <b>$currentEtos</b> για να ξεκινήσετε από το 1<br>-> <b>κενό</b> για τον επόμενο Αρ.Πρωτ.<br>&nbsp;",
            ])->validate();


            $validator = Validator::make(request()->all(), [
                'thema' => 'nullable|required_with:fakelos,in_num,in_date,in_topos_ekdosis,in_arxi_ekdosis,in_paraliptis,in_perilipsi,diekperaiosi,out_date,diekp_date,sxetiko,out_to,out_perilipsi,keywords,paratiriseis|max:255',
                'in_date' => 'required_with:in_num,in_topos_ekdosis,in_arxi_ekdosis',
                'in_topos_ekdosis' => 'required_with:in_num,in_date,in_arxi_ekdosis|max:255',
                'in_arxi_ekdosis' => 'required_with:in_num,in_date,in_topos_ekdosis|max:255',
                'in_paraliptis' => 'required_with:in_num,in_date,in_arxi_ekdosis,in_topos_ekdosis|max:255',
                'out_date' => 'required_with:out_to,out_perilipsi',
                'out_to' => 'required_with:out_date,out_perilipsi|max:255',
            ], [
                'thema.required_with' => "Συμπληρώστε<br>το θέμα.",
                'in_topos_ekdosis.required_with' => "Συμπληρώστε<br>τον τόπο έκδοσης.",
                'in_arxi_ekdosis.required_with' => "Συμπληρώστε<br>την Αρχή έκδοσης.",
                'in_date.required_with' => "Συμπληρώστε<br>την Ημ/νια έκδοσης.",
                'in_paraliptis.required_with' => "Συμπληρώστε<br>τον Παραλήπτη.",
                'out_date.required_with' => "Συμπληρώστε<br>την Ημ/νια έξερχομένου.",
                'out_to.required_with' => "Συμπληρώστε<br>το πεδίο Απευθύνεται σε",
            ])->validate();
        }

        // αλλάζω τη μορφή ημερομηνίας για καταχώριση στη βάση
        $in_date = null;
        $out_date = null;
        $diekp_date = null;
        if ($data['in_date']) {
            $in_date = Carbon::createFromFormat('d/m/Y', $data['in_date'])->format('Ymd');
        }
        if ($data['out_date']) {
            $out_date = Carbon::createFromFormat('d/m/Y', $data['out_date'])->format('Ymd');
        }
        if ($data['diekp_date']) {
            $diekp_date = Carbon::createFromFormat('d/m/Y', $data['diekp_date'])->format('Ymd');
        }
        $keywords = null;
        if ($data['keywords']) {
            $keywords = rtrim($data['keywords'], ',');
        }
        $sxetiko = null;
        if ($data['sxetiko']) {
            $sxetiko = rtrim($data['sxetiko'], ',');
        }

        // αν η ρύθμιση Ασφαλής νέος Αρ.Πρ είναι ΝΑΙ
        if ($safeNewProtocolNum) {
            // εισαγωγή της εγγραφής με το νέο Αρ.Πρ που μόλις έφτιαξα
            $protocolNewNum = $newprotocolnum;
        } else {
            // εισαγωγή της εγγραφής με το Αρ.Πρ που έστειλε η φόρμα
            $protocolNewNum = $data['protocolnum'];
        }

        // δημιουργία του πρωτοκόλλου
        try {
            $protocol = Protocol::create([
                'user_id' => Auth::user()->id,
                'protocolnum' => $protocolNewNum,
                'protocoldate' => Carbon::createFromFormat('d/m/Y', $data['protocoldate'])->format('Ymd'),
                'etos' => $data['etos'],
                'fakelos' => $data['fakelos'],
                'thema' => $data['thema'],
                'in_num' => $data['in_num'],
                'in_date' => $in_date,
                'in_topos_ekdosis' =>  $data['in_topos_ekdosis'],
                'in_arxi_ekdosis' => $data['in_arxi_ekdosis'],
                'in_paraliptis' => $data['in_paraliptis'],
                'diekperaiosi' => isset($data['diekperaiosi']) ? implode(',',$data['diekperaiosi']) : '',
                'in_perilipsi' => $data['in_perilipsi'],
                'out_date' => $out_date,
                'diekp_date' => $diekp_date,
                'sxetiko' => $sxetiko,
                'out_to' => $data['out_to'],
                'out_perilipsi' => $data['out_perilipsi'],
                'keywords' => $keywords,
                'paratiriseis' => $data['paratiriseis']
            ]);
        } catch (\Throwable $e) {
            // αν υπάρξει σφάλμα το στέλνω στο log
            // στέλνω ειδοποίηση στη session
            report($e);
            $notification = array(
                'message' => 'Υπήρξε κάποιο πρόβλημα στην καταχώριση του Πρωτοκόλλου<br>Παρακαλώ επαναλάβετε την καταχώριση.',
                'alert-type' => 'error'
            );
            session()->flash('notification', $notification);
            // επιστρέφω πίσω
            return redirect("home");
        }

        // εισαγωγή συνημμένων εγγράφων
        $filescount = 3 * $data['file_inputs_count'];
        $protocol_id = $protocol->id;

        for ($i = 1; $i < $filescount + 1; $i++) {
            if ($data["ada$i"] or request()->hasFile("att$i")) {
                $filename = null;
                $mimeType = null;
                $savedPath = null;
                $expires = null;
                // αν έχει σταλεί αρχείο από τη φόρμα
                if (request()->hasFile("att$i")) {
                    $file = request()->file("att$i");
                    // παίρνω το όνομα του αρχείου
                    $filename = $file->getClientOriginalName();
                    // αφαίρεση απαγορευμένων χαρακτήρων από το όνομα του συνημμένου
                    $filename = $this->filter_filename($filename, false);
                    // τον τύπο mimetype του αρχείου
                    $mimeType = $file->getMimeType();
                    // φτιάχνω το όνομα Αρ.Πρ + Ημνια.Πρ
                    $filenameToStore = request()->protocolnum . '-' . Carbon::createFromFormat('d/m/Y', request()->protocoldate)->format('Ymd') . '_' . $filename;
                    // τον φάκελο arxeio + Φ.
                    $dir = '/arxeio/' . request()->fakelos . '/';
                    // αποθηκεύω το αρχείο και παίρνω το path αποθήκευσης
                    $savedPath = $file->storeas($dir, $filenameToStore);
                }
                if ($data['keep'] and is_numeric($data['keep'])) {
                    // αν είναι η διατήρηση αριθμός πρόσθέτω χρόνια
                    // για να φτιάξω την Ημνια λήξης
                    $dt = Carbon::createFromFormat('d/m/Y', request()->protocoldate);
                    $dt->addYears($data['keep']);
                    $expires = $dt->format('Ymd');
                }
                try {
                    // δημιουργία συνημμένου
                    Attachment::create([
                        'protocol_id' => $protocol_id,
                        'ada' => $data["ada$i"],
                        'name' => $filename,
                        'mimeType' => $mimeType,
                        'savedPath' => $savedPath,
                        'keep' => $data['keep'],
                        'expires' => $expires,
                    ]);
                } catch (\Throwable $e) {
                    // αν υπάρχει λάθος το γράφω στο log
                    report($e);
                    // δημιουργώ μήνυμα στο session
                    $notification = array(
                        'message' => 'Υπήρξε κάποιο πρόβλημα στην καταχώριση των συνημμένων αρχείων<br>Ελέγξτε αν καταχωρίστηκαν όλα σωστά.<br>' . $e->getMessage(),
                        'alert-type' => 'error'
                    );
                    session()->flash('notification', $notification);
                    // πηγαίνω στην επεξεργασία του πρωτοκόλλου που μόλις αποθήκευσα
                    return redirect("home/$protocol_id");
                }
            }
        }

        // αν πρέπει να στείλω email στον Διεκπεραιωτή $sendEmailTo = user.id
        // και οι ρυθμίσεις λένα να στέλνω email σε ανάθεση
        // στέλνω email
            $message = '';
        if ($sendEmailTo && $sendEmailOnDiekperaiosiChange) {
            $sendEmailToArray = explode(',', $sendEmailTo);
            foreach ($sendEmailToArray as $smt) {
                if (substr($smt, 0, 1) == 'd') {
                    $message .= $this->sendMailToDiekperaioti(ltrim($smt, 'd'), $protocol);
                }
                if (substr($smt, 0, 1) == 'e') {
                    $message .= $this->diekpInformMail(ltrim($smt, 'e'), $protocol);
                }
            }
        }

        // αν έχει συμπληρωθεί email για αποστολή απόδειξης παραλαβής
        if ($reply_to_email) {
            // φτιάχνω τα δεδομένα
            $emaildate = Carbon::now()->format('d/m/Y H:m:s');
            $protocoldate = Carbon::createFromFormat('Ymd', $protocol->protocoldate)->format('d/m/Y');
            $sendReplyTo = $reply_to_email;
            $html = view('receiptProtocol', compact('protocol', 'protocoldate'))->render();
            // στέλνω το email
            Mail::send([], [], function ($message) use ($sendReplyTo, $html) {
                $message->from(config('mail.from.address'));
                $message->to($sendReplyTo);
                $message->subject("Καταχώριση στο Ηλεκτρονικό Πρωτόκολλο.");
                $message->setBody($html, 'text/html');
            });
            // αν η αποστολή γίνει κανονικά
            if (!count(Mail::failures())) {
                // προσθέτω στις παρατηρήσεις ότι στάλθηκε απόδειξη παραλαβής με email
                $parMessage = $protocol->paratiriseis ? $protocol->paratiriseis . ', ' : '';
                $parMessage .= "$emaildate απόδειξη παραλαβής σε $sendReplyTo";
                //$parMessage = mb_strlen($parMessage) > 250 ? mb_substr($parMessage, 0, 250) . ' ...' : $parMessage;
                $protocol->paratiriseis = $parMessage;
                $protocol->update();
                $message .= "<li>" . $parMessage . "</li";
            } else {
                // αν υπάρχουν λάθη ενημερώνω
                $notification = array(
                    'message' => 'Μη δυνατή αποστολή βεβαίωσης καταχώρισης Ηλ. Πρωτοκόλλου με Email',
                    'alert-type' => 'warning'
                );
                session()->flash('notification', $notification);
            }
        }
        // ενημέρωση για την επιτυχία της καταχώρισης
        $notification = array(
            'message' => 'Επιτυχημένη ενημέρωση.' . "<ul>$message</ul>",
            'alert-type' => 'success'
        );
        session()->flash('notification', $notification);
        // μετάβαση στην Επεξεργασία του Πρ. που μόλις αποθήκευσα
        return redirect("home/$protocol_id");
    }


    public function update(Protocol $protocol)
    {
        // παίρνω τα δεδομένα
        $data = request()->all();
        // και κάποιες μεταβλητές που χρειάζομαι
        // από το παλιό πρωτόκολλο για ελέγχους
        $id = $protocol->id;
        $oldFakelos = $protocol->fakelos;
        // το έτος
        $etos = request('etos');
        // το id του Διεκπεραιωτή για email
        $sendEmailTo = request('sendEmailTo');
        // διαβάζω ρυθμίσεις
        $protocolValidate = Config::getConfigValueOf('protocolValidate');
        $sendEmailOnDiekperaiosiChange = Config::getConfigValueOf('sendEmailOnDiekperaiosiChange');

        // πάντα validate
        // validation της μορφής ημνιων
        $validator = Validator::make(request()->all(), [
            'protocolnum' => "required|integer|unique:protocols,protocolnum,$id,id,etos,$etos",
            'etos' => 'required|integer|digits:4',
            'protocoldate' => 'required',
            'protocoldate' => 'regex:/^\d{2}\/\d{2}\/\d{4}$/',
            'in_date' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
            'out_date' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
            'diekp_date' => 'nullable|regex:/^\d{2}\/\d{2}\/\d{4}$/',
        ], [
            'protocoldate.regex' => "Η ημερομηνία πρέπει να έχει τη μορφή 'ηη/μμ/εεεε'.<br>&nbsp;",
            'in_date.regex' => "Η ημερομηνία πρέπει να έχει τη μορφή 'ηη/μμ/εεεε'.<br>&nbsp;",
            'out_date.regex' => "Η ημερομηνία πρέπει να έχει τη μορφή 'ηη/μμ/εεεε'.<br>&nbsp;",
            'diekp_date.regex' => "Η ημερομηνία πρέπει να έχει τη μορφή 'ηη/μμ/εεεε'.<br>&nbsp;",
        ])->validate();

        // παίρνω τις ρυθμίσεις [Έλεγχοι & περιορισμοί κατά την καταχώριση]
        // αν είναι true (1)
        if ($protocolValidate) {
            $validator = Validator::make(request()->all(), [
                'thema' => 'nullable|required_with:fakelos,in_num,in_date,in_topos_ekdosis,in_arxi_ekdosis,in_paraliptis,in_perilipsi,diekperaiosi,out_date,diekp_date,sxetiko,out_to,out_perilipsi,keywords,paratiriseis|max:255',
                'in_date' => 'required_with:in_num,in_topos_ekdosis,in_arxi_ekdosis',
                'in_topos_ekdosis' => 'required_with:in_num,in_date,in_arxi_ekdosis|max:255',
                'in_arxi_ekdosis' => 'required_with:in_num,in_date,in_topos_ekdosis|max:255',
                'in_paraliptis' => 'required_with:in_num,in_date,in_arxi_ekdosis,in_topos_ekdosis|max:255',
                'out_date' => 'required_with:out_to,out_perilipsi',
                'out_to' => 'required_with:out_date,out_perilipsi|max:255',
            ], [
                'thema.required_with' => "Συμπληρώστε<br>το θέμα.",
                'in_topos_ekdosis.required_with' => "Συμπληρώστε<br>τον τόπο έκδοσης.",
                'in_arxi_ekdosis.required_with' => "Συμπληρώστε<br>την Αρχή έκδοσης.",
                'in_date.required_with' => "Συμπληρώστε<br>την Ημ/νια έκδοσης.",
                'in_paraliptis.required_with' => "Συμπληρώστε<br>τον Παραλήπτη.",
                'out_date.required_with' => "Συμπληρώστε<br>την Ημ/νια έξερχομένου.",
                'out_to.required_with' => "Συμπληρώστε<br>το πεδίο Απευθύνεται σε",
            ])->validate();
        }

        // αλλαγή μορφής ημνιων για αποθήκευση στη ΒΔ
        $in_date = null;
        $out_date = null;
        $diekp_date = null;
        if ($data['in_date']) {
            $in_date = Carbon::createFromFormat('d/m/Y', $data['in_date'])->format('Ymd');
        }
        if ($data['out_date']) {
            $out_date = Carbon::createFromFormat('d/m/Y', $data['out_date'])->format('Ymd');
        }
        if ($data['diekp_date']) {
            $diekp_date = Carbon::createFromFormat('d/m/Y', $data['diekp_date'])->format('Ymd');
        }
        // βγάζω το τελευταίο (,) κόμα από τις λέξεις κλειδιά
        $keywords = null;
        if ($data['keywords']) {
            $keywords = rtrim($data['keywords'], ',');
        }
        $sxetiko = null;
        if ($data['sxetiko']) {
            $sxetiko = rtrim($data['sxetiko'], ',');
        }

        // αν διαγράφηκε και υπάρχουν συνημμένα αρχεία
        // ενημερώνω ότι δεν μπορεί ο φάκελος Φ. να είναι κενός
        if ($protocol->attachments()->count() && ! $data['fakelos']) {
                $validator = Validator::make(request()->all(), [
                    'fakelos' => "required",
                ], [
                    'fakelos.required' => "Ο Φάκελλος πρωτοκόλλου με συνημμένα αρχεία δεν μπορεί να είναι κενός.<br>Για να επιτευχθεί αυτό πρέπει πρώτα να διαγράψετε τα συνημμένα αρχεία.",
                ])->validate();
        }

        // ενημέρωση του πρωτοκόλλου
        try {
            Protocol::whereId($id)->update([
                'user_id' => Auth::user()->id,
                'protocolnum' => $data['protocolnum'],
                'protocoldate' => Carbon::createFromFormat('d/m/Y', $data['protocoldate'])->format('Ymd'),
                'etos' => $data['etos'],
                'fakelos' => $data['fakelos'],
                'thema' => $data['thema'],
                'in_num' => $data['in_num'],
                'in_date' => $in_date,
                'in_topos_ekdosis' =>  $data['in_topos_ekdosis'],
                'in_arxi_ekdosis' => $data['in_arxi_ekdosis'],
                'in_paraliptis' => $data['in_paraliptis'],
                'diekperaiosi' => isset($data['diekperaiosi']) ? implode(',', $data['diekperaiosi']) : '',
                'in_perilipsi' => $data['in_perilipsi'],
                'out_date' => $out_date,
                'diekp_date' => $diekp_date,
                'sxetiko' => $sxetiko,
                'out_to' => $data['out_to'],
                'out_perilipsi' => $data['out_perilipsi'],
                'keywords' => $keywords,
                'paratiriseis' => $data['paratiriseis']
            ]);
        } catch (\Throwable $e) {
            // αν χτυπήσει λάθος ενημερώνω το log
            report($e);
            // στέλνω μήνυμα στο χρήστη
            $notification = array(
                'message' => 'Υπήρξε κάποιο πρόβλημα στην ενημέρωση του Πρωτοκόλλου<br>Παρακαλώ επαναλάβετε την ενημέρωση.',
                'alert-type' => 'error'
            );
            session()->flash('notification', $notification);
            // γυρνάω στην επεξεργασία Πρωτοκόλλου
            return redirect("home/$id");
        }


        // αν άλλαξε ο Φακελλος και υπάρχουν συνημμένα αρχεία
        if ($protocol->attachments()->count()) {
            if ($data['fakelos'] !== $oldFakelos) {

                $attachments = $protocol->attachments()->get();
                foreach( $attachments as $attachment){
                    $savedPath = $attachment->savedPath;
                    $newPath = str_replace($oldFakelos, $data['fakelos'], $savedPath);
                    // αν υπάρχει το αρχείο
                    if (Storage::exists($attachment->savedPath)) {
                        // το μετακινώ στον νέο φάκελο
                        Storage::move($savedPath, $newPath);
                    }
                    $attachment->savedPath = $newPath;
                    $attachment->save();
                }
            }
        }


        // αποθηκεύω τα συνημμένα αρχεία
        $filescount = 3 * $data['file_inputs_count'];
        for ($i = 1; $i < $filescount + 1; $i++) {
            if ($data["ada$i"] or request()->hasFile("att$i")) {
                $filename = null;
                $mimeType = null;
                $savedPath = null;
                $expires = null;
                // αν η φόρμα περιέχει αρχείο
                if (request()->hasFile("att$i")) {
                    $file = request()->file("att$i");
                    // παίρνω το όνομα αρχείου
                    $filename = $file->getClientOriginalName();
                    // αφαίρεση απαγορευμένων χαρακτήρων από το όνομα του συνημμένου
                    $filename = $this->filter_filename($filename, false);
                    // τον τύπο αρχείου
                    $mimeType = $file->getMimeType();
                    // φτιάχνω το όνομα αποθήκευσης
                    $filenameToStore = request()->protocolnum . '-' . Carbon::createFromFormat('d/m/Y', request()->protocoldate)->format('Ymd') . '_' . $filename;
                    // τον φάκελο αποθήκευσης
                    $dir = '/arxeio/' . request()->fakelos . '/';
                    // αποθηκεύω και παίρνω το path
                    $savedPath = $file->storeas($dir, $filenameToStore);
                }
                if ($data['keep'] and is_numeric($data['keep'])) {
                    // αν η διατήρηση είναι αριθμός
                    // προσθέτω χρόνια και φτιάχνω ημνια λήξης
                    $dt = Carbon::createFromFormat('d/m/Y', request()->protocoldate);
                    $dt->addYears($data['keep']);
                    $expires = $dt->format('Ymd');
                }
                // αποθήκευση συνημμένου στη ΒΔ
                try {
                    Attachment::create([
                        'protocol_id' => $id,
                        'ada' => $data["ada$i"],
                        'name' => $filename,
                        'mimeType' => $mimeType,
                        'savedPath' => $savedPath,
                        'keep' => $data['keep'],
                        'expires' => $expires,
                    ]);
                } catch (\Throwable $e) {
                    // σε λάθος στέλνω στο log
                    report($e);
                    // ενημερώνω το χρήστη
                    $notification = array(
                        'message' => 'Υπήρξε κάποιο πρόβλημα στην καταχώριση των συνημμένων αρχείων<br>Ελέγξτε αν καταχωρίστηκαν όλα σωστά.<br>' . $e->getMessage(),
                        'alert-type' => 'error'
                    );
                    session()->flash('notification', $notification);
                    // γυρνάω πίσω
                    return redirect("home/$id");
                }
            }
        }
        // αν έχω id του Διεκπεραιωτή και επιτρέπεται από τις ρυθμίσεις στέλνω email στον Διεκπεραιωτή
        $message = '';
        if ($sendEmailTo && $sendEmailOnDiekperaiosiChange) {
            $sendEmailToArray = explode(',', $sendEmailTo);
            foreach ($sendEmailToArray as $smt) {
                if (substr($smt, 0, 1) == 'd') {
                    $message .= $this->sendMailToDiekperaioti(ltrim($smt, 'd'), $protocol);
                }
                if (substr($smt, 0, 1) == 'e') {
                    $message .= $this->diekpInformMail(ltrim($smt, 'e'), $protocol);
                }
            }
        }
        // ενημέρωση όλα ok
        $notification = array(
            'message' => 'Επιτυχημένη ενημέρωση.' . "<ul>$message</ul>",
            'alert-type' => 'success'
        );
        session()->flash('notification', $notification);
        // γυρνάω στο Πρωτόκολλο
        return redirect("home/$id");
    }

    public function sendMailToDiekperaioti($sendEmailTo, $protocol)
    {
        // βρίσκω τα δεδομένα
        $diekperaiotis = User::find($sendEmailTo)->name;
        $emailTo = User::find($sendEmailTo)->email;
        $date = Carbon::createFromFormat('Ymd', $protocol->protocoldate)->format('d/m/Y');
        // παίρνω το περιεχόμενο του μηνύματος
        $html = view('diekperaiosiMail', compact('protocol', 'diekperaiotis', 'date'))->render();
        // ρυθμίσεις για τον 2ο (εναλλακτικό) mailer
        $configuration = [
            'smtp_host'    => config('intra-mail.host'),
            'smtp_port'    => config('intra-mail.port'),
            'smtp_username'  => config('intra-mail.username'),
            'smtp_password'  => config('intra-mail.password'),
            'smtp_encryption'  => config('intra-mail.encryption'),

            'from_email'    => config('intra-mail.from.address'),
            'from_name'    => config('intra-mail.from.name'),
        ];
        // φτιάχνω τον mailer και στέλνω το mail
        $mailer = app()->makeWith('user.mailer', $configuration);
        $mailer->send([], [], function ($message) use ($emailTo, $html) {
            $message->subject("Ανάθεση Πρωτοκόλλου για Διεκπεραίωση");
            $message->setBody($html, 'text/html');
            $message->to($emailTo);
        });
        if (!count($mailer->failures())) {
            // προσθέτω στις παρατηρήσεις ότι στάλθηκε email στον διεκπεραιωτή
            $emaildate = Carbon::now()->format('d/m/Y H:m:s');
            $parMessage = $protocol->paratiriseis ? $protocol->paratiriseis . ', ' : '';
            $parMessage .= "$emaildate email διεκπεραίωσης σε $diekperaiotis";
            //$parMessage = mb_strlen($parMessage) > 250 ? mb_substr($parMessage, 0, 250) . ' ...' : $parMessage;
            $protocol->paratiriseis = $parMessage;
            $protocol->update();
            // ενημερώνω το χρήστη
            $message = "<li>Στάλθηκε email ανάθεσης στον $diekperaiotis.</li>";
        }
        return $message;
    }

    public function diekpInformMail($sendEmailTo, $protocol)
    {
        // βρίσκω τα δεδομένα
        $diekperaiotis = User::find($sendEmailTo)->name;
        $emailTo = User::find($sendEmailTo)->email;
        $date = Carbon::createFromFormat('Ymd', $protocol->protocoldate)->format('d/m/Y');
        // παίρνω το περιεχόμενο του μηνύματος
        $html = view('diekpInformMail', compact('protocol', 'diekperaiotis', 'date'))->render();
        // ρυθμίσεις για τον 2ο (εναλλακτικό) mailer
        $configuration = [
            'smtp_host'    => config('intra-mail.host'),
            'smtp_port'    => config('intra-mail.port'),
            'smtp_username'  => config('intra-mail.username'),
            'smtp_password'  => config('intra-mail.password'),
            'smtp_encryption'  => config('intra-mail.encryption'),

            'from_email'    => config('intra-mail.from.address'),
            'from_name'    => config('intra-mail.from.name'),
        ];
        // φτιάχνω τον mailer και στέλνω το mail
        $mailer = app()->makeWith('user.mailer', $configuration);
        $mailer->send([], [], function ($message) use ($emailTo, $html) {
            $message->subject("Ενημέρωση για Πρωτόκολλο");
            $message->setBody($html, 'text/html');
            $message->to($emailTo);
        });
        if (!count($mailer->failures())) {
            // προσθέτω στις παρατηρήσεις ότι στάλθηκε email στον διεκπεραιωτή
            $emaildate = Carbon::now()->format('d/m/Y H:m:s');
            $parMessage = $protocol->paratiriseis ? $protocol->paratiriseis . ', ' : '';
            $parMessage .= "$emaildate email ενημέρωσης σε $diekperaiotis";
            //$parMessage = mb_strlen($parMessage) > 250 ? mb_substr($parMessage, 0, 250) . ' ...' : $parMessage;
            $protocol->paratiriseis = $parMessage;
            $protocol->update();
            // ενημερώνω το χρήστη
            $message = "<li>Στάλθηκε email ενημέρωσης στον $diekperaiotis.</li>";
        }
        return $message;
    }


    public function delprotocol(Protocol $protocol)
    {
        $protocolnum = $protocol->protocolnum;
        $etos = $protocol->etos;
        // αν υπάρχουν συνημμένα δεν διαγράφω και ενημερώνω
        if ($protocol->attachments()->count()) {
            //return $protocol->attachments()->get();
            foreach($protocol->attachments()->get() as $att){
                // διαγραφή του αρχείου
                Storage::delete($att->savedPath);
                // hard delete
                $att->forceDelete();
            }
        }
        // διαγραφή Πρωτοκόλλου
        Protocol::destroy($protocol->id);
        // ενημέρωση του χρήστη
        $notification = array(
            'message' => "Διαγράφηκε το Πρωτόκολλο με αριθμό $protocolnum για το έτος $etos",
            'alert-type' => 'success'
        );
        session()->flash('notification', $notification);
        // επιστροφή στο home
        return redirect("home");
    }


    public function attachDelete(Attachment $attachment)
    {
        // διαβάζω τα δεδομένα
        $protocol = $attachment->protocol;
        $savedPath = $attachment->savedPath;
        $trashPath = str_replace('arxeio', 'trash', $savedPath);
        // αν υπάρχει το αρχείο
        if (Storage::exists($attachment->savedPath)) {
            // το μετακινώ στον Κάδο Ανακύκλωσης
            Storage::move($savedPath, $trashPath);
        }
        // διαγράφω την καταχώριση στη ΒΔ (softDelete)
        $attachment->delete();
        // επιστρέφω τα συνημμένα αρχεία του Πρωτοκόλλου
        return view('getArxeia', compact('protocol'));
    }

    public function gotonum($etos, $protocolnum, $step=null)
    {
        $limitProtocolAccessList = $this->limitProtocolAccessList();
         if (request('find')) {
            // φιλτραρω τα πρωτόκολλα για το χρήστη
            if ($limitProtocolAccessList) {
                $count = Protocol::whereEtos($etos)->where('protocolnum', $protocolnum)->where(function ($query) {
                    $query->where(DB::raw("CONCAT(`diekperaiosi`, ',')"), 'like', "%" . 'd' . Auth::user()->id . ",%")->orWhere(DB::raw("CONCAT(`diekperaiosi`, ',')"), 'like', "%" . 'e' . Auth::user()->id . ",%")->orWhere('user_id', Auth::user()->id );
                })->count();
                if ($count) {
                    $protocol_id = Protocol::whereEtos($etos)->where('protocolnum', $protocolnum)->where(function ($query) {
                        $query->where(DB::raw("CONCAT(`diekperaiosi`, ',')"), 'like', "%" . 'd' . Auth::user()->id . ",%")->orWhere(DB::raw("CONCAT(`diekperaiosi`, ',')"), 'like', "%" . 'e' . Auth::user()->id . ",%")->orWhere('user_id', Auth::user()->id);
                    })->first()->id;
                    return redirect("home/$protocol_id");
                }
            }else{
                if (Protocol::whereEtos($etos)->where('protocolnum', $protocolnum)->count()) {
                    $protocol_id = Protocol::whereEtos($etos)->where('protocolnum', $protocolnum)->first()->id;
                    return redirect("home/$protocol_id");
                }


            }
         } else {
            if ($step == 'b') {
                if($limitProtocolAccessList){
                    $protocol_id = Protocol::where(function ($query) {
                        $query->where(DB::raw("CONCAT(`diekperaiosi`, ',')"), 'like', "%" . 'd' . Auth::user()->id . ",%")->orWhere(DB::raw("CONCAT(`diekperaiosi`, ',')"), 'like', "%" . 'e' . Auth::user()->id . ",%")->orWhere('user_id', Auth::user()->id);
                    })->where(function ($query) use($protocolnum, $etos) {
                        $query->where([['protocolnum', '<', $protocolnum],['etos', $etos]])->orwhere('etos', "<", $etos);
                    })->orderby('etos', 'DESC')->orderby('protocolnum', 'DESC')->take(1)->get('id');
                    if (count($protocol_id)) return redirect("home/" . $protocol_id[0]->id);
                    return redirect("home");
                }else{
                    $protocol_id = Protocol::where([['protocolnum', '<', $protocolnum],['etos', $etos]])->orwhere('etos', "<", $etos)->orderby('etos', 'DESC')->orderby('protocolnum', 'DESC')->take(1)->get('id');
                    if (count($protocol_id)) return redirect("home/" . $protocol_id[0]->id);
                    return redirect("home");
                }
            } elseif($step == 'f') {
                if ($limitProtocolAccessList) {
                    $protocol_id = Protocol::where(function ($query) {
                        $query->where(DB::raw("CONCAT(`diekperaiosi`, ',')"), 'like', "%" . 'd' . Auth::user()->id . ",%")->orWhere(DB::raw("CONCAT(`diekperaiosi`, ',')"), 'like', "%" . 'e' . Auth::user()->id . ",%")->orWhere('user_id', Auth::user()->id);
                    })->where(function ($query) use($protocolnum, $etos) {
                        $query->where([['protocolnum', '>', $protocolnum],['etos', $etos]])->orwhere('etos', ">", $etos);
                    })->orderby('etos', 'ASC')->orderby('protocolnum', 'ASC')->take(1)->get('id');
                    if (count($protocol_id)) return redirect("home/" . $protocol_id[0]->id);
                    return redirect("home");
                } else {
                    $protocol_id = Protocol::where([['protocolnum', '>', $protocolnum],['etos', $etos]])->orwhere('etos', ">", $etos)->orderby('etos', 'ASC')->orderby('protocolnum', 'ASC')->take(1)->get('id');
                    if (count($protocol_id)) return redirect("home/" . $protocol_id[0]->id);
                    return redirect("home");
                }
            } elseif($step == 'bb') {
                if ($limitProtocolAccessList) {
                    $protocol_id = Protocol::where(function ($query) {
                        $query->where(DB::raw("CONCAT(`diekperaiosi`, ',')"), 'like', "%" . 'd' . Auth::user()->id . ",%")->orWhere(DB::raw("CONCAT(`diekperaiosi`, ',')"), 'like', "%" . 'e' . Auth::user()->id . ",%")->orWhere('user_id', Auth::user()->id);
                    })->where(function ($query) use($protocolnum, $etos) {
                        $query->where([['protocolnum', '<', $protocolnum],['etos', $etos]])->orwhere('etos', "<", $etos);
                    })->orderby('etos', 'DESC')->orderby('protocolnum', 'DESC')->skip(Config::getConfigValueOf('protocolArrowStep')-1)->take(1)->get('id');
                    if (count($protocol_id)) return redirect("home/" . $protocol_id[0]->id);
                    return redirect("home");
                } else {
                    $protocol_id = Protocol::where([['protocolnum', '<', $protocolnum],['etos', $etos]])->orwhere('etos', "<", $etos)->orderby('etos', 'DESC')->orderby('protocolnum', 'DESC')->skip(Config::getConfigValueOf('protocolArrowStep') - 1)->take(1)->get('id');
                    if (count($protocol_id)) return redirect("home/" . $protocol_id[0]->id);
                    return redirect("home");
                }
            } elseif( $step == 'ff') {
                if ($limitProtocolAccessList) {
                    $protocol_id = Protocol::where(function ($query) {
                        $query->where(DB::raw("CONCAT(`diekperaiosi`, ',')"), 'like', "%" . 'd' . Auth::user()->id . ",%")->orWhere(DB::raw("CONCAT(`diekperaiosi`, ',')"), 'like', "%" . 'e' . Auth::user()->id . ",%")->orWhere('user_id', Auth::user()->id);
                    })->where(function ($query) use($protocolnum, $etos) {
                        $query->where([['protocolnum', '>', $protocolnum],['etos', $etos]])->orwhere('etos', ">", $etos);
                    })->orderby('etos', 'ASC')->orderby('protocolnum', 'ASC')->skip(Config::getConfigValueOf('protocolArrowStep') - 1)->take(1)->get('id');
                    if (count($protocol_id)) return redirect("home/" . $protocol_id[0]->id);
                    return redirect("home");
                } else {
                    $protocol_id = Protocol::where([['protocolnum', '>', $protocolnum],['etos', $etos]])->orwhere('etos', ">", $etos)->orderby('etos', 'ASC')->orderby('protocolnum', 'ASC')->skip(Config::getConfigValueOf('protocolArrowStep') - 1)->take(1)->get('id');
                    if (count($protocol_id)) return redirect("home/" . $protocol_id[0]->id);
                    return redirect("home");
                }
            }
        }
        $notification = array(
            'message' => 'Δεν βρέθηκε Πρωτόκολλο να ικανοποιεί τα στοιχεία που δώσατε.',
            'alert-type' => 'warning'
        );
        session()->flash('notification', $notification);
        return back();
    }

    public function download(Attachment $attachment)
    {
        if (Storage::exists($attachment->savedPath)) {
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
        session()->flash('notification', $notification);
        return back();
    }


    public function find()
    {
        $fields = array_merge($this->protocolfields, $this->attachmentfields);
        $attachmentfields = $this->attachmentfields;

        $searchField1 = Config::getConfigValueOf('searchField1');
        $searchField2 = Config::getConfigValueOf('searchField2');
        $searchField3 = Config::getConfigValueOf('searchField3');

        return view('find', compact('fields', 'attachmentfields', 'searchField1', 'searchField2', 'searchField3'));
    }

    public function getFindData()
    {

        $maxRowsInFindPage = Config::getConfigValueOf('maxRowsInFindPage');
        $diekpStr = 'd';

        $fields = array_merge($this->protocolfields, $this->attachmentfields);
        $attachmentfields = $this->attachmentfields;

        $wherevalues = [];
        $whereNullFields = [];
        $whereAttachmentvalues = [];
        $whereNullAttachmentvalues = [];

        if (request('aponum')) {
            $wherevalues[] = ['protocolnum', '>', request('aponum') - 1];
        }
        if (request('eosnum')) {
            $wherevalues[] = ['protocolnum', '<', request('eosnum') + 1];
        }
        if (request('etosForMany')) {
            $wherevalues[] = ['etos', request('etosForMany')];
        }

        if (request('apoProtocolDate')) {
            $wherevalues[] = ['protocoldate', '>=', Carbon::createFromFormat('d/m/Y', request('apoProtocolDate'))->format('Ymd')];
        }
        if (request('eosProtocolDate')) {
            $wherevalues[] = ['protocoldate', '<=', Carbon::createFromFormat('d/m/Y', request('eosProtocolDate'))->format('Ymd')];
        }
        if (request('apoEiserxDate')) {
            $wherevalues[] = ['in_date', '>=', Carbon::createFromFormat('d/m/Y', request('apoEiserxDate'))->format('Ymd')];
        }
        if (request('eosEiserxDate')) {
            $wherevalues[] = ['in_date', '<=', Carbon::createFromFormat('d/m/Y', request('eosEiserxDate'))->format('Ymd')];
        }
        if (request('apoExerxDate')) {
            $wherevalues[] = ['out_date', '>=', Carbon::createFromFormat('d/m/Y', request('apoExerxDate'))->format('Ymd')];
        }
        if (request('eosExerxDate')) {
            $wherevalues[] = ['out_date', '<=', Carbon::createFromFormat('d/m/Y', request('eosExerxDate'))->format('Ymd')];
        }
        if (request('searchData1') or request('searchData1chk')) {
            if (request('searchData1chk')) {
                if (array_key_exists(request('searchField1'), $attachmentfields)) {
                    $whereNullAttachmentvalues[] = request('searchField1');
                } else {
                    $whereNullFields[] = request('searchField1');
                }
            } else {
                if (array_key_exists(request('searchField1'), $attachmentfields)) {
                    $whereAttachmentvalues[] = [request('searchField1'), 'LIKE', '%' . request('searchData1') . '%'];
                } else {
                    if (request('searchField1') == 'diekperaiosi') {
                        $searchData = $diekpStr . User::where('name', 'LIKE', '%' . request('searchData1') . '%')->first('id')->id;
                        if ($searchData) {
                            $wherevalues[] = [request('searchField1'), $searchData];
                        }
                    } else {
                        $wherevalues[] = [request('searchField1'), 'LIKE', '%' . request('searchData1') . '%'];
                    }
                }
            }
        }
        if (request('searchData2') or request('searchData2chk')) {
            if (request('searchData2chk')) {
                if (array_key_exists(request('searchField2'), $attachmentfields)) {
                    $whereNullAttachmentvalues[] = request('searchField2');
                } else {
                    $whereNullFields[] = request('searchField2');
                }
            } else {
                if (array_key_exists(request('searchField2'), $attachmentfields)) {
                    $whereAttachmentvalues[] = [request('searchField2'), 'LIKE', '%' . request('searchData2') . '%'];
                } else {
                    if (request('searchField2') == 'diekperaiosi') {
                        $searchData = $diekpStr . User::where('name', 'LIKE', '%' . request('searchData2') . '%')->first('id')->id;
                        if ($searchData) {
                            $wherevalues[] = [request('searchField2'), $searchData];
                        }
                    } else {
                        $wherevalues[] = [request('searchField2'), 'LIKE', '%' . request('searchData2') . '%'];
                    }
                }
            }
        }
        if (request('searchData3') or request('searchData3chk')) {
            if (request('searchData3chk')) {
                if (array_key_exists(request('searchField3'), $attachmentfields)) {
                    $whereNullAttachmentvalues[] = request('searchField3');
                } else {
                    $whereNullFields[] = request('searchField3');
                }
            } else {
                if (array_key_exists(request('searchField3'), $attachmentfields)) {
                    $whereAttachmentvalues[] =  [request('searchField3'), 'LIKE', '%' . request('searchData3')  . '%'];
                } else {
                    if (request('searchField3') == 'diekperaiosi') {
                        $searchData = $diekpStr . User::where('name', 'LIKE', '%' . request('searchData3') . '%')->first('id')->id;
                        if ($searchData) {
                            $wherevalues[] = [request('searchField3'), $searchData];
                        }
                    } else {
                        $wherevalues[] = [request('searchField3'), 'LIKE', '%' . request('searchData3') . '%'];
                    }
                }
            }
        }
        if (!$wherevalues and !$whereAttachmentvalues and !$whereNullFields and !$whereNullAttachmentvalues) {
            return;
        }
        $foundProtocolsCount = null;
        $protocols = Protocol::with('attachments');
        foreach ($whereNullFields as $whereNullField) {
            $protocols = $protocols->whereNull($whereNullField);
        }
        if ($wherevalues) {
            // αν η ρύθμιση για Συγγραφείς και Αναγνώστες είναι ΝΑΙ περιορισμός στη λίστα
            // φιλτράρω τα μηνύματα. Επιτρέπονται μόνο όσα είναι για διεκπεραίωση - ενημέρωση - δημιουργήθηκαν από αυτούς
            if ($this->limitProtocolAccessList()) {
                $protocols = $protocols->where(function ($query) {
                    $query->where(DB::raw("CONCAT(`diekperaiosi`, ',')"), 'like', "%" . 'd' . Auth::user()->id . ",%")->orWhere(DB::raw("CONCAT(`diekperaiosi`, ',')"), 'like', "%" . 'e' . Auth::user()->id . ",%")->orWhere('user_id', Auth::user()->id);
                });
            }
            $protocols = $protocols->where($wherevalues);
        }
        foreach ($whereNullAttachmentvalues as $whereNullAttachmentvalue) {
            $protocols = $protocols->whereHas('attachments', function ($query) use ($whereNullAttachmentvalue) {
                $query->whereNull($whereNullAttachmentvalue);
            });
        }
        if ($whereAttachmentvalues) {
            $protocols = $protocols->whereHas('attachments', function ($query) use ($whereAttachmentvalues) {
                $query->where($whereAttachmentvalues);
            });
        }
        $protocols = $protocols->orderby('protocoldate', 'desc')->orderby('protocolnum', 'desc')->take($maxRowsInFindPage);
        $protocols = $protocols->get();
        $foundProtocolsCount = $protocols->count();

        foreach ($protocols as $protocol) {
            if ($protocol->protocoldate) {
                $protocol->protocoldate = Carbon::createFromFormat('Ymd', $protocol->protocoldate)->format('d/m/Y');
            }
            if ($protocol->diekperaiosi) {
                $protocol->diekperaiosi = User::where('id', ltrim($protocol->diekperaiosi, $diekpStr))->first('name')->name ?? null;
            }
        }

        $searchField1 = request('searchField1');
        $searchField2 = request('searchField2');
        $searchField3 = request('searchField3');
        $searchData1 = request('searchData1');
        $searchData2 = request('searchData2');
        $searchData3 = request('searchData3');

        return view('getFindData', compact('protocols', 'foundProtocolsCount', 'maxRowsInFindPage', 'fields', 'attachmentfields', 'searchField1', 'searchField2', 'searchField3', 'searchData1', 'searchData2', 'searchData3'));
    }

    public function printprotocols()
    {
        return view('print');
    }

    public function printed()
    {

        $etos = Config::getConfigValueOf('yearInUse');
        $datetime = Carbon::now()->format('d/m/Y H:m:s');
        $target = request('target');

        $wherevalues = [];

        if (request('aponum')) {
            $wherevalues[] = ['protocolnum', '>', request('aponum') - 1];
        }
        if (request('eosnum')) {
            $wherevalues[] = ['protocolnum', '<', request('eosnum') + 1];
        }
        if (request('etosForMany')) {
            $wherevalues[] = ['etos', request('etosForMany')];
            $etos = request('etosForMany');
        }
        if (request('apoProtocolDate')) {
            $wherevalues[] = ['protocoldate', '>=', Carbon::createFromFormat('d/m/Y', request('apoProtocolDate'))->format('Ymd')];
        }
        if (request('eosProtocolDate')) {
            $wherevalues[] = ['protocoldate', '<=', Carbon::createFromFormat('d/m/Y', request('eosProtocolDate'))->format('Ymd')];
        }
        $foundProtocolsCount = null;
        if (!$wherevalues) {
            return back();
        } else {
            $foundProtocolsCount = Protocol::where($wherevalues)->count();
            $protocols = Protocol::where($wherevalues)->orderby('etos', 'asc')->orderby('protocolnum', 'asc')->get();
        }
        foreach ($protocols as $protocol) {
            if ($protocol->protocoldate) {
                $protocol->protocoldate = Carbon::createFromFormat('Ymd', $protocol->protocoldate)->format('d/m/Y');
            }
            if ($protocol->in_date) {
                $protocol->in_date = Carbon::createFromFormat('Ymd', $protocol->in_date)->format('d/m/Y');
            }
            if ($protocol->out_date) {
                $protocol->out_date = Carbon::createFromFormat('Ymd', $protocol->out_date)->format('d/m/Y');
            }
            if ($protocol->diekp_date) {
                $protocol->diekp_date = Carbon::createFromFormat('Ymd', $protocol->diekp_date)->format('d/m/Y');
            }
        }

        if($target == 'xls'){
            $filename = $this->filter_filename( Config::getConfigValueOf('ipiresiasName') . " - εξαγωγή πρωτοκόλλου σε xls - " . Carbon::now()->format('Ymd-Hms') . '.xlsx', false);
            return Excel::download(new ProtocolExport("printedXls", compact('protocols', 'etos', 'datetime')), $filename );
        }
        return view('printed', compact('protocols', 'etos', 'datetime'));
    }

    public function receipt(Protocol $protocol)
    {
        if ($protocol->protocoldate) {
            $protocol->protocoldate = Carbon::createFromFormat('Ymd', $protocol->protocoldate)->format('d/m/Y');
        }

        $datetime = Carbon::now()->format('d/m/Y H:m:s');
        return view('receipt', compact('protocol', 'datetime'));
    }

    public function receiptToEmail(Request $request)
    {
        $protocol = Protocol::where('id', $request->id)->first();
        $emaildate = Carbon::now()->format('d/m/Y H:m:s');
        $protocoldate = Carbon::createFromFormat('Ymd', $protocol->protocoldate)->format('d/m/Y');
        $sendReplyTo = $request->email;
        $html = view('receiptProtocol', compact('protocol', 'protocoldate'))->render();

        Mail::send([], [], function ($message) use ($sendReplyTo, $html) {
            $message->from(config('mail.from.address'));
            $message->to($sendReplyTo);
            $message->subject("Καταχώριση στο Ηλεκτρονικό Πρωτόκολλο.");
            $message->setBody($html, 'text/html');
        });
        if (!count(Mail::failures())) {
            $parMessage = $protocol->paratiriseis ? $protocol->paratiriseis . ', ' : '';
            $parMessage .= "$emaildate απόδειξη παραλαβής σε $sendReplyTo";
            //$parMessage = mb_strlen($parMessage) > 250 ? mb_substr($parMessage, 0, 250) . ' ...' : $parMessage;
            $protocol->paratiriseis = $parMessage;
            $protocol->update();
            return response()->json($parMessage);
        } else {
            return response()->json(Mail::failures());
        }
    }

    public function setDiekpDate(Request $request)
    {
        $diekp_date = Carbon::createFromFormat('d/m/Y', $request->diekp_date)->format('Ymd');
        $now = Carbon::now()->format('d/m/Y H:m:s');
        $paratiriseis = Protocol::whereId($request->id)->first()->paratiriseis;
        $parMessage = $paratiriseis ? $paratiriseis . ', ' : '';
        $parMessage .= "$now διεκπεραιώθηκε από " . Auth::user()->name;
        //$parMessage = mb_strlen($parMessage) > 250 ? mb_substr($parMessage, 0, 250) . ' ...' : $parMessage;
        $result = Protocol::whereId($request->id)->update([
            'user_id' => Auth::user()->id,
            'diekp_date' => $diekp_date,
            'paratiriseis' => $parMessage,
        ]);
        // ενημερώνω το χρήστη
        $notification = array(
            'message' => "Επιτυχής ενημέρωση της ημερομηνίας Διεκπεραίωσης σε $request->diekp_date.",
            'alert-type' => 'success'
        );
        session()->flash('notification', $notification);
        // επιστρέφω
        return response()->json($result);
    }

    public function about()
    {
        return view('about');
    }

    public function updated()
    {
        $file = storage_path('conf/.updateCheck');
        unlink($file);

        Config::setConfigValueOf('needsUpdate', 0);
        return redirect("home/list");
    }

    public function printAttachments()
    {
        return view('printAttachments');
    }

    public function printedAttachments()
    {

        $etos = Config::getConfigValueOf('yearInUse');
        $datetime = Carbon::now()->format('d/m/Y H:m:s');

        $wherevalues = [];

        if (request('aponum')) {
            $wherevalues[] = ['protocolnum', '>', request('aponum') - 1];
        }
        if (request('eosnum')) {
            $wherevalues[] = ['protocolnum', '<', request('eosnum') + 1];
        }
        if (request('etosForMany')) {
            $wherevalues[] = ['etos', request('etosForMany')];
            $etos = request('etosForMany');
        }
        if (request('apoProtocolDate')) {
            $wherevalues[] = ['protocoldate', '>=', Carbon::createFromFormat('d/m/Y', request('apoProtocolDate'))->format('Ymd')];
        }
        if (request('eosProtocolDate')) {
            $wherevalues[] = ['protocoldate', '<=', Carbon::createFromFormat('d/m/Y', request('eosProtocolDate'))->format('Ymd')];
        }

        $foundProtocolsCount = null;
        if (!$wherevalues) {
            return back();
        } else {
            $protocols = Protocol::has('attachments')->where($wherevalues)->orderby('protocolnum', 'asc')->get();
        }
        foreach ($protocols as $protocol) {
            if ($protocol->protocoldate) {
                $protocol->protocoldate = Carbon::createFromFormat('Ymd', $protocol->protocoldate)->format('d/m/Y');
            }
            if ($protocol->in_date) {
                $protocol->in_date = Carbon::createFromFormat('Ymd', $protocol->in_date)->format('d/m/Y');
            }
            if ($protocol->out_date) {
                $protocol->out_date = Carbon::createFromFormat('Ymd', $protocol->out_date)->format('d/m/Y');
            }
        }

        return view('printedAttachments', compact('protocols', 'etos', 'datetime'));
    }

    public function getEmailNum()
    {
        // διαβάζω τον λογαριασμό imap
        $defaultImapEmail = Config::getConfigValueOf('defaultImapEmail');
        // διαβάζω πόσες μέρες πίσω να κοιτάξω για email
        $daysToCheckEmailBack = Config::getConfigValueOf('daysToCheckEmailBack');
        // υπολογισμός από την ημνια
        $sinceDate = Carbon::now()->subDays($daysToCheckEmailBack)->format('d-m-Y');
        // αν ο λογαριασμός είναι κενός δεν προχωράω
        if (!$defaultImapEmail) {
            return null;
        }
        // αν η βιβλιοθήκη imap δεν είναι φορτωμένη δεν προχωράω
        if (!extension_loaded('imap')) {
            return null;
        }
        // φορτώνω τον πελάτη (λογαριασμό)
        $oClient = Client::account($defaultImapEmail);
        try {
            //σύνδεση στον IMAP Server
            $oClient->connect();
        } catch (\Throwable $e) {
            report($e);
            return 0;
        }
        // επιλέγω τον φάκελο INBOX
        $oFolder = $oClient->getFolder('INBOX');
        // αριθμός μηνυμάτων από $sinceDate και μετά
        $aMessageNum = $oFolder->query()->since($sinceDate)->count();
        return $aMessageNum;
    }

    public function viewEmails()
    {
        $imap_page = request('imap_page') ?? 1;
        // διαβάζω τις ρυθμίσεις
        $writers_admins = User::get_writers_and_admins();
        $allowUserChangeKeepSelect = Config::getConfigValueOf('allowUserChangeKeepSelect');
        $emailNumFetch = Config::getConfigValueOf('emailNumFetch');
        $defaultImapEmail = Config::getConfigValueOf('defaultImapEmail');
        $daysToCheckEmailBack = Config::getConfigValueOf('daysToCheckEmailBack');
        $sinceDate = Carbon::now()->subDays($daysToCheckEmailBack)->format('d-m-Y');
        $alwaysShowFakelosInViewEmails = Config::getConfigValueOf('alwaysShowFakelosInViewEmails');
        $alwaysSendReceitForEmails = Config::getConfigValueOf('alwaysSendReceitForEmails');
        $allowListValuesMatchingInput = Config::getConfigValueOf('allowListValuesMatchingInput');
        // Μόνο Διαχειριστής και Αναθέτων μπορούν να αναθέσουν Διεκπεραίωση σε χρήστη
        $forbidenChangeDiekperaiosiSelect = 1;
        if (in_array(Auth::user()->role_description(), ["Διαχειριστής",  "Αναθέτων"])) {
            $forbidenChangeDiekperaiosiSelect = null;
        }

        // παίρνω τα ΠΡΩΤΑ ή ΤΕΛΕΥΤΑΙΑ μηνύματα  με βάση τις ρυθμίσεις
        // emailFetchOrderDesc == 1 => ΤΕΛΕΥΤΑΙΑ
        if (Config::getConfigValueOf('emailFetchOrderDesc')) {
            config(['imap.options.fetch_order' => 'desc']);
        }


        // φορτώνω τον πελάτη (λογαριασμό)
        $oClient = Client::account($defaultImapEmail);
        try {
            //σύνδεση στον IMAP Server
            $oClient->connect();
        } catch (\Throwable $e) {
            // σε λάθος ενημερώνω το log
            report($e);
            // ενημερώνω το χρήστη
            $notification = array(
                'message' => "Η σύνδεση με τον λογαριασμό email απέτυχε.<br>Ελέγξτε τις ρυθμίσεις.",
                'alert-type' => 'error'
            );
            session()->flash('notification', $notification);
            // επιστρέφω
            return back();
        }
        // διάβασμα και ταξινόμηση των φακέλων
        $fakeloi = Keepvalue::orderBy(DB::raw("SUBSTR(`fakelos`,3,LENGTH(`fakelos`)-3)+0<>0 DESC, SUBSTR(`fakelos`,3,LENGTH(`fakelos`)-(3))+0, `fakelos`"))->select('fakelos', 'describe')->get();
        // γεμίζω τη λίστα με τη διατήρηση αρχείων
        $years = Keepvalue::whereNotNull('keep')->select('keep')->distinct()->orderby('keep', 'asc')->get();
        $words = Keepvalue::whereNotNull('keep_alt')->select('keep_alt')->distinct()->orderby('keep_alt', 'asc')->get();
        // σύνδεση στον φάκελο INBOX
        $oFolder = $oClient->getFolder('INBOX');
        // σειρά ταξινόμησης μηνυμάτων στον imap server πριν τα κατεβάσω
        // παίρνω τον αριθμό των μηνυμάτων από sinceDate και μετά
        $aMessageNum = $oFolder->query()->since($sinceDate)->count();
        // παίρνω τα μηνύματα από sinceDate και μετά
        $aMessage = $oFolder->query()->since($sinceDate)->paginate($emailNumFetch, $imap_page);
        $aMessageCount = $aMessage->count();

        if(Config::getConfigValueOf('emailFetchOrderDesc')){
            $aSortedMessage = $aMessage->sortByDesc(function ($oMessage) {
                return Carbon::parse($oMessage->getDate())->timezone('UTC');
            });
          } else {
            $aSortedMessage = $aMessage->sortBy(function ($oMessage) {
                return Carbon::parse($oMessage->getDate())->timezone('UTC');
            });
          }

        // διαγραφή τυχόν προηγούμενα αποθηκευμένων email.html
        $files = Storage::disk('tmp')->files();
        Storage::disk('tmp')->delete($files);

        $emailFilePaths = array();
        // για κάθε μήνυμα
        foreach ($aMessage as $oMessage) {
            $mailMessage = Message::from($oMessage->getHeader()->raw . $oMessage->getRawBody());
            $Uid = $oMessage->getUid();
            $dir = '';
            // αν έχει σώμα html το αποθηκεύω
            if (strlen($mailMessage->getHtmlContent())) {
                // περιεχόμενο HTML
                $content = $mailMessage->getHtmlContent();
                // αντικαθιστώ οτιδήποτε μετά το charset= με utf-8
                $content = preg_replace('/charset=[\s\S]+?"/', 'charset=utf-8"', $content);
                // φτιάχνω φάκελο και όνομα αρχείου /tmp/$Uid.html
                $filenameToStore = "$Uid.html";
                $savedPath = $dir . $filenameToStore;
                // αποθηκεύω το email στο /public/tmp
                Storage::disk('tmp')->put($savedPath, $content);
                // κρατάω σε πίνακα το path
                $emailFilePaths[$Uid] = $savedPath;
            }
            // αποθηκεύω το email σαν eml για να μη το ξανακατεβάζω
            $filenameToStore = "$Uid.eml";
            $savedPath = $dir . $filenameToStore;
            $contentRaw = $oMessage->getHeader()->raw . $oMessage->getRawBody();
            // αποθηκεύω το email στο /public/tmp
            Storage::disk('tmp')->put($savedPath, $contentRaw);
        }
        session()->put('imap_page', $imap_page);
        return view('viewEmails', compact('aMessage', 'aSortedMessage', 'aMessageNum', 'aMessageCount', 'defaultImapEmail', 'fakeloi', 'allowUserChangeKeepSelect', 'years', 'words', 'alwaysShowFakelosInViewEmails', 'forbidenChangeDiekperaiosiSelect', 'writers_admins', 'emailFilePaths', 'alwaysSendReceitForEmails', 'allowListValuesMatchingInput', 'imap_page'));
    }

    // εμφάνιση του συνημμένου αρχείου
    public function viewEmailAttachment($messageUid, $attachmentKey)
    {
        $mailMessage = $this->readSavedEmailFromFile($messageUid);
        // αν είναι null σταματάω
        if( ! $mailMessage ) return;
        // παίρνω το συνημμένο
        $oAttachment = $mailMessage->getAttachmentPart($attachmentKey);
        // παίρνω το περιεχόμενο
        $content = $oAttachment->getContent();
        // παίρνω το όνομα του αρχείου
        $filename = $oAttachment->getFilename();
        // το στέλνω για εμφάνιση
        return response($content)
            ->header('Content-Type', $oAttachment->getContentType())
            ->header('Content-Disposition', "filename=" . $filename);
    }

    public function setEmailRead($messageUid)
    {
        $defaultImapEmail = Config::getConfigValueOf('defaultImapEmail');
        if (!$defaultImapEmail) {
            return back();
        }
        if (!extension_loaded('imap')) {
            return back();
        }

        $oClient = Client::account($defaultImapEmail);
        try {
            $oClient->connect();
        } catch (\Throwable $e) {
            report($e);
            $notification = array(
                'message' => "Η σύνδεση με τον λογαριασμό email απέτυχε.<br>Ελέγξτε τις ρυθμίσεις.",
                'alert-type' => 'error'
            );
            session()->flash('notification', $notification);
            return back();
        }
        // αν δεν υπάρχει ο φάκελος INBOX.beenRead τον φτιάχνω
        if (!$oClient->getFolder('INBOX.beenRead')) {
            $oClient->createFolder('INBOX.beenRead');
        }

        $oFolder = $oClient->getFolder('INBOX');
        $oMessage = $oFolder->query()->getMessageByUid($messageUid, null, null, false, false, false);
        if ($oMessage) {
            // μεταφέρω το μήνυμα στα διαβασμένα
            $oMessage->move('INBOX.beenRead', true);
            // ενημερώνω τον χρήστη
            $notification = array(
                'message' => "Το μήνυμα μεταφέρθηκε στα Αναγνωσμένα",
                'alert-type' => 'success'
            );
            session()->flash('notification', $notification);
        } else {
            // ενημερώνω τον χρήστη
            $notification = array(
                'message' => "Δεν βρέθηκε μήνυμα με τον κωδικό: " . $messageUid,
                'alert-type' => 'error'
            );
            session()->flash('notification', $notification);
        }

        if(session()->has('imap_page')) {
            $imap_page = session('imap_page');
            session()->pull('imap_page');
            return redirect('/viewEmails?imap_page=' . $imap_page);
        }

        // επιστρέφω
        return redirect('/viewEmails');
    }

    public function storeFromEmail()
    {
        // παίρνω τα δεδομένα
        $data = request()->all();

        // διαγράφω το προσωρινά αποθηκευμένο email
        // στον φάκελο public/tmp
        $uid = $data['uid'];

        // φορτώνω σε μεταβλητές τιμές που θα χρησιμοποιήσω
        // φάκελος
        isset($data["fakelos$uid"]) ? $fakelos = $data["fakelos$uid"] : $fakelos = null;
        // χρόνος διατήρησης
        isset($data["keep$uid"]) ? $keep = $data["keep$uid"] : $keep = null;

        // id περαιωτή. Αν υπάρχει θα στείλω email για ανάθεση Πρωτοκόλλου
        $sendEmailTo = $data["sendEmailTo"] ?? '';
        // θα στείλω απόδειξη παραλαβής; ΝΑΙ(1) - ΟΧΙ(0)
        $sendReceipt = $data["sendReceipt$uid"] ?? '';
        // παίρνω το email για απόδειξη παραλαβής
        $sendReplyTo = trim($data["reply_to"]?? '') ;
        // βρίσκω τα τσεκαρισμένα checkboxes για να δω ποια συνημμένα θα αποθηκευτούν
        $chkboxes = array_filter($data, function ($k) use ($uid) {
            return strpos($k, "chk$uid-") !== false;
        }, ARRAY_FILTER_USE_KEY);
        $attachmentKeys = array();
        foreach ($chkboxes as $key => $val) {
            // τα keys των συνημμένων για αποθήκευση σε πινακα
            $attachmentKeys[] = substr($key, strpos($key, "-") + 1);
        }

        // διαβάζω ρυθμίσεις
        $sendEmailOnDiekperaiosiChange = Config::getConfigValueOf('sendEmailOnDiekperaiosiChange');

        $mailMessage = $this->readSavedEmailFromFile($uid);
        // αν είναι null σταματάω
        if( ! $mailMessage ){
            $notification = array(
                'message' => "Δυστυχώς δεν μπορώ να αποθηκεύσω το email με κωδικό: $uid",
                'alert-type' => 'error'
            );
            session()->flash('notification', $notification);
            return back();

        }

        // βάζω τα δεδομένα σε μεταβλητές
        if (isset($data["thema"])) {
            $thema = $data["thema"];
        } else {
            $thema = $mailMessage->getHeaderValue(HeaderConsts::SUBJECT);
        }
        // αλλάζω τις ημνιες στην κατάλληλη μορφή για αποθήκευση
        $in_num = isset($data["in_num"]) ? $data["in_num"] : Carbon::parse($mailMessage->getHeader(HeaderConsts::DATE)->getDateTime())->format('H:i:s');
        $in_date = isset($data["in_date"]) ? Carbon::createFromFormat('d/m/Y', $data["in_date"])->format('Ymd') : Carbon::parse($mailMessage->getHeader(HeaderConsts::DATE)->getDateTime())->format('Ymd');
        $in_arxi_ekdosis = null;
        // αν πληκτρολογήθηκε αρχή έκδοσης
        if (isset($data["in_arxi_ekdosis"])) {
            $in_arxi_ekdosis = $data["in_arxi_ekdosis"];
        } else {
            // αν δεν πληκτρολογήθηκε αρχή έκδοσης παίρνω από το email το πεδίο from
            // έχει δύο μέρη ΟΝΟΜΑΤΕΠΩΝΥΜΟ & EMAIL που τα προσθέτω ΟΝΟΜΑΤΕΠΩΝΥΜΟ <EMAIL>
            // Το ΟΝΟΜΑΤΕΠΩΝΥΜΟ αν υπάρχει του αλλάζω κωδικοποίηση (αχ κακόμοιρα Ελληνικά!)
            if ( $mailMessage->getHeader(HeaderConsts::FROM)->getAddresses()[0]->getName()) {
                    $in_arxi_ekdosis =  $mailMessage->getHeader(HeaderConsts::FROM)->getAddresses()[0]->getName();
                    $in_arxi_ekdosis .= " <" .  $mailMessage->getHeader(HeaderConsts::FROM)->getAddresses()[0]->getEmail() . ">";
            }else{
                $in_arxi_ekdosis .= $mailMessage->getHeader(HeaderConsts::FROM)->getAddresses()[0]->getEmail();
            }
        }
        // άλλα πεδία
        $in_topos_ekdosis = isset($data["in_topos_ekdosis"]) ? $data["in_topos_ekdosis"] : "";
        $in_paraliptis = isset($data["in_paraliptis"]) ? $data["in_paraliptis"] : Auth::user()->name;
        $in_perilipsi = isset($data["in_perilipsi"]) ? mb_substr($data["in_perilipsi"], 0, 250) : mb_substr(preg_replace('~^\s+|\s+$~us', "", trim($mailMessage->getTextContent())), 0, 250);
        $paratiriseis = 'παρελήφθη με email';
        $diekperaiosi = isset($data['diekperaiosi']) ? implode(',', $data['diekperaiosi']) : "";
        $etos = Carbon::now()->format('Y');

        // βρίσκω το νέο Αρ.Πρωτ στην εισαγωγή δεδομένων
        $firstProtocolNum = Config::getConfigValueOf('firstProtocolNum');
        if (Protocol::count()) {
            if (Config::getConfigValueOf('yearInUse')) {
                $newprotocolnum = Protocol::whereEtos($etos)->max('protocolnum') ? Protocol::whereEtos($etos)->max('protocolnum') + 1 : 1;
            } else {
                $newprotocolnum = Protocol::last()->protocolnum ? Protocol::last()->protocolnum + 1 : 1;
            }
        } else {
            if ($firstProtocolNum) {
                $newprotocolnum = $firstProtocolNum;
            } else {
                $newprotocolnum = 1;
            }
        }

        // αποθηκεύω το Πρωτόκολλο
        try {
            $protocolCreated = Protocol::create([
                'user_id' => Auth::user()->id,
                'protocolnum' => $newprotocolnum,
                'protocoldate' => Carbon::now()->format('Ymd'),
                'etos' => $etos,
                'fakelos' => $fakelos,
                'thema' => $thema,
                'in_num' => $in_num,
                'in_date' => $in_date,
                'in_topos_ekdosis' =>  $in_topos_ekdosis,
                'in_arxi_ekdosis' => $in_arxi_ekdosis,
                'in_paraliptis' => $in_paraliptis,
                'diekperaiosi' => $diekperaiosi,
                'in_perilipsi' => $in_perilipsi,
                'out_date' => null,
                'diekp_date' => null,
                'sxetiko' => null,
                'out_to' => null,
                'out_perilipsi' => null,
                'keywords' => null,
                'paratiriseis' => $paratiriseis
            ]);
        } catch (\Throwable $e) {
            // σε λάθος γράφω στο log
            report($e);
            // ειδοποιώ τον χρήστη
            $notification = array(
                'message' => 'Υπήρξε κάποιο πρόβλημα στην καταχώριση του email στο Πρωτόκολλο<br>Παρακαλώ επαναλάβετε την καταχώριση.' . $e->getMessage(),
                'alert-type' => 'error'
            );
            session()->flash('notification', $notification);
            // επιστρέφω
            return back();
        }
        // ετοιμάζω το τελικό μήνυμα
        if ($protocolCreated) {
            $message = "Το email καταχωρίστηκε.";
        }

        $protocol = $protocolCreated;

        if(Config::getConfigValueOf('saveEmailAs')){
            // αποθηκεύω το email σαν συνημμένο eml
            $html = file_get_contents('tmp/' . $uid . '.eml');
            $filename = 'email_' . Carbon::parse($mailMessage->getHeader(HeaderConsts::DATE)->getDateTime())->format('Ymd_His') . '.' . Config::getConfigValueOf('saveEmailAs');
            $mimetype = 'message/rfc822';
        }else{
            // αποθηκεύω το email σαν συνημμένο html
            $html = view('viewEmail', compact('mailMessage'))->render();
            $filename = 'email_' . Carbon::parse($mailMessage->getHeader(HeaderConsts::DATE)->getDateTime())->format('Ymd_His') . '.html';
            $mimetype = 'text/html';
        }
        $filenameToStore = $protocol->protocolnum . '-' . $protocol->protocoldate . '_' . $filename;
        $dir = $fakelos ? '/arxeio/' . $fakelos . '/' : '/arxeio/emails/';
        $savedPath = $dir . $filenameToStore;
        Storage::put($savedPath, $html);
        $expires = null;
        if ($keep and is_numeric($keep)) {
            $dt = Carbon::createFromFormat('Ymd', $protocol->protocoldate);
            $dt->addYears($keep);
            $expires = $dt->format('Ymd');
        }

        try {
            Attachment::create([
                'protocol_id' => $protocol->id,
                'ada' => null,
                'name' => $filename,
                'mimeType' => $mimetype,
                'savedPath' => $savedPath,
                'keep' => $keep,
                'expires' => $expires,
            ]);

            // αποθηκεύω τα συνημμένα
            $numCreatedAttachments = 0;
            $numMissedAttachments = 0;
            foreach ($attachmentKeys as $attachmentKey) {
                $oAttachment = $mailMessage->getAttachmentPart($attachmentKey);
                if (!$oAttachment) {
                    $numMissedAttachments++;
                    continue;
                }
                $content = $oAttachment->getContent();
                $mimeType = $oAttachment->getHeaderValue(HeaderConsts::CONTENT_TYPE);
                $filename = $oAttachment->getFilename();
                // αφαίρεση απαγορευμένων χαρακτήρων από το όνομα του συνημμένου
                $filename = $this->filter_filename($filename, false);
                $filenameToStore = $protocol->protocolnum . '-' . $protocol->protocoldate . '_' . $attachmentKey . '_' . $filename;
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
                
                if ($createdAttachment) {
                    $numCreatedAttachments++;
                }
            }
        } catch (\Throwable $e) {
            report($e);
            $notification = array(
                'message' => 'Υπήρξε κάποιο πρόβλημα στην καταχώριση των συνημμένων αρχείων<br>Ελέγξτε αν καταχωρίστηκαν όλα σωστά.<br>' . $e->getMessage(),
                'alert-type' => 'error'
            );
            session()->flash('notification', $notification);
            return redirect("home/" . $protocol->id);
        }

        if ($numCreatedAttachments) {
            $message .= "<li>Εισήχθηκαν $numCreatedAttachments συνημμένα αρχεία.</li>";
        }
        if ($numMissedAttachments) {
            $message .= "<li>Παραλείφθηκαν $numMissedAttachments συνημμένα αρχεία.<br> Δοκιμαστε να τα εισάγετε στο Ηλ. Πρωτόκολλο χειροκίνητα αφού πρώτα τα αποθηκεύσετε στο δίσκο σας</li>
            ";
        }

        // στέλνω mail στον Διεκπεραιωτή
        if ($sendEmailTo && $sendEmailOnDiekperaiosiChange) {
            $sendEmailToArray = explode(',', $sendEmailTo);
            $message = '';
            foreach ($sendEmailToArray as $smt) {
                if (substr($smt, 0, 1) == 'd') {
                    $message .= $this->sendMailToDiekperaioti(ltrim($smt, 'd'), $protocol);
                }
                if (substr($smt, 0, 1) == 'e') {
                    $message .= $this->diekpInformMail(ltrim($smt, 'e'), $protocol);
                }
            }
        }
        // στέλνω mail απόδειξης παραλαβής στον αποστολέα
        if ($sendReceipt) {
            $emaildate = $mailMessage->getHeader(HeaderConsts::DATE)->getDateTime()->format('d/m/Y H:i:s');
            if (!$sendReplyTo) {
                if ($mailMessage->getHeader(HeaderConsts::REPLY_TO) && $mailMessage->getHeader(HeaderConsts::REPLY_TO)->getRawValue()) {
                    $sendReplyTo = $mailMessage->getHeader(HeaderConsts::REPLY_TO)->getAddresses()[0]->getEmail();
                } else {
                    $sendReplyTo = $mailMessage->getHeader(HeaderConsts::FROM)->getAddresses()[0]->getEmail();
                }
            }
            $html = view('receiptEmail', compact('protocol', 'emaildate'))->render();
            Mail::send([], [], function ($message) use ($sendReplyTo, $html) {
                $message->from(config('mail.from.address'));
                $message->to($sendReplyTo);
                $message->subject("Καταχώριση email στο Ηλεκτρονικό Πρωτόκολλο.");
                $message->setBody($html, 'text/html');
            });
            if (!count(Mail::failures())) {
                // προσθέτω στις παρατηρήσεις ότι στάλθηκε απόδειξη παραλαβής με email
                $emaildate = Carbon::now()->format('d/m/Y H:m:s');
                $parMessage = $protocol->paratiriseis ? $protocol->paratiriseis . ', ' : '';
                $parMessage .= "$emaildate απόδειξη παραλαβής σε $sendReplyTo";
                //$parMessage = mb_strlen($parMessage) > 250 ? mb_substr($parMessage, 0, 250) . ' ...' : $parMessage;
                $protocol->paratiriseis = $parMessage;
                $protocol->update();
                $message .= "<li>Στάλθηκε με email αποδεικτικό καταχώρισης.</li>";
            }
        }

        // μεταφέρω το μήνυμα στα πρωτοκολλημένα
        $defaultImapEmail = Config::getConfigValueOf('defaultImapEmail');
        // σύνδεση στον λογαριασμό imap
        $oClient = Client::account($defaultImapEmail);
        try {
            $oClient->connect();
        } catch (\Throwable $e) {
            report($e);
            $notification = array(
                'message' => "Η σύνδεση με τον λογαριασμό email απέτυχε.<br>Ελέγξτε τις ρυθμίσεις.",
                'alert-type' => 'error'
            );
            session()->flash('notification', $notification);
            return back();
        }
        // αν δεν υπάρχει ο φακελος INBOX.inProtocol τον φτιάχνω
        if (!$oClient->getFolder('INBOX.inProtocol')) {
            $oClient->createFolder('INBOX.inProtocol');
        }
        // παίρνω τον φάκελο INBOX
        $oFolder = $oClient->getFolder('INBOX');
        // το μήνυμα με το uid για αποθήκευση
        $oMessage = $oFolder->query()->getMessageByUid($uid, null, null, true, true, false);
        $oMessage->move('INBOX.inProtocol', true);

        $alertType = 'success';
        if ($numMissedAttachments) {
            $alertType = 'warning';
        }

        $notification = array(
            'message' => "<ul>$message</ul>",
            'alert-type' => $alertType
        );
        session()->flash('notification', $notification);

        if(session()->has('imap_page')) {
            $imap_page = session('imap_page');
            session()->pull('imap_page');
            return redirect('/viewEmails?imap_page=' . $imap_page);
        }
        return redirect('/viewEmails');
    }

    // βρίσκει τις καταχωρίσεις που ταιριάζουν με αυτά που πληκτρολογεί ο χρήστης και επιστρέφει λίστα
    public function getValues($term, $field, $id, $divId, $multi)
    {
        $protocols = Protocol::latest()->take(1000);
        $protocols = $protocols->where($field, 'like', "%" . $term . "%")->groupBy($field)->orderBy(DB::raw("count($field)"), 'DESC')->get($field)->take(10);
        $sortedProtocols = $protocols->sortBy($field);
        if (!$protocols) {
            return;
        }
        $output = '';
        if ($multi) {
            $valuesArray = [];
            foreach ($sortedProtocols->values()->all() as $protocol) {
                $valuesArray = array_merge($valuesArray, preg_split('/\s*,\s*/', $protocol->$field));
            }
            $collection = collect($valuesArray)->unique()->sortBy('Key')->values()->all();
            foreach ($collection as $value) {
                if (mb_stristr($this->removeAccents($value), $this->removeAccents($term))) {
                    $output .= '<li style="cursor: pointer"><a onclick="javascript:appendValue(\'' . $id . '\',\'' . $value . '\',\'' . $divId . '\',\'' . $multi . '\')">' . e($value) . '</a></li>
            ';
                }
            }
        } else {
            foreach ($sortedProtocols->values()->all() as $protocol) {
                $value = $protocol->$field;
                $output .= '<li style="cursor: pointer"><a onclick="javascript:appendValue(\'' . $id . '\',\'' . $value . '\',\'' . $divId . '\',\'' . $multi . '\')">' . e($value) . '</a></li>
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
    public function removeAccents($str)
    {
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ', 'Ά', 'ά', 'Έ', 'έ', 'Ό', 'ό', 'Ώ', 'ώ', 'Ί', 'ί', 'ϊ', 'ΐ', 'Ύ', 'ύ', 'ϋ', 'ΰ', 'Ή', 'ή');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', 'Α', 'α', 'Ε', 'ε', 'Ο', 'ο', 'Ω', 'ω', 'Ι', 'ι', 'ι', 'ι', 'Υ', 'υ', 'υ', 'υ', 'Η', 'η');
        return str_replace($a, $b, $str);
    }

    public function filter_filename($filename, $beautify = true)
    {
        // sanitize filename
        $filename = preg_replace(
            '~
        [<>:"/\\|?*]|            # file system reserved https://en.wikipedia.org/wiki/Filename#Reserved_characters_and_words
        [\x00-\x1F]|             # control characters http://msdn.microsoft.com/en-us/library/windows/desktop/aa365247%28v=vs.85%29.aspx
        [\x7F\xA0\xAD]|          # non-printing characters DEL, NO-BREAK SPACE, SOFT HYPHEN
        [#\[\]@!$&\'()+,;=]|     # URI reserved https://tools.ietf.org/html/rfc3986#section-2.2
        [{}^\~`]                 # URL unsafe characters https://www.ietf.org/rfc/rfc1738.txt
        ~ux',
            '-',
            $filename
        );
        // avoids ".", ".." or ".hiddenFiles"
        $filename = ltrim($filename, '.-');
        // optional beautification
        if ($beautify) {
            $filename = $this->beautify_filename($filename);
        }
        // maximize filename length to 255 bytes http://serverfault.com/a/9548/44086
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        //$filename = mb_strcut(pathinfo($filename, PATHINFO_FILENAME), 0, 255 - ($ext ? strlen($ext) + 1 : 0), mb_detect_encoding($filename)) . ($ext ? '.' . $ext : '');
        //εναλλακτική λύση αν το παραπάνω δεν δουλεύει όπως πρέπει (συναντήσαμε πρόβλημα στα ubuntu)
        if (mb_strlen(pathinfo($filename, PATHINFO_FILENAME)) > 120) $filename =  mb_substr(pathinfo($filename, PATHINFO_FILENAME), 0, 120) . '.' . $ext;
        return $filename;
    }

    public function beautify_filename($filename)
    {
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

    public function checkInNum(Request $request)
    {
        $in_date = Carbon::createFromFormat('d/m/Y', $request->in_date)->format('Ymd');
        $protocols = Protocol::where('in_date', $in_date)->where('in_num', $request->in_num);
        if ($request->id) {
            $protocols =  $protocols->where('id', '!=', $request->id);
        }
        $count =  $protocols->count();
        return response()->json($count);
    }

    public function checkSameEmail(Request $request)
    {
        $count = [];
        $in_date = Carbon::createFromFormat('d/m/Y', $request->in_date)->format('Ymd');
        $count['thema'] =  Protocol::where('thema', $request->thema)->count();
        $count['in_num'] =  Protocol::where('in_date', $in_date)->where('in_num', $request->in_num)->count();
        return response()->json($count);
    }

    public function anathesiSe(Request $request)
    {
        $sendEmailTo = $request->diekperaiosi;
        $protocol = Protocol::whereId($request->id)->first();
        $protocol->diekperaiosi = $sendEmailTo;
        $protocol->diekp_date = null;
        $protocol->update();

        // στέλνω mail στον Διεκπεραιωτή
        if ($sendEmailTo) {
            $sendEmailToArray = explode(',',$sendEmailTo);
            $message = '';
            foreach($sendEmailToArray as $smt){
                if( substr($smt,0,1) == 'd'){
                    $message .= $this->sendMailToDiekperaioti(ltrim($smt, 'd'), $protocol);
                }
                if (substr($smt, 0, 1) == 'e') {
                    $message .= $this->diekpInformMail(ltrim($smt, 'e'), $protocol);
                }
            }
            $notification = array(
                'message' =>  "<ul>$message</ul>",
                'alert-type' => 'success'
            );
            session()->flash('notification', $notification);

        }
        return response()->json($message);
    }

    public static function limitProtocolAccessList(){
        $limitProtocolAccessList = 0;
        if (Config::getConfigValueOf('limitProtocolAccessList') && in_array(Auth::user()->role_description(), ["Συγγραφέας",  "Αναγνώστης"])) {
            $limitProtocolAccessList = 1;
        }
        return $limitProtocolAccessList;
    }

    public function readSavedEmailFromFile($messageUid){
        // αν δεν υπάρχει το αρχείο email σταματάω
        if(! file_exists('tmp/' . $messageUid . '.eml')) return null;
        // διαβάζω τα περιεχόμενα του email
        $mailParser = new MailMimeParser();
        $handle = fopen('tmp/' . $messageUid . '.eml' , 'r');
        $mailMessage = $mailParser->parse($handle);         // returns `Message`
        fclose($handle);
        return $mailMessage;
      
    }

}
