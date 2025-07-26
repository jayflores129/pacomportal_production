@component('emails/template/email')
   
  <p>Dear {{ $name }},</p><br>

  <p>Below is the hint of your password. <a href="{{ url('/') }}">Click here</a> to login to SPG Support Dashboard.</p>	
  <p style="font-size: 24px;color: #222;margin: 10px 0;padding:10px 20px;display:inline-block;background-color: #e7f3e7;border-radius: 5px;">{{ $hint }}</p><br>

@endcomponent  