@component('emails/template/email')
   
  <p style="margin-bottom: 10px;">Hi {{ $firstname }},</p><br>
    
    <p>R{{ $rma_no }} has a new faulty item.</p>
    <br>
    <p>Here are the details:</p>

    <p>Model: {{ $repair->model }}</p>

    <p>Serial Number: {{ $repair->serial_number }}</p>

    <br>
    <p><a href="{{ url( 'repairs' . '/'. $rma_no ) }}">Click here</a> for more details.</p>
    {{-- <p>Fault Category: {{ $repair['fault_category'] }}</p> --}}
	<br>	
   

@endcomponent    