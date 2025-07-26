@component('components/table')
      @slot('heading')
         <th width="100" style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd;">Task #</th>
         <th width="240" style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd;">Created By</th>
         <th style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd;">Type</th>
         <th style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd;">Product</th>
         <th width="240" style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd;">Assigned To</th>
         <th width="200" style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd;">Status</th>
         <th width="200" style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd;">Date</th>
         <th style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd;">Action</th>
      @endslot

     @if($tickets->total() > 0)

       @foreach($tickets as $ticket) 
          <tr class="single-task">
            <td>
               <div class="task-info">
                    <strong>{{ $ticket->id }}</strong>
                </div>
            </td>
            <td>
              <div class="task-owner">
                 <div class="img">
                    <?php $photo = DB::table('user_details')->where('user_id', $ticket->user_id )->value('photo'); ?>
                    @if( $photo )
                      <div class="photo">
                        <img src="{{ asset('public/images/uploads/' . $photo ) }}"  width="100%" />
                      </div>
                    @else
                      <div class="photo">
                        <img src="{{ asset('public/images//user-placeholder.png') }}" width="100%" />
                      </div>
                    @endif

                 </div>
                 <div class="info">
                   <strong>{{ DB::table('users')->where('id', $ticket->user_id)->value('firstname') .' '. DB::table('users')->where('id', $ticket->user_id)->value('lastname') }}</strong>
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
              <div class="task-owner">
                 <div class="img">
                    <?php $photo = DB::table('user_details')->where('user_id', $ticket->assigned_to )->value('photo'); ?>
                    @if( $photo )
                      <div class="photo">
                        <img src="{{ asset('public/images/uploads/' . $photo ) }}"  width="100%" />
                      </div>
                    @else
                      <div class="photo">
                        <img src="{{ asset('public/images//user-placeholder.png') }}" width="100%" />
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
                      case 'Resolved':
                           $class = 'bg-color-6';
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

