<div class="table-m-height">
    @if( Auth::user()->isAdmin() || 
    ( Auth::user()->company_id == $repair->company_id && ( $repair->cust_can_edit !=  0  && $repair->has_quotation != true ) )  )     
      <button id="addItem">Add Item</button> 
    @endif
    <table id="RMARequestDetails" class="table table-striped table-default-brand">
      <thead>
        <tr>
          <th width="140">Note</th>
          <th width="250">Serial No</th>
          <th>Model</th>
          <th width="250">Faults</th>
          <th>Repair Cost</th>
          <th>Order Date</th>
          <th>Warranty</th>
          <th>Additional comments</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="rma_items_table">
        @if($repair->items)
            @foreach($repair->items as $item)
              <tr data-tr-id="{{ $item->id }}">
                @php
                  switch ($item->status) {
                    case 'Pending':
                      $status = '<span class="btn-status btn-status-1">' . $item->status . '</span>';
                      break;
                    case 'Being repaired':
                      $status = '<span class="btn-status btn-status-2">' . $item->status . '</span>';
                      break;
                    case 'Repaired':
                      $status = '<span class="btn-status btn-status-3">' . $item->status . '</span>';
                      break;
                    case '':
                      $status = '';
                      break;
                    default:
                      $status = '<span class="btn-status btn-status-4">' . $item->status . '</span>';
                      break;
                  }
                @endphp
                <td>{!! $status !!} </td>
                <td>{{ $item->serial_number }}</td>
                <td>{{ $item->model }}</td>
                <td width="400">
                  @php
                    $item->faults 
                  @endphp
                  @foreach($item->faults as $fault)
                          {{ $fault->fault }}<br>
                  @endforeach
                    
                    <div class="hide for-printing">
                      <div><strong>Invalid serial number :</strong> {{ $item->invalid_serial_number ? "Yes" : "" }}</div>
                      <div><strong>Fault described by customer :</strong> {{ $item->fault_described_by_customer }}</div>
                      <div><strong>Date purchased known :</strong>{{ $item->date_purchased ? "Yes" : ""  }}</div>
                      <div><strong>Under warranty :</strong>{{ $item->under_warranty ? "Yes" : ""  }}</div>
                      <div><strong>Original order date :</strong>{{ $item->original_order_date }}</div>
                      <div><strong>Root cause analysis :</strong>{{ $item->root_cause_analysis  }}</div>
                      <div><strong>PACOM fault description :</strong>{{ $item->pacom_fault_description }}</div>
                      <div><strong>PACOM comment :</strong>{{ $item->pacom_comment }}</div>
                      <div><strong>Received date :</strong>{{ $item->received_date }}</div>
                      <div><strong>Repaired date :</strong>{{ $item->repaired_date }}</div>
                      <div><strong>Status :</strong>{{ $item->status }}</div>
                    </div>
                </td> 
                <td>
                  @php
                  switch ($repair->currency) {
                      case 'EURO':
                      $cur_sign = '€';
                      break;
                      case 'GBP':
                      $cur_sign = '£';
                      break;
                      case 'AUD':
                      $cur_sign = 'A$';
                      break;
                      case 'CAD':
                      $cur_sign = 'CA$';
                      break;
                      case 'YUAN':
                      $cur_sign = '¥';
                      break;
                      default:
                      $cur_sign = '$';
                      break;
                  }
                  @endphp
                  @if(!$item->repair_cost)
                    {{  $item->repair_cost }}
                  @else
                    {{ $cur_sign . '' . $item->repair_cost }}
                  @endif
                  
               </td>
                <td>{{ $item->original_order_date }}</td>

                <td>
                  @if($item->under_warranty == 1)
                      {{ "Yes" }}
                  @elseif($item->under_warranty != null) {
                      {{ "" }}
                  }
                  @elseif($item->under_warranty == 0 && $item->under_warranty != '')
                      {{ "No" }}
                  @else
                       {{ "" }}
                  @endif
                  </td>
                  <td>{{ $item->fault_described_by_customer }}</td>
                <td width="180">
                   
                   <button class="itemViewBTN btn btn-secondary" 
                        data-id="{{ $item->id }}"
                        data-rma-id="{{ $item->rma_id }}"
                        data-serial="{{ $item->serial_number }}"
                        data-model="{{ $item->model }}"
                        data-repair-cost="{{ $item->repair_cost }}"
                        data-order-date="{{ $item->original_order_date }}"
                        data-status="{{ $item->status }}"
                        data-faults="{{ $item->faults }}"
                        data-under-warranty="{{ $item->under_warranty }}"
                        data-invalid-serial="{{ $item->invalid_serial_number }}"
                        data-fault-described="{{ $item->fault_described_by_customer }}"
                        data-status="{{ $item->status }}"
                        data-purchase-known="{{ $item->date_purchased }}"
                        data-root-cause-analysis="{{ $item->root_cause_analysis }}"
                        data-pacom-fault-description="{{ $item->pacom_fault_description }}"
                        data-pacom-comment="{{ $item->pacom_comment }}"
                        data-received-date="{{ $item->received_date }}"
                        data-repaired-date="{{ $item->repaired_date }}"
                    ><span class="fa fa-eye"></span></button>
                    @if( Auth::user()->isAdmin() || ( Auth::user()->company_id == $repair->company_id && ( $repair->cust_can_edit !=  0 && $repair->has_quotation != true  ) )) 
                   <button class="EditBTN btn btn-primary" 
                        data-id="{{ $item->id }}"
                        data-rma-id="{{ $item->rma_id }}"
                        data-serial="{{ $item->serial_number }}"
                        data-model="{{ $item->model }}"
                        data-repair-cost="{{ $item->repair_cost }}"
                        data-order-date="{{ $item->original_order_date }}"
                        data-status="{{ $item->status }}"
                        data-faults="{{ $item->faults }}"
                        data-under-warranty="{{ $item->under_warranty }}"
                        data-invalid-serial="{{ $item->invalid_serial_number }}"
                        data-fault-described="{{ $item->fault_described_by_customer }}"
                        data-status="{{ $item->status }}"
                        data-purchase-known="{{ $item->date_purchased }}"
                        data-root-cause-analysis="{{ $item->root_cause_analysis }}"
                        data-pacom-fault-description="{{ $item->pacom_fault_description }}"
                        data-pacom-comment="{{ $item->pacom_comment }}"
                        data-received-date="{{ $item->received_date }}"
                        data-repaired-date="{{ $item->repaired_date }}"
                   ><span class="fa fa-edit"></span></button>
                   <button class="DelBTN btn btn-danger"  data-rma-id="{{ $item->rma_id }}" data-serial="{{ $item->serial_number }}" data-id="{{ $item->id }}"><span class="fa fa-trash"></span></button>
                  @endif
                </td>
              </tr>
            @endforeach
        @else
            No logs
        @endif
      </tbody>
    </table>
  </div> 

  <style>
    span.btn-status {
      padding: 5px 10px;
      font-size: 13px;
      background: #f5f5f5;
      border-radius: 5px;
    }
    span.btn-status.btn-status-1 {
      background: #e37e41;
      color: #fff;
    }
    span.btn-status.btn-status-2{
      background: #419ee3;
      color: #fff;
    }
    span.btn-status.btn-status-3 {
      background: #6bad6d;
      color: #fff;
    }
    span.btn-status.btn-status-4 {
      background: #904edb;
      color: #fff;
    }
</style>
