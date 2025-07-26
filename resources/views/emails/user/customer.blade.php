@component('emails/template/email')
   
          @php 
              $body = App\Option::where('key','new_registration_customer_body' )->value('value');
          @endphp

		{!! $body !!}
@endcomponent  