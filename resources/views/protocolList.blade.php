@extends('layouts.app')

@section('content')

<div class="container">
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


                    <div class='row bg-primary'>

                        <div class='col-md-1 col-sm-1'>
                            <br>
                            <strong>Αυξ.Αριθ</strong><br>
                            <strong>Ημνία</strong>
                        </div>
                        <div class='col-md-5 col-sm-5'>
                            <div class='row'>
                                <div class='col-md-5 col-sm-5'>
                                    <strong>Αρ./Ημ.Εισερχ.</strong><br>
                                    <strong>&#x2727;Τόπος-έκδοσης</strong><br>
                                    <strong>&#x2726;Αρχή-Έκδοσης</strong>
                                </div>
                                <div class='col-md-7 col-sm-7'>
                                    <strong>Θέμα</strong><br>
                                    <strong>&#x2727;Περ.Εισερχ.</strong><br>
                                    <strong>&#x2726;Παραλήπτης</strong>
                                </div>
                            </div>
                        </div>
                        <div class='col-md-5 col-sm-5'>
                            <div class='row'>
                                <div class='col-md-5 col-sm-5'>
                                    <strong>Ημ.Εξερχ.</strong><br>
                                    <strong>&#x2727;Απευθύνεται</strong><br>
                                    <strong>&#x2726;Περ.Εξερχ.</strong>
                                </div>
                                <div class='col-md-3 col-sm-3'>
                                    <strong>Διεκπεραίωση</strong><br>
                                    <strong>&#x2727;Ημ.Διεκπ.</strong>
                                </div>
                                <div class='col-md-4 col-sm-4'>
                                    <strong>Φάκελος</strong><br>
                                    <strong>&#x2727;Σχετ.αριθμοί</strong><br>
                                    <strong>&#x2726;Παρατηρήσεις</strong>
                                </div>
                            </div>
                        </div>
                        <div class='col-md-1 col-sm-1'>
                            <strong>Συνημμένα</strong>
                        </div>
                    </div>


                    @php ($i = 1)
                    @foreach ($protocols as $protocol)
                    @if ($i%2)
                    <div class='row'>
                    @else
                    <div class='row bg-info'>
                    @endif

                        <div class='col-md-1 col-sm-1'>
                            <a href="{{ URL::to('/') }}/home/{{$protocol->id}}" class="" role="button" title="Μετάβαση" > <img src="{{ URL::to('/') }}/images/open.png" height="15" /></a>
                            <strong>{{$protocol->protocolnum}}</strong><br>
                            {{$protocol->protocoldate}}
                        </div>
                        <div class='col-md-5 col-sm-5'>
                            <div class='row'>
                                <div class='col-md-5 col-sm-5 small'>
                                    @if($protocol->in_num)
                                    {{$protocol->in_num}}/
                                    @endif
                                    {{$protocol->in_date}}
                                    @if($protocol->in_date and $protocol->in_topos_ekdosis)
                                    <br>
                                    @endif
                                    @if($protocol->in_topos_ekdosis)
                                    &#x2727;{{$protocol->in_topos_ekdosis}}
                                    @endif
                                    @if($protocol->in_topos_ekdosis and $protocol->in_arxi_ekdosis)
                                    <br>
                                    @endif
                                    @if( ! $protocol->in_topos_ekdosis and ($protocol->in_date and $protocol->in_arxi_ekdosis))
                                    <br>
                                    @endif
                                    @if($protocol->in_arxi_ekdosis)
                                    &#x2726;{{$protocol->in_arxi_ekdosis}}
                                    @endif
                                </div>
                                <div class='col-md-7 col-sm-7 small'>
                                    {{$protocol->thema}}
                                    @if($protocol->thema and $protocol->in_perilipsi)
                                    <br>
                                    @endif
                                    @if($protocol->in_perilipsi)
                                    &#x2727;{{$protocol->in_perilipsi}}
                                    @endif
                                    @if($protocol->in_perilipsi and $protocol->in_paraliptis)
                                    <br>
                                    @endif
                                    @if( ! $protocol->in_perilipsi and ($protocol->thema and $protocol->in_paraliptis))
                                    <br>
                                    @endif
                                    @if($protocol->in_paraliptis)
                                    &#x2726;{{$protocol->in_paraliptis}}
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class='col-md-5 col-sm-5'>
                            <div class='row'>
                                <div class='col-md-5 col-sm-5 small'>
                                    {{$protocol->out_date}}
                                    @if($protocol->out_date and $protocol->out_to)
                                    <br>
                                    @endif
                                    @if($protocol->out_to)
                                    &#x2727;{{$protocol->out_to}}
                                    @endif
                                    @if($protocol->out_to and $protocol->out_perilipsi)
                                    <br>
                                    @endif
                                    @if( ! $protocol->out_to and ($protocol->out_date and $protocol->out_perilipsi))
                                    <br>
                                    @endif
                                    @if($protocol->out_perilipsi)
                                    &#x2726;{{$protocol->out_perilipsi}}
                                    @endif
                                </div>
                                <div class='col-md-3 col-sm-3 small'>
                                    {{$protocol->diekperaiosi}}
                                    @if($protocol->diekperaiosi and $protocol->diekp_date)
                                    <br>
                                    @endif
                                    @if($protocol->diekp_date)
                                    &#x2727;{{$protocol->diekp_date}}
                                    @endif
                                 </div>
                                <div class='col-md-4 col-sm-4 small'>
                                    {{$protocol->fakelos}}
                                    @if($protocol->fakelos and $protocol->sxetiko)
                                    <br>
                                    @endif
                                    @if($protocol->sxetiko)
                                    &#x2727;{{$protocol->sxetiko}}
                                    @endif
                                    @if($protocol->sxetiko and $protocol->paratiriseis)
                                    <br>
                                    @endif
                                    @if( ! $protocol->sxetiko and ($protocol->fakelos and $protocol->paratiriseis))
                                    <br>
                                    @endif
                                    @if($protocol->paratiriseis)
                                    &#x2726;{{$protocol->paratiriseis}}
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class='col-md-1 col-sm-1 small'>
                            <ul class='list-inline'>
                                @foreach ($protocol->attachments()->get() as $attachment)
                                    <li>
                                        <a href='{{ URL::to('/') }}/download/{{$attachment->id}}' target="_blank">{{ $attachment->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
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
