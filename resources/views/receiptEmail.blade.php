<!DOCTYPE html>
<html lang="el">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

       <title>Αποστολή αποδεικτικού </title>

      </head>
    <body>
      <h4>{{$ipiresiasName}}</h4>
      <h4>Ηλεκτρονικό Πρωτόκολλο</h4>
      <h3>Βεβαίωση καταχώρισης ηλεκτρονικού μηνύματος στο Ηλ. Πρωτόκολλο</h3>
      <h4>{{date('d/m/Y H:i:s')}}</h4>
      <p>Σας ενημερώνουμε ότι το ηλεκτρονικό μήνυμα που μας στείλατε τις {{$emaildate}}, καταχωρίστηκε στο
      Ηλεκτρονικό Πρωτόκολλο με τα παρακάτω στοιχεία:</p>
        @php
            $fields = [
              'Φάκελος' => $protocol->fakelos,
              'Αρ.Πρωτοκόλλου' => $protocol->protocolnum,
              'Ημνια.Πρωτοκόλλου' => $protocol->protocoldate ? \Carbon\Carbon::createFromFormat('Ymd', $protocol->protocoldate)->format('d/m/Y') : '',
              'Έτος' => $protocol->etos,
              'Θέμα' => $protocol->thema,
              'Αρ.Εισερχομένου' => $protocol->in_num,
              'Ημνια.Εισερχομένου' => $protocol->in_date ? \Carbon\Carbon::createFromFormat('Ymd', $protocol->in_date)->format('d/m/Y') : '',
              'Τόπος έκδοσης' => $protocol->in_topos_ekdosis,
              'Αρχή έκδοσης' => $protocol->in_arxi_ekdosis,
              'Παρελήφθη από' => $protocol->in_paraliptis,
              'Περίληψη εισερχομένου' => $protocol->in_perilipsi,
              'Διεκπεραίση από' => $protocol->diekperaiosi ? \App\User::find($protocol->diekperaiosi)->name : '',
              'Ημνια διεκπεραίωσης' => $protocol->diekp_date ? \Carbon\Carbon::createFromFormat('Ymd', $protocol->diekp_date)->format('d/m/Y') : '',
              'Σχετικοί αριθμόι' => $protocol->sxetiko,
              'Απευθύνεται σε' => $protocol->out_to,
              'Ημνια εξερχομένου' => $protocol->out_date ? \Carbon\Carbon::createFromFormat('Ymd', $protocol->out_date)->format('d/m/Y') : '',
              'Περίληψη εξερχομένου' => $protocol->out_perilipsi,
              //'Παρατηρήσεις' => $protocol->paratiriseis,
              //'Λέξεις κλειδιά' => $protocol-> keywords ,
          ];
        @endphp
      <p>
        <table>
            @foreach($fields as $key => $value)
              @if($value)
                <tr >
                    <td ><b>{{$key}}:</b></td>
                    <td >{{$value}}</td>
                 </tr>
              @endif
            @endforeach
         </table>
      </p>

    </body>
</html>
