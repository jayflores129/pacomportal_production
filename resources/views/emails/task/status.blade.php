@component('emails/template/email')
  
    @php 

    	$url  = url( 'admin/softwares/' . $task_id );
        $body = str_replace('[link]',$url, $status);

    @endphp

    {!! $body !!}

@endcomponent    
