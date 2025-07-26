{!! Form::open(['route' => 'campaign.updateNewTaskAdmin', 'autocomplete' => 'off']) !!}
<div class="task-section setting-section">
	<h5>New task ( for Pacom Internal User )</h5>
	<span class="label">Subject</span>
	@php 

		$subject = App\Models\Option::where('key','new_task_admin_subject' )->value('value'); 
		$body    = App\Models\Option::where('key','new_task_admin_body' )->value('value'); 

	@endphp
	<input type="text" name="subject" placeholder="Subject Line" value="{{ $subject }}" required/>
	<span class="label">Body</span>
	<textarea type="text" name="body" required>{{ $body }}</textarea>
	 <button type='submit'  class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-check btn-check"></i><span>Save Changes</span></button>
</div>
{!! Form::close() !!} 