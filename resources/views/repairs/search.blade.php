@extends('layouts.app')

@section('content')


  @include('repairs/components/navigation')
  <div class="row">
    <div class="col-sm-12">
        <div class="search-form">
            {{ Form::open(array('url' => '/new-rma-search/', 'method' => 'get'  )) }}
                    <input type="text" name="po_number" class="form-control" />
                    <button type="submit" class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-check btn-icon"></i><span>Submit</span></button>
            {!! Form::close() !!}
        </div>
        <div class="table-responsive">
            <table class="table table-default-brand table-striped ">
               <thead>
                <tr>
                    <th style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd; padding: 0;">
                       <button class="sort-button" data-query="id">
                         <span>RMA #</span>
                         <i class="fa fa-sort"></i>
                       </button>
                     </th>
                    <th style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd; padding: 0;">
                       <button class="sort-button" data-query="requester_name">
                           <span>Name</span>
                           <i class="fa fa-sort"></i>
                       </button>
                     </th>
                   <th style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd; padding: 0;">
                       <button class="sort-button" data-query="requester_company">
                           <span>Company</span>
                           <i class="fa fa-sort"></i>
                       </button>
                   </th>
                   <th style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd; padding: 0;">
                       <button class="sort-button" data-query="status">
                           <span>Status</span>
                           <i class="fa fa-sort"></i>
                       </button>
                   </th>
                   <th style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd; padding: 0;">
                       <button class="sort-button" data-query="po_number">
                           <span>PO Number</span>
                           <i class="fa fa-sort"></i>
                       </button>
                   </th>
                   <th style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd; padding: 5px;">Total Faulty Items</th>
                   <th style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd; padding: 0;">
                       <button class="sort-button" data-query="requested_date">
                           <span>Date Requested</span>
                           <i class="fa fa-sort"></i>
                       </button>
                   </th>
                   <th style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd; padding: 0;padding: 5px;">Action</th>
                </tr>  
               </thead>
               <tbody>
        @if($repairs != '')
        @if($repairs->count() > 0 )
             <?php $count = 0; ?>
             @foreach ($repairs as $repair )
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
                   <td>{{ $repair->requester_name  }}</td>
                   <td>{{ $repair->requester_company }}</td>
                   

                   <td><span class="{{ $class }}" >{{ $rstatus }}</span></td>
                   <td><span class="">{{ $repair->po_number}}</span></td>
                   <td>{{ $repair->items->count()  }}</td>
                   <td>{{ date('d/m/Y', strtotime($repair->requested_date))  }}</td>
                   <td width="150">
                       <a href="{{ route('repairs.show', $repair->id)}}" class="btn btn-sm btn-primary" target="_blank"><span class="fa fa-eye"></span> View Details</a>
                   </td> 
               </tr>


               @endforeach
         @else

             <tr><td colspan="8">No data found</td></tr>

         @endif
         <tr><td colspan="8">No data found</td></tr>
       @endif
    </tbody>
    </div>
  </div>
@endsection

@section('js')
<script>
        var status = '',
           product = '',
           value = '',
           company = '';

      //Product Change
        $('#repairProduct').on('change', function(){
            product = $(this).find('option:selected').text();

            if(product == 'Select') {
                product = '';
            }
        }); 

      //Repair Status Change
      $('#repairStatus').on('change', function(){
           status = $(this).prop('selected', true).val();
      }); 


      //Repair Status Change
      $('#groupFilter').on('change', function(){
           status = $(this).prop('selected', true).val();
           window.location= '{{URL::to('repairs')}}?filter_id=' + $(this).val();
      }); 


      //Toggle show #groupEdit
      $('#groupEdit').on('click', function(){
           $('#editSearch').toggleClass('hide');
           $('#groupEdit .edit-filter').toggleClass('hide');
           $('#groupEdit .hide-filter').toggleClass('hide');
           $('#newSearch').addClass('hide');
      });
      //create new filter
      $('#createGroup').on('click', function(e){
           e.preventDefault();
           $('#newSearch').toggleClass('hide');
           $('#editSearch').addClass('hide');
      });
</script>
@endsection