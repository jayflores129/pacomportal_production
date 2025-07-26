<div class="table-m-height" id="commentList">
    <table class="table table-striped table-default-brand">
      <thead>
        <tr>
          <th width="250">Time</th>
          <th>Comment</th>
          <th width="200">User</th>
          @if( Auth::user()->isAdmin() )
            <th width="100">Action</th>
          @endif
        </tr>
      </thead>
      <tbody>
       
        @if($comments)
          @foreach($comments as $comment)
            <tr>
              <td><div class="date_created">{{ date('d-m-Y H:i:s', strtotime($comment->created_at)) }}</div></td>
              <td><p class="desc">{!! $comment->comment !!}</p></td>
              <td>{{ DB::table('users')->where('id', $comment->user_id )->value('firstname') }} {{ DB::table('users')->where('id', $comment->user_id )->value('lastname') }}</td>
              @if( Auth::user()->isAdmin() )
                <td>
                  <button class="btn btn-danger delRMAComments" data-id="{{ $comment->id }}" data-rma-id="{{ $comment->rma_id }}" data-comment="{{ $comment->comment }}"><span class="fa fa-trash"></span></button>
                </td>
              @endif
            </tr>
          @endforeach
        
        @else    
          <tr>
            <td colspan="4">No comment found</td>
          </tr>
        @endif
        
      </tbody>
    </table>
  </div>  
  @if($comments)
  <div id="commentPagination" class="pagination-links">
    {{ $comments->fragment('commentList')->appends(['p_comments' => $comments->currentPage()])->links() }} 
  </div>   
@endif

