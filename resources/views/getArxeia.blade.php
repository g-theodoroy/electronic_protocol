<ul class='list-inline'>
@php
    $diavgeiaUrl = \App\Config::where('key', 'diavgeiaUrl')->first()->value;
@endphp
@foreach ($protocol->attachments()->get() as $attachment)
    <li>
      @if ($attachment->name)
        <a href='{{ URL::to('/') }}/download/{{$attachment->id}}' title="Λήψη {{ $attachment->name }}">{{ $attachment->name }}</a>
      @endif
      @if ($attachment->ada)
        <a href='{{$diavgeiaUrl}}{{$attachment->ada}}' title="Λήψη {{ $attachment->ada }}">{{ $attachment->ada }}</a>
      @endif
        <a href="javascript:chkdelete('{{ $attachment->id }}','{{$attachment->name}}')" id='delatt{{ $attachment->id }}' title="Διαγραφή {{ $attachment->name ? $attachment->name : $attachment->ada }}" > <img src="{{ URL::to('/') }}/images/delete.ico" alt="delete" height="13"> </a>
    </li>
@endforeach
</ul>
