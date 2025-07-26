@component('emails/template/email')
   
  <p style="margin-bottom: 10px;">Hi {{ $firstname }},</p><br>
    
    <p>R{{ $rma_no }} has a deleted faulty item.</p>
    <br>
    <p>Here are the details:</p>

    <p>Model: {{ $repair->model }}</p>

    <p>Serial Number: {{ $repair->serial_number }}</p>

    {{-- <p>Fault Category: {{ $repair['fault_category'] }}</p> --}}
	<br>	
   

@endcomponent    