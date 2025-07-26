@component('emails/template/email')
   
  <p style="margin-bottom: 10px;">Hi {{ $firstname }},</p><br>
  <p>{!! $warranty !!}</p><br>

	  <br>	
      <p><a href="{{ url( 'repairs' . '/'. $repair_id ) }}">Click here</a> for more details.</p>

@endcomponent    