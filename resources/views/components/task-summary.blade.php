   @if(Auth::user()->isAdmin())

    <div class="log-section">
      <div class="row">
        <div class="col-md-12">
            @component('components/panel')
                @slot('title')
                   Software Features / Issues
                @endslot
                    
                    @component('components/table')
                          @slot('heading')
                            <th width="200">Time</th>
                            <th>Type</th>
                            <th width="500">Summary</th>
                            <th>Status</th>
                            <th>Assignee</th>
                            <th>Action</th>
                          @endslot

                        @if(!empty( $repair_logs) )
                            @foreach($repair_logs as $log)
                              <tr>
                                <td>{{ date(' d-m-Y H:i:s', strtotime($log->created_at)) }}</td>
                                <td>
                                  <?php

                                    switch($log->type)
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
                                  <strong><span class="{{ $class }}">{{ $log->type }}</span></strong>
                                </td>
                                <td>{{ $log->summary }}</td>
                                <td>
                                  <?php
                                    switch ($log->status) {
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
                                <span class="{{ $class }} box-bg">{{ $log->status }}</span>
                                </td>
                                <td>{!! $log->assign->firstname !!} {!! $log->assign->lastname !!}</td>
                                <td><a href="{!! url('/repairs/') !!}/{{  $log->repair_id }}">View</a></td>
                              </tr>
                            @endforeach
                         @else
                            No logs
                         @endif
            
                    @endcomponent

            @endcomponent
        </div>
      </div>
    </div>
   @endif