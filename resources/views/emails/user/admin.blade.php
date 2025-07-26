@component('emails/template/email')
   
          @php 
              $body = App\Option::where('key','new_registration_admin_body' )->value('value');
          @endphp
          {!! $body !!}
          <br>           
            <br>
             <span>Name     :</span>{{ $firstname }} {{ $lastname }}<br>
             <span>Email    :</span>{{ $email }}<br>
             <span>Country  :</span>{{ $country }}<br>
             <span>Phone No :</span>{{ $phone }}<br>
             <span>Company  :</span>{{ $company }}<br>
            <br>
          </p>

@endcomponent    