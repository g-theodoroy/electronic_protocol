@extends('layouts.app')

@section('content')
    @php
    use ZBateson\MailMimeParser\Header\HeaderConsts;
    @endphp

    <div class="{{ $wideListProtocol ? 'container-fluid' : 'container' }}">
        <div class="row">
            <div class="col-md-12 col-sm-12 ">
                <div class="panel panel-default">
                    <div class="panel-heading h1 text-center">Πρωτοκόλληση εισερχομένων email</div>
                    <div class="panel-body ">
                        <div class="panel panel-default col-md-12 col-sm-12  ">

                            @if ($aMessage->links())
                                <div class="row">
                                    <div class="small text-center">
                                        <span class="small">{{ $aMessage->links() }}</span>
                                    </div>
                                </div>
                            @endif

                            @if ($aMessageCount)
                                @if ($aMessageCount < $aMessageNum)
                                    <div class="row bg-warning">
                                        <div
                                            class="form-control-static col-md-10 col-sm-10 col-md-offset-1 col-sm-offset-1 text-center">
                                            <strong>{{ $defaultImapEmail }}</strong>:
                                            {{ config('imap.accounts.' . $defaultImapEmail . '.username') }} -
                                            Εμφανίζονται
                                            τα
                                            <strong>{{ ($aMessage->currentPage() - 1) * $aMessage->perPage() + 1 }}</strong>
                                            έως
                                            <strong>{{ $aMessage->currentPage() * $aMessage->perPage() > $aMessageNum? $aMessageNum: $aMessage->currentPage() * $aMessage->perPage() }}</strong>
                                            από <strong>{{ $aMessageNum }}</strong> εισερχόμενα emails -
                                            ταξινόμηση
                                            <strong>{{ $emailFetchOrderDesc ? 'φθίνουσα' : 'αύξουσα' }}</strong>
                                        </div>
                                    @else
                                        <div class="row bg-success">
                                            <div
                                                class="form-control-static col-md-10 col-sm-10 col-md-offset-1 col-sm-offset-1 text-center">
                                                <strong>{{ $defaultImapEmail }} - Εισερχόμενα emails:
                                                    {{ count($aMessage) }}
                                                    - ταξινόμηση
                                                    {{ $emailFetchOrderDesc ? 'φθίνουσα' : 'αύξουσα' }}</strong>
                                            </div>
                                @endif
                            @else
                                <div class="row bg-info">
                                    <div
                                        class="form-control-static col-md-10 col-sm-10 col-md-offset-1 col-sm-offset-1 text-center">
                                        <strong>{{ $defaultImapEmail }} - Δεν υπάρχουν εισερχόμενα emails</strong>
                                    </div>
                            @endif
                            <div class="form-control-static col-md-1 col-sm-1 text-right">
                                <a href="{{ URL::to('/') }}/home/list" class="active" role="button"
                                    title="Λίστα Πρωτοκόλλου"> <img src="{{ URL::to('/') }}/images/protocol.png"
                                        height=25 /></a>
                            </div>
                        </div>
                    </div>

                    @if ($aMessageCount)
                        @php $num = 1; @endphp
                        @foreach ($aSortedMessage as $oMessage)
                            @php
                                $Uid = $oMessage->getUid();
                                $mailMessage = ZBateson\MailMimeParser\Message::from($oMessage->getHeader()->raw . $oMessage->getRawBody());
                            @endphp
                            <div class="panel panel-default col-md-12 col-sm-12  ">
                                <form name="frm{{ $Uid }}" id="frm{{ $Uid }}" class="form-horizontal"
                                    role="form" method="POST" action="{{ url('/') }}/storeFromEmail">
                                    {{ csrf_field() }}
                                    <div class="row bg-primary">
                                        <div class="col-md-12 col-sm-12 form-control-static strong ">
                                            {{ ($aMessage->currentPage() - 1) * $aMessage->perPage() + $num }} από
                                            {{ $aMessageNum }}</div>
                                    </div>
                                    @php
                                        $num++;
                                        $subject = $mailMessage->getHeaderValue(HeaderConsts::SUBJECT);
                                    @endphp

                                    @if ($mailMessage->getAttachmentCount() || $alwaysShowFakelosInViewEmails)
                                        <div class="row ">
                                            <div class="col-md-1 col-sm-1 form-control-static small text-center">
                                                <strong>Φάκελος</strong>
                                            </div>
                                            <div id="fakelos{{ $Uid }}Div"
                                                class="col-md-2 col-sm-2 {{ $errors->has('fakelos') ? ' has-error' : '' }}">
                                                <select id="fakelos{{ $Uid }}"
                                                    onchange='getKeep4Fakelos({{ $Uid }})'
                                                    class="form-control selectpicker" data-live-search="true"
                                                    liveSearchNormalize="true" name="fakelos{{ $Uid }}"
                                                    title='13. Φάκελος αρχείου' autofocus>
                                                    <option value=''></option>
                                                    @foreach ($fakeloi as $fakelos)
                                                        <option value='{{ $fakelos['fakelos'] }}'
                                                            title='{{ $fakelos['fakelos'] }} - {{ $fakelos['describe'] }}'
                                                            style="white-space: pre-wrap; width: 500px;">{{ $fakelos['fakelos'] }} - {{ $fakelos['describe'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-1 col-sm-1 form-control-static small text-center">
                                                <strong>Θέμα</strong>
                                            </div>
                                            <div id="themaDiv"
                                                class="col-md-6 col-sm-6 middle {{ $errors->has('thema') ? ' has-error' : '' }}">
                                                <input id="thema" oninput="getValues(this.id, 'thema', 'themaList', 0)"
                                                    type="text" class="form-control" name="thema" placeholder="thema"
                                                    value="{{ $subject }}" title='Θέμα'>
                                                <div id="themaList" class="col-md-12 col-sm-12"></div>
                                            </div>
                                            <div class="col-md-2 col-sm-2 text-right">
                                                <input id="uid" type="hidden" class="form-control" name="uid"
                                                    value="{{ $Uid }}">
                                                <input id="sendReceipt{{ $Uid }}" type="hidden"
                                                    class="form-control" name="sendReceipt{{ $Uid }}" value="0">
                                                <a href="{{ URL::to('/') }}/setEmailRead/{{ $Uid }}"
                                                    class="" role="button" title="Σήμανση ως Αναγνωσμένο"
                                                    tabindex=-1> <img src="{{ URL::to('/') }}/images/mark-read.png"
                                                        height="25" /></a>
                                                @if (!$alwaysSendReceitForEmails)
                                                    <a href="javascript:$('#sendReceipt{{ $Uid }}').val(0);sendEmailTo({{ $Uid }});chkSubmitForm({{ $Uid }});"
                                                        class="" role="button"
                                                        title="Καταχώριση email χωρίς αποστολή Απόδειξης παραλαβής"> <img
                                                            src="{{ URL::to('/') }}/images/save.ico" height=25 /></a>
                                                @endif
                                                <a href="javascript:$('#sendReceipt{{ $Uid }}').val(1);sendEmailTo({{ $Uid }});chkSubmitForm({{ $Uid }});"
                                                    class="" role="button"
                                                    title="Καταχώριση email και αποστολή Απόδειξης παραλαβής" tabindex=-1>
                                                    <img src="{{ URL::to('/') }}/images/{{ $alwaysSendReceitForEmails ? 'save.ico' : 'receipt.png' }}"
                                                        height="25" /></a>
                                            </div>
                                        </div>


                                        <div class="row bg-success">
                                            <div class="col-md-1 col-sm-1 small text-center">
                                                <strong>Αριθ.<br>Εισερχ.</strong>
                                            </div>
                                            <div id="in_numDiv"
                                                class="col-md-2 col-sm-2 {{ $errors->has('in_num') ? ' has-error' : '' }}">
                                                <input id="in_num" type="text" class="form-control text-center"
                                                    name="in_num" placeholder="in_num"
                                                    value="{{ \Carbon\Carbon::parse($mailMessage->getHeader(HeaderConsts::DATE)->getDateTime())->timezone($timeZone)->format('H:i:s') }}"
                                                    title='3. Αριθμός εισερχομένου εγγράφου'>
                                            </div>
                                            <div class="col-md-1 col-sm-1 small text-center">
                                                <strong>Ημνία<br>Εισερχ.</strong>
                                            </div>
                                            <div id="in_dateDiv"
                                                class="col-md-2 col-sm-2 {{ $errors->has('in_date') ? ' has-error' : '' }}">
                                                <input id="in_date" type="text" class="form-control datepicker text-center"
                                                    name="in_date" placeholder="in_date"
                                                    value="{{ \Carbon\Carbon::parse($mailMessage->getHeader(HeaderConsts::DATE)->getDateTime())->timezone($timeZone)->format('d/m/Y') }}"
                                                    title='5. Χρονολογία εισερχομένου εγγράφου'>
                                            </div>
                                            <div class="col-md-1 col-sm-1 small text-center">
                                                <strong>Τόπος<br>Έκδοσης</strong>
                                            </div>
                                            <div id="in_topos_ekdosisDiv"
                                                class="col-md-5 col-sm-5 {{ $errors->has('in_topos_ekdosis') ? ' has-error' : '' }}">
                                                <input id="in_topos_ekdosis"
                                                    oninput="getValues(this.id, 'in_topos_ekdosis', 'in_topos_ekdosisList', 0)"
                                                    type="text" class="form-control" name="in_topos_ekdosis"
                                                    placeholder="in_topos_ekdosis"
                                                    value="{{ old('in_topos_ekdosis') ? old('in_topos_ekdosis') : $protocol->in_topos_ekdosis ?? '' }}"
                                                    title='4. Τόπος που εκδόθηκε'>
                                                <div id="in_topos_ekdosisList" class="col-md-12 col-sm-12"></div>
                                            </div>
                                        </div>

                                        <div class="row bg-success">
                                            <div class="col-md-6 col-sm-6 ">
                                                <div class="row">
                                                    <div class="col-md-2 col-sm-2 small text-center">
                                                        <strong>Αρχή<br>Έκδοσης</strong>
                                                    </div>
                                                    <div id="in_arxi_ekdosisDiv"
                                                        class="col-md-10 col-sm-10 {{ $errors->has('in_arxi_ekdosis') ? ' has-error' : '' }}">
                                                        <input id="in_arxi_ekdosis"
                                                            oninput="getValues(this.id, 'in_arxi_ekdosis', 'in_arxi_ekdosisList', 0)"
                                                            type="text" class="form-control" name="in_arxi_ekdosis"
                                                            placeholder="in_arxi_ekdosis"
                                                            value="{{ $mailMessage->getHeader(HeaderConsts::FROM)->getAddresses()[0]->getName() }} {{ $mailMessage->getHeader(HeaderConsts::FROM)->getAddresses()[0]->getName()? '<' .$mailMessage->getHeader(HeaderConsts::FROM)->getAddresses()[0]->getEmail() .'>': $mailMessage->getHeader(HeaderConsts::FROM)->getAddresses()[0]->getEmail() }}"
                                                            title='5. Αρχή που το έχει εκδώσει'>
                                                        <div id="in_arxi_ekdosisList" class="col-md-12 col-sm-12"></div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-2 col-sm-2 small text-center form-control-static">
                                                        <strong>Παραλήπτης</strong>
                                                    </div>
                                                    <div id="in_paraliptisDiv"
                                                        class="col-md-10 col-sm-10 {{ $errors->has('in_paraliptis') ? ' has-error' : '' }}">
                                                        <input id="in_paraliptis"
                                                            oninput="getValues(this.id, 'in_paraliptis', 'in_paraliptisList', 0)"
                                                            type="text" class="form-control" name="in_paraliptis"
                                                            placeholder="in_paraliptis" value="{{ Auth::user()->name }}"
                                                            title='7. Διεύθυνση, τμήμα, γραφείο ή πρόσωπο στο οποίο δόθηκε'>
                                                        <div id="in_paraliptisList" class="col-md-12 col-sm-12"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-6 ">
                                                <div class="row">
                                                    <div class="col-md-2 col-sm-2 small text-center form-control-static">
                                                        <strong>Περίληψη</strong>
                                                    </div>
                                                    <div id="in_perilipsiDiv"
                                                        class="col-md-10 col-sm-10 {{ $errors->has('in_perilipsi') ? ' has-error' : '' }}">
                                                        <textarea id="in_perilipsi" type="text" class="form-control"
                                                            name="in_perilipsi" placeholder="in_perilipsi" value=""
                                                            title='6. Περίληψη εισερχομένου εγγράφου'>{{ mb_substr(preg_replace('~^\s+|\s+$~us', '', trim($mailMessage->getTextContent())), 0, 250) }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row ">
                                            <div class="col-md-1 col-sm-1 small text-center form-control-static">
                                                <strong>Διεκπεραίωση</strong>
                                            </div>
                                            <div id="diekperaiosi{{ $Uid }}Div"
                                                class="col-md-3 col-sm-3 {{ $errors->has('diekperaiosi') ? ' has-error' : '' }}">

                                                <select id="diekperaiosi{{ $Uid }}" multiple
                                                    class="form-control selectpicker " style="text-overflow:hidden;"
                                                    name="diekperaiosi[]" title='Διεκπεραίωση - Ενημέρωση'
                                                    data-value="{{ $protocol->diekperaiosi ?? '' }}"
                                                    @if ($forbidenChangeDiekperaiosiSelect) disabled="disabled" @endif>
                                                    <optgroup label="Διεκπεραίωση" data-max-options="1">
                                                        @foreach ($writers_admins as $writer_admin)
                                                            <option value='d{{ $writer_admin->id }}'>
                                                                {{ $writer_admin->name }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                    <optgroup label="Ενημέρωση">
                                                        @foreach ($writers_admins as $writer_admin)
                                                            <option value='e{{ $writer_admin->id }}'>
                                                                {{ $writer_admin->name }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                </select>
                                                <input id="sendEmailTo{{ $Uid }}" name="sendEmailTo" type="hidden"
                                                    value="" />
                                            </div>

                                            @if ($allowUserChangeKeepSelect)
                                                <div class="col-md-1 col-sm-1 small text-center form-control-static">
                                                    <strong>Χρόνος διατήρησης</strong>
                                                </div>
                                                <div class="col-md-3 col-sm-3">
                                                    <select id="keep{{ $Uid }}"
                                                        class="form-control small selectpicker"
                                                        name="keep{{ $Uid }}" title='Χρόνος Διατήρησης'>
                                                    @else
                                                        <div class="col-md-1 col-sm-1 small text-center form-control-static"
                                                            title='Οι ρυθμίσεις δεν επιτρέπουν να αλλάξετε την επιλογή'>
                                                            <strong>Χρόνος διατήρησης</strong>
                                                        </div>
                                                        <div class="col-md-3 col-sm-3"
                                                            title='Οι ρυθμίσεις δεν επιτρέπουν να αλλάξετε την επιλογή'>
                                                            <select id="keep{{ $Uid }}"
                                                                class="form-control small selectpicker" data-value=""
                                                                onchange="this.value = this.getAttribute('data-value');"
                                                                name="keep{{ $Uid }}" title='Χρόνος Διατήρησης'>
                                            @endif
                                            <option value=''></option>
                                            @foreach ($years as $year)
                                                <option value='{{ $year->keep }}'
                                                    title='{{ $year->keep }} {{ $year->keep > 1 ? 'χρόνια' : 'χρόνο' }}'>
                                                    {{ $year->keep }} {{ $year->keep > 1 ? 'χρόνια' : 'χρόνο' }}
                                                </option>
                                            @endforeach
                                            @foreach ($words as $word)
                                                <option value='{{ $word->keep_alt }}' title='{{ $word->keep_alt }}'>
                                                    {{ $word->keep_alt }}</option>
                                            @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <div class="row">
                                                <div class="col-md-3 col-sm-3 small text-center">
                                                    <strong>Απάντηση<br>σε email</strong>
                                                </div>
                                                <div
                                                    class="col-md-9 col-sm-9 {{ $errors->has('reply_to') ? ' has-error' : '' }}">
                                                    <input id="reply_to" type="text" class="form-control" name="reply_to"
                                                        placeholder="reply_to"
                                                        value="{{ $mailMessage->getHeader(HeaderConsts::REPLY_TO) &&$mailMessage->getHeader(HeaderConsts::REPLY_TO)->getRawValue()? $mailMessage->getHeader(HeaderConsts::REPLY_TO)->getAddresses()[0]->getEmail(): $mailMessage->getHeader(HeaderConsts::FROM)->getAddresses()[0]->getEmail() }}"
                                                        title='5. Αρχή που το έχει εκδώσει'>
                                                    <div id="in_arxi_ekdosisList" class="col-md-12 col-sm-12"></div>
                                                </div>
                                            </div>
                                        </div>
                            </div>
                            <div class="row bg-info">
                                <div class="col-md-3 col-sm-3 form-control-static ">Στοιχεία email</div>
                            </div>
                        @else
                            <div class="row">
                                <div class="text-right">
                                    <input id="uid" type="hidden" class="form-control" name="uid"
                                        value="{{ $Uid }}">
                                    <input id="sendReceipt{{ $Uid }}" type="hidden" class="form-control"
                                        name="sendReceipt{{ $Uid }}" value="0">
                                    <input id="in_num" type="hidden" class="form-control text-center" name="in_num"
                                        placeholder="in_num"
                                        value="{{ \Carbon\Carbon::parse($mailMessage->getHeader(HeaderConsts::DATE)->getDateTime())->timezone($timeZone)->format('H:i:s') }}">
                                    <input id="in_date" type="hidden" class="form-control text-center" name="in_date"
                                        placeholder="in_date"
                                        value="{{ \Carbon\Carbon::parse($mailMessage->getHeader(HeaderConsts::DATE)->getDateTime())->timezone($timeZone)->format('d/m/Y') }}">
                                    <input id="thema" type="hidden" class="form-control" name="thema" placeholder="thema"
                                        value="{{ $subject }}">
                                    <a href="{{ URL::to('/') }}/setEmailRead/{{ $Uid }}" class=""
                                        role="button" title="Σήμανση ως Αναγνωσμένο" tabindex=-1> <img
                                            src="{{ URL::to('/') }}/images/mark-read.png" height="25" /></a>
                                    @if (!$alwaysSendReceitForEmails)
                                        <a href="javascript:$('#sendReceipt{{ $Uid }}').val(0);chkSubmitForm({{ $Uid }});"
                                            class="" role="button"
                                            title="Καταχώριση email χωρίς αποστολή Απόδειξης παραλαβής"> <img
                                                src="{{ URL::to('/') }}/images/save.ico" height=25 /></a>
                                    @endif
                                    <a href="javascript:$('#sendReceipt{{ $Uid }}').val(1);chkSubmitForm({{ $Uid }});"
                                        class="" role="button"
                                        title="Καταχώριση email και αποστολή Απόδειξης παραλαβής" tabindex=-1> <img
                                            src="{{ URL::to('/') }}/images/{{ $alwaysSendReceitForEmails ? 'save.ico' : 'receipt.png' }}"
                                            height="25" /></a>
                                </div>
                            </div>
                            <div class="row bg-info">
                                <div class="col-md-3 col-sm-3 form-control-static ">Στοιχεία email</div>
                            </div>
                        @endif

                        <div class="row bg-warning">
                            <div class="form-control-static col-md-1 col-sm-1  "><strong>Από:</strong></div>
                            <div class="form-control-static col-md-8 col-sm-8  ">
                                {{ $mailMessage->getHeader(HeaderConsts::FROM)->getAddresses()[0]->getName() }}
                                {{ $mailMessage->getHeader(HeaderConsts::FROM)->getAddresses()[0]->getName()? '<' .$mailMessage->getHeader(HeaderConsts::FROM)->getAddresses()[0]->getEmail() .'>': $mailMessage->getHeader(HeaderConsts::FROM)->getAddresses()[0]->getEmail() }}
                            </div>
                            <div class="form-control-static col-md-1 col-sm-1 "><strong>Ημνία:</strong></div>
                            <div class="form-control-static col-md-2 col-sm-2 ">
                                {{ \Carbon\Carbon::parse($mailMessage->getHeader(HeaderConsts::DATE)->getDateTime())->timezone($timeZone)->format('d/m/Y H:i:s') }}
                            </div>
                        </div>
                        <div class="row bg-warning ">
                            <div class="form-control-static col-md-1 col-sm-1"><strong>Θέμα:</strong></div>
                            <div class="form-control-static col-md-11 col-sm-11" style="overflow:hidden">
                                <strong>{{ $subject }}</strong>
                            </div>
                        </div>
                        @if ($mailMessage->getHeader(HeaderConsts::TO) && $mailMessage->getHeader(HeaderConsts::TO)->getRawValue())
                            <div class="row bg-warning ">
                                <div class="form-control-static col-md-1 col-sm-1"><strong>Προς:</strong></div>
                                <div class="form-control-static col-md-11 col-sm-11">
                                    @foreach ($mailMessage->getHeader(HeaderConsts::TO)->getAddresses() as $getTo)
                                        {{ $getTo->getName() ?? null }}{{ $getTo->getName() ? '<' . $getTo->getEmail() . '>' : $getTo->getEmail() }}
                                        @if (!$loop->last)
                                            ,&nbsp;
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @if ($mailMessage->getHeader(HeaderConsts::CC) && $mailMessage->getHeader(HeaderConsts::CC)->getRawValue())
                            <div class="row bg-warning ">
                                <div class="form-control-static col-md-1 col-sm-1"><strong>Κοιν:</strong></div>
                                <div class="form-control-static col-md-11 col-sm-11">
                                    @foreach ($mailMessage->getHeader(HeaderConsts::CC)->getAddresses() as $getCc)
                                        {{ $getCc->getName() ?? null }}{{ $getCc->getName() ? '<' . $getCc->getEmail() . '>' : $getCc->getEmail() }}
                                        @if (!$loop->last)
                                            ,&nbsp;
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @if ($mailMessage->getHeader(HeaderConsts::REPLY_TO) && $mailMessage->getHeader(HeaderConsts::REPLY_TO)->getRawValue())
                            <div class="row bg-warning ">
                                <div class="form-control-static col-md-1 col-sm-1"><strong>Απάντηση:</strong></div>
                                <div class="form-control-static col-md-11 col-sm-11">
                                    @foreach ($mailMessage->getHeader(HeaderConsts::REPLY_TO)->getAddresses() as $getReplyTo)
                                        {{ $getReplyTo->getName() ?? null }}{{ $getReplyTo->getName() ? '<' . $getReplyTo->getEmail() . '>' : $getReplyTo->getEmail() }}
                                        @if (!$loop->last)
                                            ,&nbsp;
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @php
                            $uid = $Uid;
                        @endphp
                        @if (array_key_exists($uid, $emailFilePaths) && file_exists('tmp/' . $emailFilePaths[$uid]))
                            <div class="row bg-info">
                                <div class="col-md-3 col-sm-3 form-control-static ">Σώμα email ως HTML</div>
                                <div
                                    class="col-md-4 col-sm-4 col-md-offset-5 col-sm-offset-5 form-control-static text-right">
                                    <a href="{{ asset('tmp/' . $emailFilePaths[$uid]) }}" target="_blank">Προβολή
                                        εξωτερικά</a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12  ">
                                    <iframe id="ifr{{ $Uid }}"
                                        src="{{ asset('tmp/' . $emailFilePaths[$uid]) }}" width="100%" frameBorder="0"
                                        onload="this.style.height=(this.contentWindow.document.body.scrollHeight+10)+'px';">></iframe>
                                </div>
                            </div>
                        @endif
                        @if (strlen($mailMessage->getTextContent()))
                            <div class="row bg-info">
                                <div class="col-md-3 col-sm-3 form-control-static ">Σώμα email ως Text</div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12  small" style="white-space: pre-wrap; overflow: hidden">
                                    {{ $mailMessage->getTextContent() }}</div>
                            </div>
                        @endif
                        @if ($mailMessage->getAttachmentCount())
                            <div class="row bg-warning">
                                <div class="form-control-static col-md-2 col-sm-2"><strong>Συνημμένα:</strong></div>

                                <div class="form-control-static col-md-10 col-sm-10 ">
                                    @foreach ($mailMessage->getAllAttachmentParts() as $key => $attachment)
                                        @php
                                            $filename = $attachment->getFilename();
                                        @endphp
                                        <a href='{{ URL::to('/') }}/viewEmailAttachment/{{ $Uid }}/{{ $key }}'
                                            target="_blank" title='Λήψη {{ $filename }}'>{{ $filename }}</a>
                                        <input type="checkbox" class=""
                                            id="chk{{ $Uid }}-{{ $key }}"
                                            name="chk{{ $Uid }}-{{ $key }}"
                                            title="Αν είναι επιλεγμένο αποθηκεύεται το συνημμένο {{ $filename }}"
                                            checked>
                                        @if (!$loop->last)
                                            , &nbsp;
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        </form>
                </div>
                @endforeach
                @endif

                @if ($aMessage->links())
                    <div class="row">
                        <div class="small text-center">
                            <span class="small">{{ $aMessage->links() }}</span>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
    </div>
    </div>


    <script>
        function getKeep4Fakelos(uid) {
            var fak = $("#fakelos" + uid).val()
            $.get("{{ URL::to('/') }}/getKeep4Fakelos/" + fak, function(data) {
                $("#keep" + uid).attr('data-value', data)
                $("#keep" + uid).val(data).change()
            });
        }

        function chkSubmitForm(uid) {
            // μετράω όλα τα τσεκαρισμένα chekboxes του email με το συγκεκριμμένο uid
            // var n = $('input:checkbox[id^="chk' + uid + '"]:checked').length;
            // αν υπάρχει έστω ένα τσεκαρισμένο ζητάω να συμπληρώσει φακελο Φ'
            if (!$("#fakelos" + uid).val() && $('input:checkbox[id^="chk' + uid + '"]:checked').length > 0) {
                toastr.info(
                    "<center><h4>Ενημέρωση...</h4><hr>Για να καταχωρίσετε email με συνημμένα αρχεία είναι απαραίτητο να επιλέξετε Φάκελο<br>&nbsp;</center>"
                )
                return
            }
            var fakelosRequired = {{ $alwaysShowFakelosInViewEmails ? 'true' : 'false' }}
            if (!$("#fakelos" + uid).val() && fakelosRequired) {
                toastr.info(
                    "<center><h4>Ενημέρωση...</h4><hr>Για να καταχωρίσετε ένα email είναι απαραίτητο να επιλέξετε Φάκελο<br>&nbsp;</center>"
                )
                return
            }
            var alwaysShowFakelosInViewEmails = {{ $alwaysShowFakelosInViewEmails ? 'true' : 'false' }}
            var emailHasAttachments = []
            @foreach ($aMessage as $oMessage)
                @php
                    $chkMessage = ZBateson\MailMimeParser\Message::from($oMessage->getHeader()->raw . $oMessage->getRawBody());
                @endphp
                emailHasAttachments[{{ $Uid }}] = {{ $chkMessage->getAttachmentCount() ? 'true' : 'false' }}
            @endforeach

            if (emailHasAttachments[uid] || alwaysShowFakelosInViewEmails) {
                if (!formValidate(uid)) return
            }
            var thema = $("#frm" + uid).find('input[name="thema"]').val().trim()
            var in_num = $("#frm" + uid).find('input[name="in_num"]').val().trim()
            var in_date = $("#frm" + uid).find('input[name="in_date"]').val().trim()

            $.ajax({
                type: "POST",
                url: '{{ route('checkSameEmail') }}',
                dataType: 'JSON',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'thema': thema,
                    'in_num': in_num,
                    'in_date': in_date
                },
                success: function(response) {
                    msgStr = ''
                    if (response['thema'] > 0) msgStr += '<li>Θέμα</li>'
                    if (response['in_num'] > 0) msgStr += '<li>Αρ. & Ημνια Εισερχομένου</li>'
                    if (msgStr) {
                        var html =
                            "<center><button type='button' id='confirmRevertYes' class='btn btn-primary'>Ναί</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' id='confirmRevertNo' class='btn btn-primary'>Όχι</button></center></p>"
                        msg =
                            "<center><h4>Ενημέρωση ...</h4><hr></center>Υπάρχει καταχωρισμένο πρωτόκολλο με ίδιο<ul>" +
                            msgStr + "</ul>Θέλετε ωστόσο να προχωρήσετε;<br>&nbsp;"
                        var toast = toastr.info(html, msg);
                        toast.delegate('#confirmRevertYes', 'click', function() {
                            $("#frm" + uid).submit()
                            toast.remove();
                        });
                        toast.delegate('#confirmRevertNo', 'click', function() {
                            toast.remove();
                        });
                    } else {
                        $("#frm" + uid).submit()
                    }
                },
                error: function(data) {
                    toastr.error(
                        "<center><h4>Λάθος !!!</h4><hr></center>Κάποιο λάθος συνέβη!<br>&nbsp;</center>")
                }
            });
        }

        function getValues(id, field, divId, multi) {
            @if (!$allowListValuesMatchingInput)
                return
            @endif
            var searchStr = $('#' + id).val().trim()
            if (searchStr == '') {
                clearDiv(divId)
                return
            }
            var term = extractLast(searchStr)
            if (term == '') {
                clearDiv(divId)
                return
            }

            $.ajax({
                url: '{{ URL::to('/') }}/getValues/' + term + '/' + field + '/' + id + '/' + divId + '/' +
                    multi,
                success: function(data) {
                    if (data) {
                        var front = '<ul id="' + id +
                            'Ul" class="dropdown-menu" style="display:block; position:absolute; max-height:10em; max-width: 100%; overflow:auto" >'
                        var end = '</ul>'
                        $('#' + divId).html(front + data + end)
                        $('#' + divId).show()
                    } else {
                        $('#' + divId).empty()
                        $('#' + divId).hide()
                    }
                }
            })
        }

        function clearDiv(divId) {
            $('#' + divId).empty()
            $('#' + divId).hide()
        }

        function split(val) {
            return val.split(/\s*,\s*/);
        }

        function extractLast(term) {
            return split(term).pop();
        }

        function appendValue(id, value, divId, multi) {
            if (multi == 0) {
                $('#' + id).val(value)
            }
            if (multi == 1) {
                var terms = split($('#' + id).val());
                terms.pop()
                if (!terms.includes(value)) {
                    terms.push(value)
                }
                terms.push('')
                $('#' + id).val(terms.join(', '))
            }
            $('#' + id).focus()
            $('#' + divId).empty()
            $('#' + divId).hide()
        }

        function sendEmailTo(id) {

            var oldId = $('#diekperaiosi' + id).attr('data-value')
            if ($('#diekperaiosi' + id).val()) {
                var newId = $('#diekperaiosi' + id).val().join(',')
            }
            if (!newId) {
                $('#sendEmailTo' + id).val('')
                return
            }
            if (newId == oldId) {
                $('#sendEmailTo' + id).val('')
                return
            }
            $('#sendEmailTo' + id).val(newId)
        }

        function formValidate(uid) {
            var validate = {{ $protocolValidate ? 'true' : 'false' }}

            var thema = $("#frm" + uid).find('input[name="thema"]').val().trim()
            var fakelos = $('#fakelos' + uid).val()
            var in_num = $("#frm" + uid).find('input[name="in_num"]').val().trim()
            var in_date = $("#frm" + uid).find('input[name="in_date"]').val().trim()
            var in_topos_ekdosis = $("#frm" + uid).find('input[name="in_topos_ekdosis"]').val().trim()
            var in_arxi_ekdosis = $("#frm" + uid).find('input[name="in_arxi_ekdosis"]').val().trim()
            var in_paraliptis = $("#frm" + uid).find('input[name="in_paraliptis"]').val().trim()
            var in_perilipsi = $("#frm" + uid).find('textarea[name="in_perilipsi"]').val().trim()
            if ($('#diekperaiosi' + uid).val()) {
                var diekperaiosi = $('#diekperaiosi' + uid).val().join(',').trim()
            }
            var msg = []

            if (validate) {
                msgStr = ''
                if (!thema) {
                    msgStr += "<li>το θέμα</li>"
                    $("#frm" + uid + ' div#themaDiv').addClass('has-error')
                }
                if (!in_date && (in_num || in_topos_ekdosis || in_arxi_ekdosis)) {
                    msgStr += "<li>την ημ/νια έκδοσης</li>"
                    $("#frm" + uid + ' div#in_dateDiv').addClass('has-error')
                }
                if (!in_topos_ekdosis && (in_num || in_date || in_arxi_ekdosis)) {
                    msgStr += "<li>τον τόπο έκδοσης</li>"
                    $("#frm" + uid + ' div#in_topos_ekdosisDiv').addClass('has-error')
                }
                if (!in_arxi_ekdosis && (in_num || in_date || in_topos_ekdosis)) {
                    msgStr += "<li>την αρχή έκδοσης</li>"
                    $("#frm" + uid + ' div#in_arxi_ekdosisDiv').addClass('has-error')
                }
                if (!in_paraliptis && (in_num || in_date || in_topos_ekdosis || in_arxi_ekdosis)) {
                    msgStr += "<li>τον παραλήπτη</li>"
                    $("#frm" + uid + ' div#in_paraliptisDiv').addClass('has-error')
                }
                if (msgStr) msg.push("Συμπληρώστε<ul>" + msgStr + "</ul>")
            }

            var chkerr = false
            if (in_date && !/^\d{2}[/]\d{2}[/]\d{4}$/.test(in_date)) {
                chkerr = true
                $("#frm" + uid + ' div#in_dateDiv').addClass('has-error')
            }
            if (chkerr) msg.push('Η ημερομηνία πρέπει να<br>έχει τη μορφή "ηη/μμ/εεεε".')

            if (msg.length) {
                var show = ''
                msg.forEach(function(error) {
                    show += '<li>' + error + '</li>'
                })
                toastr.error("<center><h4>Λάθος !!!</h4></center><hr><ul>" + show + "</ul><br> &nbsp;")
                return false
            }
            return true
        }
    </script>

@endsection
