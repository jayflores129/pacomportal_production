@component('components/table')
      @slot('heading')
         <th width="100">Task #</th>
         <th width="380">Assigned To</th>
         <th>Type</th>
         <th>Product</th>
         
         <th width="200">Status</th>
         <th width="200">Date</th>
         <th>Action</th>
      @endslot

     @if($softwares)

       @foreach($softwares as $ticket) 
          <tr class="single-task">
            <td>
               <div class="task-info">
                    <strong>{{ $ticket->id }}</strong>
                </div>
            </td> 
            <td>
              <div class="task-owner">
                 <div class="img">
                    <?php $photo = DB::table('user_details')->where('user_id', $ticket->assigned_to )->value('photo'); ?>
                    @if( $photo )
                      <div class="photo">
                        <img src="{{ asset('public/images/uploads/' . $photo ) }}"  width="100%" />
                      </div>
                    @else
                      <div class="photo">
                        <img src="{{ asset('/public/images//user-placeholder.png') }}" width="100%" />
                      </div>
                    @endif

                 </div>
                 <div class="info">
                   <strong>{{ DB::table('users')->where('id', $ticket->assigned_to)->value('firstname') .' '. DB::table('users')->where('id', $ticket->assigned_to)->value('lastname') }}</strong>
                   <span>Total Tasks : <span>{{ DB::table('software_tickets')->where('assigned_to', $ticket->assigned_to )->count() }}</span></span>
                 </div>
             </div>
            </td>
             <td>
               <div class="task-info">
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
                  <p class="{{ $class }}"><strong>{{ $ticket->type }}</strong></p>
                  <span>{{ $ticket->summary }}</span>
               </div>
            </td>
            <td>
               <div class="task-product">
                 {{  $ticket->product->name }}
               </div>
            </td>  
            <td>
               <div class="task-status">
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
                  <span class="{{ $class }} box-bg">{{ $ticket->status }}</span>
               </div>
            </td>
            <td>
              {{ $ticket->created_at }}
            </td>
            <td>
               <div class="task-option">
                 <ul class="list-inline">
                   <li><a href="{{ url('/admin/softwares/'. $ticket->id) }}" class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-eye"></i><span>View Task</span></a></li>
                 </ul>
              </div>
            </td>
         </tr>   
      @endforeach
    @else
        <tr><td colspan="7">No Data</td></tr>
    @endif  

@endcomponent

