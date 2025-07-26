<div class="row">
   <div class="col-lg-8">
          @component('components/panel')
              @slot('title')
                Latest Tasks
              @endslot
                  
                  @component('components/table')
                        @slot('heading')
                    
                           <th width="240">Assigned To</th>
                           <th width="300">Type</th>
                           <th width="100">Product</th>
                           <th width="200">Status</th>
                           <th width="100">Updated</th>
                           <th width="100">Action</th>
                        @endslot

                       @if($tasks->count() > 0)

                         @foreach($tasks as $task) 
                            <tr class="single-task">
                              <td>
                                <div class="task-owner">
                                   <div class="img">
                                      <?php $photo = DB::table('user_details')->where('user_id', $task->assigned_to )->value('photo'); ?>
                                      @if( $photo )
                                        <div class="photo">
                                          <img src="{{ asset('public/images/uploads/' . $photo ) }}"  width="100%" />
                                        </div>
                                      @else
                                        <div class="photo">
                                          <img src="{{ asset('public/images/user-placeholder.png') }}" width="100%" />
                                        </div>
                                      @endif


                                   </div>
                                   <div class="info">
                                     <strong>{{ DB::table('users')->where('id', $task->assigned_to)->value('firstname') .' '. DB::table('users')->where('id', $task->assigned_to)->value('lastname') }}</strong>
                                     <span>Total Tasks : <span>{{ DB::table('software_tickets')->where('assigned_to', $task->assigned_to )->count() }}</span></span>
                                   </div>
                               </div>
                              </td>    
                              <td>
                                 <div class="task-info">
                                    <?php

                                        switch($task->type)
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
                                    <p class="{{ $class }}"><strong>{{ $task->type }}</strong></p>
                                    <span>{{ $task->summary }}</span>
                                 </div>
                              </td>
                              <td>
                                {{ $task->product->name }}
                              </td>
                              <td>
                                 <div class="task-status">
                                   <?php
                                        switch ($task->status) {
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
                                    <span class="{{ $class }} box-bg">{{ $task->status }}</span>
                                 </div>
                              </td>
                              <td>
                                {{ $task->updated_at }}
                              </td>
                              <td>
                                 <div class="task-option">
                                   <ul class="list-inline">
                                     <li><a href="{{ url('/admin/softwares/'. $task->id) }}"><span>View Task</span></a></li>
                                   </ul>
                                </div>
                              </td>
                           </tr>   
                        @endforeach
                      @else
                          <tr><td colspan="7">No Data</td></tr>
                      @endif  
                  @endcomponent

          @endcomponent
      </div>
      <div class="col-lg-4">

             <div class="panel panel-default panel-brand">
              <div class="panel-heading">
                <h3>Status Chart</h3>
              </div>
                  <div class="panel-body">
                      <div id="topTasks"></div>
                  </div>
              </div>
      </div>
</div>

