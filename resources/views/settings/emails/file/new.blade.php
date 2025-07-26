{!! Form::open(['route' => 'campaign.updateNewFile', 'autocomplete' => 'off']) !!}
<div class="task-section setting-section">
	<h5>New File Updates ( for customer )</h5>
	
	@php 

		$subject  = App\Models\Option::where('key','new_file_customer_subject1' )->value('value'); 
		$subject2 = App\Models\Option::where('key','new_file_customer_subject2' )->value('value'); 
		$subject3 = App\Models\Option::where('key','new_file_customer_subject3' )->value('value'); 
		$body     = App\Models\Option::where('key','new_file_customer_body' )->value('value'); 

	@endphp

	<span class="label">Subject 1 (Firmware)</span>
	<input type="text" name="subject1" placeholder="Subject Line" value="{{ $subject }}" required/>
	<span class="label">Subject 2 ( Technical Document)</span>
	<input type="text" name="subject2" placeholder="Subject Line" value="{{ $subject2 }}" required/>
	<span class="label">Subject 3 ( Certificate)</span>
	<input type="text" name="subject3" placeholder="Subject Line" value="{{ $subject3 }}" required/>
	<span class="label">Body</span>

	<textarea type="text" name="body" required>{{ $body }}</textarea>
	<div class="alert alert-info">
	  <strong>Info!</strong> [name], [filelink], [filetype] and [filename] are dynamically generated and must be added in the body of this email.
	</div>
	 <button type='submit'  class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-check btn-check"></i><span>Save Changes</span></button>
</div>
{!! Form::close() !!} 