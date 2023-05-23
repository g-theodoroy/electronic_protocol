@extends('layouts.app', ['needsUpdate' => $needsUpdate])

@section('content')

    <div class="{{ $wideListProtocol ? 'container-fluid' : 'container' }}">
        <div class="row">
            <div class="col-md-12 col-md-offset-0">
                <div class="panel panel-default">
                    @if (count($activeusers2show) > 1)
                        <div class="col-md-2 col-sm-2 small text-center">Ενεργοί χρήστες:
                            <strong>{{ count($activeusers2show) }}</strong>
                        </div>
                        <div class="col-md-10 col-sm-10 small text-left">
                            @foreach ($activeusers2show as $user2show)
                                {{ $user2show }}@if (!$loop->last)
                                    ,
                                @endif
                            @endforeach
                        </div>
                    @endif
                    <div class="panel-heading h1 text-center col-md-1 col-sm-1 col-xs-1" {!! $titleColorStyle !!}>&nbsp;</div>
                    <div class="panel-heading h1 text-center col-md-10 col-sm-10 col-xs-10" {!! $titleColorStyle !!}>
                        {{ $protocoltitle }}</div>
                    <div id="emailNumDiv" class="panel-heading h1 text-center col-md-1 col-sm-1 col-xs-1"
                        {!! $titleColorStyle !!}>&nbsp;</div>
                    <div class="panel-body">
                        <div class="panel panel-default col-md-12 col-sm-12 col-xs-12  ">





                            {{--  ΜΠΑΡΑ ΚΟΥΜΠΙΑ   --}}
                            <div class='row'>
                                <div class="col-md-3 col-sm-3 form-control-static">
                                    <a href="{{ URL::to('/find') }}" class="active" role="button" title="Αναζήτηση">
                                        <img src="{{ URL::to('/') }}/images/find.ico" height=30 /></a>
                                    <a href="{{ url()->full() }}" class="active" role="button" title="Ανανέωση τώρα"> <img
                                            src="{{ URL::to('/') }}/images/refresh.png" height=30 /></a>
                                    &nbsp;<span id='timer' style='color:#BFBFBF' title='Αυτόματη ανανέωση σε'></span>
                                </div>
                                @if ($protocols->links())
                                    <div class="col-md-6 col-sm-6 small text-center">
                                        <span class="small">{{ $protocols->withQueryString()->links() }}</span>
                                    </div>
                                @endif
                                <div class="col-md-3 col-sm-3 form-control-static text-right">
                                    <a href="{{ URL::to('/') }}/home" class="active" role="button" title="Νέο">
                                        <img src="{{ URL::to('/') }}/images/addnew.ico" height=30 /></a>
                                    <a href="{{ URL::to('/') }}/home/list/d" class="active" role="button"
                                        title="προς Διεκπεραίωση"> <img src="{{ URL::to('/') }}/images/todo.png"
                                            height=30 /></a>
                                    <a href="{{ URL::to('/') }}/home/list/e" class="active" role="button"
                                        title="προς Ενημέρωση"> <img src="{{ URL::to('/') }}/images/info.png"
                                            height=30 /></a>
                                    <a href="{{ URL::to('/') }}/home/list/f" class="active" role="button"
                                        title="Διεκπεραιώθηκε"> <img src="{{ URL::to('/') }}/images/done.png"
                                            height=30 /></a>
                                    <a href="javascript:toggleList()" class="active" role="button" title="Λίστα Αναλυτική <=> Συνοπτική">
                                        <img src="{{ URL::to('/') }}/images/toggle.png" height=30 /></a>
                                    <a href="{{ URL::to('/') }}/home/list" class="active" role="button"
                                        title="Πρωτόκολλο"> <img src="{{ URL::to('/') }}/images/protocol.png"
                                            height=30 /></a>
                                </div>
                            </div>


                            @if (request()->get('exp'))
                                {{-- SHORT-LONG LIST --}}


                                {{-- LONG LIST --}}

                                {{-- ΕΠΙΚΕΦΑΛΙΔΕΣ TABLE --}}

                                <div data-spy="affix" data-offset-top="240">
                                    <div class='row  bg-primary'>&nbsp;</div>
                                    <div class='row bg-primary'>

                                        <div class='col-md-1 col-sm-1'>
                                            <br>
                                            <strong style="cursor:pointer"
                                                onClick="setFieldSort('protocolnum')">{!! request()->get('field') == 'protocolnum'
                                                    ? (request()->get('sort') == 'desc'
                                                        ? '&#8657;&nbsp;&nbsp;'
                                                        : '&#8659;&nbsp;&nbsp;')
                                                    : '' !!}Αυξ.Αριθ</strong>
                                            <hr>
                                            <strong style="cursor:pointer"
                                                onClick="setFieldSort('protocoldate')">{!! request()->get('field') == 'protocoldate'
                                                    ? (request()->get('sort') == 'desc'
                                                        ? '&#8657;&nbsp;&nbsp;'
                                                        : '&#8659;&nbsp;&nbsp;')
                                                    : '' !!}Ημνία</strong>
                                        </div>
                                        <div class='col-md-5 col-sm-5'>
                                            <div class='row'>
                                                <div class='col-md-5 col-sm-5'>
                                                    <strong style="cursor:pointer"
                                                        onClick="setFieldSort('in_date')">{!! request()->get('field') == 'in_date'
                                                            ? (request()->get('sort') == 'desc'
                                                                ? '&#8657;&nbsp;&nbsp;'
                                                                : '&#8659;&nbsp;&nbsp;')
                                                            : '' !!}Αρ./Ημ.Εισερχ.</strong>
                                                    <hr>
                                                    <strong style="cursor:pointer"
                                                        onClick="setFieldSort('in_topos_ekdosis')">{!! request()->get('field') == 'in_topos_ekdosis'
                                                            ? (request()->get('sort') == 'desc'
                                                                ? '&#8657;&nbsp;&nbsp;'
                                                                : '&#8659;&nbsp;&nbsp;')
                                                            : '' !!}&#x2727;Τόπος Έκδοσης</strong>
                                                    <hr>
                                                    <strong style="cursor:pointer"
                                                        onClick="setFieldSort('in_arxi_ekdosis')">{!! request()->get('field') == 'in_arxi_ekdosis'
                                                            ? (request()->get('sort') == 'desc'
                                                                ? '&#8657;&nbsp;&nbsp;'
                                                                : '&#8659;&nbsp;&nbsp;')
                                                            : '' !!}&#x2726;Αρχή Έκδοσης</strong>
                                                </div>
                                                <div class='col-md-7 col-sm-7'>

                                                    <strong style="cursor:pointer"
                                                        onClick="setFieldSort('thema')">{!! request()->get('field') == 'thema'
                                                            ? (request()->get('sort') == 'desc'
                                                                ? '&#8657;&nbsp;&nbsp;'
                                                                : '&#8659;&nbsp;&nbsp;')
                                                            : '' !!}Θέμα</strong>
                                                    <hr>

                                                    <strong style="cursor:pointer"
                                                        onClick="setFieldSort('in_perilipsi')">{!! request()->get('field') == 'in_perilipsi'
                                                            ? (request()->get('sort') == 'desc'
                                                                ? '&#8657;&nbsp;&nbsp;'
                                                                : '&#8659;&nbsp;&nbsp;')
                                                            : '' !!}&#x2727;Περίλ.Εισερχ.</strong>
                                                    <hr>
                                                    <strong style="cursor:pointer"
                                                        onClick="setFieldSort('in_paraliptis')">{!! request()->get('field') == 'in_paraliptis'
                                                            ? (request()->get('sort') == 'desc'
                                                                ? '&#8657;&nbsp;&nbsp;'
                                                                : '&#8659;&nbsp;&nbsp;')
                                                            : '' !!}&#x2726;Παραλήπτης</strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div class='col-md-5 col-sm-5'>
                                            <div class='row'>
                                                <div class='col-md-5 col-sm-5'>
                                                    <strong style="cursor:pointer"
                                                        onClick="setFieldSort('out_date')">{!! request()->get('field') == 'out_date'
                                                            ? (request()->get('sort') == 'desc'
                                                                ? '&#8657;&nbsp;&nbsp;'
                                                                : '&#8659;&nbsp;&nbsp;')
                                                            : '' !!}Ημ.Εξερχ.</strong>
                                                    <hr>
                                                    <strong style="cursor:pointer"
                                                        onClick="setFieldSort('out_to')">{!! request()->get('field') == 'out_to'
                                                            ? (request()->get('sort') == 'desc'
                                                                ? '&#8657;&nbsp;&nbsp;'
                                                                : '&#8659;&nbsp;&nbsp;')
                                                            : '' !!}&#x2727;Απευθύνεται</strong>
                                                    <hr>
                                                    <strong style="cursor:pointer"
                                                        onClick="setFieldSort('out_perilipsi')">{!! request()->get('field') == 'out_perilipsi'
                                                            ? (request()->get('sort') == 'desc'
                                                                ? '&#8657;&nbsp;&nbsp;'
                                                                : '&#8659;&nbsp;&nbsp;')
                                                            : '' !!}&#x2726;Περίλ.Εξερχ.</strong>
                                                </div>
                                                <div class='col-md-3 col-sm-3'>
                                                    <strong style="cursor:pointer"
                                                        onClick="setFieldSort('diekperaiosi')">{!! request()->get('field') == 'diekperaiosi'
                                                            ? (request()->get('sort') == 'desc'
                                                                ? '&#8657;&nbsp;&nbsp;'
                                                                : '&#8659;&nbsp;&nbsp;')
                                                            : '' !!}Διεκπεραίωση</strong>
                                                    <hr>
                                                    <strong style="cursor:pointer"
                                                        onClick="setFieldSort('diekp_eos')">{!! request()->get('field') == 'diekp_eos'
                                                            ? (request()->get('sort') == 'desc'
                                                                ? '&#8657;&nbsp;&nbsp;'
                                                                : '&#8659;&nbsp;&nbsp;')
                                                            : '' !!}&#x2727;Διεκπ.έως</strong>
                                                    <hr>
                                                    <strong style="cursor:pointer"
                                                        onClick="setFieldSort('diekp_date')">{!! request()->get('field') == 'diekp_date'
                                                            ? (request()->get('sort') == 'desc'
                                                                ? '&#8657;&nbsp;&nbsp;'
                                                                : '&#8659;&nbsp;&nbsp;')
                                                            : '' !!}&#x2726;Ημ.Διεκπ.</strong>
                                                </div>
                                                <div class='col-md-4 col-sm-4'>
                                                    <strong style="cursor:pointer"
                                                        onClick="setFieldSort('fakelos')">{!! request()->get('field') == 'fakelos'
                                                            ? (request()->get('sort') == 'desc'
                                                                ? '&#8657;&nbsp;&nbsp;'
                                                                : '&#8659;&nbsp;&nbsp;')
                                                            : '' !!}Φάκελος</strong>
                                                    <hr>
                                                    <strong style="cursor:pointer"
                                                        onClick="setFieldSort('sxetiko')">{!! request()->get('field') == 'sxetiko'
                                                            ? (request()->get('sort') == 'desc'
                                                                ? '&#8657;&nbsp;&nbsp;'
                                                                : '&#8659;&nbsp;&nbsp;')
                                                            : '' !!}&#x2727;Σχετ.αριθμοί</strong>
                                                    <hr>
                                                    <strong style="cursor:pointer"
                                                        onClick="setFieldSort('paratiriseis')">{!! request()->get('field') == 'paratiriseis'
                                                            ? (request()->get('sort') == 'desc'
                                                                ? '&#8657;&nbsp;&nbsp;'
                                                                : '&#8659;&nbsp;&nbsp;')
                                                            : '' !!}&#x2726;Παρατηρήσεις</strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div class='col-md-1 col-sm-1'>
                                            <strong>Συνημμένα</strong>
                                            <hr>
                                            <strong>Διατήρηση</strong><br>
                                            <strong>Διατηρ.έως</strong>
                                        </div>
                                    </div>
                                    <div class='row  bg-primary'>&nbsp;</div>
                                </div>



                                {{-- ΔΕΔΟΜΕΝΑ TABLE --}}
                                @php($i = 1)
                                @foreach ($protocols as $protocol)
                                    @if ($i % 2)
                                        {{-- <div class='row'>&nbsp;</div> --}}
                                        <div class='row line'>
                                        @else
                                            {{-- <div class='row  bg-info'>&nbsp;</div> --}}
                                            <div class='row line line2'>
                                    @endif

                                    <div class='col-md-1 col-sm-1 {{$protocol->deadLineClass}}' @if($protocol->deadLineClass) title='Διεκπεραίωση έως {{$protocol->diekp_eos}}' @endif style="cursor:pointer"
                                        onclick="window.location.href='{{ URL::to('/') }}/home/{{ $protocol->id }}'">
                                        <a href="{{ URL::to('/') }}/home/{{ $protocol->id }}/1" class=""
                                            role="button" title="Αντιγραφή ως Νέο"> <img
                                                src="{{ URL::to('/') }}/images/copy-stamp.png" height="20" /></a>
                                        &nbsp;
                                        <span title='Αυξων Αριθμός'>
                                            <strong>{{ $protocol->protocolnum }}</strong>
                                            <hr>
                                        </span>
                                        <span title='Ημνία'>
                                            {{ $protocol->protocoldate }}
                                        </span>
                                    </div>
                                    <div class='col-md-5 col-sm-5'style="cursor:pointer"
                                        onclick="window.location.href='{{ URL::to('/') }}/home/{{ $protocol->id }}'">
                                        <div class='row'>
                                            <div class='col-md-5 col-sm-5 small'>
                                                <span title='Αριθμός/Ημνία Εισερχομένου'>
                                                    @if ($protocol->in_num)
                                                        {{ $protocol->in_num }}/
                                                    @endif
                                                    {{ $protocol->in_date ? $protocol->in_date : '' }}
                                                </span>
                                                <hr>
                                                <span title='Τόπος Έκδοσης'>
                                                    @if ($protocol->in_topos_ekdosis)
                                                        &#x2727;{{ $protocol->in_topos_ekdosis }}
                                                    @else
                                                        &nbsp;
                                                    @endif
                                                </span>
                                                <hr>
                                                <span title='Αρχή Έκδοσης'>
                                                    @if ($protocol->in_arxi_ekdosis)
                                                        &#x2726;{{ $protocol->in_arxi_ekdosis }}
                                                    @else
                                                        &nbsp;
                                                    @endif
                                                </span>
                                            </div>
                                            <div class='col-md-7 col-sm-7 small' style="cursor:pointer"
                                                onclick="window.location.href='{{ URL::to('/') }}/home/{{ $protocol->id }}'">
                                                <span title='Θέμα'><strong>
                                                        {{ $protocol->thema ? $protocol->thema : '' }}
                                                    </strong></span>
                                                <hr>
                                                <span title='Περίληψη Εισερχομένου'>
                                                    @if ($protocol->in_perilipsi)
                                                        &#x2727;{{ $protocol->in_perilipsi }}
                                                    @else
                                                        &nbsp;
                                                    @endif
                                                </span>
                                                <hr>
                                                <span title='Παραλήπτης'>
                                                    @if ($protocol->in_paraliptis)
                                                        &#x2726;{{ $protocol->in_paraliptis }}
                                                    @else
                                                        &nbsp;
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-md-5 col-sm-5' style="cursor:pointer"
                                        onclick="window.location.href='{{ URL::to('/') }}/home/{{ $protocol->id }}'">
                                        <div class='row'>
                                            <div class='col-md-5 col-sm-5 small'>
                                                <span title='Ημνία Εξερχομένου'>
                                                    {{ $protocol->out_date ? $protocol->out_date : '' }}
                                                </span>
                                                <hr>
                                                <span title='Απευθύνεται'>
                                                    @if ($protocol->out_to)
                                                        &#x2727;{{ $protocol->out_to }}
                                                    @else
                                                        &nbsp;
                                                    @endif
                                                </span>
                                                <hr>
                                                <span title='Περίληψη Εξερχομένου'>
                                                    @if ($protocol->out_perilipsi)
                                                        &#x2726;{{ $protocol->out_perilipsi }}
                                                    @else
                                                        &nbsp;
                                                    @endif
                                                </span>
                                            </div>
                                            <div class='col-md-3 col-sm-3 small {{$protocol->deadLineClass}}'  @if($protocol->deadLineClass) title='Διεκπεραίωση έως {{$protocol->diekp_eos}}' @endif style="cursor:pointer"
                                                onclick="window.location.href='{{ URL::to('/') }}/home/{{ $protocol->id }}'">
                                                <span title='Διεκπεραίωση'>
                                                    @if ($protocol->diekperaiosi)
                                                        @php($str = 'd')
                                                        @foreach (explode(',', $protocol->diekperaiosi) as $d)
                                                            @if (strpos($str, substr($d, 0, 1)) !== false)
                                                                @if ($myUsers->where('id', '==', ltrim($d, $str))->count())
                                                                    {{ $myUsers->where('id', '==', ltrim($d, $str))->first()->name }}
                                                                @else
                                                                    {{ ltrim($d, $str) }}
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </span>
                                                <hr>
                                                <span title='Διεκπεραίωση έως {{$protocol->diekp_eos}}' ">
                                                    @if ($protocol->diekp_eos)
                                                        &#x2727;{{ $protocol->diekp_eos }}
                                                    @else
                                                        &nbsp;
                                                    @endif
                                                </span>
                                                <hr>
                                                <span title='Διεκπεραιώθηκε τις {{ $protocol->diekp_date }}'>
                                                    @if ($protocol->diekp_date)
                                                        &#x2727;{{ $protocol->diekp_date }}
                                                    @else
                                                        &nbsp;
                                                    @endif
                                                </span>
                                            </div>
                                            <div class='col-md-4 col-sm-4 small' style="cursor:pointer"
                                                onclick="window.location.href='{{ URL::to('/') }}/home/{{ $protocol->id }}'">
                                                <span
                                                    title='{{ $protocol->describe ? 'Φάκελος ' . $protocol->fakelos . '->' . $protocol->describe : 'Φάκελος' }}'>
                                                    {{ $protocol->fakelos ? $protocol->fakelos : '' }}
                                                </span>
                                                <hr>
                                                <span title='Σχετικοί αριθμοί'>
                                                    @if ($protocol->sxetiko)
                                                        &#x2727;{!! $protocol->sxetiko !!}
                                                    @else
                                                        &nbsp;
                                                    @endif
                                                </span>
                                                <hr>
                                                <span title='Παρατηρήσεις'>
                                                    @if ($protocol->paratiriseis)
                                                        &#x2726;{{ $protocol->paratiriseis }}
                                                    @else
                                                        &nbsp;
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-md-1 col-sm-1 small' style='overflow:hidden'>
                                        <ul class='list-unstyled'>
                                            @foreach ($protocol->attachments as $attachment)
                                                <li>
                                                    @if ($attachment->name)
                                                        <a href='{{ URL::to('/') }}/download/{{ $attachment->id }}'
                                                            target="_blank" title='Λήψη {{ $attachment->name }}'>
                                                            @if (strlen($attachment->name) > 17)
                                                                {{ mb_substr($attachment->name, 0, 7, 'utf-8') }}...{{ mb_substr($attachment->name, -7, 7, 'utf-8') }}@else{{ $attachment->name }}
                                                            @endif
                                                        </a>
                                                    @endif
                                                    @if ($attachment->name and $attachment->ada)
                                                        <br>
                                                    @endif
                                                    @if ($attachment->ada)
                                                        <a href='{{ $diavgeiaUrl }}{{ $attachment->ada }}'
                                                            target="_blank" title='Λήψη {{ $attachment->ada }}'>
                                                            @if (strlen($attachment->ada) > 17)
                                                                {{ mb_substr($attachment->ada, 0, 7, 'utf-8') }}...{{ mb_substr($attachment->ada, -3, 3, 'utf-8') }}@else{{ $attachment->ada }}
                                                            @endif
                                                        </a>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                        <hr>
                                        @foreach ($protocol->attachments as $attachment)
                                            @if ($loop->first)
                                                @if (is_numeric($attachment->keep))
                                                    <span
                                                        title='Διατηρηση: {{ $attachment->keep == 1 ? $attachment->keep . ' χρόνo' : $attachment->keep . ' χρόνια' }}'>
                                                        {{ $attachment->keep == 1 ? $attachment->keep . ' χρόνo' : $attachment->keep . ' χρόνια' }}
                                                    </span>
                                                @else
                                                    <span title='Διατηρηση: {{ $attachment->keep }}'>
                                                        {{ $attachment->keep }}
                                                    </span>
                                                @endif
                                            @endif
                                        @endforeach
                                        <br>
                                        @foreach ($protocol->attachments as $attachment)
                                            @if ($loop->first and $attachment->expires)
                                                <span
                                                    title='Διατήρηση έως {{ \Carbon\Carbon::createFromFormat('Ymd', $attachment->expires)->format('d/m/Y') }}'>
                                                    {{ \Carbon\Carbon::createFromFormat('Ymd', $attachment->expires)->format('d/m/Y') }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                        </div>
                       {{--  @if ($i % 2)
                            <div class='row'>&nbsp;</div>
                        @else
                            <div class='row  bg-info'>&nbsp;</div>
                        @endif --}}
                        @php($i++)
                        @endforeach

                        @else{{-- SHORT-LONG LIST --}}

                        {{-- SHORT LIST --}}

                        {{-- ΕΠΙΚΕΦΑΛΙΔΕΣ TABLE --}}

                        <div data-spy="affix" data-offset-top="240">
                            <div class='row  bg-primary'>&nbsp;</div>
                            <div class='row bg-primary'>

                                <div class='col-md-1 col-sm-1'>
                                    <strong style="cursor:pointer"
                                        onClick="setFieldSort('protocolnum')">{!! request()->get('field') == 'protocolnum'
                                            ? (request()->get('sort') == 'desc'
                                                ? '&#8657;&nbsp;&nbsp;'
                                                : '&#8659;&nbsp;&nbsp;')
                                            : '' !!}Αυξ.Αριθ</strong>
                                </div>
                                <div class='col-md-1 col-sm-1'>
                                    <strong style="cursor:pointer"
                                        onClick="setFieldSort('protocoldate')">{!! request()->get('field') == 'protocoldate'
                                            ? (request()->get('sort') == 'desc'
                                                ? '&#8657;&nbsp;&nbsp;'
                                                : '&#8659;&nbsp;&nbsp;')
                                            : '' !!}Ημνία</strong>
                                </div>

                                <div class='col-md-7 col-sm-7'>
                                    <div class='row'>
                                        <div class='col-md-3 col-sm-3'>
                                            <strong style="cursor:pointer"
                                                onClick="setFieldSort('in_arxi_ekdosis')">{!! request()->get('field') == 'in_arxi_ekdosis'
                                                    ? (request()->get('sort') == 'desc'
                                                        ? '&#8657;&nbsp;&nbsp;'
                                                        : '&#8659;&nbsp;&nbsp;')
                                                    : '' !!}Αρχή Έκδοσης</strong>
                                        </div>
                                        <div class='col-md-9 col-sm-9'>

                                            <strong style="cursor:pointer"
                                                onClick="setFieldSort('thema')">{!! request()->get('field') == 'thema'
                                                    ? (request()->get('sort') == 'desc'
                                                        ? '&#8657;&nbsp;&nbsp;'
                                                        : '&#8659;&nbsp;&nbsp;')
                                                    : '' !!}Θέμα</strong>

                                        </div>
                                    </div>
                                </div>

                                <div class='col-md-3 col-sm-3'>
                                <div class='col-md-4 col-sm-4'>
                                    <strong style="cursor:pointer"
                                                onClick="setFieldSort('out_to')">{!! request()->get('field') == 'out_to'
                                                    ? (request()->get('sort') == 'desc'
                                                        ? '&#8657;&nbsp;&nbsp;'
                                                        : '&#8659;&nbsp;&nbsp;')
                                                    : '' !!}Απευθύνεται</strong>
                                </div>
                                <div class='col-md-4 col-sm-4'>
                                    <strong style="cursor:pointer"
                                                onClick="setFieldSort('diekp_eos')">{!! request()->get('field') == 'diekp_eos'
                                                    ? (request()->get('sort') == 'desc'
                                                        ? '&#8657;&nbsp;&nbsp;'
                                                        : '&#8659;&nbsp;&nbsp;')
                                                    : '' !!}Διεκπ.έως</strong>
                                </div>

                                <div class='col-md-4 col-sm-4'>
                                    <strong>Συνημμένα</strong>
                                </div>
                            </div>
                        </div>
                            <div class='row  bg-primary'>&nbsp;</div>
                        </div>


                        {{-- ΔΕΔΟΜΕΝΑ TABLE --}}
                        @php($i = 1)
                        @foreach ($protocols as $protocol)
                            @if ($i % 2)
                                <div class='row line'>
                                @else
                                    <div class='row line line2'">
                            @endif

                            <div class='col-md-1 col-sm-1 {{$protocol->deadLineClass}}'  @if($protocol->deadLineClass) title='Διεκπεραίωση έως {{$protocol->diekp_eos}}' @endif style="cursor:pointer"
                                onclick="window.location.href='{{ URL::to('/') }}/home/{{ $protocol->id }}'">
                                <a href="{{ URL::to('/') }}/home/{{ $protocol->id }}/1" class="" role="button"
                                    title="Αντιγραφή ως Νέο"> <img src="{{ URL::to('/') }}/images/copy-stamp.png"
                                        height="20" /></a>
                                &nbsp;
                                <span title='Αυξων Αριθμός'>
                                    <strong>{{ $protocol->protocolnum }}</strong>
                            </div>

                            <div class='col-md-1 col-sm-1' style="cursor:pointer"
                                onclick="window.location.href='{{ URL::to('/') }}/home/{{ $protocol->id }}'">
                                </span>
                                <span title='Ημνία'>
                                    {{ $protocol->protocoldate }}
                                </span>
                            </div>

                            <div class='col-md-7 col-sm-7'style="cursor:pointer"
                                onclick="window.location.href='{{ URL::to('/') }}/home/{{ $protocol->id }}'">
                                <div class='row'>
                                    <div class='col-md-3 col-sm-3 small'>
                                        <span title='Αρχή Έκδοσης'>
                                            {{ $protocol->in_arxi_ekdosis }}
                                        </span>
                                    </div>
                                    <div class='col-md-9 col-sm-9 small' style="cursor:pointer"
                                        onclick="window.location.href='{{ URL::to('/') }}/home/{{ $protocol->id }}'">
                                        <span title='Θέμα'><strong>
                                                {{ $protocol->thema ? $protocol->thema : '' }}
                                            </strong></span>
                                    </div>
                                </div>
                            </div>

                            <div class='col-md-3 col-sm-3' style="cursor:pointer">

                                <div class='col-md-4 col-sm-4' style="cursor:pointer"
                                    onclick="window.location.href='{{ URL::to('/') }}/home/{{ $protocol->id }}'">

                                    <span title='Απευθύνεται'>
                                        {{ $protocol->out_to }}
                                    </span>

                                </div>
                               <div class='col-md-4 col-sm-4 {{$protocol->deadLineClass}}' 
                                    @if($protocol->deadLineClass) title='Διεκπεραίωση έως {{$protocol->diekp_eos}}' @endif 
                                    @if(!$protocol->deadLineClass && $protocol->diekp_date)
                                        title='Διεκπεραιώθηκε τις {{$protocol->diekp_date}}'
                                    @endif
                                    style="cursor:pointer"
                                    onclick="window.location.href='{{ URL::to('/') }}/home/{{ $protocol->id }}'">

                                    <span>
                                        {{ $protocol->diekp_eos }}
                                    </span>
                                    @if(!$protocol->deadLineClass && $protocol->diekp_date)
                                        <span class="pull-right">
                                            &check;
                                        </span>
                                    @endif
                                    

                                </div>

                                <div class='col-md-4 col-sm-4 small' style='overflow:hidden'>
                                    <ul class='list-unstyled'>
                                        @foreach ($protocol->attachments as $attachment)
                                            <li>
                                                @if ($attachment->name)
                                                    <a href='{{ URL::to('/') }}/download/{{ $attachment->id }}'
                                                        target="_blank" title='Λήψη {{ $attachment->name }}'>
                                                        @if (strlen($attachment->name) > 17)
                                                            {{ mb_substr($attachment->name, 0, 7, 'utf-8') }}...{{ mb_substr($attachment->name, -7, 7, 'utf-8') }}@else{{ $attachment->name }}
                                                        @endif
                                                    </a>
                                                @endif
                                                @if ($attachment->name and $attachment->ada)
                                                    <br>
                                                @endif
                                                @if ($attachment->ada)
                                                    <a href='{{ $diavgeiaUrl }}{{ $attachment->ada }}' target="_blank"
                                                        title='Λήψη {{ $attachment->ada }}'>
                                                        @if (strlen($attachment->ada) > 17)
                                                            {{ mb_substr($attachment->ada, 0, 7, 'utf-8') }}...{{ mb_substr($attachment->ada, -3, 3, 'utf-8') }}@else{{ $attachment->ada }}
                                                        @endif
                                                    </a>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                    </div>

                    @php($i++)
                    @endforeach
                    @endif

                    {{-- ΕΠΙΣΤΡΟΦΗ ΚΟΡΥΦΗ --}}
                    <div class='row'>
                        <div class="col-md-12 col-sm-12 text-center">
                            <a href="#">&#8686; Στην κορυφή &#8686;</a>
                        </div>
                    </div>

                    {{-- PAGINATION LINKS --}}
                    @if ($protocols->links() and $protocols->count() > $protocols->perPage() / 2)
                        <div class='row'>
                            <div class="col-md-12 col-sm-12 small text-center">
                                <span class="small">{{ $protocols->withQueryString()->links() }}</span>
                            </div>
                        </div>
                    @endif

                </div>

            </div>
        </div>
    </div>
    </div>
    </div>

    @if ($refreshInterval > 0)
        <script type="text/javascript">
            function startTimer(duration, display) {
                var timer = duration,
                    minutes, seconds
                setInterval(function() {
                    minutes = parseInt(timer / 60, 10)
                    seconds = parseInt(timer % 60, 10)

                    //minutes = minutes < 10 ? "0" + minutes : minutes
                    seconds = seconds < 10 ? "0" + seconds : seconds

                    display.textContent = minutes + ":" + seconds

                    if (--timer < 0) {
                        window.location.reload()

                    }
                }, 1000)
            }

            window.onload = function() {
                var duration = {{ $refreshInterval }},
                    display = document.querySelector('#timer')
                startTimer(duration, display);
                @if (!$allowedEmailUsers || strpos($allowedEmailUsers, Auth::user()->username) !== false)
                    $.ajax({
                        url: '{{ URL::to('/') }}/getEmailNum',
                        success: function(data) {
                            if (data > 0) {
                                $('#emailNumDiv').html(
                                    `<a href="{{ URL::to('/') }}/viewEmails" id="emailNum" class="active" role="button"
                        title="" style="display:block"><img src="{{ URL::to('/') }}/images/email-in.png" height=30 /></a>`
                                )
                                $('#emailNum').prop('title', "Eισερχόμενα email: " + data);
                                $('#emailNum').show();
                            }
                        }
                    })
                @endif
            }

            function setFieldSort(field) {
                //alert(field)
                //alert(url)
                url = '{!! request()->fullurl() !!}'
                const params = getQueryParams(url)

                //console.log(params)
                
                const baseUrl = url.split('?')[0]


                if (!params) {
                    window.location.href = baseUrl + "?field=" + field + "&sort=asc"
                } else {
                    if(!params['field'] ){
                        params['field'] = field
                        params['sort'] = 'asc'
                    }else if(params['field'] && params['field'] == field){
                         params['sort'] == 'asc' ? params['sort'] = 'desc' : params['sort'] = 'asc'
                    }else{
                        params['field'] = field
                        params['sort'] = 'asc'
                    }

                    querystr = '?'

                    Object.keys(params).forEach(key => {
                        querystr += key + '=' + params[key] + '&'
                    });

                    querystr = querystr.substring(0, querystr.length-1)

                    window.location.href = baseUrl + querystr
                }
            }

            function getQueryParams(url) {
                if (url.indexOf('?') == -1) return false;
                const paramArr = url.slice(url.indexOf('?') + 1).split('&');
                const params = {};
                paramArr.map(param => {
                    const [key, val] = param.split('=');
                    params[key] = decodeURIComponent(val);
                })
                return params;
            }

            function toggleList(){
                //alert('toggle')
                url = '{!! request()->fullurl() !!}'
                const params = getQueryParams(url)
                
                const baseUrl = url.split('?')[0]

                if (!params) {
                    window.location.href = baseUrl + "?exp=true"
                } else {
                    if(!params['exp'] ){
                        params['exp'] = 'true'
                    }else{
                        delete params['exp'] 
                    }

                    querystr = '?'

                    Object.keys(params).forEach(key => {
                        querystr += key + '=' + params[key] + '&'
                    });

                    querystr = querystr.substring(0, querystr.length-1)

                    window.location.href = baseUrl + querystr
                }
            }
        </script>
    @endif

    <style>
        @media (min-width: 768px) {
            .affix {
                top: 0px;
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
        .line{
            padding-top:7px;
            padding-bottom:5px;
        }
        .line:hover{
            background-color:#E6E6E6
        }
        .line2{
            background-color:WhiteSmoke
        }
        hr{
            margin: 3px
        }
    </style>
@endsection
