@component('emails/template/email')
   
                      
            <p style="margin-bottom: 10px;">To {{ $firstname }},</p><br>

            <p style="margin-bottom: 10px;">Your registration has been approved and access granted.</p>

            <p >You can now <a href="{{ url('/') }}">log in</a> here with your username and password.</p>



@endcomponent    
