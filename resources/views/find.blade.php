
@extends('layouts.app')

@section('content')
    <style>
        .hideoverflow {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>

    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading h1 text-center" {!! $titleColorStyle !!}>
                        Αναζήτηση Πρωτοκόλλου
                    </div>

                    <div class="panel-body">

                        {{-- Search by one protocol number --}}
                        <div class="panel panel-default col-md-6 col-sm-6">
                            <div class="row bg-info">
                                <div class="form-control-static h4 text-center">
                                    Ο Αριθμός Πρωτοκόλλου να είναι
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2 col-sm-2 form-control-static">
                                    Αριθμός
                                </div>

                                <div class="col-md-3 col-sm-3" id="protocolnumdiv">
                                    <input id="protocolnum"
                                        type="text"
                                        class="form-control text-center"
                                        name="protocolnum"
                                        placeholder="Αρ. Πρωτ."
                                        value=""
                                        title="Αριθμός πρωτοκόλλου">
                                </div>

                                <div class="col-md-2 col-sm-2 form-control-static">
                                    Έτος
                                </div>

                                <div class="col-md-3 col-sm-3" id="etosForOnediv">
                                    <input id="etosForOne"
                                        type="text"
                                        class="form-control text-center"
                                        name="etosForOne"
                                        placeholder="Έτος"
                                        value=""
                                        title="Έτος">
                                </div>

                                <div class="col-md-2 col-sm-2 form-control-static text-center">
                                    <a href="#"
                                        onclick="chkFindOne(); return false;"
                                        role="button"
                                        title="Αναζήτηση">
                                        <img src="{{ URL::to('/') }}/images/find.ico" height="20" alt="Αναζήτηση">
                                    </a>
                                </div>
                            </div>
                        </div>

                        <form id="findform">
                            {{-- Search by protocol number range --}}
                            <div class="panel panel-default col-md-6 col-sm-6">
                                <div class="row bg-info">
                                    <div class="form-control-static h4 text-center">
                                        Ο Αριθμός Πρωτοκόλλου από - έως - έτος
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3 col-sm-3 col-md-offset-1 col-sm-offset-1"
                                        id="aponumdiv">
                                        <input id="aponum"
                                            type="text"
                                            class="form-control text-center"
                                            name="aponum"
                                            placeholder="από"
                                            value=""
                                            title="Από αριθμό πρωτοκόλλου">
                                    </div>

                                    <div class="col-md-3 col-sm-3" id="eosnumdiv">
                                        <input id="eosnum"
                                            type="text"
                                            class="form-control text-center"
                                            name="eosnum"
                                            placeholder="έως"
                                            value=""
                                            title="Έως αριθμό πρωτοκόλλου">
                                    </div>

                                    <div class="col-md-3 col-sm-3" id="etosForManydiv">
                                        <input id="etosForMany"
                                            type="text"
                                            class="form-control text-center"
                                            name="etosForMany"
                                            placeholder="έτος"
                                            value=""
                                            title="Για το έτος">
                                    </div>

                                    <div class="col-md-2 col-sm-2 form-control-static text-center">
                                        <a href="#"
                                            onclick="getFindData(); return false;"
                                            role="button"
                                            title="Αναζήτηση">
                                            <img src="{{ URL::to('/') }}/images/find.ico" height="20" alt="Αναζήτηση">
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- Date ranges --}}
                            <div class="panel panel-default col-md-6 col-sm-6">
                                <div class="row bg-success">
                                    <div class="form-control-static h4 text-center">
                                        Ημερομηνίες από - έως
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 col-sm-4 form-control-static hideoverflow">
                                        Ημ/νία παραλαβής
                                    </div>

                                    <div class="col-md-3 col-sm-3" id="apoProtocolDatediv">
                                        <input id="apoProtocolDate"
                                            type="text"
                                            class="form-control datepicker text-center"
                                            name="apoProtocolDate"
                                            placeholder="από"
                                            value=""
                                            title="Από ημ/νία παραλαβής">
                                    </div>

                                    <div class="col-md-3 col-sm-3" id="eosProtocolDatediv">
                                        <input id="eosProtocolDate"
                                            type="text"
                                            class="form-control datepicker text-center"
                                            name="eosProtocolDate"
                                            placeholder="έως"
                                            value=""
                                            title="Έως ημ/νία παραλαβής">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 col-sm-4 form-control-static hideoverflow">
                                        Ημ/νία εισερχομένου
                                    </div>

                                    <div class="col-md-3 col-sm-3" id="apoEiserxDatediv">
                                        <input id="apoEiserxDate"
                                            type="text"
                                            class="form-control datepicker text-center"
                                            name="apoEiserxDate"
                                            placeholder="από"
                                            value=""
                                            title="Από ημ/νία εισερχομένου">
                                    </div>

                                    <div class="col-md-3 col-sm-3" id="eosEiserxDatediv">
                                        <input id="eosEiserxDate"
                                            type="text"
                                            class="form-control datepicker text-center"
                                            name="eosEiserxDate"
                                            placeholder="έως"
                                            value=""
                                            title="Έως ημ/νία εισερχομένου">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 col-sm-4 form-control-static hideoverflow">
                                        Ημ/νία εξερχομένου
                                    </div>

                                    <div class="col-md-3 col-sm-3" id="apoExerxDatediv">
                                        <input id="apoExerxDate"
                                            type="text"
                                            class="form-control datepicker text-center"
                                            name="apoExerxDate"
                                            placeholder="από"
                                            value=""
                                            title="Από ημ/νία εξερχομένου">
                                    </div>

                                    <div class="col-md-3 col-sm-3" id="eosExerxDatediv">
                                        <input id="eosExerxDate"
                                            type="text"
                                            class="form-control datepicker text-center"
                                            name="eosExerxDate"
                                            placeholder="έως"
                                            value=""
                                            title="Έως ημ/νία εξερχομένου">
                                    </div>
                                </div>
                            </div>

                            {{-- Text field searches --}}
                            <div class="panel panel-default col-md-6 col-sm-6">
                                <div class="row bg-success">
                                    <div class="form-control-static h4 text-center">
                                        Τα πεδία να περιλαμβάνουν
                                    </div>
                                </div>

                                @for ($i = 1; $i <= 3; $i++)
                                    <div class="row">
                                        <div class="col-md-5 col-sm-5">
                                            <select id="searchField{{ $i }}"
                                                onchange="getFindData()"
                                                name="searchField{{ $i }}"
                                                class="form-control selectpicker"
                                                title="Αναζήτηση στο πεδίο {{ $fields[${'searchField' . $i}] ?? '' }}">

                                                @foreach ($fields as $key => $value)
                                                    <option value="{{ $key }}"
                                                        {{ $key == ${'searchField' . $i} ? 'selected' : '' }}>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-5 col-sm-5">
                                            <input id="searchData{{ $i }}"
                                                oninput="getFindData()"
                                                type="text"
                                                class="form-control"
                                                name="searchData{{ $i }}"
                                                placeholder="Κείμενο προς αναζήτηση"
                                                value=""
                                                title="Κείμενο προς αναζήτηση">
                                        </div>

                                        <div id="searchData{{ $i }}chkDiv"
                                            class="checkbox-inline col-md-2 col-sm-2"
                                            title="Επιλέξτε για αναζήτηση κενών (Null) τιμών">

                                            <label>
                                                <input id="searchData{{ $i }}chk"
                                                    onchange="getFindData()"
                                                    type="checkbox"
                                                    name="searchData{{ $i }}chk"
                                                    value="1">
                                                Κενό
                                            </label>
                                        </div>
                                    </div>
                                @endfor
                            </div>

                            {{-- Form buttons --}}
                            <div class="row">
                                <div class="col-md-2 col-sm-2 col-md-offset-4 col-sm-offset-4 form-control-static text-center">
                                    <a href="#"
                                        onclick="clearFindForm(); return false;"
                                        role="button"
                                        title="Καθάρισμα φόρμας">
                                        <img src="{{ URL::to('/') }}/images/clear.ico" height="30" alt="Καθάρισμα">
                                    </a>
                                </div>

                                <div class="col-md-2 col-sm-2 form-control-static text-center">
                                    <a href="#"
                                        onclick="getFindData(); return false;"
                                        role="button"
                                        title="Αναζήτηση">
                                        <img src="{{ URL::to('/') }}/images/find.ico" height="30" alt="Αναζήτηση">
                                    </a>
                                </div>

                                <div class="col-md-1 col-sm-1 col-md-offset-3 col-sm-offset-3 form-control-static text-right">
                                    <a href="{{ URL::to(config('landing-page.page.' . auth()->user()->role_id)) }}"
                                        role="button"
                                        title="Πρωτόκολλο">
                                        <img src="{{ URL::to('/') }}/images/protocol.png" height="30" alt="Πρωτόκολλο">
                                    </a>
                                </div>
                            </div>
                        </form>

                        <div id="showFindData"></div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var findRequest = null;

        // Validate a numeric field.
        function chkNum(id, notnull) {
            var value = $('#' + id).val().trim();
            var div = $('#' + id + 'div');

            if (notnull && !value) {
                toastr.info(
                    "<center><h4>Ενημέρωση...</h4><hr>Πληκτρολογείστε Αριθμό Πρωτοκόλλου.<br>&nbsp;</center>"
                );
                div.addClass('has-error');
                return false;
            }

            if (value && !/^\d+$/.test(value)) {
                toastr.error(
                    "<center><h4>Λάθος !!!</h4><hr>Πληκτρολογείστε μόνο Αριθμούς.<br>&nbsp;</center>"
                );
                div.addClass('has-error');
                return false;
            }

            div.removeClass('has-error');
            return true;
        }

        // Validate a four-digit year.
        function chkEtos(id, notnull) {
            var value = $('#' + id).val().trim();
            var div = $('#' + id + 'div');

            if (notnull && !value) {
                toastr.info(
                    "<center><h4>Ενημέρωση...</h4><hr>Πληκτρολογείστε Έτος.<br>&nbsp;</center>"
                );
                div.addClass('has-error');
                return false;
            }

            if (value && !/^\d{4}$/.test(value)) {
                toastr.error(
                    "<center><h4>Λάθος !!!</h4><hr>Το έτος πρέπει να είναι τετραψήφιος αριθμός με μορφή εεεε.<br>&nbsp;</center>"
                );
                div.addClass('has-error');
                return false;
            }

            div.removeClass('has-error');
            return true;
        }

        // Parse and validate a date in dd/mm/yyyy format.
        function parseGreekDate(value) {
            var match = value.match(
                /^(0[1-9]|[12]\d|3[01])\/(0[1-9]|1[0-2])\/(\d{4})$/
            );

            if (!match) {
                return null;
            }

            var day = parseInt(match[1], 10);
            var month = parseInt(match[2], 10);
            var year = parseInt(match[3], 10);

            var date = new Date(year, month - 1, day);

            // Reject impossible dates such as 31/02/2025.
            if (
                date.getFullYear() !== year ||
                date.getMonth() !== month - 1 ||
                date.getDate() !== day
            ) {
                return null;
            }

            return date.getTime();
        }

        // Validate a date field.
        function chkDate(id, notnull) {
            var value = $('#' + id).val().trim();
            var div = $('#' + id + 'div');

            if (notnull && !value) {
                toastr.info(
                    "<center><h4>Ενημέρωση...</h4><hr>Πληκτρολογείστε ημερομηνία.<br>&nbsp;</center>"
                );
                div.addClass('has-error');
                return false;
            }

            if (value && parseGreekDate(value) === null) {
                toastr.error(
                    "<center><h4>Λάθος !!!</h4><hr>Η ημερομηνία πρέπει να έχει τη μορφή ηη/μμ/εεεε και να είναι έγκυρη.<br>&nbsp;</center>"
                );
                div.addClass('has-error');
                return false;
            }

            div.removeClass('has-error');
            return true;
        }

        // Validate that a numeric range is in the correct order.
        function validateNumberRange(fromId, toId) {
            var from = $('#' + fromId).val().trim();
            var to = $('#' + toId).val().trim();

            $('#' + fromId + 'div').removeClass('has-error');
            $('#' + toId + 'div').removeClass('has-error');

            if (from && to && Number(from) > Number(to)) {
                $('#' + fromId + 'div').addClass('has-error');
                $('#' + toId + 'div').addClass('has-error');

                toastr.warning(
                    "<center><h4>Προσοχή</h4><hr>Ο αρχικός αριθμός δεν μπορεί να είναι μεγαλύτερος από τον τελικό.<br>&nbsp;</center>"
                );

                return false;
            }

            return true;
        }

        // Validate that a date range is in the correct order.
        function validateDateRange(fromId, toId) {
            var from = $('#' + fromId).val().trim();
            var to = $('#' + toId).val().trim();

            $('#' + fromId + 'div').removeClass('has-error');
            $('#' + toId + 'div').removeClass('has-error');

            if (
                from &&
                to &&
                parseGreekDate(from) !== null &&
                parseGreekDate(to) !== null &&
                parseGreekDate(from) > parseGreekDate(to)
            ) {
                $('#' + fromId + 'div').addClass('has-error');
                $('#' + toId + 'div').addClass('has-error');

                toastr.warning(
                    "<center><h4>Προσοχή</h4><hr>Η αρχική ημερομηνία δεν μπορεί να είναι μεταγενέστερη της τελικής.<br>&nbsp;</center>"
                );

                return false;
            }

            return true;
        }

        // Search for one protocol.
        function chkFindOne() {
            if (!chkNum('protocolnum', true)) {
                return false;
            }

            if (!chkEtos('etosForOne', true)) {
                return false;
            }

            var protocolnum = $('#protocolnum').val().trim();
            var etosForOne = $('#etosForOne').val().trim();

            window.location.href =
                "{{ URL::to('/') }}" +
                "/goto/" +
                encodeURIComponent(etosForOne) +
                "/" +
                encodeURIComponent(protocolnum) +
                "?find=1";

            return true;
        }

        // Clear the search form and previous results.
        function clearFindForm() {
            if (findRequest) {
                findRequest.abort();
                findRequest = null;
            }

            document.getElementById('findform').reset();

            if ($.fn.selectpicker) {
                $('#findform .selectpicker').selectpicker('render');
            }

            $('#findform .has-error').removeClass('has-error');
            $('#showFindData').empty();
        }

        // Search protocols using the form.
        function getFindData(page = 1) {

            // Update select titles.
            $('#searchField1').attr(
                'title',
                'Αναζήτηση στο πεδίο ' + $('#searchField1 :selected').text()
            );

            $('#searchField2').attr(
                'title',
                'Αναζήτηση στο πεδίο ' + $('#searchField2 :selected').text()
            );

            $('#searchField3').attr(
                'title',
                'Αναζήτηση στο πεδίο ' + $('#searchField3 :selected').text()
            );

            // Update empty-value checkbox titles.
            for (var i = 1; i <= 3; i++) {
                $('#searchData' + i + 'chkDiv').attr(
                    'title',
                    'Επιλέξτε για αναζήτηση κενών (Null) τιμών στο πεδίο ' +
                    $('#searchField' + i + ' :selected').text()
                );
            }

            var chk_ok = true;

            // Validate protocol number range.
            if (!chkNum('aponum', false)) {
                chk_ok = false;
            }

            if (!chkNum('eosnum', false)) {
                chk_ok = false;
            }

            if (!validateNumberRange('aponum', 'eosnum')) {
                chk_ok = false;
            }

            // Validate year.
            if (!chkEtos('etosForMany', false)) {
                chk_ok = false;
            }

            // Validate receipt dates.
            if (!chkDate('apoProtocolDate', false)) {
                chk_ok = false;
            }

            if (!chkDate('eosProtocolDate', false)) {
                chk_ok = false;
            }

            if (!validateDateRange('apoProtocolDate', 'eosProtocolDate')) {
                chk_ok = false;
            }

            // Validate incoming dates.
            if (!chkDate('apoEiserxDate', false)) {
                chk_ok = false;
            }

            if (!chkDate('eosEiserxDate', false)) {
                chk_ok = false;
            }

            if (!validateDateRange('apoEiserxDate', 'eosEiserxDate')) {
                chk_ok = false;
            }

            // Validate outgoing dates.
            if (!chkDate('apoExerxDate', false)) {
                chk_ok = false;
            }

            if (!chkDate('eosExerxDate', false)) {
                chk_ok = false;
            }

            if (!validateDateRange('apoExerxDate', 'eosExerxDate')) {
                chk_ok = false;
            }

            // Do not search if validation failed.
            if (!chk_ok) {
                $('#showFindData').empty();
                return false;
            }

            // Build query string.
            var querystr = '?' + $('#findform').serialize();

            if (page !== 1) {
                querystr += '&page=' + encodeURIComponent(page);
            }

            // Cancel the previous request if it is still running.
            if (findRequest) {
                findRequest.abort();
            }

            // Load results through AJAX.
            findRequest = $.ajax({
                url: "{{ URL::to('/getFindData') }}" + querystr,
                type: 'GET',
                dataType: 'html',

                success: function(response) {
                    $('#showFindData').html(response);
                },

                error: function(xhr, status) {
                    if (status !== 'abort') {
                        toastr.error(
                            'Παρουσιάστηκε σφάλμα κατά την αναζήτηση.'
                        );
                    }
                },

                complete: function() {
                    findRequest = null;
                }
            });

            return true;
        }
    </script>
@endsection
