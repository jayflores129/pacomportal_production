@extends('layouts.app')

@section('content')
  @if(Auth::user()->hasRole(['admin', 'super admin', 'SPG Internal User']))
    <div class="panel panel-top">
        <div class="grid justify-space-between">
            <div class="col">
              {!! Breadcrumbs::render('certificates') !!}
            </div>
            <div class="col text-right">
              <a href="{{ url('certificates/create') }}" class="btn-brand-icon btn-brand-primary btn-brand"><i class="fa fa-upload"></i><span>Upload Certificate</span></a>
            </div>
        </div>
    </div> 
  @endif
  @include('components/flash')
  <div class="panel panel-default panel-brand">
    <div class="panel-heading"><h3>List of Certificates</h3></div>
    <div class="panel-body">
        <div class="table-responsive">                         
         <table class="table table-default-brand">
            <thead>
               <tr>
                   <th>Filename</th>
                   <th>Filesize</th>
                   <th>Date Added</th>
                   <th width="380">Action</th>
               </tr>
              </thead>
               @if($files)
                <tbody>
                @foreach($files as $file)
      
                  <tr id="{{ $file->id }}">
                      <?php 

                        $doc_name = DB::table('documents')->where('file_id', $file->id)->value('name');

                            if ($file->filesize > 0) {
                                    $size = (int) $file->filesize;
                                    $base = log($size) / log(1024);
                                    $suffixes = array(' bytes', ' KB', ' MB', ' GB', ' TB');

                                    $filesize = round(pow(1024, $base - floor($base)), 2) . $suffixes[floor($base)];
                            } else {
                                   $filesize =  $size;
                            }
                          ?>
                       <td>
                       
                        <strong>{{ $file->name }}  
                            @if(Auth::user()->isAdmin()) 
                             <a href="{{ URL::to('/certificates/edit-name', ['id' => $file->id] ) }}" class="sm-text">Edit</a>
                             @endif 
                        </strong>
                       
                       </td>
                       <td>{{ $filesize }}</td>
                       <td>{{ date('F d, Y', strtotime($file->created_at))  }}</td>
                       <td>
                            <ul class="list-inline">
                                  <li><a href="{{ route('files.show', $file->id)}}" class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-eye btn-icon"></i><span>View</span></a></li>
                                  <li><a href="{{ URL::to('/download/' . $file->id ) }}" target="_blank" download class="btn-brand btn-brand-icon btn-brand-success downloadable-cert"><i class="fa fa-download btn-icon"></i><span>Download</span></a></li>
                                  @if(Auth::user()->isAdmin())
                                    <li>{!! Form::open([ 'method' => 'delete','route' => ['files.destroy', $file->id] ]) !!}
                                        <button type="submit" class="btn-brand btn-brand-danger btn-brand-icon"><i class="btn-icon fa fa-close"></i> <span>Delete</span></button>
                                    {!! Form::close() !!}</li>
                                  @endif
                                </ul>
                       </td>
                  </tr>

                  @endforeach
                </tbody>  
               @else
                 <tr>
                    <td colspan="4">No Data Found</td>
                 </tr>
               @endif

           </table>
          </div>             
    </div>
</div>
@endsection

@section('css')
<style>
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

        $('.downloadable-cert').on('click', function(){

            var filename = $(this).closest('tr').find('td:nth-child(1)').text();
            

            $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type: 'post',
              url: '{{ URL::to('certificate-downloaded') }}',
              data   : {
                'file' : true,
                'certificate' : filename
              },
              success : function(data){
               
              },
              error : function(err) {
                console.log(err);
              }  

            });

        });


    })(jQuery) 

 </script>
@stop