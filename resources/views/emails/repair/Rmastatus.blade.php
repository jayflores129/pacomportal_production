@component('emails/template/email')
   
  <p style="margin-bottom: 10px;">Hi {{ $firstname }},</p><br>  

   
      <div>R{{ $rmaID }}</div><br/>
      
      <div><strong>Status</strong>: {{  $status }} by {{ $firstname  }} </div><br/>

	  <br>	
      <p><a href="{{ url( 'repairs' . '/'. $rmaID ) }}">Click here</a> for more details.</p>

@endcomponent    