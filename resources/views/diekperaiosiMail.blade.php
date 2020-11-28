<!DOCTYPE html>
<html lang="el">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

       <title>Ανάθεση Ηλ. Πρωτοκόλλου προς Διεκπεραίωση</title>

    </head>
    <body>
      <h4>{{$ipiresiasName}}</h4>
      <h4>Ηλεκτρονικό Πρωτόκολλο</h4>
      <p>&nbsp;</p>
      <p>{{date('d/m/Y H:i:s')}}</p>
      <p>Αποστολέας: <b>{{Auth::user()->name}}</b></p>
      <p>Παραλήπτης: <b>{{$diekperaiotis}}</b></p>
      <p>&nbsp;</p>
      <p>Σας ενημερώνουμε ότι σας ανατέθηκε το ακόλουθο Ηλ. Πρωτοκόλλο προς Διεκπεραίωση:</p>
      <p>
        <table >
           <tr >
               <td >Αρ.Πρωτ.:</td>
               <td >{{$protocol->protocolnum}}</td>
            </tr>
                <tr >
                    <td >Ημ.Παραλ.:</td>
                    <td >{{$date}}</d>
                </tr>
                <tr >
                    <td>Θέμα:</td>
                    <td >{{$protocol->thema}}</td>
                </tr>
                <tr >
                    <td>Διεκπεραίωση:</td>
                    <td >{{$diekperaiotis}}</td>
                </tr>
                <tr >
                    <td>Αναθέτων:</td>
                    <td >{{Auth::user()->name}}</td>
                </tr>
                <tr >
                    <td >Σύνδεσμος:</td>
                    <td ><a href="{{ URL::to(env('APP_URL') . '/home/' . $protocol->id) }}" >{{ URL::to( env('APP_URL') . '/home/' . $protocol->id) }}</a></td>
                 </tr>
        </table>
      </p>
      <p>&nbsp;</p>
    </body>
</html>
