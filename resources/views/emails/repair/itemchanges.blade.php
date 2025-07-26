@component('emails/template/email')
   
  <p style="margin-bottom: 10px;">Hi {{ $firstname }},</p><br>  
    @php $changeFault = false; @endphp
    @foreach($fields as $field)
       

            @if($field['field'] == 'Fault Category' && $changeFault == false) 
                <div>The {{ $field['field'] }} has been updated.</div><br/>
                @php $changeFault = true; @endphp
            @else

                @if($field['old'] == '')
                    <div>The {{ $field['field'] }} field was updated to {{ $field['new'] }}. </div><br/>
                @else
                    <div>The {{ $field['field'] }} field was updated from {{ $field['old'] }} to {{ $field['new'] }}. </div><br/>
                @endif
            @endif

    @endforeach


	  <br>	
      <p><a href="{{ url( 'repairs' . '/'. $rma_no ) }}">Click here</a> for more details.</p>

@endcomponent    