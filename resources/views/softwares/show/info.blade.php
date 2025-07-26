 <div class="panel panel-default panel-brand">

  <div class="panel-heading">

      <h3>Task Information</h3>

  </div>  

  <div class="panel-body repair-details">

      <div class="row">

          <div class="col-md-12">

             <!--  Single Task Info -->
              <div class="single-task" style="background: #fff;margin-bottom: 20px">

                  
                  <div class="row">
                    
                      <div class="col-md-6">
                        
                          <!-- start of summary -->
                          <div class="task-info summary">

                            <div class="field field-group clearfix">

                               <label>Summary</label>

                               {{ $ticket->summary }}

                             </div>

                          </div>
                          <!-- end of summary -->

                          <!-- start of details -->
                          <div class="task-info details">

                            <div class="field field-group clearfix">

                              <label for="input_i">Type</label>

                              <?php
                                      switch($ticket->type)
                                      {
                                        case 'Feature':
                                          $class = 'text-color-1';
                                          break;
                                        case 'Request':
                                          $class = 'text-color-2';
                                          break;
                                        case 'Defect':
                                          $class = 'text-color-3';
                                          break;
                                        default:
                                          $class = 'text-color-1';
                                          break;
                                      }
                                  ?>

                              <p><strong class="<?php echo $class; ?>">{{ $ticket->type }}</strong></p>

                            </div>

                            <div class="field field-group clearfix">

                              <label for="input_pn">Product Name</label>

                              <p>{{ $ticket->product->name }}</p>

                            </div>

                            <div class="field field-group clearfix">

                              <label for="input_pn">Date Created</label>

                              <p>{{ $ticket->created_at }}</p>

                            </div>

                          </div>
                          <!-- end of summary -->                  

                      </div>

                      <div class="col-md-6">
                        

                          <div class="field field-group clearfix">

                            <label for="input_cn">Task ID #</label>

                            <p>{{ $ticket->id }}</p>

                          </div>

                           <!-- start of description -->
                          <div class="task-info desription">

                            <div class="field field-group clearfix">

                              <label for="input_sn">Description</label>

                              <p >{!! $ticket->description !!}</p>

                            </div>

                          </div>
                          
                          @if( !$ticket->resolve ) 

                               <!-- end of description -->
                              <div class="field field-group clearfix">

                                  <label for="input-pd">Status</label>

                                  <?php
                                      switch ($ticket->status) {
                                         case 'To Do':
                                           $class = 'bg-color-1';
                                           break;

                                        case 'In Progress':
                                           $class = 'bg-color-2';
                                           break;
                                           
                                        case 'Completed':
                                           $class = 'bg-color-3';
                                           break; 
                                        default:
                                           $class = '';
                                           break;
                                     }
                                  ?>

                                  <p><span class="box-bg <?php echo $class; ?>">{{ $ticket->status }}</span></p>

                              </div> 

                          @endif 
                        
                      </div>

                  </div>

             
              </div>
              <!--//  Single Task Info -->

          </div>

          <div class="col-md-12">
            
            @if( $ticket->resolve ) 

                <label for="input_sn">Resolution</label>
                <div class="resolve-section clearfix" style="padding: 20px; background: #e7fbf8;margin-bottom: 20px;border: 1px solid #d1f1ec;">

                    <p>{{ $ticket->resolution }}</p>

                </div>  

            @endif 

          </div>

          <div class="col-md-12">

             <div class="row">

                <div class="col-sm-6"> 

                   <label>Created by</label>
                  <div class="single-profile clearfix" style="display: flex;align-items: center;margin-top: 5px; padding: 20px;background: #def2fd;border: 1px solid #b9dbef;">

                  
                      <?php $photo = DB::table('user_details')->where('user_id', $ticket->user_id )->value('photo'); ?>

                      @if( $photo )

                        <div class="photo" style="padding-right: 20px;">
                          <img src="{{ asset('images/uploads/' . $photo ) }}"  width="64" />

                        </div>

                      @else

                        <div class="photo" style="padding-right: 20px;">

                          <img src="{{ asset('images/user-placeholder.png') }}" width="64" />

                        </div>

                      @endif
             
                    <p><a href="{{ url('profile') }}/{{ $ticket->user_id }}">{{ DB::table('users')->where('id', $ticket->user_id)->value('firstname') }} {{ DB::table('users')->where('id', $ticket->user_id)->value('lastname') }}</a></p>

                  </div>

                </div>

                <div class="col-sm-6">

                  <label>Assigned to</label>
                  <div class="single-profile clearfix" style="display: flex;align-items: center;margin-top: 5px;padding: 20px;background: #f3fff5;border: 1px solid #c3f1cb;">

                     

                     <?php $photo = DB::table('user_details')->where('user_id', $ticket->assigned_to )->value('photo'); ?>

                      @if( $photo )

                        <div class="photo" style="padding-right: 20px;"><img src="{{ asset('images/uploads/' . $photo ) }}"  width="64" /></div>

                      @else

                        <div class="photo" style="padding-right: 20px;"><img src="{{ asset('/images/user-placeholder.png') }}" width="64" /></div>

                      @endif

                       <p>
                        <a href="{{ url('profile') }}/{{ $ticket->assigned_to }}">{{ $ticket->assign->firstname . ' ' .  $ticket->assign->lastname }}</a><br>
                        <span>Company : {{ $ticket->assign->company }}</span>

                      </p>

                    </div>
                  
                 </div>

             </div>     

          </div>

      </div>

 </div><!-- ./panel body --> 

</div><!-- ./panel --> 

@section('css')
<style>
.field {
    padding: 20px 0;
    border-bottom: 1px solid #f7f6f6;
}
</style>

@stop