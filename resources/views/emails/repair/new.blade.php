@component('emails/template/email')
   
  <p style="margin-bottom: 10px;">Hi {{ $firstname }},</p><br>  

   
      <div>Here are the details of the ticket R{{ $rma_no }}</div><br/>
      
      <div><strong>Requested Date</strong>: {{  $repair->requested_date }} </div><br/>
      <div><strong>Requester Name</strong>: {{  $repair->requester_name }} </div><br/>
      <div><strong>Requester Phone</strong>: {{  $repair->requester_phone }} </div><br/>
      <div><strong>Requester Company</strong>: {{  $repair->requester_company }} </div><br/>
      <div><strong>Requester Email</strong>: {{  $repair->requester_email }} </div><br/>

      <div><strong>PO Number</strong>: {{  $repair->po_number }} </div><br/>
      <div><strong>Country</strong>: {{  $repair->country }} </div><br/>

	  <br>	
      <p><a href="{{ url( 'repairs' . '/'. $rma_no ) }}">Click here</a> for more details.</p>

@endcomponent    