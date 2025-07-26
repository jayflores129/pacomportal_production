@extends('layouts.app')

@section('content')

          <div class="panel panel-top">
              <div class="row">
                  <div class="col-sm-6">
                    {!! Breadcrumbs::render('firmware') !!}
                  </div>
                  <div class="col-sm-6 text-right">
                  </div>
              </div>
          </div> 
      
            <div class="panel panel-default panel-brand">
                <div class="panel-heading">
                    <h3>Software / Firmware information </h3>
                </div>
                <div class="panel-body">
                        
                        <div class="grid">
                            <div class="col col-sm-6">
                              <h4>File Detail</h4>
                              <?php

                                if ($file->filesize > 0) {
                                            $size = (int) $file->filesize;
                                            $base = log($size) / log(1024);
                                            $suffixes = array(' bytes', ' KB', ' MB', ' GB', ' TB');

                                            $filesize = round(pow(1024, $base - floor($base)), 2) . $suffixes[floor($base)];
                                } else {
                                           $filesize =  $size;       
                                }
                            ?>
                                <div class="field field-group clearfix">
                                   <label>File name  </label>
                                   <p>{{ $file->filename }}</p>
                                </div>
                                <div class="field field-group clearfix">
                                   <label>File size </label>
                                   <p>{{ $filesize }}</p>
                                </div>
                                <div class="field field-group clearfix">
                                   <label>Date added</label>
                                   <p>{{ $file->created_at }}</p>
                                </div>
                            </div>
                            <div class="col col-sm-6">
                                <h4>Downloads</h4>
                                <div class="field field-group clearfix">
                                   <label>Total  </label>
                                   <p>{{ $file->downloads }}</p>
                                </div>
                            </div>
                            
                        </div>
                    
                </div>
            </div>
       

@endsection


@section('js')
<script>
    function goBack() {
        window.history.back();
    }
</script>
@endsection
