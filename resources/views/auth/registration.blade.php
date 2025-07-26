@extends('layouts.default')

@section('content')
<div class="container position-ref">
    <div class="register-form">
            <div class="title m-b-md">
                <a href="{{ url('/') }}"><img src="{{ asset('images/pacom_logo.jpg') }}" width="300" /></a>
            </div>
            <h3>Registration</h3>

            @if (Route::has('login'))
                @if (Auth::check())
                    <p class="text-center">You're currently login!</p>
                @else  
                 {!! Form::open([
                 	'route' => 'reg',
                 	'class' => 'form-horizontal'
                 	 ]) !!}

                        {{ csrf_field() }}

                        @if( $errors->any() )
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->all() as $error) 
                                      <li>{{ $error }}</li>
                                     @endforeach 
                                </ul>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-sm-12">
                                @include('components/flash')
                                <div class="form-group">
                                    <label for="name" class="col-md-12 control-label">First Name</label>
                                    <div class="col-md-12">
                                        <input id="name" type="text" class="form-control" name="firstname" value="{{ old('firstname') }}" required autofocus>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="name" class="col-md-12 control-label">Last Name</label>
                                    <div class="col-md-12">
                                        <input id="name" type="text" class="form-control" name="lastname" value="{{ old('lastname') }}" required autofocus>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="password" class="col-md-12 control-label">Password</label>
                                    <div class="col-md-12">
                                        <input id="password" type="password" class="form-control" name="password" required>
                                    </div>
                                </div>                                
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="password-confirm" class="col-md-12 control-label">Confirm Password</label>
                                    <div class="col-md-12">
                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                                    </div>
                                </div>                                
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <strong>Note</strong>
                                        <ul style="list-style: none;padding: 0;">
                                            <li>Password must contain at least 6 characters.</li>   
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="password-confirm" class="col-md-12 control-label">Password Hint :</label>
                                    <div class="col-md-12">
                                        <input id="password-hint" type="text" class="form-control" name="password_hint" required>
                                    </div>
                                </div>                                
                            </div>
                        </div>

                        <hr />
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="email" class="col-md-12 control-label">E-Mail Address</label>
                                    <div class="col-md-12">
                                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                                    </div>
                                </div>                                
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="text-c" class="col-md-12 control-label">Company</label>
                                    <div class="col-md-12">
                                        <input id="text-c" type="text" class="form-control" name="company" value="{{ old('company') }}" required>
                                    </div>
                                </div>   
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="text-c" class="col-md-12 control-label">Country</label>
                                    <div class="col-md-12">
                                        <select type="text" name="country" class="form-control">
                                            <option value="">Select</option>
                                            @if( old('country') ) 
                                                <option value="{{old('country') }}" selected>{{ old('country') }}</option>
                                                @include('components/countries')
                                            @else
                                                @include('components/countries')
                                            @endif
                                            
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                              <div class="form-group">
                                    <label for="text-c" class="col-md-12 control-label">Phone Number</label>
                                    <div class="col-md-12">
                                        <input id="text-c" type="text" class="form-control" name="phone" value="{{ old('phone') }}" required>
                                    </div>
                                </div>  
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <input type="checkbox" name="subscribe" checked="checked"/> <span>Send me latest software/firmware updates</span>
                            </div>
                        </div>  
                        <div class="form-group">
                            <div class="col-md-12">
                                <input type="checkbox" name="gdpr_agreed" checked="checked"/> <span>I have read and accept the I agree <a href="{{ url('/privacy-policy') }}" target="_blank">privacy policy</a></span>
                            </div>
                        </div> 

                        <div class="form-group">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary btn-submit">
                                    Register
                                </button>
                            </div>
                        </div>

                    {!! Form::close() !!}

                 @endif
            @endif
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