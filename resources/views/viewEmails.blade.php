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
                      <div class="form-control-static col-md-10 col-sm-10 col-md-offset-1 col-sm-offset-1 text-center"><strong>{{$defaultImapEmail}}</strong> - Εμφανίζονται τα <strong>{{count($aMessage)}} {{App\Config::getConfigValueOf('emailFetchOrderDesc') ? 'τελευταία' : 'πρώτα' }}</strong> από <strong>{{$aMessageNum}}</strong> εισερχόμενα emails - ταξινόμηση <strong>{{App\Config::getConfigValueOf('emailShowOrderDesc') ? 'φθίνουσα' : 'αύξουσα' }}</strong></div>
                      @else
                      <div class="row bg-success">
                      <div class="form-control-static col-md-10 col-sm-10 col-md-offset-1 col-sm-offset-1 text-center"><strong>{{$defaultImapEmail}} - Εισερχόμενα emails: {{count($aMessage)}}  - ταξινόμηση {{App\Config::getConfigValueOf('emailShowOrderDesc') ? 'φθίνουσα' : 'αύξουσα' }}</strong></div>
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
                 @php $num = 1; @endphp
                 @foreach($aMessage as $oMessage)
                    <div class="panel panel-default col-md-12 col-sm-12  ">
                      <form name="frm{{$oMessage->getUid()}}" id="frm{{$oMessage->getUid()}}" class="form-horizontal" role="form" method="POST" action="{{ url('/') }}/storeFromEmail" >
                      {{ csrf_field() }}

                        @if($oMessage->hasAttachments() || $alwaysShowFakelosInViewEmails)

                      
                      <div class="row bg-info"><div class="col-md-1 col-sm-1 form-control-static strong text-center">{{$num}}</div></div>
                      @php $num++; @endphp
                      <div class="row ">
                        <div class="col-md-1 col-sm-1 form-control-static small text-center">
                            <strong>Φάκελος</strong>
                        </div>
                        <div class="col-md-2 col-sm-2 {{ $errors->has('fakelos') ? ' has-error' : '' }}">
                            <select id="fakelos{{$oMessage->getUid()}}" onchange='getKeep4Fakelos({{$oMessage->getUid()}})' class="form-control selectpicker" data-live-search="true" liveSearchNormalize="true" name="fakelos{{$oMessage->getUid()}}"  title='13. Φάκελος αρχείου' autofocus >
                                <option value=''></option>
                                @foreach($fakeloi as $fakelos)
                                    <option value='{{$fakelos['fakelos']}}' title='{{$fakelos['fakelos']}} - {{$fakelos['describe']}}' style="white-space: pre-wrap; width: 500px;" >{{$fakelos['fakelos']}} - {{$fakelos['describe']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 col-sm-1 form-control-static small text-center">
                            <strong>Θέμα</strong>
                        </div>
                        <div class="col-md-6 col-sm-6 middle {{ $errors->has('thema') ? ' has-error' : '' }}">
                            <input id="thema" oninput="getValues(this.id, 'thema', 'themaList', 0)" type="text" class="form-control" name="thema" placeholder="thema" value="{{ $oMessage->getSubject() }}" title='Θέμα'>
                            <div id="themaList" class="col-md-12 col-sm-12" ></div>
                        </div>
                        <div class="col-md-2 col-sm-2 text-right">
                          <input id="uid" type="hidden" class="form-control" name="uid" value="{{$oMessage->getUid()}}">
                          <input id="sendReceipt{{$oMessage->getUid()}}" type="hidden" class="form-control" name="sendReceipt{{$oMessage->getUid()}}" value="0">
                          <a href="{{ URL::to('/') }}/setEmailRead/{{$oMessage->getUid()}}" class="" role="button" title="Σήμανση ως Αναγνωσμένο" tabindex=-1 > <img src="{{ URL::to('/') }}/images/mark-read.png" height="25" /></a>
                          @if(! $alwaysSendReceitForEmails)
                            <a href="javascript:$('#sendReceipt{{$oMessage->getUid()}}').val(0);sendEmailTo({{$oMessage->getUid()}});chkSubmitForm({{$oMessage->getUid()}});" class="" role="button" title="Καταχώριση email χωρίς αποστολή Απόδειξης παραλαβής" > <img src="{{ URL::to('/') }}/images/save.ico" height=25 /></a>
                          @endif
                         <a href="javascript:$('#sendReceipt{{$oMessage->getUid()}}').val(1);sendEmailTo({{$oMessage->getUid()}});chkSubmitForm({{$oMessage->getUid()}});" class="" role="button" title="Καταχώριση email και αποστολή Απόδειξης παραλαβής" tabindex=-1 > <img src="{{  URL::to('/') }}/images/{{ $alwaysSendReceitForEmails ? 'save.ico' : 'receipt.png'}}" height="25" /></a>
                        </div>
                    </div>


                    <div class="row bg-success">
                        <div class="col-md-1 col-sm-1 small text-center">
                            <strong>Αριθ.<br>Εισερχ.</strong>
                        </div>
                        <div class="col-md-2 col-sm-2 {{ $errors->has('in_num') ? ' has-error' : '' }}">
                            <input id="in_num" type="text" class="form-control text-center" name="in_num" placeholder="in_num" value="{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $oMessage->getDate())->format('H:i:s') }}" title='3. Αριθμός εισερχομένου εγγράφου' >
                        </div>
                        <div class="col-md-1 col-sm-1 small text-center">
                            <strong>Ημνία<br>Εισερχ.</strong>
                        </div>
                        <div class="col-md-2 col-sm-2 {{ $errors->has('in_date') ? ' has-error' : '' }}">
                            <input id="in_date" type="text" class="form-control datepicker text-center" name="in_date" placeholder="in_date" value="{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $oMessage->getDate())->format('d/m/Y') }}" title='5. Χρονολογία εισερχομένου εγγράφου'>
                        </div>
                        <div class="col-md-1 col-sm-1 small text-center">
                            <strong>Τόπος<br>Έκδοσης</strong>
                        </div>
                        <div class="col-md-5 col-sm-5 {{ $errors->has('in_topos_ekdosis') ? ' has-error' : '' }}">
                            <input id="in_topos_ekdosis"  oninput="getValues(this.id, 'in_topos_ekdosis', 'in_topos_ekdosisList', 0)" type="text" class="form-control" name="in_topos_ekdosis" placeholder="in_topos_ekdosis" value="{{ old('in_topos_ekdosis') ? old('in_topos_ekdosis') : $protocol->in_topos_ekdosis }}"  title='4. Τόπος που εκδόθηκε'>
                            <div id="in_topos_ekdosisList" class="col-md-12 col-sm-12" ></div>
                        </div>
                    </div>

                    <div class="row bg-success">
                        <div class="col-md-6 col-sm-6 ">
                            <div class="row">
                                <div class="col-md-2 col-sm-2 small text-center">
                                    <strong>Αρχή<br>Έκδοσης</strong>
                                </div>
                                <div class="col-md-10 col-sm-10 {{ $errors->has('in_arxi_ekdosis') ? ' has-error' : '' }}">
                                <input id="in_arxi_ekdosis" oninput="getValues(this.id, 'in_arxi_ekdosis', 'in_arxi_ekdosisList', 0)" type="text" class="form-control" name="in_arxi_ekdosis" placeholder="in_arxi_ekdosis" value="{{ mb_detect_encoding($oMessage->getFrom()[0]->personal, 'UTF-8, ISO-8859-7', true)== 'ISO-8859-7' ? iconv("ISO-8859-7", "UTF-8//IGNORE", $oMessage->getFrom()[0]->personal) : $oMessage->getFrom()[0]->personal }} {{ $oMessage->getFrom()[0]->personal ? "<" : "" }}{{ $oMessage->getFrom()[0]->mail  }}{{ $oMessage->getFrom()[0]->personal ? ">" : "" }}" title='5. Αρχή που το έχει εκδώσει'>
                                    <div id="in_arxi_ekdosisList" class="col-md-12 col-sm-12" ></div>
                                </div>
                                </div>
                                <div class="row">
                                <div class="col-md-2 col-sm-2 small text-center form-control-static">
                                    <strong>Παραλήπτης</strong>
                                </div>
                                <div class="col-md-10 col-sm-10 {{ $errors->has('in_paraliptis') ? ' has-error' : '' }}">
                                    <input id="in_paraliptis" oninput="getValues(this.id, 'in_paraliptis', 'in_paraliptisList', 0)" type="text" class="form-control" name="in_paraliptis" placeholder="in_paraliptis" value="{{ Auth::user()->name }}" title='7. Διεύθυνση, τμήμα, γραφείο ή πρόσωπο στο οποίο δόθηκε'>
                                    <div id="in_paraliptisList" class="col-md-12 col-sm-12" ></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6 ">
                            <div class="row">
                                <div class="col-md-2 col-sm-2 small text-center form-control-static">
                                    <strong>Περίληψη</strong>
                                </div>
                                <div class="col-md-10 col-sm-10 {{ $errors->has('in_perilipsi') ? ' has-error' : '' }}">
                                    <textarea id="in_perilipsi" type="text" class="form-control" name="in_perilipsi"  placeholder="in_perilipsi" value="" title='6. Περίληψη εισερχομένου εγγράφου'>{{ mb_substr(preg_replace('#\s+#',' ',trim($oMessage->getTextBody())), 0, 250) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row ">
                        <div class="col-md-1 col-sm-1 small text-center form-control-static">
                            <strong>Διεκπεραίωση</strong>
                        </div>
                        <div class="col-md-3 col-sm-3 {{ $errors->has('diekperaiosi') ? ' has-error' : '' }}">
                          <select id="diekperaiosi{{$oMessage->getUid()}}" class="form-control small selectpicker" name="diekperaiosi" title='Διεκπεραίωση' data-value="" @if($forbidenChangeDiekperaiosiSelect) onchange="this.value = this.getAttribute('data-value');" @endif>
                          <option value=''></option>
                          @foreach($writers_admins as $writer_admin)
                                  <option value='{{$writer_admin->id}}' >{{$writer_admin->name}}</option>
                              @endforeach
                            </select>
                            <input id="sendEmailTo{{$oMessage->getUid()}}" name="sendEmailTo" type="hidden" />
                        </div>



                          @if($allowUserChangeKeepSelect)
                              <div class="col-md-1 col-sm-1 small text-center form-control-static">
                                  <strong>Χρόνος διατήρησης</strong>
                              </div>
                              <div class="col-md-3 col-sm-3">
                                <select id="keep{{$oMessage->getUid()}}" class="form-control small selectpicker" name="keep{{$oMessage->getUid()}}" title='Χρόνος Διατήρησης' >
                          @else
                              <div class="col-md-1 col-sm-1 small text-center form-control-static" title='Οι ρυθμίσεις δεν επιτρέπουν να αλλάξετε την επιλογή'>
                                  <strong>Χρόνος διατήρησης</strong>
                              </div>
                              <div class="col-md-3 col-sm-3" title='Οι ρυθμίσεις δεν επιτρέπουν να αλλάξετε την επιλογή'>
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
                              <div class="col-md-4 col-sm-4">
                                <div class="row">
                                <div class="col-md-3 col-sm-3 small text-center">
                                    <strong>Απάντηση<br>σε email</strong>
                                </div>
                                <div class="col-md-9 col-sm-9 {{ $errors->has('reply_to') ? ' has-error' : '' }}">
                                <input id="reply_to" type="text" class="form-control" name="reply_to" placeholder="reply_to" value="{{  $oMessage->getReplyTo()[0]->mail ? $oMessage->getReplyTo()[0]->mail : $oMessage->getFrom()[0]->mail  }}" title='5. Αρχή που το έχει εκδώσει'>
                                    <div id="in_arxi_ekdosisList" class="col-md-12 col-sm-12" ></div>
                                </div>
                                </div>
                              </div>
                      </div>
                      <div class="row bg-info">&nbsp;</div>

                      @else
                        <div class="row">
                          <div class="form-control-static col-md-9 col-sm-9 ">&nbsp;</div>

                        <div class="col-md-3 col-sm-3 text-right">
                          <input id="uid" type="hidden" class="form-control" name="uid" value="{{$oMessage->getUid()}}">
                          <input id="sendReceipt{{$oMessage->getUid()}}" type="hidden" class="form-control" name="sendReceipt{{$oMessage->getUid()}}" value="0">
                          <a href="{{ URL::to('/') }}/setEmailRead/{{$oMessage->getUid()}}" class="" role="button" title="Σήμανση ως Αναγνωσμένο" tabindex=-1 > <img src="{{ URL::to('/') }}/images/mark-read.png" height="25" /></a>
                          @if(! $alwaysSendReceitForEmails)
                            <a href="javascript:$('#sendReceipt{{$oMessage->getUid()}}').val(0);chkSubmitForm({{$oMessage->getUid()}});" class="" role="button" title="Καταχώριση email χωρίς αποστολή Απόδειξης παραλαβής" > <img src="{{ URL::to('/') }}/images/save.ico" height=25 /></a>
                          @endif
                        <a href="javascript:$('#sendReceipt{{$oMessage->getUid()}}').val(1);chkSubmitForm({{$oMessage->getUid()}});" class="" role="button" title="Καταχώριση email και αποστολή Απόδειξης παραλαβής" tabindex=-1 > <img src="{{  URL::to('/') }}/images/{{ $alwaysSendReceitForEmails ? 'save.ico' : 'receipt.png'}}" height="25" /></a>
                        </div>

                      </div>
                      @endif
                      
                      <div class="row bg-warning">
                        <div class="form-control-static col-md-1 col-sm-1  "><strong>Από:</strong></div>
                        <div class="form-control-static col-md-8 col-sm-8  ">@if($oMessage->getFrom()[0]->personal) {{mb_detect_encoding($oMessage->getFrom()[0]->personal, 'UTF-8, ISO-8859-7', true)== 'ISO-8859-7' ? iconv("ISO-8859-7", "UTF-8//IGNORE", $oMessage->getFrom()[0]->personal) . " <" : $oMessage->getFrom()[0]->personal . " <" }}@endif{{$oMessage->getFrom()[0]->mail}}@if($oMessage->getFrom()[0]->personal)&gt;@endif</div>
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
                            {{$getCc->name}}{{$getCc->mail}}@if(! $loop->last),&nbsp;@endif
                            @endforeach
                          </div>
                      </div>
                      @endif
                      @if($oMessage->getReplyTo())
                      <div class="row bg-warning ">
                          <div class="form-control-static col-md-1 col-sm-1"><strong>Απάντηση:</strong></div>
                          <div class="form-control-static col-md-11 col-sm-11">
                            @foreach($oMessage->getReplyTo() as $getReplyTo)
                            {{$getReplyTo->name}}{{$getReplyTo->mail}}@if(! $loop->last),&nbsp;@endif
                            @endforeach
                          </div>
                      </div>
                      @endif
                      @if($oMessage->hasHTMLBody())
                      @php
                       $uid = $oMessage->getUid();
                       @endphp
                      <div class="row">
                        <div class="col-md-12 col-sm-12  ">
                          <iframe id="ifr{{$oMessage->getUid()}}" src="{{ asset( 'tmp/' .$emailFilePaths[$uid]) }}" width="100%" frameBorder="0" onload="this.style.height=(this.contentWindow.document.body.scrollHeight+10)+'px';">></iframe>
                        </div>
                      </div>
                      @endif
                      @if($oMessage->hasTextBody())
                      <div class="row">
                        <div class="col-md-12 col-sm-12  small">{{$oMessage->getTextBody()}}</div>
                      </div>
                      @endif
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
    // μετράω όλα τα τσεκαρισμένα chekboxes του email με το συγκεκριμμένο uid
    // var n = $('input:checkbox[id^="chk' + uid + '"]:checked').length;
    // αν υπάρχει έστω ένα τσεκαρισμένο ζητάω να συμπληρώσει φακελο Φ'
    if (! $("#fakelos" + uid).val() && $('input:checkbox[id^="chk' + uid + '"]:checked').length > 0){
        toastr.info("<center><h4>Ενημέρωση...</h4><hr>Για να καταχωρίσετε email με συνημμένα αρχεία είναι απαραίτητο να επιλέξετε Φάκελο<br>&nbsp;</center>")
        return
    }
    var fakelosRequired = {{ $alwaysShowFakelosInViewEmails ? 'true' : 'false'}}
    if (! $("#fakelos" + uid).val() && fakelosRequired){
        toastr.info("<center><h4>Ενημέρωση...</h4><hr>Για να καταχωρίσετε ένα email είναι απαραίτητο να επιλέξετε Φάκελο<br>&nbsp;</center>")
        return
    }
    $("#frm" + uid).submit()
}

function getValues(id, field, divId,  multi){
    @if (! $allowListValuesMatchingInput)
    return
    @endif
  var searchStr = $('#' + id).val().trim()
    if (searchStr == ''){
        clearDiv( divId )
        return
    }
    var term = extractLast(searchStr)
    if (term == ''){
        clearDiv( divId )
        return
     }

    $.ajax({
      url: '{{ URL::to('/') }}/getValues/' + term + '/' + field + '/' + id + '/' + divId + '/' + multi ,
      success: function(data){
        if(data){
          var front = '<ul id="' + id + 'Ul" class="dropdown-menu" style="display:block; position:absolute; max-height:{{\App\Config::getConfigValueOf('maxRowsInFindPage')*2/3}}em; max-width: 100%; overflow:auto" >'
          var end = '</ul>'
          $('#' + divId).html(front + data + end)
          $('#' + divId).show()
        }else{
          $('#' + divId).empty()
          $('#' + divId).hide()
        }
      }
    })
}

function clearDiv( divId ) {
      $('#' + divId).empty()
      $('#' + divId).hide()
}

function split( val ) {
      return val.split( /\s*,\s*/ );
    }
function extractLast( term ) {
  return split( term ).pop();
}

function appendValue(id, value, divId, multi){
    if ( multi == 0) {
        $('#' + id).val(value)
    }
    if ( multi == 1) {
      var terms = split($('#' + id).val());
      terms.pop()
      if (!terms.includes(value)){
        terms.push(value)
      }
     terms.push('')
      $('#' + id).val(terms.join( ', ' ))
    }
  $('#' + id).focus()
  $('#' + divId).empty()
  $('#' + divId).hide()
}

function sendEmailTo(id){
  var oldId = $('#diekperaiosi' + id).attr('data-value')
  var newId = $('#diekperaiosi' + id).val()
  var userId = {{Auth::user()->id}}
  if( ! newId  )return
  if (newId == userId) return
  if (newId == oldId) return
  $('#sendEmailTo' + id).val(newId)
}


</script>

@endsection
