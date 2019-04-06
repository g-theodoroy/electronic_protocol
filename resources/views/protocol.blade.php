@extends('layouts.app')

@section('content')
<style>

.asd {
    background:rgba(0,0,0,0);
    border:none;
    font-weight: bold;
}
input[readonly].asd {
    background:rgba(0,0,0,0);
    border:none;
    font-weight: bold;
}
</style>
<script >

function chkdelete(id, name){

    var html = "<center><button type='button' id='confirmationRevertYes' class='btn btn-primary'>Ναί</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' id='confirmationRevertNo' class='btn btn-primary'>Όχι</button></center></p>"
    var msg = '<center><h4>Διαγραφή ?</h4><hr>Διαγραφή συννημένου ' + name + '. Είστε σίγουροι;<br>&nbsp;</center>'

    toastr.options = {
      "closeButton": true,
      "debug": false,
      "newestOnTop": false,
      "progressBar": false,
      "positionClass": "toast-top-center",
      "preventDuplicates": false,
      "onclick": null,
      "showDuration": "0",
      "hideDuration": "0",
      "timeOut": "0",
      "extendedTimeOut": "0",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"
    }
    var $toast = toastr.warning(html,msg);
    $toast.delegate('#confirmationRevertYes', 'click', function () {
            $('#show_arxeia').load("{{ URL::to('/') }}" + "/attach/del/" + id);
            $toast.remove();
    });
    $toast.delegate('#confirmationRevertNo', 'click', function () {
            $toast.remove();
    });
}

function chkprotocoldelete(id, etos, num){

    var html = "<center><button type='button' id='confirmationRevertYes' class='btn btn-primary'>Ναί</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' id='confirmationRevertNo' class='btn btn-primary'>Όχι</button></center></p>"
    var msg = '<center><h4>Διαγραφή ?</h4><hr>Διαγραφή πρωτοκόλλου με αριθμό ' + num + ' για το έτος ' + etos + '. Είστε σίγουροι;<br>&nbsp;</center>'

    toastr.options = {
      "closeButton": true,
      "debug": false,
      "newestOnTop": false,
      "progressBar": false,
      "positionClass": "toast-top-center",
      "preventDuplicates": false,
      "onclick": null,
      "showDuration": "0",
      "hideDuration": "0",
      "timeOut": "0",
      "extendedTimeOut": "0",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"
    }
    var $toast = toastr.warning(html,msg);
    $toast.delegate('#confirmationRevertYes', 'click', function () {
            $(location).attr('href', "{{ URL::to('/') }}" + "/delprotocol/" + id);
            $toast.remove();
    });
    $toast.delegate('#confirmationRevertNo', 'click', function () {
            $toast.remove();
    });
}

function chkfind(){
    var protocolnum = $('#find').val()
    var etos = $('#etos').val()
    if (protocolnum){
        $(location).attr('href', "{{ URL::to('/') }}" + "/goto/" + etos + "/" + protocolnum  + "?find=1")
    }else{
        $(location).attr('href', "{{ URL::to('/find') }}")
    }
}

function periigisi(id){
    var protocolnum = parseInt($('#protocolnum').val())
    var etos = $('#etos').val()
    switch(id){
        case 'bb':
            protocolnum -= {{$protocolArrowStep}}
            break;
        case 'b':
            protocolnum -= 1
            break;
        case 'f':
            protocolnum += 1
            break;
        case 'ff':
            protocolnum += {{$protocolArrowStep}}
            break;
            }
        $(location).attr('href', "{{ URL::to('/') }}" + "/goto/" + etos + "/" + protocolnum)

}

</script>

<div class="container">
    <div class="row">
        <div class="col-md-12 ">
            <div class="panel panel-default">
              @if(count($activeusers2show)>1)
              <div class="col-md-2 col-sm-2 small text-center">Ενεργοί χρήστες: <strong>{{count($activeusers2show)}}</strong></div>
              <div class="col-md-10 col-sm-10 small text-left">
                @foreach($activeusers2show as $user2show)
                {{$user2show}}@if(! $loop->last), @endif
                @endforeach
              </div>
              @endif
                <div class="panel-heading h1 text-center" {!!$titleColorStyle!!}>{{$protocoltitle}}</div>

                <div class="panel-body">
                <div class="panel panel-default col-md-12 col-sm-12  ">

                    <form name="myProtocolForm" id="myProtocolForm" class="form-horizontal" role="form" method="POST" action="{{ url('/home') }}{{$protocol->id ? '/' . $protocol->id : '' }}" enctype="multipart/form-data" >
                    {{ csrf_field() }}

                    <div class="row {{$class}}">
                        <div class="col-md-1 col-sm-1 ">
                            <input id="find" type="text" class="form-control text-center asd" name="find" title="Αναζήτηση"  placeholder="" value="{{ old('find') ? old('find') :  '' }}" tabindex=-1 >
                        </div>
                        <div class="col-md-1 col-sm-1  form-control-static  text-center">
                            <a href="javascript:chkfind()" class="active" role="button" title="Αναζήτηση" > <img src="{{ URL::to('/') }}/images/find.ico" height=25 / ></a></td>
                            <a href="{{ URL::to('/print') }}" class="" role="button" title="Εκτύπωση" > <img src="{{ URL::to('/') }}/images/print.png" height=25 /></a>
                        </div>

                        <div class="col-md-7 col-sm-7 ">
                            <div class="row">
                                <div class="col-md-1 col-sm-1 form-control-static small text-center">
                                    <strong>Έτος</strong>
                                </div>
                                <div class="col-md-3 col-sm-3 middle {{ $errors->has('etos') ? ' has-error' : '' }}">
                                    <input id="etos" type="text" class="form-control input-lg text-center asd" name="etos" placeholder="etos" value="{{ old('etos') ? old('etos') :  $newetos }}" required tabindex=-1 {{$readonly}}  >
                                </div>
                                <div class="col-md-1 col-sm-1 small text-center">
                                    <strong>Αύξων<br>Αριθμός</strong>
                                </div>
                                <div class="col-md-3 col-sm-3 middle {{ $errors->has('protocolnum') ? ' has-error' : '' }}">
                                    <input id="protocolnum" type="text" class="form-control input-lg text-center asd text-bold {{$newprotocolnumvisible}}" name="protocolnum" placeholder="num" value="{{ old('protocolnum') ? old('protocolnum') :  $newprotocolnum }}" title='1. Αύξων αριθμός' required tabindex=-1 {{$readonly}} >
                                </div>
                                <div class="col-md-1 col-sm-1 small text-center">
                                    <strong>Ημνια<br>παραλαβής</strong>
                                </div>
                                <div class="col-md-3 col-sm-3 middle {{ $errors->has('protocoldate') ? ' has-error' : '' }}">
                                    <input id="protocoldate" type="text" class="form-control input-lg text-center asd" name="protocoldate" placeholder="date" value="{{ old('protocoldate') ? old('protocoldate') : $newprotocoldate  }}" title='2. Ημερομηνία παραλαβής εγγράφου' required tabindex=-1 {{$readonly}}  >
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-2 text-center ">
                            <a href="javascript:periigisi('bb')" class="active" role="button" title="- {{$protocolArrowStep}}" > <img src="{{ URL::to('/') }}/images/arrow-left-double.png" height=13 / ></a>
                            <a href="javascript:periigisi('ff')"  class="active" role="button" title="+ {{$protocolArrowStep}}" > <img src="{{ URL::to('/') }}/images/arrow-right-double.png" height=13 / ></a><br>
                            <a href="javascript:periigisi('b')" class="active" role="button" title="- 1" > <img src="{{ URL::to('/') }}/images/arrow-left.png" height=13 / ></a>
                            <a href="javascript:periigisi('f')" class="active" role="button" title="+ 1" > <img src="{{ URL::to('/') }}/images/arrow-right.png" height=13/ ></a>
                        </div>
                        <div class="col-md-1 col-sm-1 text-center  form-control-static ">
                            <a href="{{ URL::to('/') }}/home" class="active" role="button" title="Νέο" > <img src="{{ URL::to('/') }}/images/addnew.ico" height=25 / ></a>
                            <a href="javascript:$('#keep').removeAttr('disabled');document.forms['myProtocolForm'].submit();" class="{{$submitVisible}}" role="button" title="Αποθήκευση" > <img src="{{ URL::to('/') }}/images/save.ico" height=25 /></a>
                        </div>
                    </div>

                    <div class="row ">
                        <div class="col-md-1 col-sm-1 form-control-static small text-center">
                            <strong>Φάκελος</strong>
                        </div>
                        <div class="col-md-2 col-sm-2 {{ $errors->has('fakelos') ? ' has-error' : '' }}">
                            <select id="fakelos" onchange='getKeep4Fakelos()' class="form-control selectpicker" data-live-search="true" liveSearchNormalize="true" name="fakelos"  title='13. Φάκελος αρχείου' autofocus >
                                <option value=''></option>
                                @foreach($fakeloi as $fakelos)
                                @if ($fakelos['fakelos'] == $protocol->fakelos)
                                    <option value='{{$fakelos['fakelos']}}' title='{{$fakelos['fakelos']}} - {{$fakelos['describe']}}' style="white-space: pre-wrap; width: 500px;" selected >{{$fakelos['fakelos']}} - {{$fakelos['describe']}}</option>
                                @else
                                    <option value='{{$fakelos['fakelos']}}' title='{{$fakelos['fakelos']}} - {{$fakelos['describe']}}' style="white-space: pre-wrap; width: 500px;" >{{$fakelos['fakelos']}} - {{$fakelos['describe']}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 col-sm-1 form-control-static small text-center">
                            <strong>Θέμα</strong>
                        </div>
                        <div class="col-md-7 col-sm-7 middle {{ $errors->has('thema') ? ' has-error' : '' }}">
                            <input id="thema" type="text" class="form-control" name="thema" placeholder="thema" value="{{ old('thema') ? old('thema') : $protocol->thema }}" title='Θέμα'>
                        </div>
                        <div class="col-md-1 col-sm-1 text-center">
                            @if($protocol->id)
                            <a href="javascript:chkprotocoldelete('{{ $protocol->id }}','{{$protocol->etos}}','{{$protocol->protocolnum}}')" class="{{$delVisible}}" role="button" title="Διαγραφή Πρωτοκόλλου" tabindex=-1 > <img src="{{ URL::to('/') }}/images/delete.ico" height="20" /></a>
                            <a href="{{ URL::to('/')}}/receipt/{{$protocol->id}}" class="{{$submitVisible}}" role="button" title="Απόδειξη παραλαβής" target="_blank" tabindex=-1 > <img src="{{ URL::to('/') }}/images/receipt.png" height="20" /></a>
                            @endif
                            <a href="javascript:document.forms['myProtocolForm'].reset();" class="active" role="button" title="Καθάρισμα φόρμας" tabindex=-1 > <img src="{{ URL::to('/') }}/images/clear.ico" height="20" /></a>
                        </div>
                    </div>

                    <div class="row bg-success">
                        <div class="col-md-1 col-sm-1 small text-center">
                            <strong>Αριθ.<br>Εισερχ.</strong>
                        </div>
                        <div class="col-md-2 col-sm-2 {{ $errors->has('in_num') ? ' has-error' : '' }}">
                            <input id="in_chk" type="hidden" name="in_chk"  value="1" >
                            <input id="in_num" type="text" class="form-control text-center" name="in_num" placeholder="in_num" value="{{ old('in_num') ? old('in_num') : $protocol->in_num }}" title='3. Αριθμός εισερχομένου εγγράφου' >
                        </div>
                        <div class="col-md-1 col-sm-1 small text-center">
                            <strong>Ημνία<br>Εισερχ.</strong>
                        </div>
                        <div class="col-md-2 col-sm-2 {{ $errors->has('in_date') ? ' has-error' : '' }}">
                            <input id="in_date" type="text" class="form-control datepicker text-center" name="in_date" placeholder="in_date" value="{{ old('in_date') ? old('in_date') : $in_date }}" title='5. Χρονολογία εισερχομένου εγγράφου'>
                        </div>
                        <div class="col-md-1 col-sm-1 small text-center">
                            <strong>Τόπος<br>Έκδοσης</strong>
                        </div>
                        <div class="col-md-5 col-sm-5 {{ $errors->has('in_topos_ekdosis') ? ' has-error' : '' }}">
                            <input id="in_topos_ekdosis" type="text" class="form-control" name="in_topos_ekdosis" placeholder="in_topos_ekdosis" value="{{ old('in_topos_ekdosis') ? old('in_topos_ekdosis') : $protocol->in_topos_ekdosis }}"  title='4. Τόπος που εκδόθηκε'>
                        </div>
                    </div>

                    <div class="row bg-success">
                        <div class="col-md-6 col-sm-6 ">
                            <div class="row">
                                <div class="col-md-2 col-sm-2 small text-center">
                                    <strong>Αρχή<br>Έκδοσης</strong>
                                </div>
                                <div class="col-md-10 col-sm-10 {{ $errors->has('in_arxi_ekdosis') ? ' has-error' : '' }}">
                                    <input id="in_arxi_ekdosis" type="text" class="form-control" name="in_arxi_ekdosis" placeholder="in_arxi_ekdosis" value="{{ old('in_arxi_ekdosis') ? old('in_arxi_ekdosis') : $protocol->in_arxi_ekdosis }}" title='5. Αρχή που το έχει εκδώσει'>
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-md-2 col-sm-2 small text-center form-control-static">
                                    <strong>Παραλήπτης</strong>
                                </div>
                                <div class="col-md-10 col-sm-10 {{ $errors->has('in_paraliptis') ? ' has-error' : '' }}">
                                    <input id="in_paraliptis" type="text" class="form-control" name="in_paraliptis" placeholder="in_paraliptis" value="{{ old('in_paraliptis') ? old('in_paraliptis') : $protocol->in_paraliptis }}" title='7. Διεύθυνση, τμήμα, γραφείο ή πρόσωπο στο οποίο δόθηκε'>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6 ">
                            <div class="row">
                                <div class="col-md-2 col-sm-2 small text-center form-control-static">
                                    <strong>Περίληψη</strong>
                                </div>
                                <div class="col-md-10 col-sm-10 {{ $errors->has('in_perilipsi') ? ' has-error' : '' }}">
                                    <textarea id="in_perilipsi" type="text" class="form-control" name="in_perilipsi"  placeholder="in_perilipsi" value="" title='6. Περίληψη εισερχομένου εγγράφου'>{{ old('in_perilipsi') ? old('in_perilipsi') : $protocol->in_perilipsi }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row ">
                        <div class="col-md-1 col-sm-1 small text-center form-control-static">
                            <strong>Διεκπεραίωση</strong>
                        </div>
                        <div class="col-md-5 col-sm-5 {{ $errors->has('diekperaiosi') ? ' has-error' : '' }}">
                          <select id="diekperaiosi" class="form-control small selectpicker" name="diekperaiosi" title='Διεκπεραίωση' @if($forbidenChangeDiekperaiosiSelect) data-value="{{$protocol->diekperaiosi}}" onchange="this.value = this.getAttribute('data-value');" @endif>
                          <option value=''></option>
                          @foreach($writers_admins as $writer_admin)
                                  <option value='{{$writer_admin->id}}' @if($writer_admin->id == $protocol->diekperaiosi) selected @endif>{{$writer_admin->name}}</option>
                              @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 col-sm-1 small text-center">
                            <strong>Ημνία<br>Διεκπεραίωσης</strong>
                        </div>
                        <div class="col-md-2 col-sm-2 {{ $errors->has('diekp_date') ? ' has-error' : '' }}">
                            <input id="diekp_date" type="text" class="form-control datepicker text-center" name="diekp_date" placeholder="diekp_date" value="{{ old('diekp_date') ? old('diekp_date') : $diekp_date }}" title='11. Ημερομηνία διεκπεραίωσης'>
                        </div>
                        <div class="col-md-1 col-sm-1 small text-center">
                            <strong>Σχετικοί<br>αριθμοί</strong>
                        </div>
                        <div class="col-md-2 col-sm-2 {{ $errors->has('sxetiko') ? ' has-error' : '' }}">
                            <input id="sxetiko" type="text" class="form-control text-center" name="sxetiko" placeholder="sxetiko" value="{{ old('sxetiko') ? old('sxetiko') : $protocol->sxetiko }}" title='12. Σχετικοί αριθμοί'>
                        </div>
                    </div>

                    <div class="row bg-info">
                        <div class="col-md-6 col-sm-6 ">
                            <div class="row">
                                <div class="col-md-2 col-sm-2 small text-center form-control-static">
                                    <strong>Απευθύνεται</strong>
                                </div>
                                <div class="col-md-10 col-sm-10 {{ $errors->has('out_to') ? ' has-error' : '' }}">
                                    <input id="out_to" type="text" class="form-control" name="out_to" placeholder="out_to" value="{{ old('out_to') ? old('out_to') : $protocol->out_to }}"  title='8. Αρχή στην οποία απευθύνεται'>
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-md-2 col-sm-2 col-md-offset-3 col-sm-offset-3 small text-center ">
                                    <strong>Ημνία<br>Εξερχ.</strong>
                                </div>
                                <div class="col-md-4 col-sm-4 {{ $errors->has('out_date') ? ' has-error' : '' }}">
                                    <input id="out_date" type="text" class="form-control datepicker text-center" name="out_date" placeholder="out_date" value="{{ old('out_date') ? old('out_date') : $out_date }}" title='10. Χρονολογία εξερχομένου εγγράφου'>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6 ">
                            <div class="row">
                                <div class="col-md-2 col-sm-2 small text-center form-control-static">
                                    <strong>Περίληψη</strong>
                                </div>
                                <div class="col-md-10 col-sm-10 {{ $errors->has('out_perilipsi') ? ' has-error' : '' }}">
                                    <textarea id="out_perilipsi" type="text" class="form-control" name="out_perilipsi"  placeholder="out_perilipsi" value=""  title='9. Περίληψη εξερχομένου εγγράφου'>{{ old('out_perilipsi') ? old('out_perilipsi') : $protocol->out_perilipsi }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row ">
                        <div class="col-md-1 col-sm-1 small text-center ">
                            <strong>Λέξεις<br>κλειδιά</strong>
                        </div>
                        <div class="col-md-5 col-sm-5 {{ $errors->has('keywords') ? ' has-error' : '' }}">
                            <textarea id="keywords" type="text" class="form-control" name="keywords"  placeholder="keywords" >{{ old('keywords') ? old('keywords') : $protocol->keywords }}</textarea>
                            </div>
                        <div class="col-md-1 col-sm-1 small text-center form-control-static">
                            <strong>Παρατηρήσεις</strong>
                        </div>
                        <div class="col-md-5 col-sm-5 {{ $errors->has('paratiriseis') ? ' has-error' : '' }}">
                            <textarea id="paratiriseis" type="text" class="form-control" name="paratiriseis"  placeholder="paratiriseis" title='Παρατηρήσεις' >{{ old('paratiriseis') ? old('paratiriseis') : $protocol->paratiriseis }}</textarea>
                        </div>
                    </div>

                    <div class="row ">
                        <div class="col-md-1 col-sm-1 small text-center form-control-static">
                            <strong>Συνημμένα</strong>
                        </div>
                        <div id='show_arxeia' class="col-md-9 col-sm-9 form-control-static">
                            <ul class='list-inline'>
                                @foreach ($protocol->attachments()->get() as $attachment)
                                    <li>
                                      @if ($attachment->name)
                                        <a href='{{ URL::to('/') }}/download/{{$attachment->id}}' target="_blank" title="Λήψη {{ $attachment->name }}">{{ $attachment->name }}</a>
                                      @endif
                                      @if ($attachment->ada)
                                        <a href='{{$diavgeiaUrl}}{{$attachment->ada}}' target="_blank" title="Λήψη {{ $attachment->ada }}">{{ $attachment->ada }}</a>
                                      @endif
                                        <a href="javascript:chkdelete('{{ $attachment->id }}','{{$attachment->name}}')" class="{{$submitVisible}}" id='delatt{{ $attachment->id }}' title="Διαγραφή {{ $attachment->name ? $attachment->name : $attachment->ada }}" > <img src="{{ URL::to('/') }}/images/delete.ico" alt="delete" height="13"> </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-2 col-sm-2 small text-right form-control-static">
                            <input id="file_inputs_count" type="hidden" class="form-control" name="file_inputs_count"  value="0"  >
                            <a href="#" onclick="getFileInputs()" class="{{$submitVisible}}" role="button" title="Προσθήκη συνημμένων αρχείων" > <img src="{{ URL::to('/') }}/images/attachment.png" height=25 /></a>
                            <a href="#" onclick='$("#file_inputs_count").val(0);$("#show_protocol_file_inputs").empty();$("#keepdiv").addClass("hidden")' class="{{$submitVisible}}" role="button" title="Καθάρισμα συνημμένων αρχείων" > <img src="{{ URL::to('/') }}/images/clear.ico" height="20" /></a>
                            <a href="{{ URL::to('/') }}/home/list" class="active" role="button" title="Λίστα Πρωτοκόλλου" > <img src="{{ URL::to('/') }}/images/protocol.png" height=25 / ></a>
                        </div>
                    </div>
                    <div id="keepdiv" class="row hidden">
                    <div class="col-md-4 col-sm-4 small form-control-static">
                        <strong>Επιλέξτε αρχείο ή<br>πληκτρολογείστε ΑΔΑ</strong>
                    </div>
                        @if($allowUserChangeKeepSelect)
                            <div class="col-md-4 col-sm-4 small text-right form-control-static">
                                <strong>Χρόνος διατήρησης</strong>
                            </div>
                            <div class="col-md-4 col-sm-4">
                            <select id="keep" class="form-control small selectpicker" name="keep" title='Χρόνος Διατήρησης' >
                        @else
                            <div class="col-md-4 col-sm-4 small text-right form-control-static" title='Οι ρυθμίσεις δεν επιτρέπουν να αλλάξετε την επιλογή'>
                                <strong>Χρόνος διατήρησης</strong>
                            </div>
                            <div class="col-md-4 col-sm-4" title='Οι ρυθμίσεις δεν επιτρέπουν να αλλάξετε την επιλογή'>
                            <select id="keep" class="form-control small selectpicker" data-value="{{$keepval}}" onchange="this.value = this.getAttribute('data-value');" name="keep" title='Χρόνος Διατήρησης' >
                        @endif
                            <option value=''></option>
                            @foreach($years as $year)
                                @if($year->keep == $keepval)
                                    <option value='{{$year->keep}}' title='{{$year->keep}} {{ $year->keep > 1 ? "χρόνια" : "χρόνο" }}' selected >{{$year->keep}} {{ $year->keep > 1 ? 'χρόνια' : 'χρόνο' }} </option>
                                @else
                                    <option value='{{$year->keep}}' title='{{$year->keep}} {{ $year->keep > 1 ? "χρόνια" : "χρόνο" }}' >{{$year->keep}} {{ $year->keep > 1 ? 'χρόνια' : 'χρόνο' }} </option>
                                @endif
                                @endforeach
                            @foreach($words as $word)
                                @if($word->keep_alt == $keepval)
                                    <option value='{{$word->keep_alt}}' title='{{$word->keep_alt}}' selected >{{$word->keep_alt}}</option>
                                @else
                                    <option value='{{$word->keep_alt}}' title='{{$word->keep_alt}}' >{{$word->keep_alt}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    </div>
                    <div class="row ">
                    <div id="show_protocol_file_inputs" class="col-md-12 col-sm-12"></div>
                    </div>
                    </form>
                </div>
                <div class="col-md-6 col-sm-6 small text-left">
                  @if($showUserInfo == 1)
                    @if($protocol->id)
                      @if($protocol->created_at == $protocol->updated_at)
                        Καταχωρίστηκε {{$protocol->updated_at}}
                        @if($protocolUser) από {{$protocolUser->username}} @endif
                      @else
                        Ενημερώθηκε {{$protocol->updated_at}}
                        @if($protocolUser) από {{$protocolUser->username}} @endif
                      @endif
                    @endif
                  @elseif($showUserInfo == 2)
                    @if($protocol->id)
                      @if($protocol->created_at == $protocol->updated_at)
                        Καταχωρίστηκε {{$protocol->updated_at}}
                        @if($protocolUser) από {{$protocolUser->name}} @endif
                      @else
                        Ενημερώθηκε {{$protocol->updated_at}}
                        @if($protocolUser) από {{$protocolUser->name}} @endif
                      @endif
                    @endif
                  @endif
                </div>
                <div id='timer' class="col-md-6 col-sm-6 small text-right" title=''>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function getFileInputs() {
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
    if (! $("#fakelos").val()){
        toastr.info("<center><h4>Ενημέρωση...</h4><hr>Για να προσθέσετε συνημμένα αρχεία είναι απαραίτητο να επιλέξετε Φάκελο<br>&nbsp;</center>")
        return false;
    }
    var num = $("#file_inputs_count").val()
    var fak = $("#fakelos").val()
    $("#keepdiv").removeClass('hidden')
    $("#file_inputs_count").val(parseInt(num)+1)
    $('#show_protocol_file_inputs').load("{{ URL::to('/') }}/getFileInputs/"+ num );
}
function getKeep4Fakelos(){
    var fak = $("#fakelos").val()
    $.get("{{ URL::to('/') }}/getKeep4Fakelos/" + fak, function(data){
        $("#keep").attr('data-value',data)
        $("#keep").val(data).change()
    });
}
</script>

@if ($time2update > 0)
<script type="text/javascript">
function startTimer(duration, display) {
    var timer = duration, minutes, seconds
    interval = setInterval(function () {
        minutes = parseInt(timer / 60, 10)
        seconds = parseInt(timer % 60, 10)

        //minutes = minutes < 10 ? "0" + minutes : minutes
        seconds = seconds < 10 ? "0" + seconds : seconds

        display.textContent = 'Δυνατότητα επεξεργασίας: ' + minutes + ":" + seconds + ' λεπτά'

        if (--timer < 0) {
            window.location.reload()
        }
    }, 1000)
}

window.onload = function () {
    var duration =  {{$time2update}},
        display = document.querySelector('#timer')
    startTimer(duration, display);
}
</script>
@endif

@endsection
