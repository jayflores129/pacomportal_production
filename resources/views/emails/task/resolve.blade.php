@component('emails/template/email')
   

	@php 
        $body = App\Option::where('key','task_resolve_customer_body' )->value('value');

        $body = str_replace('[resolution]',$resolution, $body);
       
        $body = str_replace('[total_days]',$time, $body);

        $body = str_replace('[link]',$url, $body);

    @endphp

    {!! $body !!}

@endcomponent    

