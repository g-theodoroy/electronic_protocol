@extends('layouts.app')

@section('content')

<style>
.hideoverflow{
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>

<div class="container">
    <div class="row">
        <div class="col-md-8 col-sm-8 col-md-offset-2 col-sm-offset-2">
           <div class="panel panel-default">
                <div class="panel-heading h1 text-center">Ρυθμίσεις Εφαρμογής</div>

                <div class="panel-body ">
                    <!-- ________________________________form______________________________________________________ -->
                    <form id='configform' name="configform" class="form-horizontal" role="form" method="POST" action="{{ url('/config') }}" >
                        {{ csrf_field() }}
                    <div class="panel panel-default col-md-12 col-sm-12  ">
                        <div class="row bg-info">
                            <div class="form-control-static h4 text-center">Στοιχεια εφαρμογής</div>
                        </div>
                        <div class="row">
                            <div class="form-control-static col-md-7 col-sm-7  col-md-offset-1 col-sm-offset-1" >
                                Όνομα υπηρεσίας - σχολείου
                            </div>
                            <div class="col-md-4 col-sm-4  " id="ipiresiasNamediv">
                                <input id="ipiresiasName" type="text" class="form-control text-center" name="ipiresiasName" placeholder="ipiresiasName" value="{{$configs['ipiresiasName']}}" title=''>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-control-static col-md-8 col-sm-8  col-md-offset-1 col-sm-offset-1" >
                                Ενεργό έτος πρωτοκόλλησης
                            </div>
                            <div class="col-md-2 col-sm-2  " id="yearInUsediv">
                                <input id="yearInUse" type="text" class="form-control text-center" name="yearInUse" placeholder="yearInUse" value="{{$configs['yearInUse']}}" title='Αφήστε κενό για να συνεχίζεται η αρίθμηση διαχρονικά'>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-control-static col-md-8 col-sm-8  col-md-offset-1 col-sm-offset-1" >
                                Αρ.Πρωτοκόλλου για ξεκίνημα
                            </div>
                            <div class="col-md-2 col-sm-2  " id="firstProtocolNumdiv">
                                <input id="firstProtocolNum" type="text" class="form-control text-center" name="firstProtocolNum" placeholder="firstProtocolNum" value="{{$configs['firstProtocolNum']}}" title=''>
                            </div>
                        </div>
                    </div>

                <div class="panel panel-default col-md-12 col-sm-12  ">
                        <div class="row bg-warning">
                            <div class="form-control-static h4 text-center">Ρυθμίσεις Εμφάνισης</div>
                        </div>
                      <div class="row">
                            <div class="form-control-static col-md-8 col-sm-8  col-md-offset-1 col-sm-offset-1  " >
                                Ξεκίνα νέα σελίδα πάνω από αριθμό γραμμών
                            </div>
                            <div class="col-md-2 col-sm-2  " id="showRowsInPagediv">
                                <input id="showRowsInPage" type="text" class="form-control text-center" name="showRowsInPage" placeholder="showRowsInPage" value="{{$configs['showRowsInPage']}}" title=''>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-control-static col-md-8 col-sm-8  col-md-offset-1 col-sm-offset-1" >
                                Ανανέωση Πρωτοκόλλου κάθε τόσα λεπτά
                            </div>
                            <div class="col-md-2 col-sm-2  " id="minutesRefreshIntervaldiv">
                                <input id="minutesRefreshInterval" type="text" class="form-control text-center" name="minutesRefreshInterval" placeholder="minutesRefreshInterval" value="{{$configs['minutesRefreshInterval']}}" title=''>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-control-static col-md-8 col-sm-8  col-md-offset-1 col-sm-offset-1" >
                                Χρώμα επικεφαλίδας
                            </div>
                            <div class="col-md-2 col-sm-2  " id="titleColordiv">
                                <input id="titleColor" type="text" class="form-control text-center" name="titleColor" placeholder="titleColor" value="{{$configs['titleColor']}}" title=''>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-control-static col-md-8 col-sm-8  col-md-offset-1 col-sm-offset-1 ">
                                Η  Λίστα Πρωτοκόλλου απλώνεται σε όλο τον διαθέσιμο χώρο
                            </div>
                            <div class="col-md-2 col-sm-2  " id="wideListProtocoldiv">
                                <select id='wideListProtocol' name='wideListProtocol' class="form-control"  title=''>
                                @if ($configs['wideListProtocol'] )
                                <option value="0"  >ΟΧΙ</option>
                                <option value="1" selected >ΝΑΙ</option>
                                @else
                                <option value="0" selected >ΟΧΙ</option>
                                <option value="1"  >ΝΑΙ</option>
                                @endif
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-control-static col-md-8 col-sm-8  col-md-offset-1 col-sm-offset-1 ">
                                Εμφάνιση πληροφοριών Χρηστών
                            </div>
                            <div class="col-md-2 col-sm-2  " id="showUserInfodiv">
                                <select id='showUserInfo' name='showUserInfo' class="form-control"  title=''>
                                @if ( ! $configs['showUserInfo'] )
                                <option value="0" selected >ΟΧΙ</option>
                                <option value="1" >username</option>
                                <option value="2" >Όνομα</option>
                                @elseif($configs['showUserInfo'] == 1)
                                <option value="0" >ΟΧΙ</option>
                                <option value="1" selected >username</option>
                                <option value="2" >Όνομα</option>
                                @else
                                <option value="0" >ΟΧΙ</option>
                                <option value="1" >username</option>
                                <option value="2" selected >Όνομα</option>
                                @endif
                                </select>
                            </div>
                        </div>
                    </div>

                <div class="panel panel-default col-md-12 col-sm-12  ">
                        <div class="row bg-success">
                            <div class="form-control-static h4 text-center">Ρυθμίσεις Αναζήτησης</div>
                        </div>
                        <div class="row">
                            <div class="form-control-static col-md-6 col-sm-6  col-md-offset-2 col-sm-offset-2" >
                                1ο πεδίο αναζήτησης
                            </div>
                            <div class="col-md-4 col-sm-4  " id="searchField1div">
                                <select id='searchField1' name='searchField1' class="form-control"  title=''>
                                @foreach($fields as $key => $value)
                                @if ($key == $configs['searchField1'])
                                <option value="{{$key}}" selected >{{$value}}</option>
                                @else
                                <option value="{{$key}}" >{{$value}}</option>
                                @endif
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-control-static col-md-6 col-sm-6  col-md-offset-2 col-sm-offset-2" >
                                2ο πεδίο αναζήτησης
                            </div>
                            <div class="col-md-4 col-sm-4  " id="searchField2div">
                                <select id='searchField2' name='searchField2' class="form-control"  title=''>
                                @foreach($fields as $key => $value)
                                @if ($key == $configs['searchField2'])
                                <option value="{{$key}}" selected >{{$value}}</option>
                                @else
                                <option value="{{$key}}" >{{$value}}</option>
                                @endif
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-control-static col-md-6 col-sm-6  col-md-offset-2 col-sm-offset-2" >
                                3ο πεδίο αναζήτησης
                            </div>
                            <div class="col-md-4 col-sm-4  " id="searchField3div">
                                <select id='searchField3' name='searchField3' class="form-control"  title=''>
                                @foreach($fields as $key => $value)
                                @if ($key == $configs['searchField3'])
                                <option value="{{$key}}" selected >{{$value}}</option>
                                @else
                                <option value="{{$key}}" >{{$value}}</option>
                                @endif
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-control-static col-md-8 col-sm-8  col-md-offset-1 col-sm-offset-1  " >
                                Βήμα μετακίνησης με το κλικ των κουμπιών <img src="{{ URL::to('/') }}/images/arrow-left-double.png" height=20 / > και <img src="{{ URL::to('/') }}/images/arrow-right-double.png" height=20 / >
                            </div>
                            <div class="col-md-2 col-sm-2  " id="protocolArrowStepdiv">
                                <input id="protocolArrowStep" type="text" class="form-control text-center" name="protocolArrowStep" placeholder="protocolArrowStep" value="{{$configs['protocolArrowStep']}}" title=''>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-control-static col-md-8 col-sm-8  col-md-offset-1 col-sm-offset-1  " >
                                Μέγιστος αριθμός γραμμών που επιστρέφει η αναζήτηση
                            </div>
                            <div class="col-md-2 col-sm-2  " id="maxRowsInFindPagediv">
                                <input id="maxRowsInFindPage" type="text" class="form-control text-center" name="maxRowsInFindPage" placeholder="maxRowsInFindPage" value="{{$configs['maxRowsInFindPage']}}" title=''>
                            </div>
                        </div>
                    </div>

                <div class="panel panel-default col-md-12 col-sm-12  ">
                        <div class="row bg-danger">
                            <div class="form-control-static h4 text-center">Δικαιώματα χρηστών</div>
                        </div>
                        <div class="row">
                            <div class="form-control-static col-md-8 col-sm-8  col-md-offset-1 col-sm-offset-1 ">
                                Έλεγχοι & περιορισμοί κατά την καταχώριση
                            </div>
                            <div class="col-md-2 col-sm-2  " id="protocolValidatediv">
                                <select id='protocolValidate' name='protocolValidate' class="form-control"  title=''>
                                @if ($configs['protocolValidate'] )
                                <option value="0"  >ΟΧΙ</option>
                                <option value="1" selected >ΝΑΙ</option>
                                @else
                                <option value="0" selected >ΟΧΙ</option>
                                <option value="1"  >ΝΑΙ</option>
                                @endif
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-control-static col-md-8 col-sm-8   col-md-offset-1 col-sm-offset-1" >
                                Ασφαλής, όχι διπλότυπος Νέος Αρ.Πρωτοκόλλου
                            </div>
                            <div class="col-md-2 col-sm-2  " id="safeNewProtocolNumdiv">
                                <select id='safeNewProtocolNum' name='safeNewProtocolNum' class="form-control"  title=''>
                                @if ($configs['safeNewProtocolNum'])
                                <option value="0"  >ΟΧΙ</option>
                                <option value="1" selected >ΝΑΙ</option>
                                @else
                                <option value="0" selected >ΟΧΙ</option>
                                <option value="1"  >ΝΑΙ</option>
                                @endif
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-control-static col-md-8 col-sm-8   col-md-offset-1 col-sm-offset-1" >
                                Έλεύθερη επιλογή χρόνου διατήρησης αρχείων
                            </div>
                            <div class="col-md-2 col-sm-2  " id="allowUserChangeKeepSelectdiv">
                                <select id='allowUserChangeKeepSelect' name='allowUserChangeKeepSelect' class="form-control"  title=''>
                                @if ($configs['allowUserChangeKeepSelect'])
                                <option value="0"  >ΟΧΙ</option>
                                <option value="1" selected >ΝΑΙ</option>
                                @else
                                <option value="0" selected >ΟΧΙ</option>
                                <option value="1"  >ΝΑΙ</option>
                                @endif
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-control-static col-md-8 col-sm-8   col-md-offset-1 col-sm-offset-1" >
                                Δυνατότητα Επεξεργασίας Πρωτοκόλλου από Συγγραφέα
                            </div>
                            <div class="col-md-2 col-sm-2  " id="allowWriterUpdateProtocoldiv">
                                <select id='allowWriterUpdateProtocol' name='allowWriterUpdateProtocol' class="form-control"  title=''>
                                @if( ! $configs['allowWriterUpdateProtocol'])
                                <option value="0" selected >ΚΑΝΕΙΣ</option>
                                <option value="1" >ΕΝΑΣ</option>
                                <option value="2" >ΟΛΟΙ</option>
                                @elseif ($configs['allowWriterUpdateProtocol'] == 1)
                                <option value="0" >ΚΑΝΕΙΣ</option>
                                <option value="1" selected >ΕΝΑΣ</option>
                                <option value="2" >ΟΛΟΙ</option>
                                @else
                                <option value="0" >ΚΑΝΕΙΣ</option>
                                <option value="1" >ΕΝΑΣ</option>
                                <option value="2" selected >ΟΛΟΙ</option>
                                @endif
                                </select>
                            </div>
                          </div>
                          <div class="row">
                                <div class="form-control-static col-md-8 col-sm-8  col-md-offset-1 col-sm-offset-1  " >
                                    Χρόνος σε λεπτά δυνατότητας επεξεργασίας Πρωτ. από Συγγραφέα
                                </div>
                                <div class="col-md-2 col-sm-2  " id="allowWriterUpdateProtocolTimeInMinutesdiv">
                                    <input id="allowWriterUpdateProtocolTimeInMinutes" type="text" class="form-control text-center" name="allowWriterUpdateProtocolTimeInMinutes" placeholder="allowWriterUpdateProtocolTimeInMinutes" value="{{$configs['allowWriterUpdateProtocolTimeInMinutes']}}" title=''>
                                </div>
                          </div>
                    </div>

                <div class="panel panel-default col-md-12 col-sm-12  ">
                        <div class="row bg-info">
                            <div class="form-control-static h4 text-center">Έλεγχος για ενημερώσεις</div>
                        </div>
                        <div class="row">
                            <div class="form-control-static col-md-8 col-sm-8   col-md-offset-1 col-sm-offset-1" >
                                Να γίνεται έλεγχος για ενημερώσεις
                            </div>
                            <div class="col-md-2 col-sm-2  " id="updatesAutoCheckdiv">
                                <select id='updatesAutoCheck' name='updatesAutoCheck' class="form-control"  title=''>
                                @if ($configs['updatesAutoCheck'])
                                <option value="0"  >ΟΧΙ</option>
                                <option value="1" selected >ΝΑΙ</option>
                                @else
                                <option value="0" selected >ΟΧΙ</option>
                                <option value="1"  >ΝΑΙ</option>
                                @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-default col-md-12 col-sm-12  ">
                             <div class="row bg-warning">
                                 <div class="form-control-static h4 text-center">Ρυθμίσεις συνημμένων αρχείων</div>
                             </div>
                             <div class="row">
                                 <div class="form-control-static col-md-7 col-sm-7  col-md-offset-1 col-sm-offset-1" >
                                     URL Διαύγειας
                                 </div>
                                 <div class="col-md-4 col-sm-4  " id="diavgeiaUrldiv">
                                     <input id="diavgeiaUrl" type="text" class="form-control text-center" name="diavgeiaUrl" placeholder="diavgeiaUrl" value="{{$configs['diavgeiaUrl']}}" title=''>
                                 </div>
                             </div>
                         </div>

		@if (env('DB_CONNECTION') !== 'sqlite')
               <div class="panel panel-default col-md-12 col-sm-12  ">
                        <div class="row bg-success">
                            <div class="form-control-static h4 text-center">Ρυθμίσεις αντιγράφων ασφαλείας</div>
                        </div>
                        <div class="row">
                            <div class="form-control-static col-md-7 col-sm-7  col-md-offset-1 col-sm-offset-1" >
                                Εκτελέσιμο αρχείο της mysqldump στον server
                            </div>
                            <div class="col-md-4 col-sm-4  " id="ipiresiasNamediv">
                                <input id="mysqldumpPath" type="text" class="form-control text-center" name="mysqldumpPath" placeholder="mysqldumpPath" value="{{$configs['mysqldumpPath']}}" title=''>
                            </div>
                        </div>
                    </div>
		@endif

                    <div class="row">
                        <div class="col-md-2 col-sm-2 col-md-offset-4 col-sm-offset-4 text-center">
                            <a href="#" onclick="document.forms['configform'].reset();"  role="button" title="Καθαρισμός" > <img src="{{ URL::to('/') }}/images/clear.ico" height="30" /></a>
                        </div>
                        <div class="col-md-2 col-sm-2 text-center ">
                            <a href="#"  onclick="document.forms['configform'].submit();" role="button" title="Αποθήκευση" > <img src="{{ URL::to('/') }}/images/save.ico" height="30" /></a>
                        </div>
                        <div class="col-md-2 col-sm-2 col-md-offset-2 col-sm-offset-2 text-center ">
                            <a href="{{ URL::to('/home/list') }}"  class="" role="button" title="Πρωτόκολλο" > <img src="{{ URL::to('/') }}/images/protocol.png" height="30" /></a>
                        </div>
                    </div>


                </form> <!-- ________________________________end form______________________________________________________ -->

                    <div id='showFindData' ></div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>

    toastr.options = {
      "closeButton": true,
      "debug": false,
      "newestOnTop": false,
      "progressBar": false,
      "positionClass": "toast-top-center",
      "preventDuplicates": false,
      "onclick": null,
      "showDuration": "300",
      "hideDuration": "1000",
      "timeOut": "5000",
      "extendedTimeOut": "1000",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"
    }

function chkNum (id ,notnull){
    var value = $('#' + id ).val()
    if(notnull){
        if (! value){
            toastr.error("<center><h4>Ενημέρωση...</h4><hr>Πληκτρολογείστε Αριθμό Πρωτοκόλλου.<br>&nbsp;</center>")
            $('#' + id + 'div').addClass ('has-error')
            return false;
        }else{
            $('#' + id + 'div').removeClass ('has-error')
        }
    }
    if(value){
        var myRe = /^\d+$/g;

        if (! myRe.test(value)){
            toastr.error("<center><h4>Λάθος !!!</h4><hr>Πληκτρολογείστε μόνο Αριθμούς.<br>&nbsp;</center>")
            $('#' + id + 'div').addClass ('has-error')
            return false;
        }
            $('#' + id + 'div').removeClass ('has-error')
        }
        return true;
}

function chkEtos (id ,notnull){
    var value = $('#' + id ).val()
    if(notnull){
        if (! value){
            toastr.error("<center><h4>Ενημέρωση...</h4><hr>Πληκτρολογείστε Έτος.<br>&nbsp;</center>")
            $('#' + id + 'div').addClass ('has-error')
            return false;
        }else{
            $('#' + id + 'div').removeClass ('has-error')
        }
    }
    if(value){
        var myRe = /^\d{4}$/g ;

        if (! myRe.test(value)){
            toastr.error("<center><h4>Λάθος !!!</h4><hr>Το έτος πρέπει να έιναι τετραψήφιος αριθμός με μορφή ''εεεε''.<br>&nbsp;</center>")
            $('#' + id + 'div').addClass ('has-error')
            return false;
        }else{
            $('#' + id + 'div').removeClass ('has-error')
        }
    }
    return true;
}

function chkDate (id ,notnull){
    var value = $('#' + id ).val()
    if(notnull){
        if (! value){
            toastr.error("<center><h4>Ενημέρωση...</h4><hr>Πληκτρολογείστε Έτος.<br>&nbsp;</center>")
            $('#' + id + 'div').addClass ('has-error')
            return false;
        }else{
            $('#' + id + 'div').removeClass ('has-error')
        }
    }
    if(value){
        var myRe = /\d{2}\/\d{2}\/\d{4}/g ;

        if (! myRe.test(value)){
            toastr.error("<center><h4>Λάθος !!!</h4><hr>η Ημερομηνία πρέπει να έιναι έχει τη μορφή ''ηη/μμ/εεεε''.<br>&nbsp;</center>")
            $('#' + id + 'div').addClass ('has-error')
            return false;
        }else{
            $('#' + id + 'div').removeClass ('has-error')
        }
    }
    return true;
}


function getPrintData() {
    var chk_ok = 1
    var aponum = chkNum('aponum',false)
    if(!aponum){
        chk_ok=0
    }
    var eosnum = chkNum('eosnum',false)
    if(!eosnum){
        chk_ok=0
    }
    var etosForMany = chkEtos('etosForMany',false)
    if(!etosForMany){
        chk_ok=0
    }
    var apoProtocolDate = chkDate('apoProtocolDate', false)
    if(! apoProtocolDate){
        chk_ok=0
    }
    var eosProtocolDate = chkDate('eosProtocolDate', false)
    if(! eosProtocolDate){
        chk_ok=0
    }
    if(chk_ok == 0){
        return false
    }
    $("#printform").submit()
    return true
}


</script>

@endsection
