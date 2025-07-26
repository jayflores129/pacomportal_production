@component('emails/template/email')
    <p style="margin-bottom: 10px;">Hi {{ $firstname }},</p><br>

    {{-- <p>We have finalized the quotation of the RMA #{{$rma_no}}.</p><br> --}}

    @if ($repair_status === 'Received')
        <p>We have received your items under R{{ $rma_no }}.</p><br />
        <p><a href="{{ url('rma-quotation' . '/' . $rma_no) }}">Click here</a> for more information.</p><br />
    @endif

    @if ($repair_status === 'To Be Confirmed')
        <p>New quotation for R{{$rma_no}}.</p><br />
        <p> <a href="{{ url('rma-quotation' . '/' . $rma_no) }}">Click here</a> for more information.</p><br />
    @endif

    @if ($repair_status === 'Confirmed')
        <p>R{{$rma_no}} has been confirmed.</p><br />
        <p <a href="{{ url('rma-quotation' . '/' . $rma_no) }}">Click here</a> for more information.</p><br />
    @endif

    @if ($repair_status === 'Completed')
        <p>Ticket R{{$rma_no}} has been Completed.</p><br />
        <p><a href="{{ url('rma-quotation' . '/' . $rma_no) }}">Click here</a> for more information.</p><br />
    @endif

    @if ($repair_status === 'Cancelled')
        <p>R{{$rma_no}} has been Cancelled.</p><br />
        <p><a href="{{ url('rma-quotation' . '/' . $rma_no) }}">Click here</a> for more information.</p><br />
    @endif

    @if ($repair_status === 'Shipped')
        <p>R{{$rma_no}} has been shipped to your address.</p><br />
        <p><a href="{{ url('rma-quotation' . '/' . $rma_no) }}">Click here</a> for more information.</p><br />
    @endif

    {{-- <p>Your RMA has an update and requires your approval.</p><br />
    <p>Please <a href="{{ url('rma-quotation' . '/' . $rma_no) }}">Click here</a> to view the update and approve the
        request.</p><br />
    <br /> --}}
    Regards,
@endcomponent
