<div class="panel panel-default panel-brand">

  <div class="panel-body">

        <div class="field field-group clearfix">

           <div class="grid justify-space-between">

             <div class="col"><label for="input_sn">

                <h4 style="margin: 0;">All Comments</h4> 

            </div>

             <div class="col text-right">

                  {{-- <a href="#" class="new-comment" target="_blank"> <strong>Click here to add comment</strong></a> --}}

             </div>

           </div> 
          
        </div>

      @if( Auth::user()->isAdmin() || Auth::user()->id != $ticket->assigned_to || Auth::user()->id != $ticket->user_id  )

          <div class="comment-form" style="padding: 10px;background: #def2fd;">

              @include('components/errors')
           
              {!! Form::open(['method' => 'post', 'route' => 'softwares.comment', 'files' => true ]) !!}

              <input type="hidden" name="task_id" value="{{ $ticket->id }}" />

              <input type="hidden" name="assigned_to" value="{{ $ticket->assigned_to }}" />

              <input type="hidden" name="created_by" value="{{ Auth::user()->id }}" />

              <label>Add comment</label>

              <textarea class="form-control" name="description"></textarea>

              <input type="file" name="file" id="attachmentFile" />

              <br>
              <button type="submit"  class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-check btn-icon"></i><span>Submit</span></button>

              {!!  Form::close() !!}

          </div>   

      @endif  

      @if($comments)

        <ul class="task-comment-list">

          @foreach($comments as $comment)

            <li>
      
            <div class="row">

              <div class="col-md-2">

                  <div class="col img">

                  <?php $photo = DB::table('user_details')->where('user_id', $comment->user_id )->value('photo'); ?>

                  @if( $photo )

                    <div class="photo">

                      <img src="{{ asset('/images/uploads/' . $photo ) }}"  width="100%" />

                    </div>

                  @else

                    <div class="photo">

                      <img src="{{ asset('/images//user-placeholder.png') }}" width="100%" />

                    </div>

                  @endif

                  <div class="col meta">

                    <strong>{{ DB::table('users')->where('id', $comment->user_id )->value('firstname') }} {{ DB::table('users')->where('id', $comment->user_id )->value('lastname') }}</strong><br>
                    <span> {{  \Carbon\Carbon::parse($comment->created_at)->diffForHumans()}}</span>

                  </div>

                </div>

              </div>

              <div class="col-md-10">
                
                <p class="desc">{{ $comment->description }}</p>

                @if( $comment->attachment_id )
           
                      @php 

                        $attachment = DB::table('software_attachments')
                                        ->where('id', $comment->attachment_id )
                                        ->value('attachments');
                      @endphp
                    
                    @if( $attachment )

                      <p><strong>Download File :</strong> <a href="{{ url('download-software-file') }}/{{ $attachment  }}" target="_blank" download>{{ $attachment }}</a></p>
                      
                    @endif
                        
                @endif

              </div>

            </div>
              

            </li>

          @endforeach

          <div class="pagination">
             {{ $comments->links() }}
          </div>

        </ul>

      @endif
        

  </div><!-- ./panel body -->

</div><!-- ./panel -->    



@section('css')
<style>
  
  .comment-form {
    padding: 10px;
    background: #def2fd;
}
</style>

@stop