@extends('layouts.default')

@section('content')

<div class="container position-ref">

    <div class="register-form" style="max-width: 900px;">

        <div class="title m-b-md">
             <img src="{{ asset('/public/images/logo.png') }}" width="100%" />
        </div>

        <h3>Privacy Policy</h3>

        <div style="padding: 30px;">
            <div style="min-height: 500px;;">
                {{ $GDPR }}
            </div>
        </div>

    </div>

</div>
@endsection

@section('css')
<style>
    .row {
        margin: 0 -10px;
    }
    .row > div {
        padding: 0 10px;
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
        padding: 20px;
        background: #f5f5f5;
    }
    .register-form h3 {
        margin: 15px 0;
        padding: 10px;
        text-transform: uppercase;
        text-align: center;
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
        max-width: 600px;
        min-height: 300px;
        margin: 150px auto;
        border-radius: 5px;
        background: #fff;
        -webkit-box-shadow: 0 2px 40px 0px rgb(221, 221, 221);
        box-shadow: 0 2px 40px 0px rgba(221, 221, 221, 0.8);
    }
    .register-form form {
        padding: 15px 20px;
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