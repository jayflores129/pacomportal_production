@component('emails/template/email')
   
    @php 
        $body = App\Models\Option::where('key','new_ticket_admin_body' )->value('value');
    @endphp
    {!! $body !!}
    <br>
    <p><strong>First Name </strong> : {{ $firstname }}</p>
    <p><strong>Last Name </strong> : {{ $lastname }}</p>
    <p><strong>Country </strong> : {{ $country }}</p>
    <p><strong>Company </strong> : {{ $company }}</p>
    <p><strong>Ticket No </strong> : R{{ $rma_no }}</p><br>

    <p><a href="{{ url('/repairs/' . $rma_no ) }}">Click here</a> to view the ticket request.</p>

@endcomponent    