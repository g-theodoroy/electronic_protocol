<!DOCTYPE html>
<html lang="el">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

       <title>Εκτύπωση Ηλ. Πρωτοκόλλου</title>

        <!-- Styles -->
        <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
        <style>
            @media print
            {
                table { page-break-after:auto }
                tr    { page-break-inside:avoid; page-break-after:auto }
                td    { page-break-inside:avoid; page-break-after:auto }
                thead { display:table-header-group }
                tfoot { display:table-footer-group }
            }
            .table thead>tr>th.middle{
                vertical-align: middle;
            }
            .table thead>tr>th.middle{
                vertical-align: middle;
            }
            .table tbody>tr>td.middle{
                vertical-align: middle;
            }
            .table tfoot>tr>td.middle{
                vertical-align: middle;
            }
             .table-bordered td, .table-bordered th{
                border-color: black !important;
            }
        </style>
    </head>
    <body>
        <div class="col-md-12 ">
            <div class="table-responsive">

        <table class="table table-condensed table-bordered" >
            <thead>
                <tr>
                    <th colspan=7>
                        <div class="col-md-3">{{$ipiresiasName}}</div>
                        <div class="col-md-6 text-center">{{ $title }}</div>
                    </th>
                </tr>
                <tr >
                    <th  class="small middle"><span class='small'>Φ./</span>Αύξ.Αρ.</th>
                    <th  class="small middle"><span class='small'>Ημ.Παραλ.</span></th>
                    <th  class="small middle"><span class='small'>Ημ.Λήξης.</span></th>
                    <th  class="small middle"><span class='small'>Έγγραφο</span></th>
                    <th  class="small middle"><span class='small'>Θέμα</span></th>
                    <th  class="small middle"><span class='small'>Αρ/Ημ.Εισερχ.<br>&#x2727; Ημ.Εξερχ.</span></th>
                    <th  class="small middle"><span class='small'>Περίλ.Εισερχ.<br>&#x2727; Περίλ.Εξερχ.</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach($arxeia as $arxeio)
                <tr>
                <td class="small middle">
                    @if($arxeio->protocol->fakelos)
                    <span class='small'>{{$arxeio->protocol->fakelos}}/</span>
                    @endif
                    <strong>{{$arxeio->protocol->protocolnum}}</strong>
                </td>
                <td class="small middle">
                    <span class='small'>{{$arxeio->protocol->protocoldate}}</span></td>
                <td class="small middle"><span class='small'>{{$arxeio->expires}}</span></td>
                <td class="small middle">
                  @if ($arxeio->name)<span class='small'>{{$arxeio->name}}</span> @endif
                  @if ($arxeio->name and $arxeio->ada)<br> @endif
                  @if ($arxeio->ada)<span class='small'>ΑΔΑ:{{$arxeio->ada}}</span> @endif
                </td>
                <td class="small middle"><span class='small'>{{$arxeio->protocol->thema}}</span></td>
                <td class="small middle">
                    <span class='small'>
                    @if($arxeio->protocol->in_num)
                    {{$arxeio->protocol->in_num}}/
                    @endif
                    @if($arxeio->protocol->in_date)
                    {{$arxeio->protocol->in_date}}
                    @endif
                    @if($arxeio->protocol->in_date and $arxeio->protocol->out_date)
                    <br>
                    @endif
                    @if($arxeio->protocol->out_date)
                    &#x2727; {{$arxeio->protocol->out_date}}</span>
                    @endif
                </td>
                 <td class="small middle"><span class='small'>
                    @if($arxeio->protocol->in_perilipsi)
                    {{$arxeio->protocol->in_perilipsi}}</span>
                    @endif
                    @if($arxeio->protocol->in_perilipsi and $arxeio->protocol->out_perilipsi)
                    <br>
                    @endif
                    @if($arxeio->protocol->out_perilipsi)
                    &#x2727; {{$arxeio->protocol->out_perilipsi}}</span>
                    @endif
                </td>
                </tr>
                @endforeach

            </tbody>
             <tfoot>
                 <tr>
                     <td class="small" colspan=6 >Εκτυπώθηκε: {{$datetime}}</td>
                     <td class="small text-right">Σελ ........</td>
                 </tr>
             </tfoot>
        </table>

            </div>
        </div>
    </body>
</html>
