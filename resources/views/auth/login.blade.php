<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Pacom</title>

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
                width: 400px;
                min-height: 300px;
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
                   <img src="{{ asset('public/images/pacom_logo.jpg') }}" width="100%" />
                </div>
                @if (Route::has('login'))
                    @if (Auth::check())

                        <p class="text-center">You're currently login!</p>

                    @else

                        <form class="form-v row" method="POST" action="{{ route('login') }}">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-12 control-label">E-Mail Address</label>
                                <div class="col-md-12">
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="password" class="col-md-12 control-label">Password</label>

                                <div class="col-md-12">
                                    <input id="password" type="password" class="form-control" name="password" required>

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <button type="submit" class="btn-submit btn btn-primary">
                                        Login
                                    </button>
                                </div>
                            </div>
                        </form>

                     @endif
                @endif    

                <div class="login">
                    @if (Route::has('login'))
                        <div class="links">

                            @if (Auth::check())
                                <div class="text-center link-to-go">
                                    <a href="http://spgcontrols.com/">Go to Homepage</a>
                                    <a href="{{ url('/home') }}">Go to Dashboard</a>
                                </div>
                            @else
                                <a class="btn btn-link" href="{{ route('password.request') }}">Forgot Your Password?</a>
                                <a class="btn btn-link" href="{{ url('/registration') }}">Register</a>
                            @endif
                        </div>
                    @endif
                    
                    <!-- <div class="title" onClick="window.location='/oauth'">Sign in to Microsoft</div> -->

                </div>
            </div>
        </div>
    </body>
</html>
