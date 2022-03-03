<!DOCTYPE html>
<html lang="el">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Εκτύπωση Αποδεικτικού </title>

    <!-- Styles -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        .table tbody>tr>td.middle {
            vertical-align: middle;
        }

        .table tbody>tr>td.center {
            text-align: center;
        }

        .table tbody>tr>th.center {
            text-align: center;
        }

        .table tbody>tr>td.right {
            text-align: right;
        }

        .table-bordered td,
        .table-bordered th {
            border-color: black !important;
        }

    </style>
</head>

<body>
    <div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2 col-xs-8 col-xs-offset-2 text-center h4">
        {{ $ipiresiasName }}<br>Απόδειξη καταχώρισης στο Ηλ.Πρωτόκολλο
    </div>

    <div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2 col-xs-8 col-xs-offset-2">
        <div class="table-responsive">

            <table class="table table-condensed table-bordered">
                <tr>
                    <td class="col-md-3 col-sm-3 col-xs-3 right">Αρ.Πρωτ.:</td>
                    <th class="center">{{ $protocol->protocolnum }}</th>
                </tr>
                <tr>
                    <td class="right">Ημ.Παραλ.:</td>
                    <th class="center">{{ $protocol->protocoldate }}</th>
                </tr>
                <tr>
                    <td class="right">Θέμα:</td>
                    <td>{{ $protocol->thema }}</td>
                </tr>
            </table>

        </div>
    </div>
    @if ($datetime)
        <div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2 col-xs-8 col-xs-offset-2 text-right ">
            Εκτυπώθηκε: {{ $datetime }}
        </div>
    @endif
</body>

</html>
