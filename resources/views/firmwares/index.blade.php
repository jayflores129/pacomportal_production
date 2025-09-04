@extends('layouts.app')

@section('content')
    
      <div class="panel panel-top">
          <div class="grid justify-space-between">
              <div class="col">
                {!! Breadcrumbs::render('firmwares') !!}
              </div>

              @if(Auth::user()->hasRole(['admin', 'super admin', 'SPG Internal User']))
                <div class="col text-right">
                  <a href="{{ url('firmwares/create') }}" class="btn-brand-icon btn-brand btn-brand-primary"><i class="fa fa-upload"></i><span>Upload Firmware</span></a>
                </div>
              @else 
                <div style="display: flex;align-items:center; gap: 10px;padding:4px 0;">
                  <label class="switch">
                    <input type="checkbox" name="notify_firmware" id="notify" {{ Auth::user()->notify_firmware == 1 ? 'checked' : '' }}>
                    <span class="slider round"></span>
                  </label>
                  <span>Notify me for new updates</span>
                </div>
              @endif
          </div>
      </div> 
    
    @include('components/flash')
    <div class="panel panel-default panel-brand"> 
      <div class="panel-heading"><h3>List of Softwares And Firmwares</h3></div>  
      <div class="panel-body">
            <?php 
              $collection = json_decode($files_collection);
              $cache_category   = '';
              $cache_release    = '';
            ?>

          @if($collection)
            
            @foreach($collection as $key => $value) 

              <h4 class="collection-heading">
                 {{ DB::table('categories')->where('id', $key)->value('name') }}
                 @if(Auth::user()->isAdmin()) 
                      <a href="{{ url('firmwares/edit-category', ['id' => $key]) }}" class="sm-text">Edit</a>
                 @endif
              </h4>

                 @if($value)
                   <div class="table-responsive">
                      <table class="table table-default-brand">
                        <thead>
                          <tr>
                            <th width="200">Release</th>
                            <th width="200">Version</th>
                            <th>Filename</th>
                            <th width="200">Date</th>
                            <th width="350">Action</th>
                          </tr>
                        </thead>
                      
                        @foreach($value as $item)
                          <tr>
                            <td>
                                @if( $cache_release != $item->release )
                                <strong>{{$item->release}}</strong> <a href="{{ url('firmwares/edit-release', ['id' => $item->release_id]) }}" class="sm-text">Edit</a>
                               <?php $cache_release  = $item->release; ?>
                           @endif 

                            </td>
                            <td>{{$item->version}}</td>
                            <td>
                              @if(!empty( $item->name) )
                                {{ $item->name }} 
                               @else
                                {{ $item->filename }} 
                               @endif 
                               @if(Auth::user()->isAdmin() && $item->latest ) 
                                   <span class="fa fa-star latest-file"></span>
                              @endif
                              <a href="{{ route('firmwares.show', $item->id) }}" title="view info"><span class="fa fa-info"></span></a>
                              @if(Auth::user()->isAdmin()) 
                                <a href="{{ route('firmwares.edit', ['firmware' => $item->id ]) }}" class="sm-text">Edit</a>
                              @endif                                        
                             </td>
                            <td>{{ date('d/m/Y', strtotime($item->created_at))  }}</td>
                            <td><?php $document = DB::table('documents')->where('file_id', $item->id)->value('name');  ?>
                            <ul class="list-inline">
                              @if( $item->filelink )
                                <li><a href="{{ $item->filename }}" target="_blank" class="btn-brand btn-brand-success btn-brand-icon downloadable-file" download="{{ $item->filename }}"><i class="fa fa-download"></i><span>Download</span></a></li>
                              @else
                                <li><a href="{{ URL::to('/download/' . $item->id ) }}" target="_blank" class="btn-brand btn-brand-success btn-brand-icon downloadable-file" download="{{ $item->filename }}"><i class="fa fa-download"></i><span>Download</span></a></li>
                              @endif
                              
                              <li><a href="{{ URL::to('/download-document/' . $document ) }}" target="_blank" class="btn-brand btn-brand-success btn-brand-icon downloadable-document" download><i class="fa fa-file-text-o"></i><span>Document</span></a></li>
                              @if( Auth::user()->isAdmin() )
                                <li>{!! Form::open([
                                   'method' => 'delete',
                                   'route' => ['files.destroy', $item->id]
                                  ]) !!}
                                  <button type="submit" class="btn-brand btn-brand-danger btn-brand-icon"><i class="btn-icon fa fa-close"></i> <span>Delete</span></button>
                                  {!! Form::close() !!}
                                </li>
                              @endif
                            </ul></td>
                          </tr>
                              
                
                        @endforeach
                    </table>
                  </div>
                @endif           


            @endforeach 
          @endif

      </div>
    </div>
@endsection


@section('css')
<style>
  .latest-file {
      color: #f5795b;
  }
  .table-default-brand > thead > tr > th {
    font-weight: 500 !important;
    color: #8a8585;
  }
  .list-inline li {
    vertical-align: top;
  }
  span.fa.fa-info {
      color: #1b9c32;
      margin-left: 7px;
      background: #dcf7e1;
      border-radius: 50%;
      width: 15px;
      height: 15px;
      text-align: center;
      font-size: 10px;
      line-height: 15px;
  }
  .sm-text {
    font-size: 12px;
    margin-left: 5px;
  }
  .list-inline {
    margin-left: 0;
    display: flex;
}
</style>
    
@stop


@section('js')
 <script>
    
    (function($){

        $('.downloadable-file').on('click', function(){

            var filename = $(this).closest('tr').find('td:nth-child(3)').text();

           
            $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type: 'post',
              url: '{{ URL::to('file-downloaded') }}',
              data   : {
                'downloaded' : true,
                'filename' : filename
              },
              success : function(data){
                  console.log(data);
              },
              error : function(err) {
                console.log(err);
              }  

            });

        });


        $('.downloadable-document').on('click', function(){

            var docname = $(this).closest('tr').find('td:nth-child(3)').text();

           
            $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type: 'post',
              url: '{{ URL::to('document-downloaded') }}',
              data   : {
                'downloaded' : true,
                'docname' : docname
              },
              success : function(data){
                  console.log(data);
              },
              error : function(err) {
                console.log(err);
              }  

            });

        });

    })(jQuery) 

    document.getElementById('notify')?.addEventListener('click', async (e) => {
      const { checked } = e.target;

      var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content')

      const formData = new FormData();
      formData.append('_token', CSRF_TOKEN);
      formData.append('notify', checked ? 1 : 0)
      formData.append('name', e.target.name)
      
      const res = await fetch("{{URL::to('/user/document-notification')}}", {
        method: 'post',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': CSRF_TOKEN
        }
      })
      const json = await res.json();

      if (json.success) {
        location.reload()
      }
    })

 </script>
@stop
