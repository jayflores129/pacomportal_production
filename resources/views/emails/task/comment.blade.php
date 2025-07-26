@component('emails/template/email')
   

      @php 
          $body = App\Option::where('key','task_comment_customer_body' )->value('value');
      @endphp

      {!! $body !!}
	  <br><br>
      <p><a href="{{ url( 'admin/softwares' . '/'. $ticket ) }}">Click here</a> for more details.</p>

@endcomponent    
