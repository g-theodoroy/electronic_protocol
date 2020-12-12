<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Ηλ. Πρωτόκολλο</title>

        <!-- Styles -->
        <style>
            html, body {
                background-image:url('images/books.jpg');
                background-repeat: no-repeat;
                background-position: center;
                background-size:cover;
                color: #FCF4D9;
                font-family: 'Noto', serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
                text-shadow: 2px 2px green;
            }

            .full-height {
                height: 80vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 2vmax;
                top: 2vmax;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 6vmax;
            }

            .small-title {
                font-size: 3vmax;
            }

            .links > a {
                color: #FCF4D9;
                padding: 0 1vmax;
                font-size: 1.5vmax;
                font-weight: 00;
                letter-spacing: 2px;
                text-decoration: none;
                //text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 2vh;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height full">
            @if (Route::has('login'))
                <div class="top-right links">
                    <a href="{{ url('/login') }}">Είσοδος</a>
                    @if ($allowregister)
                    <a href="{{ url('/register') }}">Εγγραφή</a>
                    @endif
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    Ηλ.Πρωτόκολλο
                </div>
                <div class="small-title m-b-md">
                    GΘ &copy; Laravel
                </div>

            </div>
        </div>
    </body>
</html>
