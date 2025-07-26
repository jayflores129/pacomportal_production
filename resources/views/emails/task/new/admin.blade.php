@component('emails/template/email')
   
          @php 
              $body = App\Option::where('key','new_task_admin_body' )->value('value');
          @endphp
          {!! $body !!}
          <br><br>           
           <span>Summary  :</span> <strong>{{ $summary }}</strong><br>
           <span>Task #   :</span> <strong>{{ $taskID }}</strong><br>
           <span>Type     :</span> <strong>{{ $type }}</strong><br>
           <span>Product  :</span> <strong>{{ $product }}</strong><br>
           <span>Assignee :</span> <strong>{{ $assignee }}</strong><br>
          <br>
        </p>
        <p>
          <a href="{{ $link }}">Click here</a>  to see more information of the task.
        </p>

@endcomponent    