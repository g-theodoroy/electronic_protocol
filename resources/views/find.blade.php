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
        <div class="col-md-12 col-sm-12 ">
           <div class="panel panel-default">
                <div class="panel-heading h1 text-center" {!!$titleColorStyle!!}>Αναζήτηση Πρωτοκόλλου</div>

                <div class="panel-body ">

                    <div class="panel panel-default col-md-6 col-sm-6  ">
                        <div class="row bg-info">
                            <div class="form-control-static h4 text-center">Ο Αριθμός Πρωτοκόλλου να είναι</div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 col-sm-2  form-control-static ">Αριθμός</div>
                            <div class="col-md-3 col-sm-3" id='protocolnumdiv'>
                                <input id="protocolnum" type="text" class="form-control text-center" name="protocolnum" placeholder="Αρ. Πρωτ." value="" title='Αριθμός πρωτοκόλλου'>
                            </div>
                            <div class="col-md-2 col-sm-2  form-control-static ">Έτος</div>
                            <div class="col-md-3 col-sm-3  " id='etosForOnediv'>
                                <input id="etosForOne" type="text" class="form-control text-center" name="etosForOne" placeholder="Έτος" value="" title='Έτος'>
                            </div>
                            <div class="col-md-2 col-sm-2  form-control-static text-center">
                                <a href="#" onclick="chkFindOne()"  class="" role="button" title="Αναζήτηση" > <img src="{{ URL::to('/') }}/images/find.ico" height="20" /></a>
                            </div>
                        </div>
                    </div>
                    <form id='findform' > <!-- ________________________________form______________________________________________________ -->
                    <div class="panel panel-default col-md-6 col-sm-6  ">
                        <div class="row bg-info">
                            <div class="form-control-static h4 text-center">Ο Αριθμός Πρωτοκόλλου από - έως - έτος</div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 col-sm-3  col-md-offset-1 col-sm-offset-1" id="aponumdiv">
                                <input id="aponum" type="text" class="form-control text-center" name="aponum" placeholder="από" value="" title='Από αριθμό πρωτοκόλλου'>
                            </div>
                            <div class="col-md-3 col-sm-3  " id="eosnumdiv">
                                <input id="eosnum" type="text" class="form-control text-center" name="eosnum" placeholder="έως" value="" title='Έως αριθμό πρωτοκόλλου'>
                            </div>
                             <div class="col-md-3 col-sm-3  " id="etosForManydiv">
                                <input id="etosForMany" type="text" class="form-control text-center" name="etosForMany" placeholder="έτος" value="" title='Για το έτος'>
                            </div>
                            <div class="col-md-2 col-sm-2  form-control-static text-center">
                                <a href="#" onclick="getFindData()"  class="" role="button" title="Αναζήτηση" > <img src="{{ URL::to('/') }}/images/find.ico" height="20" /></a>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-default col-md-6 col-sm-6  ">

                        <div class="row row bg-success">
                            <div class="form-control-static h4 text-center">Ημερομηνίες από - έως</div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 col-sm-4 form-control-static hideoverflow">Ημ/νία παραλαβής</div>
                            <div class="col-md-3 col-sm-3  " id="apoProtocolDatediv">
                                <input id="apoProtocolDate" type="text" class="form-control datepicker text-center" name="apoProtocolDate" placeholder="από" value="" title='Από ημ/νία παραλαβής'>
                            </div>
                            <div class="col-md-3 col-sm-3  " id="eosProtocolDatediv">
                                <input id="eosProtocolDate" type="text" class="form-control datepicker text-center" name="eosProtocolDate" placeholder="έως" value="" title='Έως ημ/νία παραλαβής'>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 col-sm-4 form-control-static hideoverflow">Ημνία εισερχομένου</div>
                            <div class="col-md-3 col-sm-3  " id="apoEiserxDatediv">
                                <input id="apoEiserxDate" type="text" class="form-control datepicker text-center" name="apoEiserxDate" placeholder="από" value="" title='Από ημ/νία εισερχομένου'>
                            </div>
                            <div class="col-md-3 col-sm-3  " id="eosEiserxDatediv">
                                <input id="eosEiserxDate" type="text" class="form-control datepicker text-center" name="eosEiserxDate" placeholder="έως" value="" title='Έως ημ/νία εισερχομένου'>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 col-sm-4 form-control-static hideoverflow">Ημνία εξερχομένου</div>
                            <div class="col-md-3 col-sm-3  " id="apoExerxDatediv">
                                <input id="apoExerxDate" type="text" class="form-control datepicker text-center" name="apoExerxDate" placeholder="από" value="" title='Από ημ/νία εξερχομένου'>
                            </div>
                            <div class="col-md-3 col-sm-3  " id="eosExerxDatediv">
                                <input id="eosExerxDate" type="text" class="form-control datepicker text-center" name="eosExerxDate" placeholder="έως" value="" title='Έως ημ/νία εξερχομένου'>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-default col-md-6 col-sm-6  ">

                        <div class="row row bg-success">
                            <div class="form-control-static h4 text-center">Τα πεδία να περιλαμβάνουν</div>
                        </div>

                        <div class="row">
                            <div class="col-md-5 col-sm-5  ">
                                <select id='searchField1' onchange="getFindData()"  name='searchField1' class="form-control selectpicker"  title="Αναζήτηση στο πεδίο {{$fields[$searchField1]}}">
                                @foreach($fields as $key => $value)
                                @if ($key == $searchField1)
                                <option value="{{$key}}" selected >{{$value}}</option>
                                @else
                                <option value="{{$key}}" >{{$value}}</option>
                                @endif
                                @endforeach
                                </select>
                            </div>
                            <div class="col-md-5 col-sm-5">
                                <input id="searchData1" oninput="getFindData()"  type="text" class="form-control " name="searchData1" placeholder="Κείμενο προς αναζήτηση" value="" title='Κείμενο προς αναζήτηση'>
                            </div>
                            <div id="searchData1chkDiv" class="checkbox-inline col-md-2 col-sm-2" title='Επιλέξτε για αναζήτηση κενών (Null) τιμών στο πεδίο {{$searchField1}}' >
                                <label><input id="searchData1chk" onchange="getFindData()"  type="checkbox" name="searchData1chk" value="1" >Κενό</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5 col-sm-5  ">
                                <select id='searchField2' onchange="getFindData()"  name='searchField2' class="form-control selectpicker"  title="Αναζήτηση στο πεδίο {{$fields[$searchField2]}}">
                                @foreach($fields as $key => $value)
                                @if ($key == $searchField2)
                                <option value="{{$key}}" selected >{{$value}}</option>
                                @else
                                <option value="{{$key}}" >{{$value}}</option>
                                @endif
                                @endforeach
                                </select>
                            </div>
                            <div class="col-md-5 col-sm-5">
                                <input id="searchData2" oninput="getFindData()"  type="text" class="form-control" name="searchData2" placeholder="Κείμενο προς αναζήτηση" value="" title='Κείμενο προς αναζήτηση'>
                            </div>
                            <div id="searchData2chkDiv" class="checkbox-inline col-md-2 col-sm-2" title='Επιλέξτε για αναζήτηση κενών (Null) τιμών στο πεδίο {{$searchField2}}' >
                                <label><input id="searchData2chk" onchange="getFindData()"  type="checkbox" name="searchData2chk" value="1" >Κενό</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5 col-sm-5  ">
                                <select id='searchField3' onchange="getFindData()"  name='searchField3' class="form-control selectpicker"  title="Αναζήτηση στο πεδίο {{$fields[$searchField3]}}"  >
                                @foreach($fields as $key => $value)
                                @if ($key == $searchField3)
                                <option value="{{$key}}" selected >{{$value}}</option>
                                @else
                                <option value="{{$key}}" >{{$value}}</option>
                                @endif
                                @endforeach
                                </select>
                            </div>
                            <div class="col-md-5 col-sm-5">
                                <input id="searchData3" oninput="getFindData()"  type="text" class="form-control " name="searchData3" placeholder="Κείμενο προς αναζήτηση" value="" title='Κείμενο προς αναζήτηση'>
                            </div>
                            <div id="searchData3chkDiv" class="checkbox-inline col-md-2 col-sm-2" title='Επιλέξτε για αναζήτηση κενών (Null) τιμών στο πεδίο {{$searchField3}}' >
                                <label><input id="searchData3chk" onchange="getFindData()"  type="checkbox" name="searchData3chk" value="1" >Κενό</label>
                            </div>
                        </div>
                    </div>
                        <div class="row">
                             <div class="col-md-2 col-sm-2 col-md-offset-4 col-sm-offset-4 form-control-static text-center">
                                <a href="" onclick="$('#showFindData').html("");document.forms['findform'].reset();"  role="button" title="Καθάρισμα φόρμας" > <img src="{{ URL::to('/') }}/images/clear.ico" height="30" /></a>
                            </div>
                             <div class="col-md-2 col-sm-2 form-control-static text-center">
                                 <a href="#" onclick="getFindData()"  class="" role="button" title="Αναζήτηση" > <img src="{{ URL::to('/') }}/images/find.ico" height="30" /></a>
                            </div>
                             <div class="col-md-1 col-sm-1 col-md-offset-3 col-sm-offset-3 form-control-static text-right">
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
function chkNum (id ,notnull){
    var value = $('#' + id ).val()
    if(notnull){
        if (! value){
            toastr.info("<center><h4>Ενημέρωση...</h4><hr>Πληκτρολογείστε Αριθμό Πρωτοκόλλου.<br>&nbsp;</center>")
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
            toastr.info("<center><h4>Ενημέρωση...</h4><hr>Πληκτρολογείστε Έτος.<br>&nbsp;</center>")
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
            toastr.info("<center><h4>Ενημέρωση...</h4><hr>Πληκτρολογείστε Έτος.<br>&nbsp;</center>")
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

function chkFindOne(){

    var chkprotocolnum = chkNum('protocolnum',true)
    if(!chkprotocolnum)return false;
    var chketos = chkEtos('etosForOne',true)
    if(!chketos)return false;
    protocolnum = $('#protocolnum').val()
    etosForOne = $('#etosForOne').val()
    $(location).attr('href', "{{ URL::to('/') }}" + "/goto/" + etosForOne + "/" + protocolnum + "?find=1")
    return true;
}

function getFindData( page=1) {
    $('#searchField1').attr('title', 'Αναζήτηση στο πεδίο ' + $('#searchField1 :selected').text())
    $('#searchField2').attr('title', 'Αναζήτηση στο πεδίο ' + $('#searchField2 :selected').text())
    $('#searchField3').attr('title', 'Αναζήτηση στο πεδίο ' + $('#searchField3 :selected').text())
    $('#searchData1chkDiv').attr('title', 'Επιλέξτε για αναζήτηση κενών (Null) τιμών στο πεδίο ' + $('#searchField1 :selected').text())
    $('#searchData2chkDiv').attr('title', 'Επιλέξτε για αναζήτηση κενών (Null) τιμών στο πεδίο ' + $('#searchField2 :selected').text())
    $('#searchData3chkDiv').attr('title', 'Επιλέξτε για αναζήτηση κενών (Null) τιμών στο πεδίο ' + $('#searchField3 :selected').text())

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
    var apoEiserxDate = chkDate('apoEiserxDate', false)
    if(! apoEiserxDate){
        chk_ok=0
    }
    var eosEiserxDate = chkDate('eosEiserxDate', false)
    if(! eosEiserxDate){
        chk_ok=0
    }
    var apoExerxDate = chkDate('apoExerxDate', false)
    if(! apoExerxDate){
        chk_ok=0
   }
    var eosExerxDate = chkDate('eosExerxDate', false)
    if(! eosExerxDate){
        chk_ok=0
    }
    if(chk_ok == 0){
        $('#showFindData').html("");
    }else{
        var querystr = '?' + $("#findform").serialize()
        if(page !== 1) querystr += '&page=' + page
        $('#showFindData').load("{{ URL::to('/getFindData') }}" + querystr )
    }
}

</script>

@endsection
