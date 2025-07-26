<div class="table-m-height">
    <table class="table table-striped table-default-brand">
      <thead>
        <tr>
          <th>Status</th>
          @if( Auth::user()->isAdmin() )
            <th width="100">Action</th>
          @endif
        </tr>
      </thead>
      <tbody>
        @if($repair->rma_status)
   
          @foreach($repair->rma_status as $stat)

          {{-- {{$stat }} --}}
            <tr>
              <td style="text-decoration: capitalize">
                {{ $stat->status }} <br>
                @if($stat->courier != '') 
                   <div class="courier-detail"><strong>Courier : </strong> {{ $stat->courier }}</div>
                @endif
                @if($stat->consignment_note != '') 
                   <div class="consignment_note"><strong>Consignment Note : </strong> {!! $stat->consignment_note !!}</div>
                @endif

              </td>
              @if( Auth::user()->isAdmin() )
                <td>
                  <button class="btn btn-danger delRMAStatus" data-rma-id="{{ $repair->id }}" data-id="{{ $stat->id }}" data-rma-status="{{ $stat->status }}"><span class="fa fa-trash"></span></button>
                </td>
              @endif
            </tr>
          @endforeach
      @endif
        
      </tbody>
    </table>
  </div>  


