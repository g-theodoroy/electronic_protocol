@extends('layouts.app')

@section('content')

<style>
.hideoverflow{
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>

<div class="container">
    <div class="row">
        <div class="col-md-8 col-sm-10 col-md-offset-2 col-sm-offset-1">
           <div class="panel panel-default">
                <div class="panel-heading h1 text-center">Πληροφορίες</div>

                <div class="panel-body ">
                 <div class="panel-body ">
                    <div class="panel panel-default col-md-12 col-sm-12  ">
                        <div class="row bg-warning">
                            <div class="form-control-static h4 text-center">Ομάδα δημιουργίας του Ηλ.Πρωτοκόλλου </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-sm-6 text-justify" >
                              <h4 class="text-center" >
                                Γεώργιος Θεοδώρου
                              <a href="mailto:g.theodoroy@gmail.com?subject=Ηλ.Πρωτόκολλο" title="Αποστολή email">
                                <img src="{{ URL::to('/') }}/images/email.png" height=15 alt="Αποστολή email" />
                              </a>
                              </h4>
                              <p>
                                Καθηγητής Φυσικής Αγωγής.<br>Υπηρετεί στην Δ/θμια Εκπση Αχαΐας.
                                Επιμελήθηκε το σχεδιασμό και την υλοποίηση του project.
                                Συντηρεί, ενημερώνει και βελτιώνει το project.
                               </p>
                            </div>
                            <div class="col-md-6 col-sm-6 text-justify" >
                              <h4 class="text-center">Παναγιώτης Ζώτος</h4>
                              <p>
                                Διοικητικος με ειδικότητα Πληροφορικής.<br>Υπηρετεί στην Δ/θμια Εκπση Αχαΐας.
                                Με την μεγάλη του διοικητική εμπειρία βοήθησε τα μάλα στον σχεδιασμό του project.
                                Επιμελήθηκε τους Φακέλους Φ. καθώς και το χρόνο διατήρησης των αρχείων.
                               </p>
                            </div>
                            <div class="col-md-12 col-sm-12 text-justify" >
                                Πολύτιμη ήταν η συμβολή μελών της κοινότητας <a href="http://alkisg.mysch.gr/steki/index.php?topic=6958.0" target="_blank">Το Στέκι των Πληροφορικών</a> που με τις ιδέες και
                                τις προτάσεις τους ραφινάρισαν το project...
                            </div>
                            <div class="col-md-12 col-sm-12" >
                              <h4 class="text-center bg-warning">Εύσημα...</h4>
                              <p>
                                Το Ηλ. Πρωτόκολλο είναι διαδικτυακή εφαρμογή. Είναι γραμμένη σε γλώσσα PHP και χρησιμοποιεί
                                τα μοντέρνα framework <a href="https://laravel.com/" target="_blank"><strong>Laravel</strong></a>,
                                 <a href="http://getbootstrap.com/" target="_blank"><strong>Bootstrap</strong></a>,
                                τη βιβλιοθήκη javascript <a href="https://jquery.com/" target="_blank"><strong>jQuery</strong></a>, ...
                             </p>
                              <h4 class="text-center bg-warning">Αποποίηση ευθυνών</h4>
                                <p>
                                Παρέχεται "ώς έχει" σύμφωνα με τη "Γενική Άδεια Δημόσιας Χρήσης GNU".
                                Τακτικά backup θα προστατέψουν τα δεδομένα σας. Η ομάδα δημιουργίας δεν φέρει καμιά ευθυνη
                                 για τυχόν προβλήματα δημιουργηθούν από τη μή σώφρονα χρήση του.
                               </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel-body ">
                    <div class="panel panel-default col-md-12 col-sm-12  ">
                        <div class="row bg-danger">
                            <div class="form-control-static h4 text-center">Υποστήριξη Ηλ.Πρωτοκόλλου</div>
                        </div>
                        <div class="row">
                            <div class="col-md-8 col-sm-8" >
                                Το Ηλ.Πρωτόκολλο είναι και θα είναι <b>δωρεάν</b>.
                                Αν σας εξυπηρετεί, σας τέρπει και επιθυμείτε μπορείτε
                               να κάνετε μια μικρή δωρεά...
                            </div>
                           <div class="col-md-4 col-sm-4 text-center form-control-static" >
                                <form action="https://www.paypal.com/donate" method="post" target="_top">
                                <input type="hidden" name="hosted_button_id" value="TW3TKMTMDLHK2" />
                                <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate with PayPal button" />
                                <img alt="" border="0" src="https://www.paypal.com/en_GR/i/scr/pixel.gif" width="1" height="1" />
                                </form> 
                           </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


@endsection
