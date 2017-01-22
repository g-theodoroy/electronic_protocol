<ul class='list-inline'>
@foreach ($protocol->attachments()->get() as $attachment)
    <li>
        <a href='{{ URL::to('/') }}/download/{{$attachment->id}}' >{{ $attachment->name }}</a>
        <a href="javascript:chkdelete('{{ $attachment->id }}','{{$attachment->name}}')" id='delatt{{ $attachment->id }}' title="Διαγραφή {{ $attachment->name }}" > <img src="{{ URL::to('/') }}/images/delete.ico" alt="delete" height="13"> </a>
    </li>
@endforeach
</ul>
