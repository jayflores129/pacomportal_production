@component('emails/template/email')
   
  <p style="margin-bottom: 10px;">Hi {{ $firstname }},</p><br>
    
    <p>R{{ $rma_no }} has beed approved by the client.</p>
	  <br>	
      <p><a href="{{ url( 'repairs' . '/'. $rma_no ) }}">Click here</a> for more details.</p>

@endcomponent    