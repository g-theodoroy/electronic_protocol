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
        <link rel="icon" href="{{ URL::asset('favicon.ico') }}" type="image/x-icon" />
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
                <div class="{{ App\Config::getConfigValueOf('wideListProtocol') ? 'container-fluid' : 'container'}}">
                    <div class="navbar-header">

                        <!-- Collapsed Hamburger -->
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                            <span class="sr-only">Toggle Navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>

                        <!-- Branding Image -->
                        <a class="navbar-brand" href="{{ url('/home/list') }}">
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
                                          <li><a  tabindex="-1" href="{{ url('/home') }}">Νέο</a></li>
                                          @if( Auth::user()->role_description() != "Αναγνώστης")
                                          @if(isset($defaultImapEmail))
                                          @if( ! $allowedEmailUsers || strpos($allowedEmailUsers,Auth::user()->username) !== false) 
                                          <li class="divider"></li>
                                          <li><a  tabindex="-1" href="{{ url('/viewEmails') }}">Εισερχόμενα Email</a></li>
                                          @endif
                                          @endif
                                          @endif
                                            <li class="divider"></li>
                                            <li><a  tabindex="-1" href="{{ url('/find') }}">Αναζήτηση</a></li>
                                            <li class="dropdown-submenu">
                                            <a class="test" tabindex="-1" href="#">Εκτύπωση</a>
                                                <ul class="dropdown-menu">
                                                    <li><a  tabindex="-1" href="{{ url('/print') }}">Πρωτόκολλο</a></li>
                                                    <li><a  tabindex="-1" href="{{ url('/printAttachments') }}">Συνημμένα</a></li>
                                                </ul>
                                            </li>
                                            <li><a  tabindex="-1" href="{{ url('/keep') }}">Διατήρηση</a></li>
                                            @if(in_array ( Auth::user()->role_description(), [ "Διαχειριστής",  "Αναθέτων"]))
                                              <li class="divider"></li>
                                              <li class="dropdown-submenu"><a  tabindex="-1" href="{{ url('/home/list') }}">Πρωτόκολλο</a>
                                                <ul class="dropdown-menu">
                                                  @foreach($myActiveUsers as $myAU)
                                                    <li><a  tabindex="-1" href="{{ url('/home/list/a/') }}/{{$myAU->id}}">{{$myAU->name}}</a></li>
                                                    @endforeach
                                                </ul>
                                              </li>
                                              <li class="dropdown-submenu"><a  tabindex="-1" href="{{ url('/home/list/d') }}">προς Διεκπ/ση</a>
                                                <ul class="dropdown-menu">
                                                  <li><a  tabindex="-1" href="{{ url('/home/list/d/a') }}">Όλοι οι χρήστες</a></li>
                                                  <li class="divider"></li>
                                                  @foreach($myActiveUsers as $myAU)
                                                    <li><a  tabindex="-1" href="{{ url('/home/list/d/') }}/{{$myAU->id}}">{{$myAU->name}}</a></li>
                                                    @endforeach
                                                </ul>
                                              </li>
                                              <li class="dropdown-submenu"><a  tabindex="-1" href="{{ url('/home/list/f') }}">Διεκπεραιώθηκε</a>
                                              <ul class="dropdown-menu">
                                                <li><a  tabindex="-1" href="{{ url('/home/list/f/a') }}">Όλοι οι χρήστες</a></li>
                                                <li class="divider"></li>
                                                  @foreach($myActiveUsers as $myAU)
                                                    <li><a  tabindex="-1" href="{{ url('/home/list/f/') }}/{{$myAU->id}}">{{$myAU->name}}</a></li>
                                                    @endforeach
                                                </ul>
                                              </li>
                                            @endif
                                              <li class="divider"></li>
                                              <li><a  tabindex="-1" href="{{ url('/home/list/d') }}"> προς Διεκπ/ση</a></li>
                                              <li><a  tabindex="-1" href="{{ url('/home/list/f') }}">Διεκπεραιώθηκε</a></li>

                                        </ul>
                                    </li>

                                    @if(Auth::user()->role_description() == "Διαχειριστής")
                                    <li class="dropdown-submenu">
                                        <a class="test" tabindex="-1" href="#">Διαχείριση</a>
                                        <ul class="dropdown-menu">
                                            <li><a  tabindex="-1" href="{{ url('/users') }}">Χρήστες</a></li>
                                            <li><a  tabindex="-1" href="{{ url('/backups') }}">Backup</a></li>
                                            <li><a  tabindex="-1" href="{{ url('/arxeio') }}">Εκκαθάριση</a></li>
                                            <li><a  tabindex="-1" href="{{ url('/settings') }}">Ρυθμίσεις</a></li>
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
            toastr.options = {
              "closeButton": true,
              "debug": false,
              "newestOnTop": false,
              "progressBar": true,
              "positionClass": "toast-top-center",
              "preventDuplicates": true,
              "onclick": null,
              "showDuration": "300",
              "hideDuration": "700",
              "timeOut": "4000",
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

            @if($errors->any())
                toastr.error("<center><h4>Λάθος !!!</h4></center><hr><ul>@foreach($errors->all() as $error)<li>{!! $error !!}</li>@endforeach</ul><br> &nbsp;");
            @endif
        </script>

    </body>
</html>
