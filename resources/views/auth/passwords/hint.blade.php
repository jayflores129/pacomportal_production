@extends('layouts.default')

@section('content')
<div class="container t-pad-m">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Password Hint</div>

                <div class="panel-body">
                    {!! Form::open(array('url' => url('send_password_hint'), 'method' => 'post')) !!}
                    
                        
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <div class="title m-b-md">
                                <a href="{{ url('/') }}"><img src="{{ asset('public/images/pacom_logo.jpg') }}" width="300" /></a>
                             </div>
                            @if( $error)
                                <p><span class="label label-<?php echo ($has_error == 1 ) ? 'danger' : 'success'; ?>">@php  echo $error;  @endphp</span></p>
                            @endif

                            <label for="email" class=" control-label">E-Mail Address</label>

                            <div class="input-area">
                                <input id="email" type="email" class="form-control" name="email" value="" required autofocus>
                                <span class="note">We will send your password hint to your email</span>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="">
                                <button type="submit" class="btn btn-primary">
                                    Send Password Hint
                                </button>
                            </div>
                        </div>
                    {!! Form::close() !!}

                    <div class="links">
                        @if (Auth::check())
                            <div class="text-center link-to-go">
                                <a href="http://spgcontrols.com/">Go to Homepage</a>
                                <a href="{{ url('/home') }}">Go to Dashboard</a>
                            </div>
                        @else
                            <a href="{{ route('password.request') }}">Forgot Your Password?</a>
                            <a href="{{ url('/registration') }}">Register</a>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection


@section('css')
<style>
    .note {
        font-size: 13px;
        line-height: 1;
        color: #e68a8a;
    }
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
    .container.t-pad-m {
        margin-top: 100px;
    }
    .form-group:after {
        content: '';
        display: block;
        clear: both;
        margin: 0 0 2px 0;
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