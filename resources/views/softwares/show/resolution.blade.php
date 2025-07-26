@if( !$ticket->resolve ) 

	<div style="padding: 20px;background: #fff;border: 1px solid #ddd;margin-bottom: 20px;">

		 {!! Form::open( array('method' => 'patch', 'route' =>array('admin.softwares.resolve', $ticket->id ), 'autocomplete' => 'off' ) ) !!}

			<label for="resolution">Resolution (Required)</label>

			<textarea name="resolution" id="resolution" cols="30" rows="10" style="padding: 10px;" required></textarea>

			<input type="hidden" name="resolve" value="1"/>

			<input type="hidden" name="task_id" value="{{ $ticket->id }}"/>

			<input type="hidden" name="assignee_id" value="{{ $ticket->assigned_to }}"/>

			<input type="hidden" name="creator_id" value="{{ $ticket->user_id }}"/>

			<button  class="btn-brand btn-brand-icon btn-brand-success" id="resolvetask"><i class="fa fa-edit btn-wrench"></i><span style="color: #000">Click to resolve</span></button>
			
		{!! Form::close() !!}	

	</div>

@endif
