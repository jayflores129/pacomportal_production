          @if( $categories )
              <div class="firmwares">

              @foreach( $categories as $category )
                 <?php 
                    $cat_id  = $category->id;

                    $has_cat = DB::table('files')
                                        ->where(function( $query ) use ( $cat_id) {
                                          $query->where('type', 1 )
                                                ->where('category', $cat_id);
                                        })
                                        ->count()    ?>                       
                @if( $has_cat )
                 <div class="category">
                    <div><h3>{{ $category->name }}</h3></div>
                    <div>
                        <table class="table-block">

                            @if( $releases )
                                  @foreach( $releases as $release )

                                    <?php 

                                         $id  = $release->id;
                       

                                    $has_file = DB::table('files')
                                                        ->where('category', $cat_id )
                                                        ->where( function( $query ) use ( $id ){
                                                          $query->where('release_id', $id )
                                                                ->where('type', 1); 
                                                        })
                                                        ->count(); 

                                        $file_releases = DB::table('files')
                                                          ->where('category', $cat_id )
                                                                  ->where( function( $query ) use ( $id ){
                                                                    $query->where('release_id', $id )
                                                                          ->where('type', 1); 
                                                                  })
                                                                  ->orderby('created_at', 'desc')
                                                                  ->get();               
                                       ?>
                                    @if($has_file)
                                      <tr>
                                        <td><h5>{{ $release->name }}</h5></td>

                                          @if( $file_releases )
                                          <td>
                                          <div class="table-container">
                                             @foreach( $file_releases as $file ) 

                                                @if( $file->release_id == $release->id && $file->category == $category->id )

                                                <?php 

                                                    $doc_name = DB::table('documents')->where('file_id', $file->id)->value('name');

                                                        if ($file->filesize > 0) {
                                                                $size = (int) $file->filesize;
                                                                $base = log($size) / log(1024);
                                                                $suffixes = array(' bytes', ' KB', ' MB', ' GB', ' TB');

                                                                $filesize = round(pow(1024, $base - floor($base)), 2) . $suffixes[floor($base)];
                                                        } else {
                                                               $filesize =  $size;
                                                        } ?>
                                                <div class="grid firmware-list">
                                                    <div class="column">Version {{ $file->version }}</div>
                                                    <div class="column">{{ $file->filename }}</div>
                                                    <div class="column">{{ date('F d, Y', strtotime($file->created_at))  }}</div>
                                                    <div class="column">                                          
                                                        <a href="{{ route('files.show', $file->id)}}"><span class="fa fa-eye btn btn-primary"></span></a>
                                                        <a href="{{ URL::to('/download/' . $file->filename ) }}" target="_blank" download><span class="fa fa-download  btn btn-info"></span></a>
                                                        <a href="{{ URL::to('/download-document/' . $doc_name ) }}" target="_blank" download><span class="fa fa-file-text-o  btn btn-success"></span></a>
                                                        @if(Auth::user()->hasRole(['admin', 'super admin']))
                                                        <a href="{{ route('files.edit', $file->id)}}"><span class="fa fa-trash  btn btn-danger"></span></a>
                                                        @endif
                                                    </div>
                                                </div> 
                                                @endif
                                             @endforeach
                                          </div>
                                          </td>
                                          @endif
                                      </tr>
                                      @endif
                                  @endforeach
                             @endif
                         </table>
                      </div>   
                 </div>
                 @endif

              @endforeach
            </div>
          @endif