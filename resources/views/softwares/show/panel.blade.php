    <div class="panel panel-top">
      <div class="grid justify-space-between">
        <div class="col">
          <a href="{{ url('admin/softwares') }}" class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-angle-left btn-icon"></i><span>Go Back</span></a>
        </div>
        <div class="col text-right">
       
             <ul class="list-inline">

                @if( !$ticket->resolve )
                  <li>
                    <a href="{{ route('softwares.edit', $ticket->id)}}"  class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-edit btn-icon"></i><span>Edit</span></a>
                  </li>
                @endif
                @if( ( $ticket->user_id === Auth::user()->id  &&   $ticket->resolve === NULL || $ticket->resolve == 0 ) || ( Auth::user()->isAdmin()  && $ticket->resolve === NULL || $ticket->resolve == 0 ))
                <li>
                  <a href="{{ route('softwares.resolving', $ticket->id)}}"  class="btn-brand btn-brand-icon btn-brand-info"><i class="fa fa-edit btn-icon"></i><span>Resolve</span></a>

                </li>
                @endif
                 <li>
                  <a href="{{ url('admin/softwares/create') }}" class="btn-brand btn-brand-icon btn-brand-success"><i class="fa fa-pencil btn-icon"></i><span>Create Task</span></a>
                </li>
                @if( Auth::user()->isAdmin() &&  $ticket->resolve === NULL || $ticket->resolve == 0  )
                <li>
                   {!! Form::open(['method' => 'delete','route' => ['softwares.destroy', $ticket->id]]) !!}
                    <button type="submit" class="btn-brand btn-brand-icon btn-brand-danger"><i class="fa fa-trash btn-icon"></i><span> Delete</span></button>
                    {!! Form::close() !!}
                </li>
                @endif
               
             </ul>  
         
        </div>
      </div>
    </div> 