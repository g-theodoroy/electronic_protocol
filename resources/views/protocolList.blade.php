@extends('layouts.app',  ['needsUpdate' => $needsUpdate ])

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading h1 text-center">Πρωτόκολλο</div>

                <div class="panel-body">
                <div class="panel panel-default col-md-12 col-sm-12  ">

                    <div class='row'>
                        <div class="col-md-1 col-sm-1 form-control-static">
                        <a href="{{ URL::to('/') }}/home" class="active" role="button" title="Νέο" > <img src="{{ URL::to('/') }}/images/addnew.ico" height=30 / ></a>
                        </div>
                    @if ($protocols->links())
                        <div class="col-md-10 col-sm-10 small text-center">
                            <span class="small">{{$protocols->links()}}</span>
                        </div>
                    @endif
                        <div class="col-md-1 col-sm-1 form-control-static text-right">
                        <a href="{{ URL::to('/') }}/home/list" class="active" role="button" title="Ανανέωση" > <img src="{{ URL::to('/') }}/images/refresh.png" height=30 / ></a>
                        </div>
                    </div>


                    <div class='row  bg-primary'>&nbsp;</div>
                    <div class='row bg-primary'>

                        <div class='col-md-1 col-sm-1'>
                            <br>
                            <strong>Αυξ.Αριθ</strong><hr>
                            <strong>Ημνία</strong>
                        </div>
                        <div class='col-md-5 col-sm-5'>
                            <div class='row'>
                                <div class='col-md-5 col-sm-5'>
                                    <strong>Αρ./Ημ.Εισερχ.</strong><hr>
                                    <strong>&#x2727;Τόπος Έκδοσης</strong><hr>
                                    <strong>&#x2726;Αρχή Έκδοσης</strong>
                                </div>
                                <div class='col-md-7 col-sm-7'>
                                    <strong>Θέμα</strong><hr>
                                    <strong>&#x2727;Περίλ.Εισερχ.</strong><hr>
                                    <strong>&#x2726;Παραλήπτης</strong>
                                </div>
                            </div>
                        </div>
                        <div class='col-md-5 col-sm-5'>
                            <div class='row'>
                                <div class='col-md-5 col-sm-5'>
                                    <strong>Ημ.Εξερχ.</strong><hr>
                                    <strong>&#x2727;Απευθύνεται</strong><hr>
                                    <strong>&#x2726;Περίλ.Εξερχ.</strong>
                                </div>
                                <div class='col-md-3 col-sm-3'>
                                    <br>
                                    <strong>Διεκπεραίωση</strong><hr>
                                    <strong>&#x2727;Ημ.Διεκπ.</strong>
                                </div>
                                <div class='col-md-4 col-sm-4'>
                                    <strong>Φάκελος</strong><hr>
                                    <strong>&#x2727;Σχετ.αριθμοί</strong><hr>
                                    <strong>&#x2726;Παρατηρήσεις</strong>
                                </div>
                            </div>
                        </div>
                        <div class='col-md-1 col-sm-1'>
                            <br>
                            <strong>Συνημμένα</strong>
                        </div>
                    </div>
                    <div class='row  bg-primary'>&nbsp;</div>


                    @php ($i = 1)
                    @foreach ($protocols as $protocol)
                    @if ($i%2)
                    <div class='row'>&nbsp;</div>
                    <div class='row'>
                    @else
                    <div class='row  bg-info'>&nbsp;</div>
                    <div class='row bg-info'>
                    @endif

                        <div class='col-md-1 col-sm-1'>
                            <br>
                            <a href="{{ URL::to('/') }}/home/{{$protocol->id}}" class="" role="button" title="Μετάβαση" > <img src="{{ URL::to('/') }}/images/open.png" height="15" /></a>
                            <span title='Αυξων Αριθμός'>
                            <strong>{{$protocol->protocolnum}}</strong><hr>
                            </span>
                            <span title='Ημνία'>
                            {{$protocol->protocoldate}}
                            </span>
                        </div>
                        <div class='col-md-5 col-sm-5'>
                            <div class='row'>
                                <div class='col-md-5 col-sm-5 small'>
                                    <span title='Αριθμός/Ημνία Εισερχομένου'>
                                    @if($protocol->in_num)
                                    {{$protocol->in_num}}/
                                    @endif
                                    {{$protocol->in_date ? $protocol->in_date : '&nbsp;'}}
                                    </span>
                                    <hr>
                                    <span title='Τόπος Έκδοσης'>
                                    @if($protocol->in_topos_ekdosis)
                                    &#x2727;{{$protocol->in_topos_ekdosis}}
                                    @else
                                    &nbsp;
                                    @endif
                                    </span>
                                    <hr>
                                    <span title='Αρχή Έκδοσης'>
                                    @if($protocol->in_arxi_ekdosis)
                                    &#x2726;{{$protocol->in_arxi_ekdosis}}
                                    @else
                                    &nbsp;
                                    @endif
                                    </span>
                                </div>
                                <div class='col-md-7 col-sm-7 small'>
                                    <span title='Θέμα'><strong>
                                    {{$protocol->thema ? $protocol->thema : '&nbsp;'}}
                                    </strong></span>
                                    <hr>
                                    <span title='Περίληψη Εισερχομένου'>
                                    @if($protocol->in_perilipsi)
                                    &#x2727;{{$protocol->in_perilipsi}}
                                    @else
                                    &nbsp;
                                    @endif
                                    </span>
                                    <hr>
                                    <span title='Παραλήπτης'>
                                    @if($protocol->in_paraliptis)
                                    &#x2726;{{$protocol->in_paraliptis}}
                                    @else
                                    &nbsp;
                                    @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class='col-md-5 col-sm-5'>
                            <div class='row'>
                                <div class='col-md-5 col-sm-5 small'>
                                    <span title='Ημνία Εξερχομένου'>
                                    {{$protocol->out_date  ? $protocol->out_date : '&nbsp;'}}
                                    </span>
                                    <hr>
                                    <span title='Απευθύνεται'>
                                    @if($protocol->out_to)
                                    &#x2727;{{$protocol->out_to}}
                                    @else
                                    &nbsp;
                                    @endif
                                    </span>
                                    <hr>
                                    <span title='Περίληψη Εξερχομένου'>
                                    @if($protocol->out_perilipsi)
                                    &#x2726;{{$protocol->out_perilipsi}}
                                    @else
                                    &nbsp;
                                    @endif
                                    </span>
                                </div>
                                <div class='col-md-3 col-sm-3 small'>
                                    <br>
                                    <span title='Διεκπεραίωση'>
                                    {{$protocol->diekperaiosi ? $protocol->diekperaiosi : '&nbsp;'}}
                                    </span>
                                    <hr>
                                    <span title='Ημνία Διεκπεραίωσης'>
                                    @if($protocol->diekp_date)
                                    &#x2727;{{$protocol->diekp_date}}
                                    @else
                                    &nbsp;
                                    @endif
                                    </span>
                                 </div>
                                <div class='col-md-4 col-sm-4 small'>
                                    <span title='Φάκελος'>
                                    {{$protocol->fakelos  ? $protocol->fakelos : '&nbsp;'}}
                                    </span>
                                    <hr>
                                    <span title='Σχετικοί αριθμοί'>
                                    @if($protocol->sxetiko)
                                    &#x2727;{{$protocol->sxetiko}}
                                    @else
                                    &nbsp;
                                    @endif
                                    </span>
                                    <hr>
                                    <span title='Παρατηρήσεις'>
                                    @if($protocol->paratiriseis)
                                    &#x2726;{{$protocol->paratiriseis}}
                                    @else
                                    &nbsp;
                                    @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class='col-md-1 col-sm-1 small' style='overflow:hidden'>
                            <ul class='list-unstyled'>
                                @foreach ($protocol->attachments()->get() as $attachment)
                                    <li>
                                        <a href='{{ URL::to('/') }}/download/{{$attachment->id}}' target="_blank"  title='Λήψη {{ $attachment->name }}'>@if(strlen($attachment->name)> 13){{ substr($attachment->name,0,3) }}...{{ substr($attachment->name,-7,7) }}@else{{$attachment->name}}@endif</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @if ($i%2)
                    <div class='row'>&nbsp;</div>
                    @else
                    <div class='row  bg-info'>&nbsp;</div>
                    @endif
                    @php ($i++)
                    @endforeach

                     @if ($protocols->links()  and $protocols->count() > $protocols->perPage()/2)
                    <div class='row'>
                        <div class="col-md-12 col-sm-12 small text-center">
                            <span class="small">{{$protocols->links()}}</span>
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
    setInterval(function() {
                  window.location.reload()
                }, {{$refreshInterval}}) 
</script>
@endif

@endsection
