 @if( $user->status > 0)

       @if($repairs) 

          <div class="repair-log">

              <div class="table-responsive"> 

               <table class="table table-striped table-default-brand">

                  <thead>

                   <tr >

                     <th>Ticket Number</th>
                     <th>Company</th>
                     <th>Product</th>
                     <th>Issue</th>
                     <th>Status</th>
                     <th>Under Warranty</th>
                     <th>Date Added</th>
                     <th>Action</th>

                 </tr>  

                </thead>

                <tbody class="repair-log">

                  <?php $count = 0; ?>

                  @foreach ($repairs as $repair )

                        <?php 

                          $count++;
                           if($repair->under_warranty) {
                               $is_w = 'Yes' ;
                           }
                           else {
                               $is_w = '';
                           }
                           $class = '';

                           switch ($repair->status) {
                             case 'open':
                               $class = 'btn-default btn-open';
                               break;

                            case 'Partially Shipped':
                               $class = 'btn-default btn-ps';
                               break;
                               
                            case 'Completely Shipped':
                               $class = 'btn-default btn-cs';
                               break; 

                              case 'received':
                               $class = 'btn-default btn-r';
                               break;
                               
                              case 'repaired':
                               $class = 'btn-default btn-rp';
                               break;  

                              case 'returned':
                               $class = 'btn-default btn-rt';
                               break;  
                             
                             default:
                               $class = '';
                               break;
                           }
                       ?> 
                          <tr>

                             <td>{{ $repair->id }}</td>

                             <td>{{ $repair->company }}</td>

                             <td>{{ $repair->product  }}</td>

                             <td>{{ $repair->issue }}</td>
    
                              <?php 
            
                               $class = '';

                               switch ($repair->status) {
                                 case 'open':
                                   $class = 'btn-default btn-open';
                                   break;

                                case 'Partially Shipped':
                                   $class = 'btn-default btn-ps';
                                   break;
                                   
                                case 'Completely Shipped':
                                   $class = 'btn-default btn-cs';
                                   break; 

                                  case 'received':
                                   $class = 'btn-default btn-r';
                                   break;
                                   
                                  case 'repaired':
                                   $class = 'btn-default btn-rp';
                                   break;  

                                  case 'returned':
                                   $class = 'btn-default btn-rt';
                                   break;  
                                 
                                 default:
                                   $class = '';
                                   break;
                                 } 

                              ?>

                             <td><span class="{{ $class }}">{{ $repair->status }}</span></td>

                             <td>{{ $is_w }}</td>

                             <td>{{ date('F d, Y', strtotime($repair->created_at))  }}</td>

                             <td><a href="{{ route('repairs.show', $repair->id)}}">Show Info</a></td>

                         </tr>

                      @endforeach

                </tbody>

             </table>

            </div> 

        </div>  

       @else

          <p>No repair has been created.</p>

       @endif

 @endif

 @section('css')
  <style >
      .btn-default {
      background: #f5f5f5;
      padding: 5px;
      line-height: 1;
      border-radius: 4px;
    }
    .btn-open {
        background: #fb9210 !important;
        color: #000;
    }
    .btn-ps {
      background: #004181 !important;
      color: #fff;
    }
    .btn-cs {
        background: #2ba01c !important;
        color: #fff;
    }
    .btn-r {
        background: #dc3030 !important;
        color: #fff;
    }
    .btn-rp {
        background: #ebef1a !important;
        color: #000;
    }
    .btn-rt {
        background: #ae68d2 !important;
        color: #fff;
    } 
  </style>

 @endsection