<div class="add-comment-popup popup-form hide">
    <div class="popup-block">
      <div class="popup-heading"><span>Add Comment</span><button class="btn-add-close">x</button></div>
      <div class="popup-body">
            @if( Auth::user()->company == $repair->company || Auth::user()->isAdmin()  )
                <div class="comment-form">
                    @include('components/errors')
                
                    {!! Form::open(['method' => 'post', 'route' => 'rma_comment' ]) !!}
                    <input type="hidden" name="rma_id" value="{{ $repair->id }}" />
            
                    <textarea id="RmaComment" class="form-control" name="comment" ></textarea>
                    <br> 
                    <button type="submit"  class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-check btn-icon"></i><span>Submit</span></button>
                    {!!  Form::close() !!}
                </div>   

            @endif    
        </div><!-- Pop up heading End --> 
    </div><!-- Pop up block End --> 
</div><!-- Add item End --> 