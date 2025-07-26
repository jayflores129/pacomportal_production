{!! Form::open(['route' => 'campaign.updateGeneral', 'autocomplete' => 'off']) !!}
<div class="general-section setting-section">
	<h5>Footer Address</h5>
	<span class="label">Use < br > to add new line</span>
	<textarea type="text" name="address">{!! App\Models\Option::where('key','email_footer' )->value('value') !!}
	</textarea>
	 <button type='submit'  class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-check btn-check"></i><span>Save Changes</span></button>
</div>
{!! Form::close() !!} 