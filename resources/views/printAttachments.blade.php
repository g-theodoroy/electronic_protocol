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
        <div class="col-md-6 col-sm-6 col-md-offset-3 col-sm-offset-3">
           <div class="panel panel-default">
                <div class="panel-heading h1 text-center" {!!$titleColorStyle!!}>Εκτύπωση Συνημμένων</div>

                <div class="panel-body ">
                    <!-- ________________________________form______________________________________________________ -->
                    <form id='printform' name="printform" class="form-horizontal" role="form" method="POST" action="{{ url('/printedAttachments') }}" target='_blank'>
                        {{ csrf_field() }}
                    <div class="panel panel-default col-md-12 col-sm-12  ">
                        <div class="row bg-info">
                            <div class="form-control-static h4 text-center">Ο Αριθμός Πρωτοκόλλου από - έως - έτος</div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 col-sm-3  col-md-offset-1 col-sm-offset-1" id="aponumdiv">
                                <input id="aponum" type="text" class="form-control text-center" name="aponum" placeholder="από" value="" title='Από αριθμό πρωτοκόλλου. Κενό = από τον πρώτο.'>
                            </div>
                            <div class="col-md-3 col-sm-3  " id="eosnumdiv">
                                <input id="eosnum" type="text" class="form-control text-center" name="eosnum" placeholder="έως" value="" title='Έως αριθμό πρωτοκόλλου. Κενό = έως τον τελευταίο.'>
                            </div>
                             <div class="col-md-4 col-sm-4  " id="etosForManydiv">
                                <input id="etosForMany" type="text" class="form-control text-center" name="etosForMany" placeholder="έτος" value="" title='Για το έτος'>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-default col-md-12 col-sm-12  ">

                        <div class="row row bg-success">
                            <div class="form-control-static h4 text-center">Ημερομηνία παραλαβής από - έως</div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 col-sm-4  col-md-offset-2 col-sm-offset-2" id="apoProtocolDatediv">
                                <input id="apoProtocolDate" type="text" class="form-control datepicker text-center" name="apoProtocolDate" placeholder="από" value="" title='Από ημ/νία παραλαβής'>
                            </div>
                            <div class="col-md-4 col-sm-4  " id="eosProtocolDatediv">
                                <input id="eosProtocolDate" type="text" class="form-control datepicker text-center" name="eosProtocolDate" placeholder="έως" value="" title='Έως ημ/νία παραλαβής'>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-2 col-md-offset-4 col-sm-offset-4 text-center">
                        <a href="" onclick="document.forms['printform'].reset();"  role="button" title="Καθαρισμός" > <img src="{{ URL::to('/') }}/images/clear.ico" height="30" /></a>
                    </div>
                    <div class="col-md-2 col-sm-2 text-center ">
                        <a href="#" onclick="getPrintData()"  class="" role="button" title="Εκτύπωση" > <img src="{{ URL::to('/') }}/images/print.png" height="30" /></a>
                    </div>
                    <div class="col-md-2 col-sm-2 col-md-offset-2 col-sm-offset-2 text-right ">
                        <a href="{{ URL::to('/home/list') }}"  class="" role="button" title="Πρωτόκολλο" > <img src="{{ URL::to('/') }}/images/protocol.png" height="30" /></a>
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

    var chk_novalue = $("#aponum").val() +  $("#eosnum").val() +  $("#etosForMany").val() +  $("#apoProtocolDate").val() +  $("#eosProtocolDate").val()
    if(! chk_novalue) return false;

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
