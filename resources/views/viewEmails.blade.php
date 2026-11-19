
@extends('layouts.app')
 
@section('content')
    @php
        use ZBateson\MailMimeParser\Header\HeaderConsts;
 
        // Βοηθητικές συναρτήσεις (null-safe) για διευθύνσεις
        $fmtAddr = function ($addr) {
            if (!$addr) {
                return '';
            }
            return $addr->getName() ? $addr->getName() . ' <' . $addr->getEmail() . '>' : $addr->getEmail();
        };
        $fmtAddrList = function ($header) use ($fmtAddr) {
            if (!$header || !$header->getRawValue()) {
                return '';
            }
            return implode(', ', array_map($fmtAddr, $header->getAddresses()));
        };
 
        $pageFrom = ($aMessage->currentPage() - 1) * $aMessage->perPage() + 1;
        $pageTo = min($aMessage->currentPage() * $aMessage->perPage(), $aMessageNum);
        $bannerClass = !$aMessageCount ? 'bg-info' : ($aMessageCount < $aMessageNum ? 'bg-warning' : 'bg-success');
        $orderText = $emailFetchOrderDesc ? 'φθίνουσα' : 'αύξουσα';
    @endphp
 
    <div class="{{ $wideListProtocol ? 'container-fluid' : 'container' }}">
        <div class="row">
            <div class="col-md-12 col-sm-12 ">
                <div class="panel panel-default">
                    <div class="panel-heading h1 text-center">Πρωτοκόλληση εισερχομένων email</div>
                    <div class="panel-body ">
                        <div class="panel panel-default col-md-12 col-sm-12  ">
 
                            @if ($aMessage->hasPages())
                                <div class="row">
                                    <div class="small text-center">
                                        <span class="small">{{ $aMessage->links() }}</span>
                                    </div>
                                </div>
                            @endif
 
                            {{-- ΜΠΑΡΑ ΚΑΤΑΣΤΑΣΗΣ --}}
                            <div class="row {{ $bannerClass }}">
                                <div
                                    class="form-control-static col-md-10 col-sm-10 col-md-offset-1 col-sm-offset-1 text-center">
                                    @if ($aMessageCount)
                                        @if ($aMessageCount < $aMessageNum)
                                            <strong>{{ $defaultImapEmail }}</strong>:
                                            {{ config('imap.accounts.' . $defaultImapEmail . '.username') }} -
                                            Εμφανίζονται τα
                                            <strong>{{ $pageFrom }}</strong>
                                            έως
                                            <strong>{{ $pageTo }}</strong>
                                            από <strong>{{ $aMessageNum }}</strong> εισερχόμενα emails -
                                            ταξινόμηση <strong>{{ $orderText }}</strong>
                                        @else
                                            <strong>{{ $defaultImapEmail }} - Εισερχόμενα emails:
                                                {{ count($aMessage) }} - ταξινόμηση {{ $orderText }}</strong>
                                        @endif
                                    @else
                                        <strong>{{ $defaultImapEmail }} - Δεν υπάρχουν εισερχόμενα emails</strong>
                                    @endif
                                </div>
                                <div class="form-control-static col-md-1 col-sm-1 text-right">
                                    <a href="{{ URL::to(config('landing-page.page.' . auth()->user()->role_id)) }}"
                                        class="active" role="button" title="Λίστα Πρωτοκόλλου"> <img
                                            src="{{ URL::to('/') }}/images/protocol.png" height=25 /></a>
                                </div>
                            </div>
                        </div>
 
                        @if ($aMessageCount)
                            @foreach ($aSortedMessage as $oMessage)
                                @php
                                    $Uid = $oMessage->getUid();
                                    $mailMessage = \ZBateson\MailMimeParser\Message::from(
                                        $oMessage->getHeader()->raw . $oMessage->getRawBody(),
                                        true,
                                    );
 
                                    // Το old() ισχύει μόνο για τη φόρμα του email που υποβλήθηκε
                                    $isThis = (string) old('uid') === (string) $Uid;
                                    $position = $pageFrom + $loop->iteration - 1;
                                    $isFirst = $loop->first;
 
                                    $subject = (string) $mailMessage->getHeaderValue(HeaderConsts::SUBJECT);
 
                                    $fromHeader = $mailMessage->getHeader(HeaderConsts::FROM);
                                    $fromAddr = $fromHeader ? $fromHeader->getAddresses()[0] ?? null : null;
                                    $fromEmail = $fromAddr ? $fromAddr->getEmail() : '';
                                    $fromDisplay = $fmtAddr($fromAddr);
 
                                    $dateHeader = $mailMessage->getHeader(HeaderConsts::DATE);
                                    $dateObj = $dateHeader ? $dateHeader->getDateTime() : null;
                                    $carbon = $dateObj ? \Carbon\Carbon::parse($dateObj)->timezone($timeZone) : null;
                                    $inTime = $carbon ? $carbon->format('H:i:s') : '';
                                    $inDate = $carbon ? $carbon->format('d/m/Y') : '';
                                    $dateFull = $carbon ? $carbon->format('d/m/Y H:i:s') : '';
 
                                    $replyHeader = $mailMessage->getHeader(HeaderConsts::REPLY_TO);
                                    $replyAddr =
                                        $replyHeader && $replyHeader->getRawValue()
                                            ? $replyHeader->getAddresses()[0] ?? null
                                            : null;
                                    $replyToEmail = $replyAddr ? $replyAddr->getEmail() : $fromEmail;
 
                                    $toList = $fmtAddrList($mailMessage->getHeader(HeaderConsts::TO));
                                    $ccList = $fmtAddrList($mailMessage->getHeader(HeaderConsts::CC));
                                    $replyList = $fmtAddrList($replyHeader);
 
                                    $textContent = (string) $mailMessage->getTextContent();
                                    $attachmentCount = $mailMessage->getAttachmentCount();
                                    $showForm = $attachmentCount || $alwaysShowFakelosInViewEmails;
 
                                    $summary = mb_substr(
                                        preg_replace('~^\s+|\s+$~us', '', trim($textContent)) ?? '',
                                        0,
                                        250,
                                    );
                                    $htmlFile = $emailFilePaths[$Uid] ?? null;
                                @endphp
 
                                <div class="panel panel-default col-md-12 col-sm-12  ">
                                    <form name="frm{{ $Uid }}" id="frm{{ $Uid }}" class="form-horizontal"
                                        role="form" method="POST" action="{{ url('/') }}/storeFromEmail"
                                        data-has-attachments="{{ $attachmentCount ? 1 : 0 }}">
                                        {{ csrf_field() }}
                                        <input id="uid{{ $Uid }}" type="hidden" name="uid"
                                            value="{{ $Uid }}">
                                        <input id="sendReceipt{{ $Uid }}" type="hidden"
                                            name="sendReceipt{{ $Uid }}" value="0">
 
                                        <div class="row bg-primary">
                                            <div class="col-md-12 col-sm-12 form-control-static strong ">
                                                {{ $position }} από {{ $aMessageNum }}</div>
                                        </div>
 
                                        {{-- ΠΕΔΙΑ ΦΟΡΜΑΣ --}}
                                        @if ($showForm)
                                            {{-- ΠΡΩΤΗ ΓΡΑΜΜΗ --}}
                                            <div class="row ">
                                                {{-- ΦΑΚΕΛΟΣ --}}
                                                <div class="col-md-1 col-sm-1 form-control-static small text-center">
                                                    <strong>Φάκελος</strong>
                                                </div>
                                                <div id="fakelos{{ $Uid }}Div"
                                                    class="col-md-2 col-sm-2 {{ $isThis && $errors->has('fakelos') ? ' has-error' : '' }}">
                                                    <select id="fakelos{{ $Uid }}"
                                                        onchange='getKeep4Fakelos({{ $Uid }})'
                                                        class="form-control selectpicker" data-live-search="true"
                                                        data-live-search-normalize="true"
                                                        name="fakelos{{ $Uid }}" title='13. Φάκελος αρχείου'
                                                        @if ($isFirst) autofocus @endif>
                                                        <option value=''></option>
                                                        @foreach ($fakeloi as $fakelos)
                                                            <option value='{{ $fakelos['fakelos'] }}'
                                                                title='{{ $fakelos['fakelos'] }} - {{ $fakelos['describe'] }}'
                                                                style="white-space: pre-wrap; width: 500px;"
                                                                >{{ $fakelos['fakelos'] }} - {{ $fakelos['describe'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                {{-- ΘΕΜΑ --}}
                                                <div class="col-md-1 col-sm-1 form-control-static small text-center">
                                                    <strong>Θέμα</strong>
                                                </div>
                                                <div id="themaDiv{{ $Uid }}"
                                                    class="col-md-6 col-sm-6 middle {{ $isThis && $errors->has('thema') ? ' has-error' : '' }}">
                                                    <input id="thema{{ $Uid }}"
                                                        oninput="getValues(this.id, 'thema', 'themaList{{ $Uid }}', 0)"
                                                        type="text" class="form-control" name="thema"
                                                        placeholder="thema" value="{{ $subject }}" title='Θέμα'>
                                                    <div id="themaList{{ $Uid }}" class="col-md-12 col-sm-12">
                                                    </div>
                                                </div>
                                                {{-- ΚΟΥΜΠΙΑ --}}
                                                <div class="col-md-2 col-sm-2 text-right">
                                                    <a href="{{ URL::to('/') }}/setEmailRead/{{ $Uid }}"
                                                        class="" role="button" title="Σήμανση ως Αναγνωσμένο"
                                                        tabindex=-1> <img
                                                            src="{{ URL::to('/') }}/images/mark-read.png"
                                                            height="25" /></a>
                                                    @if (!$alwaysSendReceitForEmails)
                                                        <a href="javascript:$('#sendReceipt{{ $Uid }}').val(0);sendEmailTo({{ $Uid }});chkSubmitForm({{ $Uid }});void(0);"
                                                            class="" role="button"
                                                            title="Καταχώριση email χωρίς αποστολή Απόδειξης παραλαβής">
                                                            <img src="{{ URL::to('/') }}/images/save.ico"
                                                                height=25 /></a>
                                                    @endif
                                                    <a href="javascript:$('#sendReceipt{{ $Uid }}').val(1);sendEmailTo({{ $Uid }});chkSubmitForm({{ $Uid }});void(0);"
                                                        class="" role="button"
                                                        title="Καταχώριση email και αποστολή Απόδειξης παραλαβής"
                                                        tabindex=-1>
                                                        <img src="{{ URL::to('/') }}/images/{{ $alwaysSendReceitForEmails ? 'save.ico' : 'receipt.png' }}"
                                                            height="25" /></a>
                                                </div>
                                            </div>
                                            {{-- ΤΕΛΟΣ ΠΡΩΤΗ ΓΡΑΜΜΗ --}}
 
                                            {{-- ΔΕΥΤΕΡΗ ΓΡΑΜΜΗ --}}
                                            <div class="row bg-success">
                                                {{-- ΑΡΙΘΜΟΣ ΕΙΣΕΡΧΟΜΕΝΟΥ --}}
                                                <div class="col-md-1 col-sm-1 small text-center">
                                                    <strong>Αριθ.<br>Εισερχ.</strong>
                                                </div>
                                                <div id="in_numDiv{{ $Uid }}"
                                                    class="col-md-2 col-sm-2 {{ $isThis && $errors->has('in_num') ? ' has-error' : '' }}">
                                                    <input id="in_num{{ $Uid }}" type="text"
                                                        class="form-control text-center" name="in_num"
                                                        placeholder="in_num" value="{{ $inTime }}"
                                                        title='3. Αριθμός εισερχομένου εγγράφου'>
                                                </div>
                                                {{-- ΗΜΝΙΑ ΕΙΣΕΡΧΟΜΕΝΟΥ --}}
                                                <div class="col-md-1 col-sm-1 small text-center">
                                                    <strong>Ημνία<br>Εισερχ.</strong>
                                                </div>
                                                <div id="in_dateDiv{{ $Uid }}"
                                                    class="col-md-2 col-sm-2 {{ $isThis && $errors->has('in_date') ? ' has-error' : '' }}">
                                                    <input id="in_date{{ $Uid }}" type="text"
                                                        class="form-control datepicker text-center" name="in_date"
                                                        placeholder="in_date" value="{{ $inDate }}"
                                                        title='5. Χρονολογία εισερχομένου εγγράφου'>
                                                </div>
                                                {{-- ΤΟΠΟΣ ΕΚΔΟΣΗΣ --}}
                                                <div class="col-md-1 col-sm-1 small text-center">
                                                    <strong>Τόπος<br>Έκδοσης</strong>
                                                </div>
                                                <div id="in_topos_ekdosisDiv{{ $Uid }}"
                                                    class="col-md-5 col-sm-5 {{ $isThis && $errors->has('in_topos_ekdosis') ? ' has-error' : '' }}">
                                                    <input id="in_topos_ekdosis{{ $Uid }}"
                                                        oninput="getValues(this.id, 'in_topos_ekdosis', 'in_topos_ekdosisList{{ $Uid }}', 0)"
                                                        type="text" class="form-control" name="in_topos_ekdosis"
                                                        placeholder="in_topos_ekdosis"
                                                        value="{{ $isThis && old('in_topos_ekdosis') ? old('in_topos_ekdosis') : $protocol->in_topos_ekdosis ?? '' }}"
                                                        title='4. Τόπος που εκδόθηκε'>
                                                    <div id="in_topos_ekdosisList{{ $Uid }}"
                                                        class="col-md-12 col-sm-12"></div>
                                                </div>
                                            </div>
                                            {{-- ΤΕΛΟΣ ΔΕΥΤΕΡΗ ΓΡΑΜΜΗ --}}
 
                                            {{-- ΤΡΙΤΗ ΓΡΑΜΜΗ --}}
                                            <div class="row bg-success">
                                                {{-- ΑΡΧΗ ΕΚΔΟΣΗΣ --}}
                                                <div class="col-md-6 col-sm-6 ">
                                                    <div class="row">
                                                        <div class="col-md-2 col-sm-2 small text-center">
                                                            <strong>Αρχή<br>Έκδοσης</strong>
                                                        </div>
                                                        <div id="in_arxi_ekdosisDiv{{ $Uid }}"
                                                            class="col-md-10 col-sm-10 {{ $isThis && $errors->has('in_arxi_ekdosis') ? ' has-error' : '' }}">
                                                            <input id="in_arxi_ekdosis{{ $Uid }}"
                                                                oninput="getValues(this.id, 'in_arxi_ekdosis', 'in_arxi_ekdosisList{{ $Uid }}', 0)"
                                                                type="text" class="form-control"
                                                                name="in_arxi_ekdosis" placeholder="in_arxi_ekdosis"
                                                                value="{{ $fromDisplay }}"
                                                                title='5. Αρχή που το έχει εκδώσει'>
                                                            <div id="in_arxi_ekdosisList{{ $Uid }}"
                                                                class="col-md-12 col-sm-12"></div>
                                                        </div>
                                                    </div>
                                                    {{-- ΠΑΡΑΛΗΠΤΗΣ --}}
                                                    <div class="row">
                                                        <div
                                                            class="col-md-2 col-sm-2 small text-center form-control-static">
                                                            <strong>Παραλήπτης</strong>
                                                        </div>
                                                        <div id="in_paraliptisDiv{{ $Uid }}"
                                                            class="col-md-10 col-sm-10 {{ $isThis && $errors->has('in_paraliptis') ? ' has-error' : '' }}">
                                                            <input id="in_paraliptis{{ $Uid }}"
                                                                oninput="getValues(this.id, 'in_paraliptis', 'in_paraliptisList{{ $Uid }}', 0)"
                                                                type="text" class="form-control"
                                                                name="in_paraliptis" placeholder="in_paraliptis"
                                                                value="{{ Auth::user()->name }}"
                                                                title='7. Διεύθυνση, τμήμα, γραφείο ή πρόσωπο στο οποίο δόθηκε'>
                                                            <div id="in_paraliptisList{{ $Uid }}"
                                                                class="col-md-12 col-sm-12"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- ΠΕΡΙΛΗΨΗ --}}
                                                <div class="col-md-6 col-sm-6 ">
                                                    <div class="row">
                                                        <div
                                                            class="col-md-2 col-sm-2 small text-center form-control-static">
                                                            <strong>Περίληψη</strong>
                                                        </div>
                                                        <div id="in_perilipsiDiv{{ $Uid }}"
                                                            class="col-md-10 col-sm-10 {{ $isThis && $errors->has('in_perilipsi') ? ' has-error' : '' }}">
                                                            <textarea id="in_perilipsi{{ $Uid }}" class="form-control" name="in_perilipsi" placeholder="in_perilipsi"
                                                                title='6. Περίληψη εισερχομένου εγγράφου'>{{ $summary }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- ΤΕΛΟΣ ΤΡΙΤΗ ΓΡΑΜΜΗ --}}
 
                                            {{-- ΤΕΤΑΡΤΗ ΓΡΑΜΜΗ --}}
                                            <div class="row ">
                                                {{-- ΔΙΕΚΠΕΡΑΙΩΣΗ --}}
                                                <div class="col-md-1 col-sm-1 small text-center form-control-static">
                                                    <strong>Διεκπεραίωση</strong>
                                                </div>
                                                <div id="diekperaiosi{{ $Uid }}Div"
                                                    class="col-md-2 col-sm-2 {{ $isThis && $errors->has('diekperaiosi') ? ' has-error' : '' }}">
 
                                                    <select id="diekperaiosi{{ $Uid }}" multiple
                                                        class="form-control selectpicker "
                                                        style="text-overflow:ellipsis;" name="diekperaiosi[]"
                                                        title='Διεκπεραίωση - Ενημέρωση'
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
                                                    <input id="sendEmailTo{{ $Uid }}" name="sendEmailTo"
                                                        type="hidden" value="" />
                                                </div>
                                                {{-- ΔΙΕΚΠΕΡΑΙΩΣΗ ΕΩΣ ΗΜΝΙΑ --}}
                                                <div class="col-md-1 col-sm-1 small text-center">
                                                    <strong>Διεκπεραίωση<br>έως Ημνία</strong>
                                                </div>
 
                                                <div id="diekp_eosDiv{{ $Uid }}"
                                                    class="col-md-2 col-sm-2 {{ $isThis && $errors->has('diekp_eos') ? ' has-error' : '' }}">
                                                    <input id="diekp_eos{{ $Uid }}" type="text"
                                                        class="form-control @if (!$forbidenChangeDiekperaiosiSelect) datepicker @endif text-center inout"
                                                        name="diekp_eos" placeholder="diekp_eos"
                                                        value="{{ $isThis && old('diekp_eos') ? old('diekp_eos') : '' }}"
                                                        title='Διεκπεραίωση έως την Ημνία'
                                                        @if ($forbidenChangeDiekperaiosiSelect) readonly @endif>
                                                </div>
                                                {{-- ΧΡΟΝΟΣ ΔΙΑΤΗΡΗΣΗΣ --}}
                                                <div class="col-md-1 col-sm-1 small text-center form-control-static"
                                                    @if (!$allowUserChangeKeepSelect) title='Οι ρυθμίσεις δεν επιτρέπουν να αλλάξετε την επιλογή' @endif>
                                                    <strong>Χρόνος διατήρησης</strong>
                                                </div>
                                                <div class="col-md-2 col-sm-2">
                                                    <select id="keep{{ $Uid }}"
                                                        class="form-control small selectpicker"
                                                        name="keep{{ $Uid }}"
                                                        @if ($allowUserChangeKeepSelect) title='Χρόνος Διατήρησης'
                                                        @else
                                                            data-value=""
                                                            onchange="this.value = this.getAttribute('data-value');"
                                                            title='Δεν επιτρέπεται αλλαγή' @endif>
                                                        <option value=''></option>
                                                        @foreach ($years as $year)
                                                            <option value='{{ $year->keep }}'
                                                                title='{{ $year->keep }} {{ $year->keep > 1 ? 'χρόνια' : 'χρόνο' }}'>
                                                                {{ $year->keep }}
                                                                {{ $year->keep > 1 ? 'χρόνια' : 'χρόνο' }}
                                                            </option>
                                                        @endforeach
                                                        @foreach ($words as $word)
                                                            <option value='{{ $word->keep_alt }}'
                                                                title='{{ $word->keep_alt }}'>
                                                                {{ $word->keep_alt }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
 
                                                {{-- ΑΠΑΝΤΗΣΗ ΣΕ EMAIL --}}
                                                <div class="col-md-3 col-sm-3">
                                                    <div class="row">
                                                        <div class="col-md-3 col-sm-3 small text-center">
                                                            <strong>Απάντηση<br>σε email</strong>
                                                        </div>
                                                        <div
                                                            class="col-md-9 col-sm-9 {{ $isThis && $errors->has('reply_to') ? ' has-error' : '' }}">
                                                            <input id="reply_to{{ $Uid }}" type="text"
                                                                class="form-control" name="reply_to"
                                                                placeholder="reply_to" value="{{ $replyToEmail }}"
                                                                title='Απάντηση σε email'>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- ΤΕΛΟΣ ΤΕΤΑΡΤΗ ΓΡΑΜΜΗ --}}
                                        @else
                                            <div class="row">
                                                <div class="text-right">
                                                    <input id="in_num{{ $Uid }}" type="hidden"
                                                        class="form-control text-center" name="in_num"
                                                        placeholder="in_num" value="{{ $inTime }}">
                                                    <input id="in_date{{ $Uid }}" type="hidden"
                                                        class="form-control text-center" name="in_date"
                                                        placeholder="in_date" value="{{ $inDate }}">
                                                    <input id="thema{{ $Uid }}" type="hidden"
                                                        class="form-control" name="thema" placeholder="thema"
                                                        value="{{ $subject }}">
                                                    <a href="{{ URL::to('/') }}/setEmailRead/{{ $Uid }}"
                                                        class="" role="button" title="Σήμανση ως Αναγνωσμένο"
                                                        tabindex=-1> <img
                                                            src="{{ URL::to('/') }}/images/mark-read.png"
                                                            height="25" /></a>
                                                    @if (!$alwaysSendReceitForEmails)
                                                        <a href="javascript:$('#sendReceipt{{ $Uid }}').val(0);chkSubmitForm({{ $Uid }});void(0);"
                                                            class="" role="button"
                                                            title="Καταχώριση email χωρίς αποστολή Απόδειξης παραλαβής">
                                                            <img src="{{ URL::to('/') }}/images/save.ico"
                                                                height=25 /></a>
                                                    @endif
                                                    <a href="javascript:$('#sendReceipt{{ $Uid }}').val(1);chkSubmitForm({{ $Uid }});void(0);"
                                                        class="" role="button"
                                                        title="Καταχώριση email και αποστολή Απόδειξης παραλαβής"
                                                        tabindex=-1> <img
                                                            src="{{ URL::to('/') }}/images/{{ $alwaysSendReceitForEmails ? 'save.ico' : 'receipt.png' }}"
                                                            height="25" /></a>
                                                </div>
                                            </div>
                                        @endif
 
                                        {{-- ΠΡΟΒΟΛΗ ΤΩΝ EMAIL --}}
                                        <div class="row bg-info">
                                            <div class="col-md-3 col-sm-3 form-control-static ">Στοιχεία email</div>
                                        </div>
 
                                        <div class="row bg-warning">
                                            <div class="form-control-static col-md-1 col-sm-1  "><strong>Από:</strong>
                                            </div>
                                            <div class="form-control-static col-md-8 col-sm-8  ">
                                                {{ $fromDisplay }}
                                            </div>
                                            <div class="form-control-static col-md-1 col-sm-1 "><strong>Ημνία:</strong>
                                            </div>
                                            <div class="form-control-static col-md-2 col-sm-2 ">
                                                {{ $dateFull }}
                                            </div>
                                        </div>
                                        <div class="row bg-warning ">
                                            <div class="form-control-static col-md-1 col-sm-1"><strong>Θέμα:</strong>
                                            </div>
                                            <div class="form-control-static col-md-11 col-sm-11"
                                                style="overflow:hidden">
                                                <strong>{{ $subject }}</strong>
                                            </div>
                                        </div>
                                        @if ($toList !== '')
                                            <div class="row bg-warning ">
                                                <div class="form-control-static col-md-1 col-sm-1">
                                                    <strong>Προς:</strong>
                                                </div>
                                                <div class="form-control-static col-md-11 col-sm-11">{{ $toList }}
                                                </div>
                                            </div>
                                        @endif
                                        @if ($ccList !== '')
                                            <div class="row bg-warning ">
                                                <div class="form-control-static col-md-1 col-sm-1">
                                                    <strong>Κοιν:</strong>
                                                </div>
                                                <div class="form-control-static col-md-11 col-sm-11">{{ $ccList }}
                                                </div>
                                            </div>
                                        @endif
                                        @if ($replyList !== '')
                                            <div class="row bg-warning ">
                                                <div class="form-control-static col-md-1 col-sm-1">
                                                    <strong>Απάντηση:</strong>
                                                </div>
                                                <div class="form-control-static col-md-11 col-sm-11">
                                                    {{ $replyList }}</div>
                                            </div>
                                        @endif
 
                                        @if ($htmlFile && file_exists(public_path('tmp/' . $htmlFile)))
                                            <div class="row bg-info">
                                                <div class="col-md-3 col-sm-3 form-control-static ">Σώμα email ως HTML
                                                </div>
                                                <div
                                                    class="col-md-4 col-sm-4 col-md-offset-5 col-sm-offset-5 form-control-static text-right">
                                                    <a href="{{ asset('tmp/' . $htmlFile) }}"
                                                        target="_blank" rel="noopener noreferrer">Προβολή
                                                        εξωτερικά</a>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12  ">
                                                    {{-- sandbox χωρίς allow-scripts: δεν εκτελείται JavaScript από το email --}}
                                                    <iframe id="ifr{{ $Uid }}" sandbox="allow-same-origin"
                                                        src="{{ asset('tmp/' . $htmlFile) }}" width="100%"
                                                        frameBorder="0"
                                                        onload="this.style.height=(this.contentWindow.document.body.scrollHeight+10)+'px';"></iframe>
                                                </div>
                                            </div>
                                        @endif
                                        @if (strlen($textContent))
                                            <div class="row bg-info">
                                                <div class="col-md-3 col-sm-3 form-control-static ">Σώμα email ως Text
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12  small"
                                                    style="white-space: pre-wrap; overflow: hidden">
                                                    {{ $textContent }}</div>
                                            </div>
                                        @endif
                                        @if ($attachmentCount)
                                            <div class="row bg-warning">
                                                <div class="form-control-static col-md-2 col-sm-2">
                                                    <strong>Συνημμένα:</strong>
                                                </div>
 
                                                <div class="form-control-static col-md-10 col-sm-10 ">
                                                    @foreach ($mailMessage->getAllAttachmentParts() as $key => $attachment)
                                                        @php
                                                            $filename = (string) $attachment->getFilename();
                                                        @endphp
                                                        <a href='{{ URL::to('/') }}/viewEmailAttachment/{{ $Uid }}/{{ $key }}'
                                                            target="_blank" rel="noopener noreferrer"
                                                            title='Λήψη {{ $filename }}'>{{ $filename }}</a>
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
                                {{-- ΕΠΙΣΤΡΟΦΗ ΚΟΡΥΦΗ --}}
                                <div class='row'>
                                    <div class="col-md-12 col-sm-12 text-center">
                                        <a href="#">&#8686; Στην κορυφή &#8686;</a>
                                    </div>
                                </div>
                            @endforeach
                        @endif
 
                        @if ($aMessage->hasPages())
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
        var alwaysShowFakelosInViewEmails = {{ $alwaysShowFakelosInViewEmails ? 'true' : 'false' }}
        var allowListValuesMatchingInput = {{ $allowListValuesMatchingInput ? 'true' : 'false' }}
        var protocolValidate = {{ $protocolValidate ? 'true' : 'false' }}
 
        function getKeep4Fakelos(uid) {
            var fak = $("#fakelos" + uid).val()
            if (!fak) return
            $.get("{{ URL::to('/') }}/getKeep4Fakelos/" + encodeURIComponent(fak), function(data) {
                var $keep = $("#keep" + uid)
                $keep.attr('data-value', data)
                $keep.val(data)
                if ($keep.selectpicker) $keep.selectpicker('refresh')
                $keep.trigger('change')
            });
        }
 
        function submitForm($form) {
            if ($form.data('submitted')) return
            $form.data('submitted', true)
            // απενεργοποίηση των εικονιδίων αποθήκευσης για αποφυγή διπλής υποβολής
            $form.find('a[role="button"]').css({
                'pointer-events': 'none',
                'opacity': 0.5
            })
            $form.submit()
        }
 
        function chkSubmitForm(uid) {
            var $form = $("#frm" + uid)
            if ($form.data('busy') || $form.data('submitted')) return
 
            // αν υπάρχει έστω ένα τσεκαρισμένο συνημμένο ζητάω να συμπληρώσει φάκελο
            var checkedAttachments = $form.find('input:checkbox[id^="chk' + uid + '-"]:checked').length
            if (!$("#fakelos" + uid).val() && checkedAttachments > 0) {
                toastr.info(
                    "<center><h4>Ενημέρωση...</h4><hr>Για να καταχωρίσετε email με συνημμένα αρχεία είναι απαραίτητο να επιλέξετε Φάκελο<br>&nbsp;</center>"
                )
                return
            }
            if (!$("#fakelos" + uid).val() && alwaysShowFakelosInViewEmails) {
                toastr.info(
                    "<center><h4>Ενημέρωση...</h4><hr>Για να καταχωρίσετε ένα email είναι απαραίτητο να επιλέξετε Φάκελο<br>&nbsp;</center>"
                )
                return
            }
 
            var hasAttachments = $form.data('has-attachments') == 1
            if (hasAttachments || alwaysShowFakelosInViewEmails) {
                if (!formValidate(uid)) return
            }
 
            var thema = ($form.find('input[name="thema"]').val() || '').trim()
            var in_num = ($form.find('input[name="in_num"]').val() || '').trim()
            var in_date = ($form.find('input[name="in_date"]').val() || '').trim()
 
            $form.data('busy', true)
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
                    var msgStr = ''
                    if (response['thema'] > 0) msgStr += '<li>Θέμα</li>'
                    if (response['in_num'] > 0) msgStr += '<li>Αρ. & Ημνια Εισερχομένου</li>'
                    if (msgStr) {
                        var buttons =
                            "<center><button type='button' class='btn btn-primary confirm-yes'>Ναί</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-primary confirm-no'>Όχι</button></center></p>"
                        var title =
                            "<center><h4>Ενημέρωση ...</h4><hr></center>Υπάρχει καταχωρισμένο πρωτόκολλο με ίδιο<ul>" +
                            msgStr + "</ul>Θέλετε ωστόσο να προχωρήσετε;<br>&nbsp;"
                        // το toast παραμένει μέχρι να απαντήσει ο χρήστης
                        var toast = toastr.info(buttons, title, {
                            timeOut: 0,
                            extendedTimeOut: 0,
                            tapToDismiss: false,
                            closeButton: false
                        });
                        toast.find('.confirm-yes').on('click', function() {
                            toastr.clear(toast)
                            $form.data('busy', false)
                            submitForm($form)
                        });
                        toast.find('.confirm-no').on('click', function() {
                            toastr.clear(toast)
                            $form.data('busy', false)
                        });
                    } else {
                        $form.data('busy', false)
                        submitForm($form)
                    }
                },
                error: function() {
                    $form.data('busy', false)
                    toastr.error(
                        "<center><h4>Λάθος !!!</h4><hr></center>Κάποιο λάθος συνέβη!<br>&nbsp;</center>")
                }
            });
        }
 
        function getValues(id, field, divId, multi) {
            if (!allowListValuesMatchingInput) return
 
            var searchStr = ($('#' + id).val() || '').trim()
            if (searchStr == '') {
                clearDiv(divId)
                return
            }
            // Οι τιμές μπαίνουν σε path του URL: οι κάθετοι (/ \) θα έσπαγαν τη διαδρομή
            var term = extractLast(searchStr).replace(/[\/\\]/g, ' ').trim()
            if (term == '') {
                clearDiv(divId)
                return
            }
 
            $.ajax({
                url: '{{ URL::to('/') }}/getValues/' + encodeURIComponent(term) + '/' + encodeURIComponent(field) +
                    '/' + encodeURIComponent(id) + '/' + encodeURIComponent(divId) + '/' + multi,
                success: function(data) {
                    if (data) {
                        var front = '<ul id="' + id +
                            'Ul" class="dropdown-menu" style="display:block; position:absolute; max-height:10em; max-width: 100%; overflow:auto" >'
                        var end = '</ul>'
                        // ΠΡΟΣΟΧΗ: το περιεχόμενο που επιστρέφει ο server πρέπει να είναι escaped (htmlspecialchars/e())
                        $('#' + divId).html(front + data + end)
                        $('#' + divId).show()
                    } else {
                        clearDiv(divId)
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
            var newId = ''
            var sel = $('#diekperaiosi' + id).val()
            if (sel) {
                newId = sel.join(',')
            }
            if (!newId || newId == oldId) {
                $('#sendEmailTo' + id).val('')
                return
            }
            $('#sendEmailTo' + id).val(newId)
        }
 
        function formValidate(uid) {
            var $form = $("#frm" + uid)
 
            function fieldVal(name) {
                return ($form.find('[name="' + name + '"]').val() || '').trim()
            }
 
            var thema = fieldVal('thema')
            var in_num = fieldVal('in_num')
            var in_date = fieldVal('in_date')
            var in_topos_ekdosis = fieldVal('in_topos_ekdosis')
            var in_arxi_ekdosis = fieldVal('in_arxi_ekdosis')
            var in_paraliptis = fieldVal('in_paraliptis')
 
            $form.find('.has-error').removeClass('has-error')
            var msg = []
 
            if (protocolValidate) {
                var msgStr = ''
                if (!thema) {
                    msgStr += "<li>το θέμα</li>"
                    $('#themaDiv' + uid).addClass('has-error')
                }
                if (!in_date && (in_num || in_topos_ekdosis || in_arxi_ekdosis)) {
                    msgStr += "<li>την ημ/νια έκδοσης</li>"
                    $('#in_dateDiv' + uid).addClass('has-error')
                }
                /*
                if (!in_topos_ekdosis && (in_num || in_date || in_arxi_ekdosis)) {
                    msgStr += "<li>τον τόπο έκδοσης</li>"
                    $('#in_topos_ekdosisDiv' + uid).addClass('has-error')
                }
                */
                if (!in_arxi_ekdosis && (in_num || in_date || in_topos_ekdosis)) {
                    msgStr += "<li>την αρχή έκδοσης</li>"
                    $('#in_arxi_ekdosisDiv' + uid).addClass('has-error')
                }
                if (!in_paraliptis && (in_num || in_date || in_topos_ekdosis || in_arxi_ekdosis)) {
                    msgStr += "<li>τον παραλήπτη</li>"
                    $('#in_paraliptisDiv' + uid).addClass('has-error')
                }
                if (msgStr) msg.push("Συμπληρώστε<ul>" + msgStr + "</ul>")
            }
 
            if (in_date && !/^\d{2}[/]\d{2}[/]\d{4}$/.test(in_date)) {
                $('#in_dateDiv' + uid).addClass('has-error')
                msg.push('Η ημερομηνία πρέπει να<br>έχει τη μορφή "ηη/μμ/εεεε".')
            }
 
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
 