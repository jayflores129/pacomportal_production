<div class="table-m-height">
    <div class="table-responsive"> 
      <table class="table table-striped table-default-brand">
        <thead>
          <tr>
            <th width="200">Time</th>
            <th>Type</th>
            <th>Description</th>
          </tr>
        </thead>
        <tbody>
           @if($logs)
              @foreach($logs as $log)
                <tr>
                  <td>{{ date(' d-m-Y H:i:s', strtotime($log->created_at)) }}</td>
                  <td width="150">{!! $log->type !!}</td>
                  <td>{!! $log->description !!}</td>
                </tr>
              @endforeach
           @else
            <tr>
              <td colspan="4">No log found</td>
            </tr>
           @endif
        </tbody>
      </table>
      @if($logs)
        <div id="logPagination" class="pagination-links">
          {{ $logs->fragment('logPagination')->appends(['p_logs' => $logs->currentPage()])->links() }} 
        </div>   
      @endif
    </div>
 </div>   