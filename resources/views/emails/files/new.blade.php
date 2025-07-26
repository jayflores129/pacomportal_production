@component('emails/template/email')

    @php 
        $body = App\Models\Option::where('key','new_file_customer_body' )->value('value');

       
        $body = str_replace('[name]',$name, $body);
       
        $body = str_replace('[filetype]',$filetype, $body);

        $body = str_replace('[filename]',$filename, $body);

        $body = str_replace('[filelink]',$link, $body);
    @endphp
    {!! $body !!}

@endcomponent    
