<div class="table-responsive"> 

  <table class="table table-striped table-default-brand">

    <thead style="background: #fff">

      <tr>

        <th width="300">Time</th>
        <th width="200">Type</th>
        <th>Descrption</th>
        <th width="70">User ID</th>

      </tr>

    </thead>

    <tbody>

       @if($logs)

          @foreach($logs as $log)

            <tr>

              <td>{{ date(' d-m-Y H:i:s', strtotime($log->created_at)) }}</td>
              <td>{!! $log->type !!}</td>
              <td>{!! $log->description !!}</td>
              <td>#{!! $log->created_by !!}</td>

            </tr>

          @endforeach

       @else
          No logs
       @endif

    </tbody>

  </table>

</div>   
