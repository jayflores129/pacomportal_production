@component('emails/template/email')
   
  <p style="margin-bottom: 10px;">Hi {{ $firstname }},</p><br>  

    @foreach($fields as $field)
       
      <div>The {{ $field['field'] }} field was updated from {{ $field['old'] }} to {{ $field['new'] }} </div><br/>

    @endforeach


	  <br>	
      <p><a href="{{ url( 'repairs' . '/'. $rma_no ) }}">Click here</a> for more details.</p>

@endcomponent    