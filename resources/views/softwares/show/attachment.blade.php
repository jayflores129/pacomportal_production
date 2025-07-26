<!-- start of attachment -->
 <div class="task-info attachment" style="background: #fff">

  <div style="padding: 40px;min-height: 300px;">

     <div>

   

          @if( !empty( $ticket->filename ))

            
            <h3>Click the button below to download the original file attachment.</h3>
            <p><a href="{{ URL::to('/download-file-task/' . $ticket->filename) }}" target="_blank" download  class="btn-brand btn-brand-padding downloadable-file"> Download Now</a></p>


          @else

             <p>Sorry, there is no file attached to this task! </p> 

          @endif  



     </div> 
    
  </div>

</div>
<!-- end of attachment -->

