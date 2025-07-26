@extends('layouts.app')

@section('content')
    @if( Auth::user()->isAdmin() )
      <div class="panel panel-top">
          <div class="row">
              <div class="col-sm-6">
                {!! Breadcrumbs::render('settings') !!}
              </div>
              <div class="col-sm-6 text-right">
              
              </div>
          </div>
      </div> 
    @endif
    @include('components/flash')
    <div class="panel panel-default panel-brand"> 
      <div class="panel-heading"><h3>Email Settings</h3></div>  
      <div class="panel-body">  
          
            <div class="single-task-tab">
                <ul class="nav nav-tabs"> 
                  <li class="active"><a data-toggle="tab" href="#general">General</a></li>
                   <li><a data-toggle="tab" href="#ticket">Ticket</a></li>
                  <li><a data-toggle="tab" href="#task">Task</a></li>
                  <li><a data-toggle="tab" href="#taskActivity">Registration</a></li>
                </ul>
            </div>

            <div class="single-task-content tab-content" style="margin-bottom: 20px;padding: 20px;">

              <div id="general" class="tab-pane fade in active">

                    <div class="row">
                       <div class="col-sm-6">
                             <h4>General</h4>
                             @include('/settings/emails/general')
                       </div>
                       <div class="col-sm-6">
                          <h4>Latest File Updates</h4> 
                          @include('/settings/emails/file/new')
                       </div>
                    </div>

              </div>


              <div id="ticket" class="tab-pane fade in">

                    <h4>Ticket</h4> 
                    <div class="row">
                       <div class="col-sm-6">
                            @include('/settings/emails/ticket/admin')
                       </div>
                       <div class="col-sm-6">
                            @include('/settings/emails/ticket/customer')
                       </div>
                    </div>

              </div>
              <div id="task" class="tab-pane fade in">

                      <h4>Task</h4> 
                      <div class="row">
                         <div class="col-sm-6">
                              @include('/settings/emails/task/admin')
                         </div>
                         <div class="col-sm-6">
                              @include('/settings/emails/task/customer')
                         </div>
                         <div class="col-sm-6">
                              @include('/settings/emails/task/resolve')
                         </div>
                         <div class="col-sm-6">
                              @include('/settings/emails/task/comment')
                         </div>
                         {{-- <div class="col-sm-6">
                              @include('/settings/emails/task/status')
                         </div> --}}
                         <div class="col-sm-6">
                              @include('/settings/emails/task/attachment')
                         </div>
                      </div>

              </div>


              <div id="taskActivity" class="tab-pane fade in">  

                    <h4>User Registration</h4> 
                    <div class="row">
                       <div class="col-sm-6">
                            @include('/settings/emails/registration/admin')
                       </div>
                       <div class="col-sm-6">
                          @include('/settings/emails/registration/customer')
                       </div>
                    </div>

              </div>

            </div> 
           

      </div>
    </div>
@endsection


@section('css')
<style>
  h4 {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px dashed #efeaea;
  }
  .setting-section {
    margin-bottom: 30px;
    padding: 0 20px 10px 20px;
    border: 1px solid #ddd;
    background: #f5f4f4;
  }
  .setting-section h5 {
    margin-bottom: 0;
    margin-top: 20px;
  }
  .setting-section .label {
    color: #b9b7b7;
    margin-bottom: 4px;
    display: block;
    font-size: 13px;
    text-align: left;
    padding: 10px 0 5px 0;
  }
  .setting-section input,
  .setting-section textarea {
    padding: 10px;
    margin-bottom: 20px;
    background-color: #fff;
  }
  .setting-section input[type='submit'],
   button {
    margin-bottom: 30px;
  }
  .task-section.setting-section h5 {
    padding: 16px 0px;
    color: #064684;
    margin: 0 0 15px 0;
    font-size: 16px;
    border-bottom: 1px solid #ddd;
   }
</style>
@stop