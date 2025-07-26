@component('emails/template/email')

      <p style="margin-bottom: 10px;">Hi {{ $firstname }},</p><br>
                      
      {{-- <p>{!! $warranty !!}</p><br> --}}

      <p>RMA R{{$repair_id}} has been updated.</p><br />

      <p><a href="{{ url( 'repairs' . '/'. $repair_id ) }}">Click here</a> for more details.</p>

@endcomponent    

