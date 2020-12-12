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
      <h3>Αποστολή βεβαίωσης καταχώρισης στο Ηλ. Πρωτόκολλο με Email</h3>
      <h4>{{date('d/m/Y H:i:s')}}</h4>
      <p>Σας ενημερώνουμε ότι η υποβολή σας καταχωρίστηκε στο Ηλεκτρονικό Πρωτόκολλο με τα παρακάτω στοιχεία:</p>
      <p>
        <table border=1>
                <tr >
                    <th >Αρ.Πρωτ.:</th>
                    <td >{{$protocol->protocolnum}}</td>
                 </tr>
                <tr >
                    <th >Ημ.Παραλ.:</th>
                    <td >{{$protocoldate}}</d>
                </tr>
                <tr >
                    <th>Θέμα:</th>
                    <td >{{$protocol->thema}}</td>
                </tr>
                <tr >
                    <th>Αρχή έκδοσης:</th>
                    <td >{{$protocol->in_arxi_ekdosis}}</td>
                </tr>
        </table>
      </p>

    </body>
</html>
