@extends('layouts.app')

@section('content')
<div class="panel panel-default panel-brand">
    <div class="panel-heading">
      <h3>Serial Numbers</h3> 
    </div>
    <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <h2>Here are the serial number examples</h2>
                </div>
                <div class="col-md-4">
                    <div class="image-1"><img src="{{ asset('public/images//rma-ex1.png') }}" width="100%" /></div> 
                </div>
                <div class="col-md-4">
                    <div class="image-2"><img src="{{ asset('public/images//rma-ex2.png') }}" width="100%" /></div> 
                </div>
            </div>
    </div>
</div>    
@endsection