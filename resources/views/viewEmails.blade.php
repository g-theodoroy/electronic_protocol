@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12 col-sm-12 ">
           <div class="panel panel-default">
                <div class="panel-heading h1 text-center">Πρωτοκόλληση εισερχομένων email</div>
                <div class="panel-body ">
                  <div class="panel panel-default col-md-12 col-sm-12  ">
                      @if(count($aMessage))
                      @if(count($aMessage)< $aMessageNum)
                      <div class="row bg-warning">
                      <div class="form-control-static col-md-10 col-sm-10 col-md-offset-1 col-sm-offset-1 text-center"><strong>{{$defaultImapEmail}} - Εμφανίζονται {{count($aMessage)}} από {{$aMessageNum}} εισερχόμενα emails</strong></div>
                      @else
                      <div class="row bg-success">
                      <div class="form-control-static col-md-10 col-sm-10 col-md-offset-1 col-sm-offset-1 text-center"><strong>{{$defaultImapEmail}} - Εισερχόμενα emails: {{count($aMessage)}}</strong></div>
                      @endif
                      @else
                      <div class="row bg-info">
                      <div class="form-control-static col-md-10 col-sm-10 col-md-offset-1 col-sm-offset-1 text-center"><strong>{{$defaultImapEmail}} - Δεν υπάρχουν εισερχόμενα emails</strong></div>
                      @endif
                      <div class="form-control-static col-md-1 col-sm-1 text-right">
                        <a href="{{ URL::to('/') }}/home/list" class="active" role="button" title="Λίστα Πρωτοκόλλου" > <img src="{{ URL::to('/') }}/images/protocol.png" height=25 / ></a>
                      </div>
                   </div>
                 </div>
                  @foreach($aMessage as $oMessage)
                    <div class="panel panel-default col-md-12 col-sm-12  ">
                      <form name="frm{{$oMessage->getUid()}}" id="frm{{$oMessage->getUid()}}" class="form-horizontal" role="form" method="POST" action="{{ url('/') }}/storeFromEmail" >
                      {{ csrf_field() }}
                      <div class="row">
                        @if($oMessage->hasAttachments())
                        <div class="form-control-static col-md-1 col-sm-1 text-center"><strong>Φάκελος</strong></div>
                      <div class="form-control-static col-md-3 col-sm-3 ">
                      <select id="fakelos{{$oMessage->getUid()}}" onchange='getKeep4Fakelos({{$oMessage->getUid()}})' class="form-control selectpicker" data-live-search="true" liveSearchNormalize="true" name="fakelos{{$oMessage->getUid()}}"  title='13. Φάκελος αρχείου' autofocus >
                          <option value=''></option>
                          @foreach($fakeloi as $fakelos)
                              <option value='{{$fakelos['fakelos']}}' title='{{$fakelos['fakelos']}} - {{$fakelos['describe']}}' style="white-space: pre-wrap; width: 500px;" >{{$fakelos['fakelos']}} - {{$fakelos['describe']}}</option>
                          @endforeach
                      </select>
                      </div>

                          @if($allowUserChangeKeepSelect)
                              <div class="col-md-1 col-sm-1  text-center form-control-static">
                                  <strong>Χρόνος διατήρησης</strong>
                              </div>
                              <div class="col-md-4 col-sm-4">
                              <select id="keep{{$oMessage->getUid()}}" class="form-control small selectpicker" name="keep{{$oMessage->getUid()}}" title='Χρόνος Διατήρησης' >
                          @else
                              <div class="col-md-1 col-sm-1 small text-center form-control-static" title='Οι ρυθμίσεις δεν επιτρέπουν να αλλάξετε την επιλογή'>
                                  <strong>Χρόνος διατήρησης</strong>
                              </div>
                              <div class="col-md-4 col-sm-4" title='Οι ρυθμίσεις δεν επιτρέπουν να αλλάξετε την επιλογή'>
                              <select id="keep{{$oMessage->getUid()}}" class="form-control small selectpicker" data-value="" onchange="this.value = this.getAttribute('data-value');" name="keep{{$oMessage->getUid()}}" title='Χρόνος Διατήρησης' >
                          @endif
                              <option value=''></option>
                              @foreach($years as $year)
                                      <option value='{{$year->keep}}' title='{{$year->keep}} {{ $year->keep > 1 ? "χρόνια" : "χρόνο" }}' >{{$year->keep}} {{ $year->keep > 1 ? 'χρόνια' : 'χρόνο' }} </option>
                                  @endforeach
                              @foreach($words as $word)
                                      <option value='{{$word->keep_alt}}' title='{{$word->keep_alt}}' >{{$word->keep_alt}}</option>
                              @endforeach
                          </select>
                      </div>
                      @else
                      <div class="form-control-static col-md-9 col-sm-9 ">&nbsp;</div>
                      @endif
                      <div class="form-control-static col-md-3 col-sm-3 text-right">
                        <input id="uid" type="hidden" class="form-control" name="uid" value="{{$oMessage->getUid()}}">
                        <input id="sendReceipt{{$oMessage->getUid()}}" type="hidden" class="form-control" name="sendReceipt{{$oMessage->getUid()}}" value="0">
                        <a href="{{ URL::to('/') }}/setEmailRead/{{$oMessage->getUid()}}" class="" role="button" title="Σήμανση ως Αναγνωσμένο" tabindex=-1 > <img src="{{ URL::to('/') }}/images/mark-read.png" height="25" /></a>
                        <a href="javascript:$('#sendReceipt{{$oMessage->getUid()}}').val(0);chkSubmitForm({{$oMessage->getUid()}});" class="" role="button" title="Καταχώριση email χωρίς αποστολή Απόδειξης παραλαβής" > <img src="{{ URL::to('/') }}/images/save.ico" height=25 /></a>
                        <a href="javascript:$('#sendReceipt{{$oMessage->getUid()}}').val(1);chkSubmitForm({{$oMessage->getUid()}});" class="" role="button" title="Καταχώριση email και αποστολή Απόδειξης παραλαβής" tabindex=-1 > <img src="{{ URL::to('/') }}/images/receipt.png" height="25" /></a>
                      </div>
                    </div>

                      <div class="row bg-warning">
                        <div class="form-control-static col-md-1 col-sm-1  "><strong>Από:</strong></div>
                        <div class="form-control-static col-md-8 col-sm-8  ">{{$oMessage->getFrom()[0]->full}}</div>
                        <div class="form-control-static col-md-1 col-sm-1 "><strong>Ημνία:</strong></div>
                        <div class="form-control-static col-md-2 col-sm-2 ">{{$oMessage->getDate()}}</div>
                      </div>
                      <div class="row bg-warning ">
                        <div class="form-control-static col-md-1 col-sm-1"><strong>Θέμα:</strong></div>
                        <div class="form-control-static col-md-11 col-sm-11  "><strong>{{$oMessage->getSubject()}}</strong></div>
                      </div>
                      @if($oMessage->getTo())
                      <div class="row bg-warning ">
                        <div class="form-control-static col-md-1 col-sm-1"><strong>Προς:</strong></div>
                        <div class="form-control-static col-md-11 col-sm-11">
                          @foreach($oMessage->getTo() as $getTo)
                          {{$getTo->name}}{{$getTo->mail}}@if(! $loop->last),&nbsp;@endif
                          @endforeach
                        </div>
                      </div>
                      @endif
                      @if($oMessage->getCc())
                      <div class="row bg-warning ">
                          <div class="form-control-static col-md-1 col-sm-1"><strong>Κοιν:</strong></div>
                          <div class="form-control-static col-md-11 col-sm-11">
                            @foreach($oMessage->getCc() as $getCc)
                            {{$getCc->full}}
                            @if(! $loop->last), &nbsp; @endif
                            @endforeach
                          </div>
                      </div>
                      @endif
                      @if($oMessage->getReplyTo())
                      <div class="row bg-warning ">
                          <div class="form-control-static col-md-1 col-sm-1"><strong>Απάντηση:</strong></div>
                          <div class="form-control-static col-md-11 col-sm-11">
                            @foreach($oMessage->getReplyTo() as $getReplyTo)
                            {{$getReplyTo->full}}
                            @if(! $loop->last), &nbsp; @endif
                            @endforeach
                          </div>
                      </div>
                      @endif
                      <hr>
                      @if($oMessage->hasHTMLBody())
                      <div class="row">
                        <div class="form-control-static col-md-12 col-sm-12  ">{!!$oMessage->getHTMLBody()!!}</div>
                      </div>
                      @endif
                      @if($oMessage->hasHTMLBody() && $oMessage->hasTextBody())
                      <hr>
                      @endif
                      @if($oMessage->hasTextBody())
                      <div class="row">
                        <div class="form-control-static col-md-12 col-sm-12  small">{{$oMessage->getTextBody()}}</div>
                      </div>
                      @endif
                      <hr>
                      @if($oMessage->hasAttachments())
                      <div class="row bg-warning">
                        <div class="form-control-static col-md-2 col-sm-2"><strong>Συνημμένα:</strong></div>

                        <div class="form-control-static col-md-10 col-sm-10 ">
                          @foreach($oMessage->attachments as $key=>$attachment)
                          <a href='{{ URL::to('/') }}/viewEmailAttachment/{{$oMessage->getUid()}}/{{$key}}' target="_blank"  title='Λήψη {{ $attachment->name }}'>{{ $attachment->name }}</a>
                          <input type="checkbox" class="" id="chk{{$oMessage->getUid()}}-{{$key}}" name="chk{{$oMessage->getUid()}}-{{$key}}" title="Αν είναι επιλεγμένο αποθηκεύεται το συνημμένο {{ $attachment->name }}" checked  >
                          @if(! $loop->last), &nbsp; @endif
                          @endforeach
                        </div>
                      </div>
                      @endif
                    </form>
                </div>
                @endforeach
               </div>
            </div>
        </div>
    </div>
</div>

<script>
function getKeep4Fakelos(uid){
    var fak = $("#fakelos" + uid).val()
    $.get("{{ URL::to('/') }}/getKeep4Fakelos/" + fak, function(data){
        $("#keep"+uid).attr('data-value',data)
        $("#keep"+uid).val(data).change()
    });
}
function chkSubmitForm(uid) {
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
    // μετράω όλα τα τσεκαρισμένα chekboxes του email με το συγκεκριμμένο uid
    // var n = $('input:checkbox[id^="chk' + uid + '"]:checked').length;
    // αν υπάρχει έστω ένα τσεκαρισμένο ζητάω να συμπληρώσει φακελο Φ'
    if (! $("#fakelos" + uid).val() && $('input:checkbox[id^="chk' + uid + '"]:checked').length > 0){
        toastr.info("<center><h4>Ενημέρωση...</h4><hr>Για να καταχωρίσετε email με συνημμένα αρχεία είναι απαραίτητο να επιλέξετε Φάκελο<br>&nbsp;</center>")
        return
    }
    $("#frm" + uid).submit()
}
</script>

@endsection
