
        @if( Auth::user()->company == $repair->company || Auth::user()->isAdmin()  )

          <div class="comment-form" style="padding: 20px;background: #f5f5f5;margin-bottom: 10px;">

              @include('components/errors')

          </div>    

        @endif

        <p>Latest comments from SPG Team</p> 

       <div class="table-m-height">

          <table class="table table-striped table-default-brand">

            <thead>

              <tr>

                <th width="250">Time</th>

                <th>Comment</th>

                <th width="200">SPG User</th>

              </tr>

            </thead>

            <tbody>

              @if($comments)

                @foreach($comments as $comment)

                  <tr>

                    <td><div class="date_created">{{ date('d-m-Y H:i:s', strtotime($comment->created_at)) }}</div></td>

                    <td><p class="desc">{{ $comment->description }}</p></td>

                    <td>{{ DB::table('users')->where('id', $comment->created_by )->value('firstname') }} {{ DB::table('users')->where('id', $comment->created_by )->value('lastname') }}</td>

                  </tr>

                @endforeach

            @endif
              
            </tbody>

          </table>

        </div>  

