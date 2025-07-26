

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

                              <td>{!! $log->type !!}</td>

                              <td>{!! $log->description !!}</td>

                            </tr>

                          @endforeach

                       @else

                          No logs

                       @endif

                    </tbody>

                  </table>

                </div>

             </div>    

