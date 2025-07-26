  <div class="panel panel-top">
    <div class="grid justify-space-between">
      <div class="col">
        {!! Breadcrumbs::render('repairs') !!} 
      </div>
      <div class="text-right" style="display: flex;align-items: center; gap: 10px;">

        {{-- {{ Form::open(array('url' => '/print-search-result-rma-pdf', 'method' => 'post', "id" => "form-export-pdf", "style" => "display: none;"  )) }}
          <input type="hidden" name="rma_ids" id="print_rma_ids_input_pdf">
          <button class="btn-brand btn-brand-icon btn-brand-primary">
            <i class="fa fa-file btn-icon"></i>
            <span>Export PDF</span>  
          </button>

          <script>
            if (localStorage.RmaIDs) {
              document.getElementById("print_rma_ids_input_pdf").value = localStorage.RmaIDs;
            }
            if (location.pathname === '/advanced-search-rma') {
              document.getElementById('form-export-pdf').style.display = 'block';
            }
          </script>
        {!! Form::close() !!} --}}
 
        {{ Form::open(array('url' => '/print-search-result-rma', 'method' => 'post', "id" => "form-export-csv", "style" => "display: block;"  )) }}
          <input type="hidden" name="rma_ids" id="print_rma_ids_input_csv" value='{{ $rma_ids ?? '' }}'>
          <button type="submit" class="btn-brand btn-brand-icon btn-brand-primary">
            <i class="fa fa-file btn-icon"></i>
            <span>Export CSV</span>  
          </button>

          <script>
            $(window).on('load', function(){
              if (localStorage.RmaIDs) {
                document.getElementById("print_rma_ids_input_csv").value = localStorage.RmaIDs;
              }
            })
            
            if (location.pathname === '/repairs') {
              document.getElementById('form-export-csv').style.display = 'block';
            }
          </script>
        {!! Form::close() !!}

        <a href="{{ url('rma/create') }}"  class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-pencil btn-icon"></i><span>Create Repair</span></a>
      </div>
    </div>
  </div> 