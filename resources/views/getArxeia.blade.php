<ul class='list-inline'>
@foreach ($protocol->attachments()->get() as $attachment)
    <li>
      @if ($attachment->name)
        <a href='{{ URL::to('/') }}/download/{{$attachment->id}}' >{{ $attachment->name }}</a>
      @endif
      @if ($attachment->ada)
        <a href='https://diavgeia.gov.gr/doc/{{$attachment->ada}}' >{{ $attachment->ada }}</a>
      @endif
        <a href="javascript:chkdelete('{{ $attachment->id }}','{{$attachment->name}}')" id='delatt{{ $attachment->id }}' title="Διαγραφή {{ $attachment->name }}" > <img src="{{ URL::to('/') }}/images/delete.ico" alt="delete" height="13"> </a>
    </li>
@endforeach
</ul>
