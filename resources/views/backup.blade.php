@extends('layouts.app')

@section('content')

<style>
.hideoverflow{
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>

<div class="container">
    <div class="row">
        <div class="col-md-8 col-sm-8 col-md-offset-2 col-sm-offset-2">
           <div class="panel panel-default">
                <div class="panel-heading h1 text-center">Αντίγραφα ασφαλείας</div>

                <div class="panel-body ">
                    <div class="panel panel-default col-md-12 col-sm-12  ">
                        <div class="row bg-info">
                            <div class="form-control-static h4 text-center">Δημιουργία νέου αντιγράφου ασφαλείας</div>
                        </div>
                        <div class="row">
                            <div class="form-control-static text-center col-md-2 col-sm-2  col-md-offset-5 col-sm-offset-5" >
                            <a href="{{ URL::to('/') }}/backup"  role="button" title="Δημιουργία αντιγράφου ασφαλείας" > <img src="{{ URL::to('/') }}/images/save.ico" height="30" /></a>
                            </div>
                            <div class="form-control-static text-right col-md-2 col-sm-2  col-md-offset-3 col-sm-offset-3" >
                            <a href="{{ URL::to('/home/list') }}"  class="" role="button" title="Πρωτόκολλο" > <img src="{{ URL::to('/') }}/images/protocol.png" height="30" /></a>
                            </div>
                     </div>
                </div>
                @if($files)
                <div class="panel panel-default col-md-12 col-sm-12  ">
                        <div class="row bg-success">
                            <div class="form-control-static h4 text-center">Αποθηκευμένα αντίγραφα ασφαλείας</div>
                        </div>
                      <div class="row">
                            <div class="form-control-static text-center col-md-12 col-sm-12  " >

                            <ul class='list'>
                            @foreach ($files as $file)
                                <li>
                                    <a href='{{ URL::to('/') }}/downloadBackup/{{$file['basename']}}' >{{ $file['basename'] }}</a>
                                    <a href="#" id='delbackup' title="Διαγραφή {{ $file['basename'] }}" onclick="chkdelete('{{ $file['basename'] }}')"> <img src="{{ URL::to('/') }}/images/delete.ico" alt="delete" height="15"> </a>
                                </li>
                            @endforeach
                            </ul>
                        </div>
                    </div>
               </div>
               @endif
            </div>
        </div>
    </div>
</div>

<script>

function chkdelete(name){
 
    var html = "<center><button type='button' id='confirmationRevertYes' class='btn btn-primary'>Ναί</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' id='confirmationRevertNo' class='btn btn-primary'>Όχι</button></center>"
    var msg = '<center><h4>Διαγραφή ?</h4><hr>Διαγραφή αρχείου ' + name + '. Είστε σίγουροι;<br>&nbsp;</center>'
    
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
            $(location).attr('href', "{{ URL::to('/') }}" + "/deleteBackup/" + name)
            $toast.remove();
    });
    $toast.delegate('#confirmationRevertNo', 'click', function () {
            $toast.remove();
    });
}
</script>

@endsection
