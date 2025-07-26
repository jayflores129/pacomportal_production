<div class="row">
   <div class="col-lg-8">
          @component('components/panel')
              @slot('title')
                Latest Tickets
              @endslot
                  
                  @component('components/table')
                        @slot('heading')
                         <th width="100">RMA #</th>
                         <th>Name</th>
                         <th>Company</th>
                         <th>Status</th>
                         <th>PO</th>
                         <th>Date Requested</th>
                         <th>Action</th>
                        @endslot

                      @if(!empty( $tickets) )
                        <?php $count = 0; ?>
                         @foreach ($tickets as $repair )

                         <?php
                            $count++;
                            switch ($repair->status) {
                                case 'Under Reviewed':
                                    $class = 'btn-default btn-open';
                                    $rstatus = 'Open';
                                    break;
                                case 'Open':
                                    $class = 'btn-default btn-open';
                                    $rstatus = 'Open';
                                    break;
                                case 'Confirmed':
                                    $class = 'btn-default btn-cs';
                                    $rstatus = 'Confirmed';
                                    break;
                                case 'Under Review':
                                    $class = 'btn-default btn-open';
                                    $rstatus = 'Open';
                                    break;
                                case 'Received':
                                    $class = 'btn-default btn-r';
                                    $rstatus = 'Received';
                                    break;
                                case 'To Be Confirmed':
                                    $class = 'btn-default btn-ps';
                                    $rstatus = 'To Be Confirmed';
                                    break;
                            
                                case 'Submitted':
                                    $class = 'btn-default btn-cs';
                                    $rstatus = 'Submitted';
                                    break;
                            
                                case 'Completed':
                                    $class = 'btn-default btn-r';
                                    $rstatus = 'Completed';
                                    break;
                                case 'Partially Shipped':
                                    $class = 'btn-default btn-rp';
                                    $rstatus = 'Shipped';
                                    break;
                                case 'Shipped':
                                    $class = 'btn-default btn-rp';
                                    $rstatus = 'Shipped';
                                    break;
                            
                                case 'Cancelled':
                                    $class = 'btn-default btn-rt';
                                    $rstatus = 'Cancelled';
                                    break;
                            
                                default:
                                    $class = '';
                                    break;
                            } ?>
                       
                         <tr>
                             <td>R{{ $repair->id }}</td>
                             <td>{{ $repair->requester_name }}</td>
                             <td>{{ $repair->company_name }}</td>
                            <td><span class="{{ $class }}">{{ $repair->status }}</span></td>
                             <td>{{ $repair->po_number  }}</td>
                             <td>{{ $repair->requested_date  }}</td>
                             <td>
                                <a href="{{ route('repairs.show', $repair->id)}}">Show Info</a>
                             </td> 
                         </tr>

                              @endforeach
                           @else
                              No logs
                           @endif
          
                  @endcomponent

          @endcomponent
      </div>
      <div class="col-lg-4">

             <div class="panel panel-default panel-brand">
              <div class="panel-heading">
                <h3>Top Products from open tickets</h3>
              </div>
                  <div class="panel-body">
                      <div id="product_chart2"></div>
                  </div>
              </div>
      </div>
</div>