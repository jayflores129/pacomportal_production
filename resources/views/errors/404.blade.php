<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>SPG Controls</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600|Roboto:300,400,600+Mono" rel="stylesheet">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
                background: #fbfbfb;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }
            .text-center.link-to-go a {
                margin: 0 10px;
                font-weight: 600;
            }
            .text-center {
                text-align: center;
                width: 100%;
            }

            .title {
                text-align: center;
                border-bottom: 1px solid #f1f1f1;
                padding-bottom: 20px;
            }
            .links {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                margin-top: 20px;
            }
            .links > a {
                    width: 49%;
                    display: block;
                    font-size: 12px;
                    font-weight: 600;
                    letter-spacing: .1rem;
                    text-decoration: none;
            }
            .links  a:nth-child(2) {
               text-align: right;
            }
            .links > a:hover {
                color: #222;
            }
            .m-b-md {
                margin-bottom: 30px;
            }
            .login-form { 
                width: 600px;
                min-height: 400px;
                padding: 40px 30px 20px 30px;
                border: 1px solid #ddd;
                border-radius: 5px;
                background: #fff;
                -webkit-box-shadow: 0 2px 40px 0px rgb(221, 221, 221);
                box-shadow: 0 2px 40px 0px rgba(221, 221, 221, 0.8);
            }
            .login-form img {
                max-width: 200px;
            }
            .form-v label {
                margin-top: 10px;
            }
            .btn-submit {
                background-color: #337ab7;
                border-color: #2e6da4;
                color: #ffffff;
                display: block;
                font-size: 12px;
                font-weight: 600;
                line-height: 25px;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
                background: #004181;
                border-radius: 0;
                min-width: 150px;
                margin: 10px auto;
            }
            @media (max-width: 500px) {
                .login-form {
                    width: 100%;
                    max-width: 400px;
                }
                .position-ref {
                    padding: 0 15px;
                }
            }
        </style>
    </head>
    <body>

        <div class="flex-center position-ref full-height">
        

            <div class="login-form">
                <div class="title m-b-md">
                     <img src="{{ asset('images/logo.png') }}" width="100%" />
                </div>
                @if (Route::has('login'))
                    @if (Auth::check())

                        <h2 class="text-center">Sorry, this page doesn't exist</h2>

                    @else
                    	<h2 class="text-center">Sorry, this page doesn't exist</h2>
                     @endif
                @endif    

                <div class="login">
                    @if (Route::has('login'))
                        <div class="links">

                            <div class="text-center link-to-go">
                                <a href="{{ url('/home') }}">Go to Dashboard</a>
                                <a href="http://spgcontrols.com/">Go to Homepage</a>
                            </div>

                        </div>
                    @endif
                    
                    <!-- <div class="title" onClick="window.location='/oauth'">Sign in to Microsoft</div> -->

                </div>
            </div>
        </div>
    </body>
</html>
