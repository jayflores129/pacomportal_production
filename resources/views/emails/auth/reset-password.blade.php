@component('emails/template/email')
   
  <p>{!! $name !!},</p><br>

  <p>{!! $line !!}</p><br>

  <a href="{!! $action  !!}" class="btn-reset-link">Reset Password</a><br><br>

  <p>{!! $line2 !!}</p>

@endcomponent  