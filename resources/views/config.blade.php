
@extends('layouts.app')

@section('content')
    @php
        $getSetting = function ($key) use ($settings) {
            return $settings[$key] ?? App\Config::getConfigValueOf($key);
        };

        $wideListProtocol = $getSetting('wideListProtocol');
        $showUserInfo = $getSetting('showUserInfo');
        $emailFetchOrderDesc = $getSetting('emailFetchOrderDesc');
        $saveEmailAs = $getSetting('saveEmailAs');
        $defaultImapEmail = $getSetting('defaultImapEmail');

        $imapAccounts = array_keys(config('imap.accounts', []) ?? []);
    @endphp

    <style>
        .hideoverflow {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-sm-8 col-md-offset-2 col-sm-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading h1 text-center">Ρυθμίσεις Εφαρμογής</div>

                    <div class="panel-body">
                        <form id="configform" name="configform" class="form-horizontal"
                              role="form" method="POST" action="{{ url('/settings') }}">
                            {{ csrf_field() }}

                            {{-- Στοιχεία εφαρμογής --}}
                            <div class="panel panel-default col-md-12 col-sm-12">
                                <div class="row bg-info">
                                    <div class="form-control-static h4 text-center">Στοιχεια εφαρμογής</div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-7 col-sm-7 col-md-offset-1 col-sm-offset-1">
                                        Όνομα υπηρεσίας - σχολείου
                                    </div>
                                    <div class="col-md-4 col-sm-4" id="ipiresiasNamediv">
                                        <input id="ipiresiasName" type="text" class="form-control text-center"
                                               name="ipiresiasName" placeholder="ipiresiasName"
                                               value="{{ $getSetting('ipiresiasName') }}" title="">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-1">
                                        Ενεργό έτος πρωτοκόλλησης
                                    </div>
                                    <div class="col-md-2 col-sm-2" id="yearInUsediv">
                                        <input id="yearInUse" type="text" class="form-control text-center"
                                               name="yearInUse" placeholder="yearInUse"
                                               value="{{ $getSetting('yearInUse') }}"
                                               title="Αφήστε κενό για να συνεχίζεται η αρίθμηση διαχρονικά">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-1">
                                        Αρ.Πρωτοκόλλου για ξεκίνημα
                                    </div>
                                    <div class="col-md-2 col-sm-2" id="firstProtocolNumdiv">
                                        <input id="firstProtocolNum" type="text" class="form-control text-center"
                                               name="firstProtocolNum" placeholder="firstProtocolNum"
                                               value="{{ $getSetting('firstProtocolNum') }}" title="">
                                    </div>
                                </div>
                            </div>

                            {{-- Ρυθμίσεις εμφάνισης --}}
                            <div class="panel panel-default col-md-12 col-sm-12">
                                <div class="row bg-warning">
                                    <div class="form-control-static h4 text-center">Ρυθμίσεις Εμφάνισης</div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-1">
                                        Να εμφανίζονται στη σελίδα ->
                                        <strong>{{ $getSetting('showRowsInPage') }}</strong>
                                        <- γραμμές
                                    </div>
                                    <div class="col-md-2 col-sm-2" id="showRowsInPagediv">
                                        <input id="showRowsInPage" type="text" class="form-control text-center"
                                               name="showRowsInPage" placeholder="showRowsInPage"
                                               value="{{ $getSetting('showRowsInPage') }}" title="">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-1">
                                        Ανανέωση Πρωτοκόλλου κάθε ->
                                        <strong>{{ $getSetting('minutesRefreshInterval') }}</strong>
                                        <- λεπτά
                                    </div>
                                    <div class="col-md-2 col-sm-2" id="minutesRefreshIntervaldiv">
                                        <input id="minutesRefreshInterval" type="text"
                                               class="form-control text-center" name="minutesRefreshInterval"
                                               placeholder="minutesRefreshInterval"
                                               value="{{ $getSetting('minutesRefreshInterval') }}" title="">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-1">
                                        Χρώμα επικεφαλίδας
                                    </div>
                                    <div class="col-md-2 col-sm-2" id="titleColordiv">
                                        <input id="titleColor" type="text" class="form-control text-center"
                                               name="titleColor" placeholder="titleColor"
                                               value="{{ $getSetting('titleColor') }}" title="">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-1">
                                        Να απλώνεται σε όλο τον διαθέσιμο χώρο
                                    </div>
                                    <div class="col-md-2 col-sm-2" id="wideListProtocoldiv">
                                        <select id="wideListProtocol" name="wideListProtocol" class="form-control" title="">
                                            <option value="0" {{ !$wideListProtocol ? 'selected' : '' }}>ΟΧΙ</option>
                                            <option value="1" {{ $wideListProtocol ? 'selected' : '' }}>ΝΑΙ</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-1">
                                        Εμφάνιση πληροφοριών Χρηστών
                                    </div>
                                    <div class="col-md-2 col-sm-2" id="showUserInfodiv">
                                        <select id="showUserInfo" name="showUserInfo" class="form-control" title="">
                                            <option value="0" {{ $showUserInfo == 0 ? 'selected' : '' }}>ΟΧΙ</option>
                                            <option value="1" {{ $showUserInfo == 1 ? 'selected' : '' }}>username</option>
                                            <option value="2" {{ $showUserInfo == 2 ? 'selected' : '' }}>Όνομα</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-1">
                                        Εμφάνιση λίστας με τιμές που ταιριάζουν σε ότι πληκτρολογούμε
                                    </div>
                                    <div class="col-md-2 col-sm-2" id="allowListValuesMatchingInputdiv">
                                        <select id="allowListValuesMatchingInput" name="allowListValuesMatchingInput"
                                                class="form-control" title="">
                                            <option value="0" {{ !$getSetting('allowListValuesMatchingInput') ? 'selected' : '' }}>ΟΧΙ</option>
                                            <option value="1" {{ $getSetting('allowListValuesMatchingInput') ? 'selected' : '' }}>ΝΑΙ</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-1">
                                        Εμφάνιση μόνο των πρωτοκόλλων που αφορούν το χρήστη
                                    </div>
                                    <div class="col-md-2 col-sm-2" id="limitProtocolAccessListdiv">
                                        <select id="limitProtocolAccessList" name="limitProtocolAccessList"
                                                class="form-control" title="">
                                            <option value="0" {{ !$getSetting('limitProtocolAccessList') ? 'selected' : '' }}>ΟΧΙ</option>
                                            <option value="1" {{ $getSetting('limitProtocolAccessList') ? 'selected' : '' }}>ΝΑΙ</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Ρυθμίσεις αναζήτησης --}}
                            <div class="panel panel-default col-md-12 col-sm-12">
                                <div class="row bg-success">
                                    <div class="form-control-static h4 text-center">Ρυθμίσεις Αναζήτησης</div>
                                </div>

                                @foreach ([1, 2, 3] as $number)
                                    @php
                                        $searchFieldKey = 'searchField' . $number;
                                        $selectedSearchField = $getSetting($searchFieldKey);
                                    @endphp

                                    <div class="row">
                                        <div class="form-control-static col-md-6 col-sm-6 col-md-offset-2 col-sm-offset-2">
                                            {{ $number }}ο πεδίο αναζήτησης
                                        </div>
                                        <div class="col-md-4 col-sm-4" id="{{ $searchFieldKey }}div">
                                            <select id="{{ $searchFieldKey }}" name="{{ $searchFieldKey }}"
                                                    class="form-control" title="">
                                                @foreach ($fields as $key => $value)
                                                    <option value="{{ $key }}"
                                                        {{ (string) $key === (string) $selectedSearchField ? 'selected' : '' }}>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="row">
                                    <div class="form-control-static col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-1">
                                        Βήμα μετακίνησης με το κλικ των κουμπιών
                                        <img src="{{ URL::to('/') }}/images/arrow-left-double.png" height="20" alt="">
                                        και
                                        <img src="{{ URL::to('/') }}/images/arrow-right-double.png" height="20" alt="">
                                    </div>
                                    <div class="col-md-2 col-sm-2" id="protocolArrowStepdiv">
                                        <input id="protocolArrowStep" type="text" class="form-control text-center"
                                               name="protocolArrowStep" placeholder="protocolArrowStep"
                                               value="{{ $getSetting('protocolArrowStep') }}" title="">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-1">
                                        Μέγιστος αριθμός γραμμών που επιστρέφει η αναζήτηση
                                    </div>
                                    <div class="col-md-2 col-sm-2" id="maxRowsInFindPagediv">
                                        <input id="maxRowsInFindPage" type="text" class="form-control text-center"
                                               name="maxRowsInFindPage" placeholder="maxRowsInFindPage"
                                               value="{{ $getSetting('maxRowsInFindPage') }}" title="">
                                    </div>
                                </div>
                            </div>

                            {{-- Εξαγωγή σε Excel --}}
                            <div class="panel panel-default col-md-12 col-sm-12">
                                <div class="row bg-info">
                                    <div class="form-control-static h4 text-center">Εξαγωγή σε Excel</div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-1">
                                        Μέγιστος αριθμός γραμμών που εξάγονται σε Excel.
                                    </div>
                                    <div class="col-md-2 col-sm-2" id="maxRowsInXlsExportdiv">
                                        <input id="maxRowsInXlsExport" type="text" class="form-control text-center"
                                               name="maxRowsInXlsExport" placeholder="5000"
                                               value="{{ $getSetting('maxRowsInXlsExport') }}"
                                               title="Ρυθμίστε ανάλογα με τη διαθέσιμη μνήμη για να μην εξαντλείται και καταρρέει η php">
                                    </div>
                                </div>
                            </div>

                            {{-- Δικαιώματα χρηστών --}}
                            <div class="panel panel-default col-md-12 col-sm-12">
                                <div class="row bg-danger">
                                    <div class="form-control-static h4 text-center">Δικαιώματα χρηστών</div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-1">
                                        Έλεγχοι & περιορισμοί κατά την καταχώριση
                                    </div>
                                    <div class="col-md-2 col-sm-2" id="protocolValidatediv">
                                        <select id="protocolValidate" name="protocolValidate" class="form-control" title="">
                                            <option value="0" {{ !$getSetting('protocolValidate') ? 'selected' : '' }}>ΟΧΙ</option>
                                            <option value="1" {{ $getSetting('protocolValidate') ? 'selected' : '' }}>ΝΑΙ</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-1">
                                        Ασφαλής, όχι διπλότυπος Νέος Αρ.Πρωτοκόλλου
                                    </div>
                                    <div class="col-md-2 col-sm-2" id="safeNewProtocolNumdiv">
                                        <select id="safeNewProtocolNum" name="safeNewProtocolNum" class="form-control" title="">
                                            <option value="0" {{ !$getSetting('safeNewProtocolNum') ? 'selected' : '' }}>ΟΧΙ</option>
                                            <option value="1" {{ $getSetting('safeNewProtocolNum') ? 'selected' : '' }}>ΝΑΙ</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-1">
                                        Έλεύθερη επιλογή χρόνου διατήρησης αρχείων
                                    </div>
                                    <div class="col-md-2 col-sm-2" id="allowUserChangeKeepSelectdiv">
                                        <select id="allowUserChangeKeepSelect" name="allowUserChangeKeepSelect"
                                                class="form-control" title="">
                                            <option value="0" {{ !$getSetting('allowUserChangeKeepSelect') ? 'selected' : '' }}>ΟΧΙ</option>
                                            <option value="1" {{ $getSetting('allowUserChangeKeepSelect') ? 'selected' : '' }}>ΝΑΙ</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-1">
                                        Δυνατότητα Επεξεργασίας Πρωτοκόλλου
                                    </div>
                                    <div class="col-md-2 col-sm-2" id="allowWriterUpdateProtocoldiv">
                                        <select id="allowWriterUpdateProtocol" name="allowWriterUpdateProtocol"
                                                class="form-control" title="">
                                            <option value="0" {{ $getSetting('allowWriterUpdateProtocol') == 0 ? 'selected' : '' }}>ΚΑΝΕΙΣ</option>
                                            <option value="1" {{ $getSetting('allowWriterUpdateProtocol') == 1 ? 'selected' : '' }}>ΕΝΑΣ</option>
                                            <option value="2" {{ $getSetting('allowWriterUpdateProtocol') == 2 ? 'selected' : '' }}>ΟΛΟΙ</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-1">
                                        Χρόνος σε λεπτά δυνατότητας επεξεργασίας Πρωτοκόλλου
                                    </div>
                                    <div class="col-md-2 col-sm-2" id="allowWriterUpdateProtocolTimeInMinutesdiv">
                                        <input id="allowWriterUpdateProtocolTimeInMinutes" type="text"
                                               class="form-control text-center"
                                               name="allowWriterUpdateProtocolTimeInMinutes"
                                               placeholder="allowWriterUpdateProtocolTimeInMinutes"
                                               value="{{ $getSetting('allowWriterUpdateProtocolTimeInMinutes') }}"
                                               title="">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-1">
                                        Έπιτρέπεται η καταχώριση κενού πρωτοκόλλου
                                    </div>
                                    <div class="col-md-2 col-sm-2" id="allowEmptyProtocoldiv">
                                        <select id="allowEmptyProtocol" name="allowEmptyProtocol" class="form-control" title="">
                                            <option value="0" {{ !$getSetting('allowEmptyProtocol') ? 'selected' : '' }}>ΟΧΙ</option>
                                            <option value="1" {{ $getSetting('allowEmptyProtocol') ? 'selected' : '' }}>ΝΑΙ</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-1">
                                        Ενημέρωση όλων των σχετικών Πρωτοκόλλων
                                    </div>
                                    <div class="col-md-2 col-sm-2" id="saveCycleSxetikodiv">
                                        <select id="saveCycleSxetiko" name="saveCycleSxetiko" class="form-control" title="">
                                            <option value="" {{ !$getSetting('saveCycleSxetiko') ? 'selected' : '' }}>ΟΧΙ</option>
                                            <option value="1" {{ $getSetting('saveCycleSxetiko') ? 'selected' : '' }}>ΝΑΙ</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Έλεγχος για ενημερώσεις --}}
                            <div class="panel panel-default col-md-12 col-sm-12">
                                <div class="row bg-info">
                                    <div class="form-control-static h4 text-center">Έλεγχος για ενημερώσεις</div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-1">
                                        Να γίνεται έλεγχος για ενημερώσεις
                                    </div>
                                    <div class="col-md-2 col-sm-2" id="updatesAutoCheckdiv">
                                        <select id="updatesAutoCheck" name="updatesAutoCheck" class="form-control" title="">
                                            <option value="0" {{ !$getSetting('updatesAutoCheck') ? 'selected' : '' }}>ΟΧΙ</option>
                                            <option value="1" {{ $getSetting('updatesAutoCheck') ? 'selected' : '' }}>ΝΑΙ</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Ρυθμίσεις συνημμένων αρχείων --}}
                            <div class="panel panel-default col-md-12 col-sm-12">
                                <div class="row bg-warning">
                                    <div class="form-control-static h4 text-center">Ρυθμίσεις συνημμένων αρχείων</div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-7 col-sm-7 col-md-offset-1 col-sm-offset-1">
                                        URL Διαύγειας
                                    </div>
                                    <div class="col-md-4 col-sm-4" id="diavgeiaUrldiv">
                                        <input id="diavgeiaUrl" type="text" class="form-control text-center"
                                               name="diavgeiaUrl" placeholder="diavgeiaUrl"
                                               value="{{ $getSetting('diavgeiaUrl') }}" title="">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-1">
                                        Εγγραφή Αρ. Πρωτοκόλλου στα συνημμένα αρχεία
                                    </div>
                                    <div class="col-md-2 col-sm-2" id="putStampdiv">
                                        <select id="putStamp" name="putStamp" class="form-control" title="">
                                            <option value="0" {{ !$getSetting('putStamp') ? 'selected' : '' }}>ΟΧΙ</option>
                                            <option value="1" {{ $getSetting('putStamp') ? 'selected' : '' }}>ΝΑΙ</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Ρυθμίσεις email --}}
                            <div class="panel panel-default col-md-12 col-sm-12">
                                <div class="row bg-success">
                                    <div class="form-control-static h4 text-center">Ρυθμίσεις εισερχομένων email</div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-1">
                                        Χρησιμοποίησε τον λογαριασμό email
                                    </div>
                                    <div class="col-md-2 col-sm-2" id="defaultImapEmaildiv">
                                        <select id="defaultImapEmail" name="defaultImapEmail"
                                                class="form-control text-center"
                                                title="Οι λογαριασμοί ρυθμίζονται στο config/imap.php">
                                            <option value="" {{ empty($defaultImapEmail) ? 'selected' : '' }}>---</option>
                                            @foreach ($imapAccounts as $key)
                                                <option value="{{ $key }}"
                                                    {{ (string) $defaultImapEmail === (string) $key ? 'selected' : '' }}>
                                                    {{ $key }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-1">
                                        Κατέβασε email μόνο για τις τελευταίες ->
                                        <strong>{{ $getSetting('daysToCheckEmailBack') }}</strong>
                                        <- ημέρες
                                    </div>
                                    <div class="col-md-2 col-sm-2" id="daysToCheckEmailBackdiv">
                                        <input id="daysToCheckEmailBack" type="text" class="form-control text-center"
                                               name="daysToCheckEmailBack" placeholder="daysToCheckEmailBack"
                                               value="{{ $getSetting('daysToCheckEmailBack') }}" title="">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-1">
                                        Αριθμός εμφανιζομένων εισερχομένων email
                                    </div>
                                    <div class="col-md-2 col-sm-2" id="emailNumFetchdiv">
                                        <input id="emailNumFetch" type="text" class="form-control text-center"
                                               name="emailNumFetch" placeholder="emailNumFetch"
                                               value="{{ $getSetting('emailNumFetch') }}" title="">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-1">
                                        Ταξινόμηση email
                                        <strong>{{ $emailFetchOrderDesc ? 'ΦΘΙΝΟΥΣΑ' : 'ΑΥΞΟΥΣΑ' }}</strong>
                                    </div>
                                    <div class="col-md-3 col-sm-3" id="emailFetchOrderDescdiv">
                                        <select id="emailFetchOrderDesc" name="emailFetchOrderDesc"
                                                class="form-control" title="">
                                            <option value="0" {{ !$emailFetchOrderDesc ? 'selected' : '' }}>ΑΥΞΟΥΣΑ</option>
                                            <option value="1" {{ $emailFetchOrderDesc ? 'selected' : '' }}>ΦΘΙΝΟΥΣΑ</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-1">
                                        Το πεδίο Φάκελος απαιτείται για την πρωτοκόλληση email
                                    </div>
                                    <div class="col-md-2 col-sm-2" id="alwaysShowFakelosInViewEmailsdiv">
                                        <select id="alwaysShowFakelosInViewEmails" name="alwaysShowFakelosInViewEmails"
                                                class="form-control" title="">
                                            <option value="0" {{ !$getSetting('alwaysShowFakelosInViewEmails') ? 'selected' : '' }}>ΟΧΙ</option>
                                            <option value="1" {{ $getSetting('alwaysShowFakelosInViewEmails') ? 'selected' : '' }}>ΝΑΙ</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-1">
                                        Αποστολή απόδειξης σε κάθε καταχώριση Ηλ. Πρωτοκόλλου από email
                                    </div>
                                    <div class="col-md-2 col-sm-2" id="alwaysSendReceitForEmailsdiv">
                                        <select id="alwaysSendReceitForEmails" name="alwaysSendReceitForEmails"
                                                class="form-control" title="">
                                            <option value="0" {{ !$getSetting('alwaysSendReceitForEmails') ? 'selected' : '' }}>ΟΧΙ</option>
                                            <option value="1" {{ $getSetting('alwaysSendReceitForEmails') ? 'selected' : '' }}>ΝΑΙ</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-7 col-sm-7 col-md-offset-1 col-sm-offset-1">
                                        Χρήστες που επιτρέπεται να πρωτοκολλούν email
                                    </div>
                                    <div class="col-md-4 col-sm-4" id="allowedEmailUsersdiv">
                                        <input id="allowedEmailUsers" type="text" class="form-control text-center"
                                               name="allowedEmailUsers" placeholder="usernames"
                                               value="{{ $getSetting('allowedEmailUsers') }}"
                                               title='Πληκτρολογείστε τα usernames των χρηστών χωρισμένα με " " κενό'>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-1">
                                        Αποθήκευση email με τη μορφή
                                    </div>
                                    <div class="col-md-2 col-sm-2" id="saveEmailAsdiv">
                                        <select id="saveEmailAs" name="saveEmailAs" class="form-control" title="">
                                            <option value="" {{ empty($saveEmailAs) ? 'selected' : '' }}>html</option>
                                            <option value="eml" {{ $saveEmailAs === 'eml' ? 'selected' : '' }}>eml</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Εξερχόμενα email --}}
                                <div class="row bg-success">
                                    <div class="form-control-static h4 text-center">
                                        Ρυθμίσεις εξερχομένων email
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-control-static col-md-8 col-sm-8 col-md-offset-1 col-sm-offset-1">
                                        Στείλε email όταν Ανατίθεται Πρωτ. στον Διεκπεραιωτή
                                    </div>
                                    <div class="col-md-2 col-sm-2" id="sendEmailOnDiekperaiosiChangediv">
                                        <select id="sendEmailOnDiekperaiosiChange"
                                                name="sendEmailOnDiekperaiosiChange" class="form-control" title="">
                                            <option value="0" {{ !$getSetting('sendEmailOnDiekperaiosiChange') ? 'selected' : '' }}>ΟΧΙ</option>
                                            <option value="1" {{ $getSetting('sendEmailOnDiekperaiosiChange') ? 'selected' : '' }}>ΝΑΙ</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Ρυθμίσεις αντιγράφων ασφαλείας --}}
                            @if (env('DB_CONNECTION') !== 'sqlite')
                                <div class="panel panel-default col-md-12 col-sm-12">
                                    <div class="row bg-danger">
                                        <div class="form-control-static h4 text-center">
                                            Ρυθμίσεις αντιγράφων ασφαλείας
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-control-static col-md-7 col-sm-7 col-md-offset-1 col-sm-offset-1">
                                            Εκτελέσιμο αρχείο της mysqldump στον server
                                        </div>
                                        <div class="col-md-4 col-sm-4" id="mysqldumpPathdiv">
                                            <input id="mysqldumpPath" type="text" class="form-control text-center"
                                                   name="mysqldumpPath" placeholder="mysqldumpPath"
                                                   value="{{ $getSetting('mysqldumpPath') }}" title="">
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Κουμπιά ενεργειών --}}
                            <div class="row">
                                <div class="col-md-2 col-sm-2 col-md-offset-4 col-sm-offset-4 text-center">
                                    <button type="reset" class="btn btn-link" title="Καθαρισμός">
                                        <img src="{{ URL::to('/') }}/images/clear.ico" height="30" alt="Καθαρισμός">
                                    </button>
                                </div>

                                <div class="col-md-2 col-sm-2 text-center">
                                    <button type="submit" class="btn btn-link" title="Αποθήκευση">
                                        <img src="{{ URL::to('/') }}/images/save.ico" height="30" alt="Αποθήκευση">
                                    </button>
                                </div>

                                <div class="col-md-2 col-sm-2 col-md-offset-2 col-sm-offset-2 text-center">
                                    <a href="{{ URL::to(config('landing-page.page.' . auth()->user()->role_id)) }}"
                                       role="button" title="Πρωτόκολλο">
                                        <img src="{{ URL::to('/') }}/images/protocol.png" height="30" alt="Πρωτόκολλο">
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
