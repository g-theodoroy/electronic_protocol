@extends('layouts.app', ['needsUpdate' => $needsUpdate])

@section('content')

<div class="{{ $wideListProtocol ? 'container-fluid' : 'container' }}">
    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-default">

                {{-- ΕΝΕΡΓΟΙ ΧΡΗΣΤΕΣ --}}
                @if (count($activeusers2show) > 1)
                    <div class="row">
                        <div class="col-md-2 col-sm-2 small text-center">
                            Ενεργοί χρήστες:
                            <strong>{{ count($activeusers2show) }}</strong>
                        </div>
                        <div class="col-md-10 col-sm-10 small">
                            @foreach ($activeusers2show as $user2show)
                                {{ $user2show }}@if (!$loop->last), @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ΤΙΤΛΟΣ --}}
                <div class="col-md-1 col-sm-1 col-xs-1 panel-heading h1 text-center protocol-title-grey"
                    {!! $titleColorStyle !!}>&nbsp;</div>

                <div class="col-md-10 col-sm-10 col-xs-10 panel-heading h1 text-center protocol-title-grey"
                    {!! $titleColorStyle !!}>
                    {{ $protocoltitle }}
                </div>

                <div id="emailNumDiv"
                    class="col-md-1 col-sm-1 col-xs-1 panel-heading h1 text-center protocol-title-grey"
                    {!! $titleColorStyle !!}>&nbsp;</div>

                <div class="panel-body">

                    {{-- ΜΠΑΡΑ ΚΟΥΜΠΙΩΝ --}}
                    <div class="row" style="min-height: 46px;">

                        <div class="col-md-3 col-sm-3 form-control-static">
                            <a href="{{ url('/find') }}" title="Αναζήτηση">
                                <img src="{{ asset('images/find.ico') }}" height="30" alt="Αναζήτηση">
                            </a>

                            <a href="{{ url()->full() }}" title="Ανανέωση τώρα">
                                <img src="{{ asset('images/refresh.png') }}" height="30" alt="Ανανέωση">
                            </a>

                            &nbsp;
                            <span id="timer" style="color:#BFBFBF"
                                title="Αυτόματη ανανέωση σε"></span>
                        </div>

                        <div class="col-md-6 col-sm-6 small text-center">
                            @if ($protocols->hasPages())
                                {{ $protocols->withQueryString()->links() }}
                            @endif
                        </div>

                        <div class="col-md-3 col-sm-3 form-control-static text-right">

                            <a href="{{ url('/home') }}" title="Νέο">
                                <img src="{{ asset('images/addnew.ico') }}" height="30" alt="Νέο">
                            </a>

                            <a href="{{ url('/home/list/d') }}" title="προς Διεκπεραίωση">
                                <img src="{{ asset('images/todo.png') }}" height="30" alt="Διεκπεραίωση">
                            </a>

                            <a href="{{ url('/home/list/e') }}" title="προς Ενημέρωση">
                                <img src="{{ asset('images/info.png') }}" height="30" alt="Ενημέρωση">
                            </a>

                            <a href="{{ url('/home/list/f') }}" title="Διεκπεραιώθηκε">
                                <img src="{{ asset('images/done.png') }}" height="30" alt="Ολοκληρώθηκε">
                            </a>

                            <a href="javascript:toggleList()" title="Λίστα Αναλυτική - Συνοπτική">
                                <img src="{{ asset('images/toggle.png') }}" height="30" alt="Εναλλαγή λίστας">
                            </a>

                            <a href="{{ url('/home/list') }}" title="Πρωτόκολλο">
                                <img src="{{ asset('images/protocol.png') }}" height="30" alt="Πρωτόκολλο">
                            </a>

                        </div>
                    </div>
                
                    {{-- ================================================= --}}
                    {{-- ΑΝΑΛΥΤΙΚΗ ΛΙΣΤΑ --}}
                    {{-- ================================================= --}}

                    @if (request()->get('exp'))

                        {{-- ΚΕΦΑΛΙΔΕΣ --}}
                        <div data-spy="affix" data-offset-top="240">
                            <div class="row bg-primary">&nbsp;</div>

                            <div class="row bg-primary">

                                <div class="col-md-1 col-sm-1">
                                    <br>
                                    <strong class="sortable" onclick="setFieldSort('protocolnum')">
                                        {!! request()->get('field') == 'protocolnum'
                                            ? (request()->get('sort') == 'desc' ? '&#8657;&nbsp;&nbsp;' : '&#8659;&nbsp;&nbsp;')
                                            : '' !!}
                                        Αυξ.Αριθ
                                    </strong>
                                    <hr>
                                    <strong class="sortable" onclick="setFieldSort('protocoldate')">
                                        {!! request()->get('field') == 'protocoldate'
                                            ? (request()->get('sort') == 'desc' ? '&#8657;&nbsp;&nbsp;' : '&#8659;&nbsp;&nbsp;')
                                            : '' !!}
                                        Ημνία
                                    </strong>
                                </div>

                                <div class="col-md-5 col-sm-5">
                                    <div class="row">

                                        <div class="col-md-5 col-sm-5">
                                            <strong class="sortable" onclick="setFieldSort('in_date')">
                                                {!! request()->get('field') == 'in_date'
                                                    ? (request()->get('sort') == 'desc' ? '&#8657;&nbsp;&nbsp;' : '&#8659;&nbsp;&nbsp;')
                                                    : '' !!}
                                                Αρ./Ημ.Εισερχ.
                                            </strong>
                                            <hr>

                                            <strong class="sortable" onclick="setFieldSort('in_topos_ekdosis')">
                                                {!! request()->get('field') == 'in_topos_ekdosis'
                                                    ? (request()->get('sort') == 'desc' ? '&#8657;&nbsp;&nbsp;' : '&#8659;&nbsp;&nbsp;')
                                                    : '' !!}
                                                &#x2727;Τόπος Έκδοσης
                                            </strong>
                                            <hr>

                                            <strong class="sortable" onclick="setFieldSort('in_arxi_ekdosis')">
                                                {!! request()->get('field') == 'in_arxi_ekdosis'
                                                    ? (request()->get('sort') == 'desc' ? '&#8657;&nbsp;&nbsp;' : '&#8659;&nbsp;&nbsp;')
                                                    : '' !!}
                                                &#x2726;Αρχή Έκδοσης
                                            </strong>
                                        </div>

                                        <div class="col-md-7 col-sm-7">
                                            <strong class="sortable" onclick="setFieldSort('thema')">
                                                {!! request()->get('field') == 'thema'
                                                    ? (request()->get('sort') == 'desc' ? '&#8657;&nbsp;&nbsp;' : '&#8659;&nbsp;&nbsp;')
                                                    : '' !!}
                                                Θέμα
                                            </strong>
                                            <hr>

                                            <strong class="sortable" onclick="setFieldSort('in_perilipsi')">
                                                {!! request()->get('field') == 'in_perilipsi'
                                                    ? (request()->get('sort') == 'desc' ? '&#8657;&nbsp;&nbsp;' : '&#8659;&nbsp;&nbsp;')
                                                    : '' !!}
                                                &#x2727;Περίλ.Εισερχ.
                                            </strong>
                                            <hr>

                                            <strong class="sortable" onclick="setFieldSort('in_paraliptis')">
                                                {!! request()->get('field') == 'in_paraliptis'
                                                    ? (request()->get('sort') == 'desc' ? '&#8657;&nbsp;&nbsp;' : '&#8659;&nbsp;&nbsp;')
                                                    : '' !!}
                                                &#x2726;Παραλήπτης
                                            </strong>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-md-5 col-sm-5">
                                    <div class="row">

                                        <div class="col-md-5 col-sm-5">
                                            <strong class="sortable" onclick="setFieldSort('out_date')">
                                                {!! request()->get('field') == 'out_date'
                                                    ? (request()->get('sort') == 'desc' ? '&#8657;&nbsp;&nbsp;' : '&#8659;&nbsp;&nbsp;')
                                                    : '' !!}
                                                Ημ.Εξερχ.
                                            </strong>
                                            <hr>

                                            <strong class="sortable" onclick="setFieldSort('out_to')">
                                                {!! request()->get('field') == 'out_to'
                                                    ? (request()->get('sort') == 'desc' ? '&#8657;&nbsp;&nbsp;' : '&#8659;&nbsp;&nbsp;')
                                                    : '' !!}
                                                &#x2727;Απευθύνεται
                                            </strong>
                                            <hr>

                                            <strong class="sortable" onclick="setFieldSort('out_perilipsi')">
                                                {!! request()->get('field') == 'out_perilipsi'
                                                    ? (request()->get('sort') == 'desc' ? '&#8657;&nbsp;&nbsp;' : '&#8659;&nbsp;&nbsp;')
                                                    : '' !!}
                                                &#x2726;Περίλ.Εξερχ.
                                            </strong>
                                        </div>

                                        <div class="col-md-3 col-sm-3">
                                            <strong class="sortable" onclick="setFieldSort('diekperaiosi')">
                                                {!! request()->get('field') == 'diekperaiosi'
                                                    ? (request()->get('sort') == 'desc' ? '&#8657;&nbsp;&nbsp;' : '&#8659;&nbsp;&nbsp;')
                                                    : '' !!}
                                                Διεκπεραίωση
                                            </strong>
                                            <hr>

                                            <strong class="sortable" onclick="setFieldSort('diekp_eos')">
                                                {!! request()->get('field') == 'diekp_eos'
                                                    ? (request()->get('sort') == 'desc' ? '&#8657;&nbsp;&nbsp;' : '&#8659;&nbsp;&nbsp;')
                                                    : '' !!}
                                                &#x2727;Διεκπ.έως
                                            </strong>
                                            <hr>

                                            <strong class="sortable" onclick="setFieldSort('diekp_date')">
                                                {!! request()->get('field') == 'diekp_date'
                                                    ? (request()->get('sort') == 'desc' ? '&#8657;&nbsp;&nbsp;' : '&#8659;&nbsp;&nbsp;')
                                                    : '' !!}
                                                &#x2726;Ημ.Διεκπ.
                                            </strong>
                                        </div>

                                        <div class="col-md-4 col-sm-4">
                                            <strong class="sortable" onclick="setFieldSort('fakelos')">
                                                {!! request()->get('field') == 'fakelos'
                                                    ? (request()->get('sort') == 'desc' ? '&#8657;&nbsp;&nbsp;' : '&#8659;&nbsp;&nbsp;')
                                                    : '' !!}
                                                Φάκελος
                                            </strong>
                                            <hr>

                                            <strong class="sortable" onclick="setFieldSort('sxetiko')">
                                                {!! request()->get('field') == 'sxetiko'
                                                    ? (request()->get('sort') == 'desc' ? '&#8657;&nbsp;&nbsp;' : '&#8659;&nbsp;&nbsp;')
                                                    : '' !!}
                                                &#x2727;Σχετ.αριθμοί
                                            </strong>
                                            <hr>

                                            <strong class="sortable" onclick="setFieldSort('paratiriseis')">
                                                {!! request()->get('field') == 'paratiriseis'
                                                    ? (request()->get('sort') == 'desc' ? '&#8657;&nbsp;&nbsp;' : '&#8659;&nbsp;&nbsp;')
                                                    : '' !!}
                                                &#x2726;Παρατηρήσεις
                                            </strong>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-md-1 col-sm-1">
                                    <strong>Συνημμένα</strong>
                                    <hr>
                                    <strong>Διατήρηση</strong><br>
                                    <strong>Διατηρ.έως</strong>
                                </div>

                            </div>

                            <div class="row bg-primary">&nbsp;</div>
                        </div>

                        {{-- ΔΕΔΟΜΕΝΑ ΑΝΑΛΥΤΙΚΗΣ ΛΙΣΤΑΣ --}}
                        @php($i = 1)

                        @foreach ($protocols as $protocol)

                            <div class="row line {{ $i % 2 ? '' : 'line2' }}">

                                <div class="col-md-1 col-sm-1 {{ $protocol->deadLineClass }}"
                                     @if ($protocol->deadLineClass)
                                         title="Διεκπεραίωση έως {{ $protocol->diekp_eos }}"
                                     @endif
                                     style="cursor:pointer"
                                     onclick="window.location.href='{{ url('/home/' . $protocol->id) }}'">

                                    <a href="{{ url('/home/' . $protocol->id . '/1') }}"
                                       onclick="event.stopPropagation()"
                                       role="button"
                                       title="Αντιγραφή ως Νέο">
                                        <img src="{{ asset('images/copy-stamp.png') }}"
                                             height="20" alt="Αντιγραφή">
                                    </a>

                                    &nbsp;

                                    <span title="Αύξων Αριθμός">
                                        <strong>{{ $protocol->protocolnum }}</strong>
                                    </span>

                                    <hr>

                                    <span title="Ημερομηνία">
                                        {{ $protocol->protocoldate }}
                                    </span>
                                </div>

                                <div class="col-md-5 col-sm-5"
                                     style="cursor:pointer"
                                     onclick="window.location.href='{{ url('/home/' . $protocol->id) }}'">

                                    <div class="row">

                                        <div class="col-md-5 col-sm-5 small">

                                            <span title="Αριθμός/Ημερομηνία Εισερχομένου">
                                                @if ($protocol->in_num)
                                                    {{ $protocol->in_num }}/
                                                @endif
                                                {{ $protocol->in_date }}
                                            </span>

                                            <hr>

                                            <span title="Τόπος Έκδοσης">
                                                @if ($protocol->in_topos_ekdosis)
                                                    &#x2727;{{ $protocol->in_topos_ekdosis }}
                                                @else
                                                    &nbsp;
                                                @endif
                                            </span>

                                            <hr>

                                            <span title="Αρχή Έκδοσης">
                                                @if ($protocol->in_arxi_ekdosis)
                                                    &#x2726;{{ $protocol->in_arxi_ekdosis }}
                                                @else
                                                    &nbsp;
                                                @endif
                                            </span>

                                        </div>

                                        <div class="col-md-7 col-sm-7 small">

                                            <span title="Θέμα">
                                                <strong>{{ $protocol->thema }}</strong>
                                            </span>

                                            <hr>

                                            <span title="Περίληψη Εισερχομένου">
                                                @if ($protocol->in_perilipsi)
                                                    &#x2727;{{ $protocol->in_perilipsi }}
                                                @else
                                                    &nbsp;
                                                @endif
                                            </span>

                                            <hr>

                                            <span title="Παραλήπτης">
                                                @if ($protocol->in_paraliptis)
                                                    &#x2726;{{ $protocol->in_paraliptis }}
                                                @else
                                                    &nbsp;
                                                @endif
                                            </span>

                                        </div>

                                    </div>
                                </div>

                                <div class="col-md-5 col-sm-5"
                                     style="cursor:pointer"
                                     onclick="window.location.href='{{ url('/home/' . $protocol->id) }}'">

                                    <div class="row">

                                        <div class="col-md-5 col-sm-5 small">

                                            <span title="Ημερομηνία Εξερχομένου">
                                                {{ $protocol->out_date }}
                                            </span>

                                            <hr>

                                            <span title="Απευθύνεται">
                                                @if ($protocol->out_to)
                                                    &#x2727;{{ $protocol->out_to }}
                                                @else
                                                    &nbsp;
                                                @endif
                                            </span>

                                            <hr>

                                            <span title="Περίληψη Εξερχομένου">
                                                @if ($protocol->out_perilipsi)
                                                    &#x2726;{{ $protocol->out_perilipsi }}
                                                @else
                                                    &nbsp;
                                                @endif
                                            </span>

                                        </div>

                                        <div class="col-md-3 col-sm-3 small {{ $protocol->deadLineClass }}"
                                             @if ($protocol->deadLineClass)
                                                 title="Διεκπεραίωση έως {{ $protocol->diekp_eos }}"
                                             @endif>

                                            <span title="Διεκπεραίωση">
                                                @if ($protocol->diekperaiosi)
                                                    @php($str = 'd')

                                                    @foreach (explode(',', $protocol->diekperaiosi) as $d)
                                                        @if (strpos($str, substr($d, 0, 1)) !== false)
                                                            @php($userId = ltrim($d, $str))

                                                            @if ($myUsers->where('id', $userId)->count())
                                                                {{ $myUsers->where('id', $userId)->first()->name }}
                                                            @else
                                                                {{ $userId }}
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </span>

                                            <hr>

                                            <span title="Διεκπεραίωση έως {{ $protocol->diekp_eos }}">
                                                @if ($protocol->diekp_eos)
                                                    &#x2727;{{ $protocol->diekp_eos }}
                                                @else
                                                    &nbsp;
                                                @endif
                                            </span>

                                            <hr>

                                            <span title="Διεκπεραιώθηκε τις {{ $protocol->diekp_date }}">
                                                @if ($protocol->diekp_date)
                                                    &#x2727;{{ $protocol->diekp_date }}
                                                @else
                                                    &nbsp;
                                                @endif
                                            </span>

                                        </div>

                                        <div class="col-md-4 col-sm-4 small">

                                            <span title="{{ $protocol->describe
                                                ? 'Φάκελος ' . $protocol->fakelos . '->' . $protocol->describe
                                                : 'Φάκελος' }}">
                                                {{ $protocol->fakelos }}
                                            </span>

                                            <hr>

                                            <span title="Σχετικοί αριθμοί">
                                                @if ($protocol->sxetiko)
                                                    &#x2727;{{ $protocol->sxetiko }}
                                                @else
                                                    &nbsp;
                                                @endif
                                            </span>

                                            <hr>

                                            <span title="Παρατηρήσεις">
                                                @if ($protocol->paratiriseis)
                                                    &#x2726;{{ $protocol->paratiriseis }}
                                                @else
                                                    &nbsp;
                                                @endif
                                            </span>

                                        </div>

                                    </div>
                                </div>

                                {{-- ΣΥΝΗΜΜΕΝΑ --}}
                                <div class="col-md-1 col-sm-1 small" style="overflow:hidden">

                                    <ul class="list-unstyled">
                                        @foreach ($protocol->attachments as $attachment)
                                            <li>

                                                @if ($attachment->name)
                                                    <a href="{{ url('/download/' . $attachment->id) }}"
                                                       target="_blank"
                                                       rel="noopener noreferrer"
                                                       title="Λήψη {{ $attachment->name }}">

                                                        @if (mb_strlen($attachment->name, 'UTF-8') > 17)
                                                            {{ mb_substr($attachment->name, 0, 7, 'UTF-8') }}...{{ mb_substr($attachment->name, -7, 7, 'UTF-8') }}
                                                        @else
                                                            {{ $attachment->name }}
                                                        @endif

                                                    </a>
                                                @endif

                                                @if ($attachment->name && $attachment->ada)
                                                    <br>
                                                @endif

                                                @if ($attachment->ada)
                                                    <a href="{{ $diavgeiaUrl }}{{ $attachment->ada }}"
                                                       target="_blank"
                                                       rel="noopener noreferrer"
                                                       title="Λήψη {{ $attachment->ada }}">

                                                        @if (mb_strlen($attachment->ada, 'UTF-8') > 17)
                                                            {{ mb_substr($attachment->ada, 0, 7, 'UTF-8') }}...{{ mb_substr($attachment->ada, -3, 3, 'UTF-8') }}
                                                        @else
                                                            {{ $attachment->ada }}
                                                        @endif

                                                    </a>
                                                @endif

                                            </li>
                                        @endforeach
                                    </ul>

                                    <hr>

                                    @if ($protocol->attachments->isNotEmpty())
                                        @php($firstAttachment = $protocol->attachments->first())

                                        @if (is_numeric($firstAttachment->keep))
                                            <span title="Διατήρηση: {{ $firstAttachment->keep }}">
                                                {{ $firstAttachment->keep == 1
                                                    ? $firstAttachment->keep . ' χρόνο'
                                                    : $firstAttachment->keep . ' χρόνια' }}
                                            </span>
                                        @else
                                            <span title="Διατήρηση: {{ $firstAttachment->keep }}">
                                                {{ $firstAttachment->keep }}
                                            </span>
                                        @endif

                                        <br>

                                        @if ($firstAttachment->expires)
                                            <span title="Διατήρηση έως {{ \Carbon\Carbon::createFromFormat('Ymd', $firstAttachment->expires)->format('d/m/Y') }}">
                                                {{ \Carbon\Carbon::createFromFormat('Ymd', $firstAttachment->expires)->format('d/m/Y') }}
                                            </span>
                                        @endif
                                    @endif

                                </div>

                            </div>

                            @php($i++)

                        @endforeach

                    @else

                    {{-- ================================================= --}}
                    {{-- ΣΥΝΟΠΤΙΚΗ ΛΙΣΤΑ --}}
                    {{-- ================================================= --}}

                        {{-- ΚΕΦΑΛΙΔΕΣ --}}
                        <div data-spy="affix" data-offset-top="240">

                            <div class="row bg-primary">&nbsp;</div>

                            <div class="row bg-primary">

                                <div class="col-md-1 col-sm-1">
                                    <strong class="sortable" onclick="setFieldSort('protocolnum')">
                                        {!! request()->get('field') == 'protocolnum'
                                            ? (request()->get('sort') == 'desc' ? '&#8657;&nbsp;&nbsp;' : '&#8659;&nbsp;&nbsp;')
                                            : '' !!}
                                        Αυξ.Αριθ
                                    </strong>
                                </div>

                                <div class="col-md-1 col-sm-1">
                                    <strong class="sortable" onclick="setFieldSort('protocoldate')">
                                        {!! request()->get('field') == 'protocoldate'
                                            ? (request()->get('sort') == 'desc' ? '&#8657;&nbsp;&nbsp;' : '&#8659;&nbsp;&nbsp;')
                                            : '' !!}
                                        Ημνία
                                    </strong>
                                </div>

                                <div class="col-md-7 col-sm-7">
                                    <div class="row">

                                        <div class="col-md-3 col-sm-3">
                                            <strong class="sortable" onclick="setFieldSort('in_arxi_ekdosis')">
                                                {!! request()->get('field') == 'in_arxi_ekdosis'
                                                    ? (request()->get('sort') == 'desc' ? '&#8657;&nbsp;&nbsp;' : '&#8659;&nbsp;&nbsp;')
                                                    : '' !!}
                                                Αρχή Έκδοσης
                                            </strong>
                                        </div>

                                        <div class="col-md-9 col-sm-9">
                                            <strong class="sortable" onclick="setFieldSort('thema')">
                                                {!! request()->get('field') == 'thema'
                                                    ? (request()->get('sort') == 'desc' ? '&#8657;&nbsp;&nbsp;' : '&#8659;&nbsp;&nbsp;')
                                                    : '' !!}
                                                Θέμα
                                            </strong>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-3">
                                    <div class="row">

                                        <div class="col-md-4 col-sm-4">
                                            <strong class="sortable" onclick="setFieldSort('out_to')">
                                                {!! request()->get('field') == 'out_to'
                                                    ? (request()->get('sort') == 'desc' ? '&#8657;&nbsp;&nbsp;' : '&#8659;&nbsp;&nbsp;')
                                                    : '' !!}
                                                Απευθύνεται
                                            </strong>
                                        </div>

                                        <div class="col-md-4 col-sm-4">
                                            <strong class="sortable" onclick="setFieldSort('diekp_eos')">
                                                {!! request()->get('field') == 'diekp_eos'
                                                    ? (request()->get('sort') == 'desc' ? '&#8657;&nbsp;&nbsp;' : '&#8659;&nbsp;&nbsp;')
                                                    : '' !!}
                                                Διεκπ.έως
                                            </strong>
                                        </div>

                                        <div class="col-md-4 col-sm-4">
                                            <strong>Συνημμένα</strong>
                                        </div>

                                    </div>
                                </div>

                            </div>

                            <div class="row bg-primary">&nbsp;</div>

                        </div>

                        {{-- ΔΕΔΟΜΕΝΑ ΣΥΝΟΠΤΙΚΗΣ ΛΙΣΤΑΣ --}}
                        @php($i = 1)

                        @foreach ($protocols as $protocol)

                            <div class="row line {{ $i % 2 ? '' : 'line2' }}">

                                <div class="col-md-1 col-sm-1 {{ $protocol->deadLineClass }}"
                                     @if ($protocol->deadLineClass)
                                         title="Διεκπεραίωση έως {{ $protocol->diekp_eos }}"
                                     @endif
                                     style="cursor:pointer"
                                     onclick="window.location.href='{{ url('/home/' . $protocol->id) }}'">

                                    <a href="{{ url('/home/' . $protocol->id . '/1') }}"
                                       onclick="event.stopPropagation()"
                                       role="button"
                                       title="Αντιγραφή ως Νέο">
                                        <img src="{{ asset('images/copy-stamp.png') }}"
                                             height="20" alt="Αντιγραφή">
                                    </a>

                                    &nbsp;

                                    <span title="Αύξων Αριθμός">
                                        <strong>{{ $protocol->protocolnum }}</strong>
                                    </span>

                                </div>

                                <div class="col-md-1 col-sm-1"
                                     style="cursor:pointer"
                                     onclick="window.location.href='{{ url('/home/' . $protocol->id) }}'">

                                    <span title="Ημερομηνία">
                                        {{ $protocol->protocoldate }}
                                    </span>

                                </div>

                                <div class="col-md-7 col-sm-7"
                                     style="cursor:pointer"
                                     onclick="window.location.href='{{ url('/home/' . $protocol->id) }}'">

                                    <div class="row">

                                        <div class="col-md-3 col-sm-3 small">
                                            <span title="Αρχή Έκδοσης">
                                                {{ $protocol->in_arxi_ekdosis }}
                                            </span>
                                        </div>

                                        <div class="col-md-9 col-sm-9 small">
                                            <span title="Θέμα">
                                                <strong>{{ $protocol->thema }}</strong>
                                            </span>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-3">

                                    <div class="row">

                                        <div class="col-md-4 col-sm-4"
                                             style="cursor:pointer"
                                             onclick="window.location.href='{{ url('/home/' . $protocol->id) }}'">

                                            <span title="Απευθύνεται">
                                                {{ $protocol->out_to }}
                                            </span>

                                        </div>

                                        <div class="col-md-4 col-sm-4 small {{ $protocol->deadLineClass }}"
                                             @if ($protocol->deadLineClass)
                                                 title="Διεκπεραίωση έως {{ $protocol->diekp_eos }}"
                                             @elseif ($protocol->diekp_date)
                                                 title="Διεκπεραιώθηκε τις {{ $protocol->diekp_date }}"
                                             @endif
                                             style="cursor:pointer"
                                             onclick="window.location.href='{{ url('/home/' . $protocol->id) }}'">

                                            <span>
                                                {{ $protocol->diekp_eos }}
                                            </span>

                                            @if (!$protocol->deadLineClass && $protocol->diekp_date)
                                                <span class="pull-right">&check;</span>
                                            @endif

                                        </div>

                                        <div class="col-md-4 col-sm-4 small"
                                             style="overflow:hidden">

                                            <ul class="list-unstyled">

                                                @foreach ($protocol->attachments as $attachment)
                                                    <li>

                                                        @if ($attachment->name)
                                                            <a href="{{ url('/download/' . $attachment->id) }}"
                                                               target="_blank"
                                                               rel="noopener noreferrer"
                                                               title="Λήψη {{ $attachment->name }}">

                                                                @if (mb_strlen($attachment->name, 'UTF-8') > 17)
                                                                    {{ mb_substr($attachment->name, 0, 7, 'UTF-8') }}...{{ mb_substr($attachment->name, -7, 7, 'UTF-8') }}
                                                                @else
                                                                    {{ $attachment->name }}
                                                                @endif

                                                            </a>
                                                        @endif

                                                        @if ($attachment->name && $attachment->ada)
                                                            <br>
                                                        @endif

                                                        @if ($attachment->ada)
                                                            <a href="{{ $diavgeiaUrl }}{{ $attachment->ada }}"
                                                               target="_blank"
                                                               rel="noopener noreferrer"
                                                               title="Λήψη {{ $attachment->ada }}">

                                                                @if (mb_strlen($attachment->ada, 'UTF-8') > 17)
                                                                    {{ mb_substr($attachment->ada, 0, 7, 'UTF-8') }}...{{ mb_substr($attachment->ada, -3, 3, 'UTF-8') }}
                                                                @else
                                                                    {{ $attachment->ada }}
                                                                @endif

                                                            </a>
                                                        @endif

                                                    </li>
                                                @endforeach

                                            </ul>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            @php($i++)

                        @endforeach

                    @endif

                    {{-- ΕΠΙΣΤΡΟΦΗ ΣΤΗΝ ΚΟΡΥΦΗ --}}
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="#">&#8686; Στην κορυφή &#8686;</a>
                        </div>
                    </div>

                    {{-- PAGINATION --}}
                    @if ($protocols->hasPages() && $protocols->count() > $protocols->perPage() / 2)
                        <div class="row">
                            <div class="col-md-12 small text-center">
                                {{ $protocols->withQueryString()->links() }}
                            </div>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</div>

{{-- ================================================= --}}
{{-- JAVASCRIPT --}}
{{-- ================================================= --}}


<script>
(function () {
    'use strict';

    // Διατηρεί όλες τις υπάρχουσες παραμέτρους του URL.
    const protocolListUrl = new URL(@json(request()->fullUrl()));

    // Ταξινόμηση πεδίων.
    window.setFieldSort = function (field) {
        const url = new URL(protocolListUrl.toString());

        const currentField = url.searchParams.get('field');
        const currentSort = url.searchParams.get('sort');

        url.searchParams.set('field', field);

        if (currentField === field) {
            url.searchParams.set(
                'sort',
                currentSort === 'asc' ? 'desc' : 'asc'
            );
        } else {
            url.searchParams.set('sort', 'asc');
        }

        window.location.href = url.toString();
    };

    // Εναλλαγή αναλυτικής / συνοπτικής λίστας.
    window.toggleList = function () {
        const url = new URL(protocolListUrl.toString());

        if (url.searchParams.has('exp')) {
            url.searchParams.delete('exp');
        } else {
            url.searchParams.set('exp', 'true');
        }

        window.location.href = url.toString();
    };

    // Εκκίνηση όταν φορτώσει η σελίδα.
    window.addEventListener('load', function () {

        // Αυτόματη ανανέωση.
        @if ((int) $refreshInterval > 0)

            const timerDisplay = document.querySelector('#timer');

            if (timerDisplay) {
                let remainingSeconds = {{ (int) $refreshInterval }};

                function updateTimer() {
                    const minutes = Math.floor(remainingSeconds / 60);
                    const seconds = remainingSeconds % 60;

                    timerDisplay.textContent =
                        minutes + ':' + String(seconds).padStart(2, '0');

                    if (remainingSeconds <= 0) {
                        window.location.reload();
                        return;
                    }

                    remainingSeconds--;
                    window.setTimeout(updateTimer, 1000);
                }

                updateTimer();
            }

        @endif

        // Έλεγχος εισερχόμενων email.
        @if (Auth::check() && (
            !$allowedEmailUsers ||
            in_array(Auth::user()->username, array_map('trim', explode(',', $allowedEmailUsers)))
        ))

            $.ajax({
                url: @json(url('/getEmailNum')),
                type: 'GET',
                dataType: 'text',
                cache: false,

                success: function (data) {
                    const emailCount = Number(data);

                    if (Number.isFinite(emailCount) && emailCount > 0) {

                        $('#emailNumDiv').html(
                            '<a href="' + @json(url('/viewEmails')) +
                            '" id="emailNum" class="active" role="button" ' +
                            'title="Εισερχόμενα email: ' + emailCount + '">' +
                            '<img src="' + @json(asset('images/email-in.png')) +
                            '" height="30" alt="Email"></a>'
                        );
                    }
                },

                error: function (xhr, status, error) {
                    console.error('Email AJAX error:', status, error);
                }
            });

        @endif

    });

})();
</script>

<style>
    @media (min-width: 768px) {
        .affix {
            top: 0;
            position: fixed;
            left: 47px;
            right: 47px;
            z-index: 777;
        }
    }

    @media (max-width: 767px) {
        .affix {
            position: static;
        }
    }

    .line {
        padding-top: 7px;
        padding-bottom: 5px;
    }

    .line:hover {
        background-color: #E6E6E6;
    }

    .line2 {
        background-color: WhiteSmoke;
    }

    .sortable {
        cursor: pointer;
    }

    hr {
        margin: 3px;
    }

    .protocol-title-grey {
    background-color: #f5f5f5 !important;
    }

</style>

@endsection
