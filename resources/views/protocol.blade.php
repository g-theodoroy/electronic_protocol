@extends('layouts.app')

@section('content')

    <style>
        .asd {
            background: rgba(0, 0, 0, 0);
            border: none;
            font-weight: bold;
        }

        input[readonly].asd {
            background: rgba(0, 0, 0, 0);
            border: none;
            font-weight: bold;
            cursor: not-allowed;
        }

        input[readonly].inout {
            background: rgba(255, 255, 255, 255);
            cursor: not-allowed;
        }

        textarea[readonly].inout {
            background: rgba(255, 255, 255, 255);
            cursor: not-allowed;
        }

    </style>

    <div class="{{ $wideListProtocol ? 'container-fluid' : 'container' }}">
        <div class="row">
            <div class="col-md-12 ">
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
                        <div class="panel panel-default col-md-12 col-sm-12  ">

                            <form name="myProtocolForm" id="myProtocolForm" class="form-horizontal" role="form"
                                method="POST" action="{{ url('/home') }}{{ $protocol->id ? '/' . $protocol->id : '' }}"
                                enctype="multipart/form-data">
                                {{ csrf_field() }}

                                <div class="row {{ $class }}">
                                    <div class="col-md-1 col-sm-1 ">
                                        <input id="find" type="text" class="form-control text-center asd" name="find"
                                            title="Αναζήτηση" placeholder="" value="{{ old('find') ? old('find') : '' }}"
                                            tabindex=-1>
                                    </div>
                                    <div class="col-md-1 col-sm-1  form-control-static  text-center">
                                        <a href="javascript:chkfind()" class="active" role="button"
                                            title="Αναζήτηση"> <img src="{{ URL::to('/') }}/images/find.ico"
                                                height=25 /></a></td>
                                        @if (!in_array(Auth::user()->role->role, ['Συγγραφέας', 'Αναγνώστης']) || (in_array(Auth::user()->role->role, ['Συγγραφέας', 'Αναγνώστης']) && !App\Http\Controllers\ProtocolController::limitProtocolAccessList()))
                                            <a href="{{ URL::to('/print') }}" class="" role="button"
                                                title="Εκτύπωση"> <img src="{{ URL::to('/') }}/images/print.png"
                                                    height=25 /></a>
                                        @endif
                                    </div>

                                    <div class="col-md-7 col-sm-7 ">
                                        <div class="row">
                                            <div class="col-md-1 col-sm-1 form-control-static small text-center">
                                                <strong>Έτος</strong>
                                            </div>
                                            <div
                                                class="col-md-3 col-sm-3 middle {{ $errors->has('etos') ? ' has-error' : '' }}">
                                                <input id="etos" type="text" class="form-control input-lg text-center asd"
                                                    name="etos" placeholder="etos"
                                                    value="{{ old('etos') ? old('etos') : $newetos }}" required
                                                    tabindex=-1 {{ $readonly }}>
                                            </div>
                                            <div class="col-md-1 col-sm-1 small text-center">
                                                <strong>Αύξων<br>Αριθμός</strong>
                                            </div>
                                            <div
                                                class="col-md-3 col-sm-3 middle {{ $errors->has('protocolnum') ? ' has-error' : '' }}">
                                                <input id="protocolnum" type="text"
                                                    class="form-control input-lg text-center asd text-bold {{ $newprotocolnumvisible }}"
                                                    name="protocolnum" placeholder="num"
                                                    value="{{ old('protocolnum') ? old('protocolnum') : $newprotocolnum }}"
                                                    title='1. Αύξων αριθμός' required tabindex=-1 {{ $readonly }}>
                                            </div>
                                            <div class="col-md-1 col-sm-1 small text-center">
                                                <strong>Ημνια<br>παραλαβής</strong>
                                            </div>
                                            <div
                                                class="col-md-3 col-sm-3 middle {{ $errors->has('protocoldate') ? ' has-error' : '' }}">
                                                <input id="protocoldate" type="text"
                                                    class="form-control input-lg text-center asd" name="protocoldate"
                                                    placeholder="date"
                                                    value="{{ old('protocoldate') ? old('protocoldate') : $newprotocoldate }}"
                                                    title='2. Ημερομηνία παραλαβής εγγράφου' required tabindex=-1
                                                    {{ $readonly }}>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-2 text-center ">
                                        <a href="javascript:periigisi('bb')" class="active" role="button"
                                            title="- {{ $protocolArrowStep }}"> <img
                                                src="{{ URL::to('/') }}/images/arrow-left-double.png" height=13 /></a>
                                        <a href="javascript:periigisi('ff')" class="active" role="button"
                                            title="+ {{ $protocolArrowStep }}"> <img
                                                src="{{ URL::to('/') }}/images/arrow-right-double.png"
                                                height=13 /></a><br>
                                        <a href="javascript:periigisi('b')" class="active" role="button"
                                            title="- 1"> <img src="{{ URL::to('/') }}/images/arrow-left.png"
                                                height=13 /></a>
                                        <a href="javascript:periigisi('f')" class="active" role="button"
                                            title="+ 1"> <img src="{{ URL::to('/') }}/images/arrow-right.png"
                                                height=13 /></a>
                                    </div>
                                    <div class="col-md-1 col-sm-1 text-center  form-control-static ">
                                        <a href="{{ URL::to('/') }}/home" class="active" role="button"
                                            title="Νέο"> <img src="{{ URL::to('/') }}/images/addnew.ico" height=25 /></a>
                                        @if($protocol->id) 
                                        <a href="{{ url('/home') }}{{ $protocol->id ? '/' . $protocol->id . '/1' : '' }}" class="active" role="button"
                                            title="Αντιγραφή ως Νέο"> <img src="{{ URL::to('/') }}/images/copy-stamp.png" height=25 /></a>
                                        @endif
                                        <a href="javascript:formSubmit()" class="{{ $submitVisible }}" role="button"
                                            title="Αποθήκευση"> <img src="{{ URL::to('/') }}/images/save.ico"
                                                height=25 /></a>
                                    </div>
                                </div>

                                <div class="row ">
                                    <div class="col-md-1 col-sm-1 form-control-static small text-center">
                                        <strong>Φάκελος</strong>
                                    </div>
                                    <div class="col-md-2 col-sm-2 {{ $errors->has('fakelos') ? ' has-error' : '' }}">
                                        <select id="fakelos" class="form-control selectpicker" data-live-search="true"
                                            liveSearchNormalize="true" name="fakelos" autofocus
                                            data-value="{{ $protocol->fakelos }}"
                                            @if ($headReadonly) onchange="this.value = this.getAttribute('data-value');" disabled="disabled"  @else onchange='getKeep4Fakelos()' @endif>
                                            @if (!$protocol->attachments()->count())
                                                <option value=''></option>
                                            @endif
                                            @foreach ($fakeloi as $fakelos)
                                                @if ($fakelos['fakelos'] == $protocol->fakelos)
                                                    <option value='{{ $fakelos['fakelos'] }}'
                                                        title='{{ $fakelos['fakelos'] }} - {{ $fakelos['describe'] }}'
                                                        style="white-space: pre-wrap; width: 500px;" selected>{{ $fakelos['fakelos'] }} - {{ $fakelos['describe'] }}</option>
                                                @else
                                                    <option value='{{ $fakelos['fakelos'] }}'
                                                        title='{{ $fakelos['fakelos'] }} - {{ $fakelos['describe'] }}'
                                                        style="white-space: pre-wrap; width: 500px;">{{ $fakelos['fakelos'] }} - {{ $fakelos['describe'] }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-1 col-sm-1 form-control-static small text-center">
                                        <strong>Θέμα</strong>
                                    </div>
                                    <div id="themaDiv"
                                        class="col-md-7 col-sm-7 middle {{ $errors->has('thema') ? ' has-error' : '' }}">
                                        <input id="thema" oninput="getValues(this.id, 'thema', 'themaList', 0)" type="text"
                                            class="form-control inout" name="thema" placeholder="thema"
                                            value="{{ old('thema') ? old('thema') : $protocol->thema }}" title='Θέμα'
                                            {{ $headReadonly }}>
                                        <div id="themaList" class="col-md-12 col-sm-12"></div>
                                    </div>
                                    <div class="col-md-1 col-sm-1 text-center">
                                        @if ($protocol->id)
                                            <a href="javascript:chkprotocoldelete('{{ $protocol->id }}','{{ $protocol->etos }}','{{ $protocol->protocolnum }}')"
                                                class="{{ $delVisible }}" role="button" title="Διαγραφή Πρωτοκόλλου"
                                                tabindex=-1> <img src="{{ URL::to('/') }}/images/delete.ico"
                                                    height="20" /></a>
                                            <a href="javascript:receiptToEmail()" class="{{ $submitVisible }}"
                                                role="button" title="Απόδειξη παραλαβής με email" tabindex=-1> <img
                                                    src="{{ URL::to('/') }}/images/receipt-email.png" height="20" /></a>
                                            <a href="{{ URL::to('/') }}/receipt/{{ $protocol->id }}"
                                                class="{{ $submitVisible }}" role="button"
                                                title="Απόδειξη παραλαβής εκτύπωση" target="_blank" tabindex=-1> <img
                                                    src="{{ URL::to('/') }}/images/receipt.png" height="20" /></a>
                                        @endif
                                        <a href="javascript:document.forms['myProtocolForm'].reset();"
                                            class="active" role="button" title="Καθάρισμα φόρμας" tabindex=-1> <img
                                                src="{{ URL::to('/') }}/images/clear.ico" height="20" /></a>
                                    </div>
                                </div>

                                <div class="row bg-success">
                                    <div class="col-md-1 col-sm-1 small text-center">
                                        <strong>Αριθ.<br>Εισερχ.</strong>
                                    </div>
                                    <div id="in_numDiv"
                                        class="col-md-2 col-sm-2 {{ $errors->has('in_num') ? ' has-error' : '' }}">
                                        <input id="in_num" type="text" class="form-control text-center inout" name="in_num"
                                            placeholder="in_num"
                                            value="{{ old('in_num') ? old('in_num') : $protocol->in_num }}"
                                            title='3. Αριθμός εισερχομένου εγγράφου' {{ $inReadonly }}>
                                    </div>
                                    <div class="col-md-1 col-sm-1 small text-center">
                                        <strong>Ημνία<br>Εισερχ.</strong>
                                    </div>
                                    <div id="in_dateDiv"
                                        class="col-md-2 col-sm-2 {{ $errors->has('in_date') ? ' has-error' : '' }}">
                                        <input id="in_date" type="text"
                                            class="form-control @if (!$inReadonly) datepicker @endif text-center inout"
                                            name="in_date" placeholder="in_date"
                                            value="{{ old('in_date') ? old('in_date') : $in_date }}"
                                            title='5. Χρονολογία εισερχομένου εγγράφου' {{ $inReadonly }}>
                                    </div>
                                    <div class="col-md-1 col-sm-1 small text-center">
                                        <strong>Τόπος<br>Έκδοσης</strong>
                                    </div>
                                    <div id="in_topos_ekdosisDiv"
                                        class="col-md-5 col-sm-5 {{ $errors->has('in_topos_ekdosis') ? ' has-error' : '' }}">
                                        <input id="in_topos_ekdosis"
                                            oninput="getValues(this.id, 'in_topos_ekdosis', 'in_topos_ekdosisList', 0)"
                                            type="text" class="form-control inout" name="in_topos_ekdosis"
                                            placeholder="in_topos_ekdosis"
                                            value="{{ old('in_topos_ekdosis') ? old('in_topos_ekdosis') : $protocol->in_topos_ekdosis }}"
                                            title='4. Τόπος που εκδόθηκε' {{ $inReadonly }}>
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
                                                    type="text" class="form-control inout" name="in_arxi_ekdosis"
                                                    placeholder="in_arxi_ekdosis"
                                                    value="{{ old('in_arxi_ekdosis') ? old('in_arxi_ekdosis') : $protocol->in_arxi_ekdosis }}"
                                                    title='5. Αρχή που το έχει εκδώσει' {{ $inReadonly }}>
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
                                                    type="text" class="form-control inout" name="in_paraliptis"
                                                    placeholder="in_paraliptis"
                                                    value="{{ old('in_paraliptis') ? old('in_paraliptis') : $protocol->in_paraliptis }}"
                                                    title='7. Διεύθυνση, τμήμα, γραφείο ή πρόσωπο στο οποίο δόθηκε'
                                                    {{ $inReadonly }}>
                                                <div id="in_paraliptisList" class="col-md-12 col-sm-12"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 ">
                                        <div class="row">
                                            <div class="col-md-2 col-sm-2 small text-center form-control-static">
                                                <strong>Περίληψη</strong>
                                            </div>
                                            <div
                                                class="col-md-10 col-sm-10 {{ $errors->has('in_perilipsi') ? ' has-error' : '' }}">
                                                <textarea id="in_perilipsi" type="text" class="form-control inout"
                                                    name="in_perilipsi" placeholder="in_perilipsi" value=""
                                                    title='6. Περίληψη εισερχομένου εγγράφου'
                                                    {{ $inReadonly }}>{{ old('in_perilipsi') ? old('in_perilipsi') : $protocol->in_perilipsi }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row ">
                                    <div class="col-md-1 col-sm-1 small text-center form-control-static">
                                        <strong>Διεκπεραίωση</strong>
                                    </div>
                                    <input id="sendEmailTo" name="sendEmailTo" type="hidden" value="" />
                                    @if ($forbidenChangeDiekperaiosiSelect)
                                        <div
                                            class="col-md-5 col-sm-5 {{ $errors->has('diekperaiosi') ? ' has-error' : '' }}">
                                            <select id="diekperaiosi" multiple class="form-control selectpicker"
                                                style="text-overflow:hidden;" name="diekperaiosi[]"
                                                title='Διεκπεραίωση - Ενημέρωση'
                                                data-value="{{ $protocol->diekperaiosi }}" disabled="disabled">
                                                <optgroup label="Διεκπεραίωση" data-max-options="1">
                                                    @foreach ($writers_admins as $writer_admin)
                                                        <option value='d{{ $writer_admin->id }}'
                                                            @if (strpos($protocol->diekperaiosi . ',', 'd' . $writer_admin->id . ',') !== false) selected @endif>
                                                            {{ $writer_admin->name }}</option>
                                                    @endforeach
                                                </optgroup>
                                                <optgroup label="Ενημέρωση">
                                                    @foreach ($writers_admins as $writer_admin)
                                                        <option value='e{{ $writer_admin->id }}'
                                                            @if (strpos($protocol->diekperaiosi . ',', 'e' . $writer_admin->id . ',') !== false) selected @endif>
                                                            {{ $writer_admin->name }}</option>
                                                    @endforeach
                                                </optgroup>
                                            </select>
                                        </div>
                                    @else
                                        <div
                                            class="col-md-5 col-sm-5 {{ $errors->has('diekperaiosi') ? ' has-error' : '' }}">
                                            <div class="row">
                                                <div class="col=md-11 col-sm-11">
                                                    <select id="diekperaiosi" multiple class="form-control selectpicker"
                                                        style="text-overflow:hidden;" name="diekperaiosi[]"
                                                        title='Διεκπεραίωση - Ενημέρωση'
                                                        data-value="{{ $protocol->diekperaiosi }}">
                                                        <optgroup label="Διεκπεραίωση" data-max-options="1">
                                                            @foreach ($writers_admins as $writer_admin)
                                                                <option value='d{{ $writer_admin->id }}'
                                                                    @if (strpos($protocol->diekperaiosi . ',', 'd' . $writer_admin->id . ',') !== false) selected @endif>
                                                                    {{ $writer_admin->name }}</option>
                                                            @endforeach
                                                        </optgroup>
                                                        <optgroup label="Ενημέρωση">
                                                            @foreach ($writers_admins as $writer_admin)
                                                                <option value='e{{ $writer_admin->id }}'
                                                                    @if (strpos($protocol->diekperaiosi . ',', 'e' . $writer_admin->id . ',') !== false) selected @endif>
                                                                    {{ $writer_admin->name }}</option>
                                                            @endforeach
                                                        </optgroup>
                                                    </select>
                                                </div>
                                                <div class="col=md-1 col-sm-1 form-control-static"
                                                    style="padding-left: 5px;padding-right: 5px;"
                                                    onclick="javascript:anathesiSe()" title="Ανάθεση πρωτοκόλλου"><img
                                                        src="{{ URL::to('/') }}/images/todo.png" height=20 /></div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-md-1 col-sm-1 small text-center">
                                        <strong>Ημνία<br>Διεκπεραίωσης</strong>
                                    </div>

                                    <div class="col-md-5 col-sm-5">
                                        <div class="row">
                                            <div id="diekp_dateDiv"
                                                class="col-md-4 col-sm-4 {{ $errors->has('diekp_date') ? ' has-error' : '' }}">
                                                <div class="input-group">
                                                    <input id="diekp_date" type="text"
                                                        class="form-control  @if (!$diekpDateReadonly) datepicker @endif  text-center inout"
                                                        name="diekp_date" placeholder="diekp_date"
                                                        value="{{ old('diekp_date') ? old('diekp_date') : $diekp_date }}"
                                                        title='11. Ημερομηνία διεκπεραίωσης' {{ $diekpDateReadonly }}>
                                                    <div class="input-group-btn {{ $readerVisible }}"
                                                        style="padding-left: 5px;padding-right: 5px;"
                                                        onclick="javascript:setDiekperaiomeno()"
                                                        title="Σήμανση ως Διεκπεραιωμένο"><img
                                                            src="{{ URL::to('/') }}/images/done.png" height=20 /></div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-2 small text-center">
                                                <strong>Σχετικοί<br>αριθμοί</strong>
                                            </div>
                                            <div
                                                class="col-md-6 col-sm-6 {{ $errors->has('sxetiko') ? ' has-error' : '' }}">
                                                <div class="input-group">
                                                    <input id="sxetiko"
                                                        oninput="getValues(this.id, 'sxetiko', 'sxetikoList', 1)"
                                                        type="text" class="form-control text-center inout" name="sxetiko"
                                                        placeholder="sxetiko"
                                                        value="{{ old('sxetiko') ? old('sxetiko') : $protocol->sxetiko }}"
                                                        title='12. Σχετικοί αριθμοί' {{ $outReadonly }}>
                                                    <div class="input-group-btn"
                                                        style="padding-left: 5px;padding-right: 5px;"
                                                        onclick="javascript:findSxetiko()"
                                                        title="Μετάβαση στο σχετικό Πρωτόκολλο"><img
                                                            src="{{ URL::to('/') }}/images/find.ico" height=20 /></div>
                                                </div>
                                                <div id="sxetikoList" class="col-md-12 col-sm-12"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row bg-info">
                                    <div class="col-md-6 col-sm-6 ">
                                        <div class="row">
                                            <div class="col-md-2 col-sm-2 small text-center form-control-static">
                                                <strong>Απευθύνεται</strong>
                                            </div>
                                            <div id="out_toDiv"
                                                class="col-md-10 col-sm-10 {{ $errors->has('out_to') ? ' has-error' : '' }}">
                                                <input id="out_to" oninput="getValues(this.id, 'out_to', 'out_toList', 0)"
                                                    type="text" class="form-control inout" name="out_to"
                                                    placeholder="out_to"
                                                    value="{{ old('out_to') ? old('out_to') : $protocol->out_to }}"
                                                    title='8. Αρχή στην οποία απευθύνεται' {{ $outReadonly }}>
                                                <div id="out_toList" class="col-md-12 col-sm-12"></div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div
                                                class="col-md-2 col-sm-2 col-md-offset-3 col-sm-offset-3 small text-center ">
                                                <strong>Ημνία<br>Εξερχ.</strong>
                                            </div>
                                            <div id="out_dateDiv"
                                                class="col-md-4 col-sm-4 {{ $errors->has('out_date') ? ' has-error' : '' }}">
                                                <input id="out_date" type="text"
                                                    class="form-control  @if (!$outReadonly) datepicker @endif  text-center inout"
                                                    name="out_date" placeholder="out_date"
                                                    value="{{ old('out_date') ? old('out_date') : $out_date }}"
                                                    title='10. Χρονολογία εξερχομένου εγγράφου' {{ $outReadonly }}>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 ">
                                        <div class="row">
                                            <div class="col-md-2 col-sm-2 small text-center form-control-static">
                                                <strong>Περίληψη</strong>
                                            </div>
                                            <div
                                                class="col-md-10 col-sm-10 {{ $errors->has('out_perilipsi') ? ' has-error' : '' }}">
                                                <textarea id="out_perilipsi" type="text" class="form-control inout"
                                                    name="out_perilipsi" placeholder="out_perilipsi" value=""
                                                    title='9. Περίληψη εξερχομένου εγγράφου'
                                                    {{ $outReadonly }}>{{ old('out_perilipsi') ? old('out_perilipsi') : $protocol->out_perilipsi }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="row ">
                                    <div class="col-md-6 col-sm-6 ">
                                        <div class="row">
                                            <div class="col-md-2 col-sm-2 small text-center form-control-static">
                                                <strong>Παρατηρήσεις</strong>
                                            </div>
                                            <div
                                                class="col-md-10 col-sm-10 {{ $errors->has('paratiriseis') ? ' has-error' : '' }}">
                                                <textarea id="paratiriseis" type="text" class="form-control inout"
                                                    name="paratiriseis" placeholder="paratiriseis" title='Παρατηρήσεις'
                                                    {{ $outReadonly }}>{{ old('paratiriseis') ? old('paratiriseis') : $protocol->paratiriseis }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 ">
                                        <div class="row">
                                            <div class="col-md-2 col-sm-2 small text-center ">
                                                <strong>Λέξεις<br>κλειδιά</strong>
                                            </div>
                                            <div
                                                class="col-md-10 col-sm-10 {{ $errors->has('keywords') ? ' has-error' : '' }}">
                                                <input id="keywords"
                                                    oninput="getValues(this.id, 'keywords', 'keywordsList', 1)" type="text"
                                                    class="form-control inout" name="keywords" placeholder="keywords"
                                                    value="{{ old('keywords') ? old('keywords') : $protocol->keywords }}"
                                                    title='Παρατηρήσεις' {{ $outReadonly }}>
                                                <div id="keywordsList" class="col-md-12 col-sm-12"></div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2 col-sm-2 small text-center "
                                                title="Email για αποστολή Απόδειξης παραλαβής">
                                                <strong>Απ.Παραλ.<br>στο email</strong>
                                            </div>
                                            <div
                                                class="col-md-10 col-sm-10 {{ $errors->has('reply_to_email') ? ' has-error' : '' }}">
                                                <input id="reply_to_email" type="text" class="form-control inout"
                                                    name="reply_to_email" placeholder="reply_to_email"
                                                    value="{{ old('reply_to_email') ? old('reply_to_email') : '' }}"
                                                    title='Συμπληρώστε το email στο οποίο θέλετε να στείλετε Απόδειξη παραλαβής'
                                                    {{ $outReadonly }}>
                                                <div id="reply_toList" class="col-md-12 col-sm-12"></div>
                                            </div>
                                        </div>
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
                                                        <a href='{{ URL::to('/') }}/download/{{ $attachment->id }}'
                                                            target="_blank"
                                                            title="Λήψη {{ $attachment->name }}">{{ $attachment->name }}</a>
                                                    @endif
                                                    @if ($attachment->ada)
                                                        <a href='{{ $diavgeiaUrl }}{{ $attachment->ada }}'
                                                            target="_blank"
                                                            title="Λήψη {{ $attachment->ada }}">{{ $attachment->ada }}</a>
                                                    @endif
                                                    <a href="javascript:chkdelete('{{ $attachment->id }}','{{ $attachment->name }}')"
                                                        class="{{ $submitVisible }}"
                                                        id='delatt{{ $attachment->id }}'
                                                        title="Διαγραφή {{ $attachment->name ? $attachment->name : $attachment->ada }}">
                                                        <img src="{{ URL::to('/') }}/images/delete.ico" alt="delete"
                                                            height="13"> </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="col-md-2 col-sm-2 small text-right form-control-static">
                                        <input id="file_inputs_count" type="hidden" class="form-control"
                                            name="file_inputs_count" value="0">
                                        <a href="#" onclick="getFileInputs()" class="{{ $submitVisible }}"
                                            role="button" title="Προσθήκη συνημμένων αρχείων"> <img
                                                src="{{ URL::to('/') }}/images/attachment.png" height=25 /></a>
                                        <a href="#"
                                            onclick='$("#file_inputs_count").val(0);$("#show_protocol_file_inputs").empty();$("#keepdiv").addClass("hidden")'
                                            class="{{ $submitVisible }}" role="button"
                                            title="Καθάρισμα συνημμένων αρχείων"> <img
                                                src="{{ URL::to('/') }}/images/clear.ico" height="20" /></a>
                                        <a href="{{ URL::to('/') }}/home/list" class="active" role="button"
                                            title="Λίστα Πρωτοκόλλου"> <img src="{{ URL::to('/') }}/images/protocol.png"
                                                height=25 /></a>
                                    </div>
                                </div>
                                <div id="keepdiv" class="row hidden">
                                    <div class="col-md-4 col-sm-4 small form-control-static">
                                        <strong>Επιλέξτε αρχείο {!! ini_get('upload_max_filesize') ? '<span class="bg-primary padding-xs">έως ' . ini_get('upload_max_filesize') . '</span>' : '' !!}<br>ή πληκτρολογείστε ΑΔΑ</strong>
                                    </div>
                                    @if ($allowUserChangeKeepSelect)
                                        <div class="col-md-4 col-sm-4 small text-right form-control-static">
                                            <strong>Χρόνος διατήρησης</strong>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <select id="keep" class="form-control small selectpicker" name="keep"
                                                title='Χρόνος Διατήρησης'>
                                            @else
                                                <div class="col-md-4 col-sm-4 small text-right form-control-static"
                                                    title='Οι ρυθμίσεις δεν επιτρέπουν να αλλάξετε την επιλογή'>
                                                    <strong>Χρόνος διατήρησης</strong>
                                                </div>
                                                <div class="col-md-4 col-sm-4"
                                                    title='Οι ρυθμίσεις δεν επιτρέπουν να αλλάξετε την επιλογή'>
                                                    <select id="keep" class="form-control small selectpicker"
                                                        data-value="{{ $keepval }}"
                                                        onchange="this.value = this.getAttribute('data-value');" name="keep"
                                                        title='Χρόνος Διατήρησης'>
                                    @endif
                                    <option value=''></option>
                                    @foreach ($years as $year)
                                        @if ($year->keep == $keepval)
                                            <option value='{{ $year->keep }}'
                                                title='{{ $year->keep }} {{ $year->keep > 1 ? 'χρόνια' : 'χρόνο' }}'
                                                selected>{{ $year->keep }}
                                                {{ $year->keep > 1 ? 'χρόνια' : 'χρόνο' }} </option>
                                        @else
                                            <option value='{{ $year->keep }}'
                                                title='{{ $year->keep }} {{ $year->keep > 1 ? 'χρόνια' : 'χρόνο' }}'>
                                                {{ $year->keep }} {{ $year->keep > 1 ? 'χρόνια' : 'χρόνο' }}
                                            </option>
                                        @endif
                                    @endforeach
                                    @foreach ($words as $word)
                                        @if ($word->keep_alt == $keepval)
                                            <option value='{{ $word->keep_alt }}' title='{{ $word->keep_alt }}'
                                                selected>{{ $word->keep_alt }}</option>
                                        @else
                                            <option value='{{ $word->keep_alt }}' title='{{ $word->keep_alt }}'>
                                                {{ $word->keep_alt }}</option>
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
                        @if ($showUserInfo == 1)
                            @if ($protocol->id)
                                @if ($protocol->created_at == $protocol->updated_at)
                                    Καταχωρίστηκε {{ $protocol->updated_at }}
                                    @if ($protocolUser)
                                        από {{ $protocolUser->username }}
                                    @endif
                                @else
                                    Ενημερώθηκε {{ $protocol->updated_at }}
                                    @if ($protocolUser)
                                        από {{ $protocolUser->username }}
                                    @endif
                                @endif
                            @endif
                        @elseif($showUserInfo == 2)
                            @if ($protocol->id)
                                @if ($protocol->created_at == $protocol->updated_at)
                                    Καταχωρίστηκε {{ $protocol->updated_at }}
                                    @if ($protocolUser)
                                        από {{ $protocolUser->name }}
                                    @endif
                                @else
                                    Ενημερώθηκε {{ $protocol->updated_at }}
                                    @if ($protocolUser)
                                        από {{ $protocolUser->name }}
                                    @endif
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
        function chkdelete(id, name) {
            var html =
                "<center><button type='button' id='confirmationRevertYes' class='btn btn-primary'>Ναί</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' id='confirmationRevertNo' class='btn btn-primary'>Όχι</button></center></p>"
            var msg = '<center><h4>Διαγραφή ?</h4><hr>Διαγραφή συννημένου ' + name + '. Είστε σίγουροι;<br>&nbsp;</center>'
            var $toast = toastr.warning(html, msg);
            $toast.delegate('#confirmationRevertYes', 'click', function() {
                $('#show_arxeia').load("{{ URL::to('/') }}" + "/attach/del/" + id);
                $toast.remove();
            });
            $toast.delegate('#confirmationRevertNo', 'click', function() {
                $toast.remove();
            });
        }

        function chkprotocoldelete(id, etos, num) {
            var html =
                "<center><button type='button' id='confirmationRevertYes' class='btn btn-primary'>Ναί</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' id='confirmationRevertNo' class='btn btn-primary'>Όχι</button></center></p>"
            var msg = '<center><h4>Διαγραφή ?</h4><hr>Διαγραφή πρωτοκόλλου με αριθμό ' + num + ' για το έτος ' + etos +
                '. Είστε σίγουροι;<br>&nbsp;</center>'
            var $toast = toastr.warning(html, msg);
            $toast.delegate('#confirmationRevertYes', 'click', function() {
                $(location).attr('href', "{{ URL::to('/') }}" + "/delprotocol/" + id);
                $toast.remove();
            });
            $toast.delegate('#confirmationRevertNo', 'click', function() {
                $toast.remove();
            });
        }

        function chkfind() {
            var protocolnum = $('#find').val()
            var etos = $('#etos').val()
            if (protocolnum) {
                $(location).attr('href', "{{ URL::to('/') }}" + "/goto/" + etos + "/" + protocolnum + "?find=1")
            } else {
                $(location).attr('href', "{{ URL::to('/find') }}")
            }
        }

        function periigisi(step) {
            var protocolnum = parseInt($('#protocolnum').val())
            var etos = $('#etos').val()
            $(location).attr('href', "{{ URL::to('/') }}" + "/goto/" + etos + "/" + protocolnum + "/" + step)
        }

        function getFileInputs() {
            if (!$("#fakelos").val()) {
                toastr.info(
                    "<center><h4>Ενημέρωση...</h4><hr>Για να προσθέσετε συνημμένα αρχεία είναι απαραίτητο να επιλέξετε Φάκελο<br>&nbsp;</center>"
                )
                return false;
            }
            var num = $("#file_inputs_count").val()
            var fak = $("#fakelos").val()
            $("#keepdiv").removeClass('hidden')
            $("#file_inputs_count").val(parseInt(num) + 1)
            $('#show_protocol_file_inputs').load("{{ URL::to('/') }}/getFileInputs/" + num);
        }

        function getKeep4Fakelos() {
            var fak = $("#fakelos").val()
            $.get("{{ URL::to('/') }}/getKeep4Fakelos/" + fak, function(data) {
                $("#keep").attr('data-value', data)
                $("#keep").val(data).change()
            });
        }

        @if ($time2update > 0)
            function startTimer(duration, display) {
            var timer = duration, minutes, seconds
            interval = setInterval(function () {
            minutes = parseInt(timer / 60, 10)
            seconds = parseInt(timer % 60, 10)
        
            //minutes = minutes < 10 ? "0" + minutes : minutes seconds=seconds < 10 ? "0" + seconds : seconds
                display.textContent='Δυνατότητα επεξεργασίας: ' + minutes + ":" + seconds + ' λεπτά' 
                if (--timer < 0) {
                    window.location.reload() } }, 1000) 
                } 
        @endif

        window.onload = function() {
            @if ($time2update > 0)
                var duration = {{ $time2update }},
                display = document.querySelector('#timer')
                startTimer(duration, display);
            @endif
            @if (!$allowedEmailUsers || strpos($allowedEmailUsers, Auth::user()->username) !== false)
                $.ajax({
                url: '{{ URL::to('/') }}/getEmailNum',
                success: function(data){
                if(data > 0){
                $('#emailNumDiv').html(`<a href="{{ URL::to('/') }}/viewEmails" id="emailNum" class="active" role="button"
                    title="" style="display:block"><img src="{{ URL::to('/') }}/images/email-in.png" height=30 /></a>`)
                $('#emailNum').prop('title', "Eισερχόμενα email: " + data);
                $('#emailNum').show();
                }
                }
                })
            @endif
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

        function sendEmailTo() {
            var oldId = $('#diekperaiosi').attr('data-value')
            var newId = $('#diekperaiosi').val() ? $('#diekperaiosi').val().join(',') : ''
            var userId = {{ Auth::user()->id }}
            if (!newId) return
            if (newId == userId) return
            if (newId == oldId) return
            $('#sendEmailTo').val(newId)
        }

        function receiptToEmail() {
            var isNewProtocol = {{ $protocol->id ? 'false' : 'true' }}
            var email = $('#reply_to_email').val();
            if (isNewProtocol) {
                if (email && !ValidateEmail(email)) {
                    toastr.error(
                        "<center><h4>Λάθος !!!</h4><hr></center>Το email αποστολής<br>Απόδειξης παραλαβής<br><br><strong>''" +
                        email + "''</strong><br><br>δεν έχει έγκυρη μορφή.<br>Παρακαλώ διορθώστε. <br>&nbsp;</center>")
                    $('#reply_to_email').select()
                    return false
                }
                return true
            } else {
                if (!email) {
                    toastr.info(
                        "<center><h4>Ενημέρωση...</h4></center><hr>Για να στείλετε ''Απόδειξη παραλαβής με email'' πρέπει να συμπληρώσετε... το email.<br>&nbsp;</center>"
                    )
                    $('#reply_to_email').select()
                } else if (email && !ValidateEmail(email)) {
                    toastr.error(
                        "<center><h4>Λάθος !!!</h4><hr></center>Το email αποστολής<br>Απόδειξης παραλαβής<br><br><strong>''" +
                        email + "''</strong><br><br>δεν έχει σωστή μορφή.<br>Παρακαλώ διορθώστε. <br>&nbsp;</center>")
                    $('#reply_to_email').select()
                } else {
                    $.ajax({
                        type: "POST",
                        url: '{{ route('receiptToEmail') }}',
                        dataType: 'JSON',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'id': {{ $protocol->id | null }},
                            'email': email
                        },
                        success: function(response) {
                            toastr.success(
                                "<center><h4>Ωραία !!!</h4><hr></center>Επιτυχής αποστολή βεβαίωσης καταχώρισης με Email.<br>&nbsp;</center>"
                            )
                            $('#paratiriseis').val(response)
                        },
                        error: function(data) {
                            toastr.error(
                                "<center><h4>Λάθος !!!</h4><hr></center>Δεν κατέστη δυνατή η αποστολή βεβαίωσης παραλαβής με Email<br>&nbsp;</center>"
                            )
                        }
                    });

                }
            }
        }

        function ValidateEmail(mail) {
            if (/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(mail)) {
                return true
            }
            return false
        }

        function setDiekperaiomeno() {
            var diekpDate = $('#diekp_date').val()
            if (!diekpDate) {
                toastr.info("<center><h4>Ενημέρωση...</h4><hr>Συμπληρώστε την ημερομηνία Διεκπεραίωσης<br>&nbsp;</center>")
                $('#diekp_date').select()
                return
            }
            $.ajax({
                type: "POST",
                url: '{{ route('setDiekpDate') }}',
                dataType: 'JSON',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'id': {{ $protocol->id | null }},
                    'diekp_date': diekpDate
                },
                success: function(response) {
                    location.reload()
                },
                error: function(data) {
                    toastr.error(
                        "<center><h4>Λάθος !!!</h4><hr></center>Δεν κατέστη δυνατή η ενημέρωση<br>&nbsp;</center>"
                    )
                }
            });
        }

        function findSxetiko() {
            var sxetiko = $('#sxetiko').val().trim()
            if (!sxetiko) {
                return
            }
            var terms = split($('#sxetiko').val());
            for (var i = 0; i < terms.length; i++) {
                if (!/^\d+[/]\d{4}$/.test(terms[i])) {
                    toastr.error(
                        "<center><h4>Λάθος !!!</h4><hr></center>Το σχετικό πρέπει να έχει τη μορφή<br><br>Αριθμός&nbsp;&nbsp;<b>/</b>&nbsp;&nbsp;τετραψήφιο έτος<br>&nbsp;</center>"
                    )
                    return
                }
            }
            if (terms.length == 1) {
                var data = terms[0].split(/\//);
                $(location).attr('href', "{{ URL::to('/') }}" + "/goto/" + data[1] + "/" + data[0] + "?find=1")
            } else {
                var msg = '';
                for (var i = 0; i < terms.length; i++) {
                    var data = terms[i].split(/\//);
                    href = "{{ URL::to('/goto') }}" + "/" + data[1] + "/" + data[0] + "?find=1"
                    msg += '<br><a href="' + href + '">Αρ. Πρωτ: ' + data[0] + ', έτος: ' + data[1] + '</a>'
                }
                toastr.info("<center><h4>Μετάβαση σε</h4><hr></center>" + msg + "<br>&nbsp;</center>")
            }
        }

        function formSubmit() {

            if (!formValidate()) return

            // ενεργοποιώ τα disabled πεδία
            for (let field of document.forms['myProtocolForm'].elements) {
                if (field.name) field.removeAttribute("disabled")
            }
            sendEmailTo()

            var in_num = $('#in_num').val().trim()
            var in_date = $('#in_date').val().trim()
            if (in_num && in_date) {
                $.ajax({
                    type: "POST",
                    url: '{{ route('checkInNum') }}',
                    dataType: 'JSON',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': {{ $protocol->id | null }},
                        'in_num': in_num,
                        'in_date': in_date
                    },
                    success: function(response) {
                        if (response > 0) {
                            var html =
                                "<center><button type='button' id='confirmRevertYes' class='btn btn-primary'>Ναί</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' id='confirmRevertNo' class='btn btn-primary'>Όχι</button></center></p>"
                            var msg =
                                "<center><h4>Ενημέρωση ...</h4><hr></center>Υπάρχει καταχωρημένο πρωτόκολλο με ίδιο<br><br>-> Αριθμό Εισερχομένου και<br>-> Ημ/νία Εισερχομένου<br><br>Θέλετε ωστόσο να προχωρήσετε;<br>&nbsp;"
                            var toast = toastr.info(html, msg);
                            toast.delegate('#confirmRevertYes', 'click', function() {
                                @if ($protocol->id)
                                    document.forms['myProtocolForm'].submit()
                                @else
                                    var chk = receiptToEmail()
                                    if(chk) document.forms['myProtocolForm'].submit()
                                @endif
                                toast.remove();
                            });
                            toast.delegate('#confirmRevertNo', 'click', function() {
                                toast.remove();
                            });
                        } else {
                            @if ($protocol->id)
                                document.forms['myProtocolForm'].submit()
                            @else
                                var chk = receiptToEmail()
                                if(chk) document.forms['myProtocolForm'].submit()
                            @endif
                        }
                    },
                    error: function(data) {
                        toastr.error(
                            "<center><h4>Λάθος !!!</h4><hr></center>Κάποιο λάθος συνέβη!<br>&nbsp;</center>"
                        )
                    }
                });
            } else {
                @if ($protocol->id)
                    document.forms['myProtocolForm'].submit()
                @else
                    var chk = receiptToEmail()
                    if(chk) document.forms['myProtocolForm'].submit()
                @endif
            }
        }

        function formValidate() {
            var validate = {{ App\Config::getConfigValueOf('protocolValidate') ? 'true' : 'false' }}
            var allowEmptyProtocol = {{ App\Config::getConfigValueOf('allowEmptyProtocol') ? 'true' : 'false' }}

            var thema = $('#thema').val().trim()
            var fakelos = $('#fakelos').val()
            var in_num = $('#in_num').val().trim()
            var in_date = $('#in_date').val().trim()
            var in_topos_ekdosis = $('#in_topos_ekdosis').val().trim()
            var in_arxi_ekdosis = $('#in_arxi_ekdosis').val().trim()
            var in_paraliptis = $('#in_paraliptis').val().trim()
            var in_perilipsi = $('#in_perilipsi').val().trim()
            var diekperaiosi = $('#diekperaiosi').val() ? $('#diekperaiosi').val().join().trim() : ''
            var diekp_date = $('#diekp_date').val().trim()
            var sxetiko = $('#sxetiko').val().trim()
            var out_to = $('#out_to').val().trim()
            var out_date = $('#out_date').val().trim()
            var out_perilipsi = $('#out_perilipsi').val().trim()
            var keywords = $('#keywords').val().trim()
            var paratiriseis = $('#paratiriseis').val().trim()

            var msg = []

            if (validate) {
                msgStr = ''
                if (!allowEmptyProtocol) {
                    if (!thema) {
                        msgStr += "<li>θέμα</li>"
                        $('#themaDiv').addClass('has-error')
                    }
                }
                if (!thema && (fakelos || in_num || in_date || in_topos_ekdosis || in_arxi_ekdosis || in_paraliptis ||
                        in_perilipsi || diekperaiosi || out_date || diekp_date || sxetiko || out_to || out_perilipsi ||
                        keywords || paratiriseis)) {
                    msgStr += "<li>θέμα</li>"
                    $('#themaDiv').addClass('has-error')
                }
                if (!in_date && (in_num || in_topos_ekdosis || in_arxi_ekdosis)) {
                    msgStr += "<li>ημ/νια έκδοσης</li>"
                    $('#in_dateDiv').addClass('has-error')
                }
                if (!in_topos_ekdosis && (in_num || in_date || in_arxi_ekdosis)) {
                    msgStr += "<li>τόπο έκδοσης</li>"
                    $('#in_topos_ekdosisDiv').addClass('has-error')
                }
                if (!in_arxi_ekdosis && (in_num || in_date || in_topos_ekdosis)) {
                    msgStr += "<li>αρχή έκδοσης</li>"
                    $('#in_arxi_ekdosisDiv').addClass('has-error')
                }
                if (!in_paraliptis && (in_num || in_date || in_topos_ekdosis || in_arxi_ekdosis)) {
                    msgStr += "<li>παραλήπτη</li>"
                    $('#in_paraliptisDiv').addClass('has-error')
                }
                if (!diekp_date && diekperaiosi && (out_to || out_date || out_perilipsi)) {
                    msgStr += "<li>ημ/νια διεκπεραίωσης</li>"
                    $('#diekp_dateDiv').addClass('has-error')
                }
                if (!out_date && (out_to || out_perilipsi)) {
                    msgStr += "<li>ημ/νια έξερχομένου</li>"
                    $('#out_dateDiv').addClass('has-error')
                }
                if (!out_to && (out_date || out_perilipsi)) {
                    msgStr += "<li>σε ποιον απευθύνεται</li>"
                    $('#out_toDiv').addClass('has-error')
                }
                if (msgStr) msg.push("Συμπληρώστε<ul>" + msgStr + "</ul>")
            }

            var chkerr = false
            var terms = ['in_date', 'out_date', 'diekp_date']
            terms.forEach(function(term) {
                if ($('#' + term).val().trim() && !/^\d{2}[/]\d{2}[/]\d{4}$/.test($('#' + term).val().trim())) {
                    chkerr = true
                    $('#' + term + 'Div').addClass('has-error')
                }
            })
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

        function anathesiSe() {
            var oldId = $('#diekperaiosi').attr('data-value')
            var newId = $('#diekperaiosi').val() ? $('#diekperaiosi').val().join(',') : ''
            if (!newId) {
                toastr.error(
                    "<center><h4>Λάθος !!!</h4></center><hr>Επιλέξτε σε ποιον θα ανατεθεί το πρωτόκολλο<br> &nbsp;")
                return
            } else if (newId == oldId) {
                toastr.error(
                    "<center><h4>Λάθος !!!</h4></center><hr>Το παρόν πρωτόκολλο έχει ήδη ανατεθεί και έχουν ήδη ενημερωθεί οι επιλεγμένοι χρήστες<br> &nbsp;"
                )
                return
            }
            $.ajax({
                type: "POST",
                url: '{{ route('anathesiSe') }}',
                dataType: 'JSON',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'id': {{ $protocol->id | null }},
                    'diekperaiosi': newId
                },
                success: function(response) {
                    location.reload()
                },
                error: function(data) {
                    toastr.error(
                        "<center><h4>Λάθος !!!</h4><hr></center>Δεν κατέστη δυνατή η ανάθεση του πρωτοκόλλου<br>&nbsp;</center>"
                    )
                }
            });

        }
    </script>

@endsection
