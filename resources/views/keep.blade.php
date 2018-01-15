@extends('layouts.app', ['ipiresiasName' => $ipiresiasName])

@section('content')

<script >
function chkdelete(id, name){
 
    var html = "<center><button type='button' id='confirmationRevertYes' class='btn btn-primary'>Ναί</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' id='confirmationRevertNo' class='btn btn-primary'>Όχι</button></center>"
    var msg = '<center><h4>Διαγραφή ?</h4><hr>Διαγραφή Φακέλου ' + name + '. Είστε σίγουροι;<br>&nbsp;</center>'
    
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
            $(location).attr('href', "{{ URL::to('/') }}" + "/keep/del/" + id)
            $toast.remove();
    });
    $toast.delegate('#confirmationRevertNo', 'click', function () {
            $toast.remove();
    });
}
</script>

<div class="container">
    <div class="row">
        <div class="col-md-12 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading h1 text-center" {!!$titleColorStyle!!}>Διατήρηση αρχείων</div>

                <div class="panel-body">
                <div class="panel panel-default col-md-12 col-sm-12  ">

                <form name="myForm" class="form-horizontal" role="form" method="POST" action="{{ url('/keep') }}{{$keepvalue->id ? '/' . $keepvalue->id : '' }}">
                    {{ csrf_field() }}

                    @if ($keepvalues->links())
                    <div class='row'>
                        <div class="col-md-12 col-sm-12 small text-center">
                            <span class="small">{{$keepvalues->links()}}</span>
                        </div>
                    </div>
                    @endif

                    <div class='row'>
                        <div class='col-md-2 col-sm-2 text-center h4'>
                            <strong>Φ.</strong>
                        </div>
                        <div class='col-md-1 col-sm-1 h4  text-center'>
                            <strong>Χρόνια</strong>
                        </div>
                        <div class='col-md-2 col-sm-2 h4  text-center'>
                            <strong>Άλλο</strong>
                        </div>
                        <div class='col-md-4 col-sm-4 h4'>
                            <strong>Περιγραφή</strong>
                        </div>
                        <div class='col-md-2 col-sm-2 h4'>
                            <strong>Παρατηρήσεις</strong>
                        </div>
                    </div>

                    <div class='row {{$submitVisible}}'>
                        <div class='col-md-2 col-sm-2 {{ $errors->has('fakelos') ? ' has-error' : '' }}'>
                            <input id="fakelos" type="text" class="form-control text-center"  name="fakelos" placeholder="fakelos" value="{{ old('fakelos') ? old('fakelos') : $keepvalue->fakelos }}" required autofocus >
                        </div>
                        <div class="col-md-1 col-sm-1 {{ $errors->has('keep') ? ' has-error' : '' }}">
                            <input id="keep" type="text" class="form-control text-center" name="keep" placeholder="keep" value="{{ old('keep') ? old('keep') : $keepvalue->keep }}">
                        </div>
                        <div class="col-md-2 col-sm-2 {{ $errors->has('keep_alt') ? ' has-error' : '' }}">
                            <input id="keep_alt" type="text" class="form-control  text-center" name="keep_alt" placeholder="keep_alt" value="{{ old('keep_alt') ? old('keep_alt') : $keepvalue->keep_alt }}">
                        </div>
                        <div class="col-md-4 col-sm-4 {{ $errors->has('describe') ? ' has-error' : '' }}">
                            <textarea id="describe" type="text" class="form-control" name="describe"  placeholder="describe" value="" required >{{ old('describe') ? old('describe') : nl2br($keepvalue->describe) }}</textarea> 
                        </div>
                        <div class="col-md-2 col-sm-2 {{ $errors->has('remarks') ? ' has-error' : '' }}">
                            <textarea id="remarks" type="text" class="form-control" name="remarks"  placeholder="remarks" value="" >{{ old('remarks') ? old('remarks') :  nl2br($keepvalue->remarks) }}</textarea> 
                        </div>
                        <div class="col-md-1 col-sm-1  text-center">
                            <a href="javascript:document.forms['myForm'].submit();" class="{{$submitVisible}}" role="button" title="Αποθήκευση" > <img src="{{ URL::to('/') }}/images/save.ico" height="30" /></a>
                            <a href="{{ URL::to('/') }}/keep" class="active" role="button" title="Καθάρισμα" > <img src="{{ URL::to('/') }}/images/clear.ico" height="20" /></a>
                        </div>
                    </div>

                    </form>
                    <hr>

                    @php ($i = 1)
                    @foreach ($keepvalues as $keep)
                    @if ($i%2)
                    <div class='row'>
                    @else
                    <div class='row bg-info'>
                    @endif
                        <div class='col-md-2 col-sm-2 '>
                        <div class='row'>
                        <div class='col-md-2 col-sm-2 form-control-static  text-center'>
                            {{ ($keepvalues->currentPage()-1) * $keepvalues->perPage() + $i}}
                        </div>
                        <div class='col-md-8 col-sm-8 text-center form-control-static'>
                            {{$keep->fakelos}}
                        </div>
                        </div>
                        </div>
                        <div class='col-md-1 col-sm-1 form-control-static  text-center'>
                            {{$keep->keep}}
                        </div>
                        <div class='col-md-2 col-sm-2 form-control-static '>
                            {{$keep->keep_alt}}
                        </div>
                        <div class='col-md-4 col-sm-4 form-control-static'>
                            {!!nl2br($keep->describe,false)!!}
                        </div>
                        <div class='col-md-2 col-sm-2 form-control-static'>
                            {!!nl2br($keep->remarks,false)!!}
                        </div>
                        <div class='col-md-1 col-sm-1 form-control-static  text-center'>
                            <a href="{{ URL::to('/') }}/keep/{{ $keep->id }}" class="{{$submitVisible}}" title="Επεξεργασία {{ $keep->fakelos }}"> <img src="{{ URL::to('/') }}/images/edit.ico" alt="edit" height="15" > </a>
                            <a href="#" title="Διαγραφή {{ $keep->fakelos }}" class="{{$submitVisible}}" onclick="chkdelete('{{ $keep->id }}','{{$keep->fakelos}}')"> <img src="{{ URL::to('/') }}/images/delete.ico" alt="delete" height="15"> </a>
                        </div>
                    </div>
                    @php ($i++)
                    @endforeach

                    @if ($keepvalues->links() and $keepvalues->count() > $keepvalues->perPage()/2)
                    <div class='row'>
                        <div class="col-md-12 col-sm-12 small text-center">
                            <span class="small">{{$keepvalues->links()}}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
