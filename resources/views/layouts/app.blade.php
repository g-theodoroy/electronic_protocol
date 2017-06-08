<!DOCTYPE html>
<html lang="el">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Ηλ. Πρωτόκολλο') }}</title>

        <!-- Styles -->
    <!--    <link href="{{ asset('css/app.css') }}" rel="stylesheet"> -->
        <link href="{{ asset('css/jasny-bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/bootstrap-select.min.css') }}" rel="stylesheet">
        <style>


            .dropdown-submenu {
                position: relative;
            }

            .dropdown-submenu>.dropdown-menu {
                top: 5px;
                left: auto;
                right: 99%;
                margin-top: -6px;
                margin-left: -1px;
                -webkit-border-radius: 0 6px 6px 6px;
                -moz-border-radius: 0 6px 6px;
                border-radius: 0 6px 6px 6px;
            }

            .dropdown-submenu:hover>.dropdown-menu {
                display: block;
            }

            .dropdown-submenu>a:after {

                position: absolute; 
                left: 7px; 
                top: 3px;  

                display: block;
                content: " ";
                float: right;
                width: 0;
                height: 0;
                border-color: transparent;
                border-style: solid;
                border-width: 5px 5px 5px 0 ;
                border-right-color: #ccc;
                margin-top: 5px;
                margin-right: -10px;
            }

            .dropdown-submenu:hover>a:after {
                border-left-color: #fff;
            }

            .dropdown-submenu.pull-left {
                float: none;
            }

            .dropdown-submenu.pull-left>.dropdown-menu {
                left: -100%;
                margin-left: 10px;
                -webkit-border-radius: 6px 0 6px 6px;
                -moz-border-radius: 6px 0 6px 6px;
                border-radius: 6px 0 6px 6px;
            }

        </style>


        <!-- Scripts -->
        <script>
            window.Laravel = <?php echo json_encode(['csrfToken' => csrf_token(),]);?>
        </script>

   </head>
    <body>
        <div id="app">
            <nav class="navbar navbar-default navbar-static-top">
                <div class="container">
                    <div class="navbar-header">

                        <!-- Collapsed Hamburger -->
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                            <span class="sr-only">Toggle Navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>

                        <!-- Branding Image -->
                        <a class="navbar-brand" href="{{ url('/') }}">
                            {{ config('app.name', 'Ηλ. Πρωτόκολλο') }}&nbsp;&nbsp;&nbsp;{{isset($ipiresiasName)?$ipiresiasName:''}}
                        </a>
                    </div>

                    <div class="collapse navbar-collapse" id="app-navbar-collapse">
                        <!-- Left Side Of Navbar -->
                        <ul class="nav navbar-nav">
                            &nbsp;
                        </ul>

                        <!-- Right Side Of Navbar -->
                        <ul class="nav navbar-nav navbar-right">


                            <!-- Authentication Links -->
                            @if (Auth::guest())
                            <li><a href="{{ url('/login') }}">Είσοδος</a></li>
                            @if (isset($allowregister) and $allowregister)
                            <li><a href="{{ url('/register') }}">Εγγραφή</a></li>
                            @endif
                            @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->name }}&nbsp;&nbsp;<b>{{ Auth::user()->role_description() }}</b>&nbsp;&nbsp;<span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                  
                                    <li class="dropdown-submenu">
                                        <a class="test" tabindex="-1" href="{{ url('/home/list') }}">Πρωτόκολλο</a>
                                        <ul class="dropdown-menu">
                                            <li><a  tabindex="-1" href="{{ url('/home') }}">Εισαγωγή</a></li>
                                            <li><a  tabindex="-1" href="{{ url('/find') }}">Αναζήτηση</a></li>
                                            <li><a  tabindex="-1" href="{{ url('/print') }}">Εκτύπωση</a></li>
                                            <li><a  tabindex="-1" href="{{ url('/keep') }}">Διατήρηση</a></li>
                                        </ul>
                                    </li>

                                    @if(Auth::user()->role_description() == "Διαχειριστής")
                                    <li class="dropdown-submenu">
                                        <a class="test" tabindex="-1" href="#">Διαχείριση</a>
                                        <ul class="dropdown-menu">
                                            <li><a  tabindex="-1" href="{{ url('/users') }}">Χρήστες</a></li>
                                            <li><a  tabindex="-1" href="{{ url('/backups') }}">Backup</a></li>
                                            <li><a  tabindex="-1" href="{{ url('/arxeio') }}">Εκκαθάριση</a></li>
                                            <li><a  tabindex="-1" href="{{ url('/config') }}">Ρυθμίσεις</a></li>
                                            @if(isset($needsUpdate) and $needsUpdate)
                                            <li class="divider"></li>
                                            <li><a  tabindex="-1" href="{{ url('/updated') }}" title="Να μην εμφανίζεται το μήνυμα για ενημέρωση του Ηλ.Πρωτοκόλλου.">Ενημερώθηκε</a></li>
                                            @endif
                                        </ul>
                                    </li>
                                    @endif

                                    <li>
                                        <a href="{{ url('/about') }}">
                                            Περί...
                                        </a>
                                    </li>

                                    <li role="separator" class="divider"></li>

                                    <li>
                                        <a href="{{ url('/logout') }}"
                                           onclick="event.preventDefault();
                                                   document.getElementById('logout-form').submit();">
                                            Έξοδος
                                        </a>

                                        <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                    
                                </ul>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </nav>

            @yield('content')
        </div>

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}"></script> 
        <script src="{{ asset('js/npm.js') }}"></script> 
        <script src="{{ asset('js/fileinput.js') }}"></script>
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap-datepicker.js') }}"></script>
        <script src="{{ asset('js/toastr.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap-select.min.js') }}"></script>


        <script>  

        $('.datepicker').datepicker({  

           format: 'dd/mm/yyyy'  

         });  

       </script>  

        <script>

            toastr.options = {
              "closeButton": true,
              "debug": false,
              "newestOnTop": false,
              "progressBar": false,
              "positionClass": "toast-top-center",
              "preventDuplicates": false,
              "onclick": null,
              "showDuration": "300",
              "hideDuration": "1000",
              "timeOut": "5000",
              "extendedTimeOut": "1000",
              "showEasing": "swing",
              "hideEasing": "linear",
              "showMethod": "fadeIn",
              "hideMethod": "fadeOut"
            }

           @if(Session::has('notification'))
            var type = "{{ Session::get('notification.alert-type', 'info') }}";
            switch(type){
                case 'info':
                    toastr.info("<center><h4>Ενημέρωση...</h4></center><hr>{!! Session::get('notification.message') !!}<br>&nbsp;");
                    break;
                case 'warning':
                    toastr.warning("<center><h4>Προσοχή !!</h4></center><hr>{!! Session::get('notification.message') !!}<br>&nbsp;");
                    break;
                case 'success':
                    toastr.success("<center><h4>Ωραία !</h4></center><hr>{!! Session::get('notification.message') !!}<br>&nbsp;");
                    break;
                case 'error':
                    toastr.error("<center><h4>Λάθος !!!</h4><hr></center>{!! Session::get('notification.message') !!}<br>&nbsp;");
                    break;
            }
            @endif

            @if($errors->any() and  ! $errors->has('in_num'))
                toastr.error("<center><h4>Λάθος !!!</h4></center><hr>{!! $errors->first() !!}<br>&nbsp;");
            @endif

            @if($errors->has('in_num'))
            var html = "<center><button type='button' id='confirmRevertYes' class='btn btn-primary'>Ναί</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' id='confirmRevertNo' class='btn btn-primary'>Όχι</button></center></p>"
            var msg = '{!! $errors->first() !!}'
            
            toastr.options = {
              "closeButton": true,
              "debug": false,
              "newestOnTop": false,
              "progressBar": false,
              "positionClass": "toast-top-center",
              "preventDuplicates": false,
              "onclick": null,
              "showDuration": "0",
              "hideDuration": "0",
              "timeOut": "0",
              "extendedTimeOut": "0",
              "showEasing": "swing",
              "hideEasing": "linear",
              "showMethod": "fadeIn",
              "hideMethod": "fadeOut"
            }
            var $toast = toastr.info(html,msg);
            $toast.delegate('#confirmRevertYes', 'click', function () {
                    $('#in_chk').attr('value', '0')
                    $('#myProtocolForm').submit()
                    $toast.remove();
            });
            $toast.delegate('#confirmRevertNo', 'click', function () {
                    $('#in_chk').attr('value', '1')
                    $toast.remove();
            });
            @endif
        </script>

    </body>
</html>
