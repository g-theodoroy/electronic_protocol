<!DOCTYPE html>
<html lang="el">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Εκτύπωση Συνημμένων</title>

    <!-- Styles -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        @media print {
            table {
                page-break-after: auto
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto
            }

            td {
                page-break-inside: avoid;
                page-break-after: auto
            }

            thead {
                display: table-header-group
            }

            tfoot {
                display: table-footer-group
            }
        }

        .table thead>tr>th.middle {
            vertical-align: middle;
        }

        .table thead>tr>th.middle {
            vertical-align: middle;
        }

        .table tbody>tr>td.middle {
            vertical-align: middle;
        }

        .table tfoot>tr>td.middle {
            vertical-align: middle;
        }

        .table-bordered td,
        .table-bordered th {
            border-color: black !important;
        }

        .bg-grey {
            background-color: #eee;
        }

    </style>
</head>

<body>
    <div class="col-md-12 ">
        <div class="table-responsive">

            <table class="table table-condensed table-bordered">
                <thead>
                    <tr>
                        <th colspan=9>
                            <div class="col-md-3">{{ $ipiresiasName }}</div>
                            <div class="col-md-6 text-center">Εκτύπωση Πρωτοκόλλων με Συνημμένα Αρχεία έτους
                                {{ $etos }}</div>
                        </th>
                    </tr>
                    <tr>
                        <th rowspan=2 class="small middle"><span class='small'>Αύξ.Αριθ.<br><span
                                    class='small'>Ημ.Παραλ.</span></th>
                        <th colspan=2 class="small">
                            <center><span class='small'>ΕΙΣΕΡΧΟΜΕΝΑ</center>
                        </th>
                        <th colspan=2 class="small">
                            <center><span class='small'>ΕΞΕΡΧΟΜΕΝΑ</center>
                        </th>
                        <th rowspan=2 class="small middle"><span class='small'>Φάκελος</th>
                        <th colspan=3 class="small">
                            <center><span class='small'>ΣΥΝΗΜΜΕΝΑ</center>
                        </th>
                    </tr>
                    <tr>
                        <th class="small middle"><span class='small'>
                                Αριθ/Ημ.Εισερχ.<br>&#x2727;Τόπος έκδοσης<br>&#x2726;Αρχή Έκδοσης</span></th>
                        <th class="small middle"><span class='small'>Θέμα<br>&#x2727;Περίληψη Εισερχομένου</span></th>
                        <th class="small middle"><span class='small'>Ημνια Εξερχ.<br>&#x2727;Απευθύνεται</span></th>
                        <th class="small middle"><span class='small'>Περίληψη<br>Εξερχόμενου</span></th>
                        <th class="small middle">
                            <center><span class='small'>Όνομα αρχείου<br>ή<br>ΑΔΑ</center>
                        </th>
                        <th class="small middle">
                            <center><span class='small'>Διατήρηση</center>
                        </th>
                        <th class="small middle">
                            <center><span class='small'>Έως</center>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($protocols as $protocol)
                        <tr>
                            <td class="small middle">
                                <strong>{{ $protocol->protocolnum }}</strong><br>
                                <span class='small'>{{ $protocol->protocoldate }}</span>
                            </td>
                            <td class="small middle">
                                <span class='small'>
                                    @if ($protocol->in_num)
                                        {{ $protocol->in_num }}/
                                    @endif
                                    {{ $protocol->in_date }}
                                    @if ($protocol->in_date and $protocol->in_topos_ekdosis)
                                        <br>
                                    @endif
                                    @if ($protocol->in_topos_ekdosis)
                                        &#x2727;{{ $protocol->in_topos_ekdosis }}
                                    @endif
                                    @if ($protocol->in_topos_ekdosis and $protocol->in_arxi_ekdosis)
                                        <br>
                                    @endif
                                    @if (!$protocol->in_topos_ekdosis and ($protocol->in_date and $protocol->in_arxi_ekdosis))
                                        <br>
                                    @endif
                                    @if ($protocol->in_arxi_ekdosis)
                                        &#x2726;{{ $protocol->in_arxi_ekdosis }}
                                    @endif
                                </span>
                            </td>
                            <td class="small middle"><span class='small'>{{ $protocol->thema }}
                                    @if ($protocol->in_perilipsi)
                                        <br>&#x2727;{{ $protocol->in_perilipsi }}
                                </span>
                    @endif
                    </td>
                    <td class="small middle"><span class='small'>
                            {{ $protocol->out_date }}
                            @if ($protocol->out_date and $protocol->out_to)
                                <br>
                            @endif
                            @if ($protocol->out_to)
                                &#x2727;{{ $protocol->out_to }}
                            @endif
                        </span></td>
                    <td class="small middle"><span class='small'>{{ $protocol->out_perilipsi }}</span></td>
                    <td class="small middle"><span class='small'>
                            {{ $protocol->fakelos }}
                        </span>
                    </td>
                    <td class="small middle"><span class='small'>
                            <ul class='list-unstyled'>
                                @foreach ($protocol->attachments as $attachment)
                                    @if ($attachment->name)
                                        <li>{{ $attachment->name }}</li>
                                    @endif
                                    @if ($attachment->ada)
                                        <li>{{ $attachment->ada }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </span>
                    </td>
                    <td class="small middle"><span class='small'>
                            @foreach ($protocol->attachments as $attachment)
                                @if ($loop->first)
                                    @if (is_numeric($attachment->keep))
                                        {{ $attachment->keep == 1 ? $attachment->keep . ' χρόνo' : $attachment->keep . ' χρόνια' }}
                                    @else
                                        {{ $attachment->keep }}
                                    @endif
                                @endif
                            @endforeach
                        </span>
                    </td>
                    <td class="small middle"><span class='small'>
                            @foreach ($protocol->attachments as $attachment)
                                @if ($loop->first and $attachment->expires)
                                    {{ \Carbon\Carbon::createFromFormat('Ymd', $attachment->expires)->format('d/m/Y') }}
                                @endif
                            @endforeach
                        </span>
                    </td>
                    </tr>
                    @endforeach
                    @if (!count($protocols))
                        <tr>
                            <td class="small" colspan=12>Δεν υπάρχουν Πρωτόκολλα με συνημμένα αρχεία τα οποία
                                ικανοποιούν τα κριτήρια που θέσατε.</td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td class="small" colspan=8>Εκτυπώθηκε: {{ $datetime }}</td>
                        <td class="small text-right">Σελ ........</td>
                    </tr>
                </tfoot>
            </table>

        </div>
    </div>
</body>

</html>
