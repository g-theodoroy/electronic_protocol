
@extends('layouts.app')

@section('content')
    @php
        // Εδώ ρυθμίζεται το πλάτος των στηλών.
        // Το άθροισμα των πλατών πρέπει να ισούται με 12.
        $pl1 = 2; // Φ.
        $pl2 = 1; // Χρόνια
        $pl3 = 2; // Άλλο
        $pl4 = 4; // Περιγραφή
        $pl5 = 2; // Παρατηρήσεις
        $pl6 = 1; // Εικονίδια
    @endphp

    <script>

        function chkdelete(id, name) {

            var html =
                "<center><button type='button' id='confirmationRevertYes' class='btn btn-primary'>Ναί</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' id='confirmationRevertNo' class='btn btn-primary'>Όχι</button></center>"
            var msg = '<center><h4>Διαγραφή ?</h4><hr>Διαγραφή Φακέλου ' + escapeHtml(name) + '. Είστε σίγουροι;<br>&nbsp;</center>'
            var $toast = toastr.warning(html, msg);
            $toast.delegate('#confirmationRevertYes', 'click', function() {
                deleteRecord(id, "{{ url('/keep/del') }}");
                $toast.remove();
            });
            $toast.delegate('#confirmationRevertNo', 'click', function() {
                $toast.remove();
            });
        }
        
    </script>

    <div class="container">
        <div class="row">
            <div class="col-md-12 col-md-offset-0">
                <div class="panel panel-default">

                    <div class="panel-heading h1 text-center" {!! $titleColorStyle !!}>
                        Διατήρηση αρχείων
                    </div>

                    <div class="panel-body">
                        <div class="panel panel-default col-md-12 col-sm-12">

                            <form name="myForm"
                                  class="form-horizontal"
                                  role="form"
                                  method="POST"
                                  action="{{ url('/keep') }}{{ $keepvalue->id ? '/' . $keepvalue->id : '' }}">

                                {{ csrf_field() }}

                                @if ($keepvalues->hasPages())
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 small text-center">
                                            <span class="small">
                                                {{ $keepvalues->links() }}
                                            </span>
                                        </div>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-{{ $pl1 }} col-sm-{{ $pl1 }} text-center h4">
                                        <strong>Φ.</strong>
                                    </div>

                                    <div class="col-md-{{ $pl2 }} col-sm-{{ $pl2 }} h4 text-center">
                                        <strong>Χρόνια</strong>
                                    </div>

                                    <div class="col-md-{{ $pl3 }} col-sm-{{ $pl3 }} h4 text-center">
                                        <strong>Άλλο</strong>
                                    </div>

                                    <div class="col-md-{{ $pl4 }} col-sm-{{ $pl4 }} h4">
                                        <strong>Περιγραφή</strong>
                                    </div>

                                    <div class="col-md-{{ $pl5 }} col-sm-{{ $pl5 }} h4">
                                        <strong>Παρατηρήσεις</strong>
                                    </div>
                                </div>

                                <div class="row {{ $submitVisible }}">
                                    <div class="col-md-{{ $pl1 }} col-sm-{{ $pl1 }}{{ $errors->has('fakelos') ? ' has-error' : '' }}">
                                        <input id="fakelos"
                                               type="text"
                                               class="form-control text-center"
                                               name="fakelos"
                                               placeholder="fakelos"
                                               value="{{ old('fakelos', $keepvalue->fakelos) }}"
                                               required
                                               autofocus>
                                    </div>

                                    <div class="col-md-{{ $pl2 }} col-sm-{{ $pl2 }}{{ $errors->has('keep') ? ' has-error' : '' }}">
                                        <input id="keep"
                                               type="text"
                                               class="form-control text-center"
                                               name="keep"
                                               placeholder="keep"
                                               value="{{ old('keep', $keepvalue->keep) }}">
                                    </div>

                                    <div class="col-md-{{ $pl3 }} col-sm-{{ $pl3 }}{{ $errors->has('keep_alt') ? ' has-error' : '' }}">
                                        <input id="keep_alt"
                                               type="text"
                                               class="form-control text-center"
                                               name="keep_alt"
                                               placeholder="keep_alt"
                                               value="{{ old('keep_alt', $keepvalue->keep_alt) }}">
                                    </div>

                                    <div class="col-md-{{ $pl4 }} col-sm-{{ $pl4 }}{{ $errors->has('describe') ? ' has-error' : '' }}">
                                        <textarea id="describe"
                                                  class="form-control"
                                                  name="describe"
                                                  placeholder="describe"
                                                  required>{{ old('describe', $keepvalue->describe) }}</textarea>
                                    </div>

                                    <div class="col-md-{{ $pl5 }} col-sm-{{ $pl5 }}{{ $errors->has('remarks') ? ' has-error' : '' }}">
                                        <textarea id="remarks"
                                                  class="form-control"
                                                  name="remarks"
                                                  placeholder="remarks">{{ old('remarks', $keepvalue->remarks) }}</textarea>
                                    </div>

                                    <div class="col-md-{{ $pl6 }} col-sm-{{ $pl6 }} text-center">

                                        <button type="submit"
                                                class="{{ $submitVisible }}"
                                                title="Αποθήκευση"
                                                style="border: 0; background: none; padding: 0; cursor: pointer;">
                                            <img src="{{ url('/images/save.ico') }}" height="30" alt="save">
                                        </button>

                                        <a href="{{ url('/keep') }}"
                                           class="active"
                                           role="button"
                                           title="Καθάρισμα">
                                            <img src="{{ url('/images/clear.ico') }}" height="20" alt="clear">
                                        </a>
                                    </div>
                                </div>
                            </form>

                            <hr>

                            @php($i = 1)

                            @foreach ($keepvalues as $keep)
                                @if ($i % 2)
                                    <div class="row">
                                @else
                                    <div class="row bg-info">
                                @endif

                                    <div class="col-md-{{ $pl1 }} col-sm-{{ $pl1 }}">
                                        <div class="row">
                                            <div class="col-md-2 col-sm-2 form-control-static text-center">
                                                {{ ($keepvalues->currentPage() - 1) * $keepvalues->perPage() + $i }}
                                            </div>

                                            <div class="col-md-8 col-sm-8 text-center form-control-static">
                                                {{ $keep->fakelos }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-{{ $pl2 }} col-sm-{{ $pl2 }} form-control-static text-center">
                                        {{ $keep->keep }}
                                    </div>

                                    <div class="col-md-{{ $pl3 }} col-sm-{{ $pl3 }} form-control-static">
                                        {{ $keep->keep_alt }}
                                    </div>

                                    <div class="col-md-{{ $pl4 }} col-sm-{{ $pl4 }} form-control-static">
                                        {!! nl2br(e($keep->describe), false) !!}
                                    </div>

                                    <div class="col-md-{{ $pl5 }} col-sm-{{ $pl5 }} form-control-static">
                                        {!! nl2br(e($keep->remarks), false) !!}
                                    </div>

                                    <div class="col-md-{{ $pl6 }} col-sm-{{ $pl6 }} form-control-static text-center">

                                        <a href="{{ url('/keep/' . $keep->id) }}"
                                           class="{{ $submitVisible }}"
                                           title="Επεξεργασία {{ $keep->fakelos }}">
                                            <img src="{{ url('/images/edit.ico') }}"
                                                 alt="edit"
                                                 height="15">
                                        </a>

                                        <a href="#"
                                           title="Διαγραφή {{ $keep->fakelos }}"
                                           class="{{ $submitVisible }}"
                                           data-id="{{ $keep->id }}"
                                           data-name="{{ $keep->fakelos }}"
                                           onclick="chkdelete(this.getAttribute('data-id'), this.getAttribute('data-name')); return false;">
                                            <img src="{{ url('/images/delete.ico') }}"
                                                 alt="delete"
                                                 height="15">
                                        </a>
                                    </div>
                                </div>

                                @php($i++)
                            @endforeach

                            @if ($keepvalues->hasPages())
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 small text-center">
                                        <span class="small">
                                            {{ $keepvalues->links() }}
                                        </span>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
