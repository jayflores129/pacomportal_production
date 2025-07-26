{!! Form::open(['route' => 'campaign.newTaskStatus', 'autocomplete' => 'off']) !!}
<div class="task-section setting-section">
	<h5>Status</h5>
	
	@php 
        $subject = App\Models\Option::where('key','task_remove_description_subject' )->value('value');
        $body    = App\Models\Option::where('key','task_remove_description_body' )->value('value');

        $subject2 = App\Models\Option::where('key','task_new_description_subject' )->value('value');
        $body2    = App\Models\Option::where('key','task_new_description_body' )->value('value');

        $subject3 = App\Models\Option::where('key','task_update_description_subject' )->value('value');
        $body3    = App\Models\Option::where('key','task_update_description_body' )->value('value');

        $subject4 = App\Models\Option::where('key','task_update_type_subject' )->value('value');
        $body4    = App\Models\Option::where('key','task_update_type_body' )->value('value');

        $subject5 = App\Models\Option::where('key','task_update_summary_subject' )->value('value');
        $body5    = App\Models\Option::where('key','task_update_summary_body' )->value('value');

        $subject6 = App\Models\Option::where('key','task_update_product_subject' )->value('value');
        $body6    = App\Models\Option::where('key','task_update_product_body' )->value('value');


        $subject7 = App\Models\Option::where('key','task_update_status_subject' )->value('value');
        $body7    = App\Models\Option::where('key','task_update_status_body' )->value('value');

        $subject8 = App\Models\Option::where('key','task_update_assignee_subject' )->value('value');
        $body8    = App\Models\Option::where('key','task_update_assignee_body' )->value('value');

	@endphp

	<span>New description</span>
	<input type="text" name="subject_new_description" placeholder="Subject Line" value="{{ $subject }}" required/>
    <textarea type="text" name="body_new_description" required>{{ $body }}</textarea>
    <hr>	


    <span>Removed task description</span>
	<input type="text" name="subject_remove_description" placeholder="Subject Line" value="{{ $subject2 }}" required/>
    <textarea type="text" name="body_remove_description" required>{{ $body2 }}</textarea>
    <hr>	

	<span>Updated task description</span>
	<input type="text" name="subject_update_description" placeholder="Subject Line" value="{{ $subject3 }}" required/>
    <textarea type="text" name="body_update_description" required>{{ $body3 }}</textarea>
	<hr>	


	<span>Updated type</span>
	<input type="text" name="subject_type" placeholder="Subject Line" value="{{ $subject4 }}" required/>
    <textarea type="text" name="body_type" required>{{ $body4 }}</textarea>
	<hr>


    <span>Updated summary</span>
	<input type="text" name="subject_summary" placeholder="Subject Line" value="{{ $subject5 }}" required/>
    <textarea type="text" name="body_summary" required>{{ $body5 }}</textarea>
	<hr>


	<span>Updated product</span>
	<input type="text" name="subject_product" placeholder="Subject Line" value="{{ $subject6 }}" required/>
    <textarea type="text" name="body_product" required>{{ $body6 }}</textarea>
	<hr>


	<span>Updated status</span>
	<input type="text" name="subject_status" placeholder="Subject Line" value="{{ $subject7 }}" required/>
    <textarea type="text" name="body_status" required>{{ $body7 }}</textarea>
	<hr>

	<span>New assignee</span>
	<input type="text" name="subject_assignee" placeholder="Subject Line" value="{{ $subject8 }}" required/>
    <textarea type="text" name="body_assignee" required>{{ $body8 }}</textarea>
	<hr>


	 <button type='submit'  class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-check btn-check"></i><span>Save Changes</span></button>
</div>
{!! Form::close() !!} 