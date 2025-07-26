@extends('layouts.default')

@section('content')
<div class="container position-ref">
    <div class="register-form">
            <div class="title m-b-md">
                <img src="{{ asset('public/images/pacom_logo.jpg') }}" width="100%" />
                <h3 style="margin-top:20px;">Reset Password</h3>
            </div>
            @if (Route::has('login'))
                @if (Auth::check())
                    <p class="text-center">You're currently login!</p>
                @else
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form class="form-horizontal" method="POST" action="{{ route('password.email') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-12 control-label">E-Mail Address</label>

                            <div class="col-md-12">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary btn-submit">
                                    Send Password Reset Link
                                </button>
                            </div>
                        </div>
                    </form>
                     @endif
                @endif   
</div>
@endsection




@section('css')
<style>
    .navbar-default {

        display: none;
    }
    .position-ref {
        position: relative;
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
    .register-form { 
        width: 400px;
        min-height: 300px;
        padding: 40px 30px 20px 30px;
        margin: 150px auto;
        border: 1px solid #ddd;
        border-radius: 5px;
        background: #fff;
        -webkit-box-shadow: 0 2px 40px 0px rgb(221, 221, 221);
        box-shadow: 0 2px 40px 0px rgba(221, 221, 221, 0.8);
    }
    .register-form img {
        max-width: 200px;
    }
    .form-v label {
        margin-top: 10px;
        text-align: left;
    }
    .form-horizontal .control-label {
        text-align: left;
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
        .register-form {
            width: 100%;
            max-width: 400px;
        }
        .position-ref {
            padding: 0 15px;
        }
    }

</style>
@stop