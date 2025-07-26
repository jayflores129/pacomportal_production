<div class="row">
   <div class="col-lg-8">
      <div id="fileSection">
          @component('components/panel')
              @slot('title')
                Latest File Updates
              @endslot
                  
                  @component('components/table')
                        @slot('heading')
                           <th width="150">Type</th>
                           <th>Filename</th>
                           <th width="150">Category</th>
                           <th width="100">Release</th>
                           <th >Version</th>
                           <th width="100">Date</th>
                           <th width="150">Action</th>
                        @endslot

                       @if($files)

                         @php $count = 0; @endphp
                         @foreach($files as $file) 
                           @php $count++; @endphp
                            <tr class="single-task">
                              <td>
                                @php 
                                  if($file->type == '1')  {
                                    $type = 'Software/Firmware';
                                  }
                                  elseif($file->type == '2')   {
                                    $type = 'Technical Document';
                                  }
                                  elseif($file->type == '3') {
                                    $type = 'Certificate';
                                  }
                                  else   {
                                    $type = ''; 
                                  }
                                @endphp
                                 {{ $type }}
                              </td>    
                              <td>
                                 {{ $file->filename }}
                              </td>
                              <td>
                                 {{  ($file->type == 1 || $file->type == 2) ? $file->categoryName->name : '' }}
                              </td>
                              <td>
                                 {{ ($file->type == 1 || $file->type == 2) ? $file->releaseName->name : '' }}
                              </td>
                              <td>
                                 {{ $file->version }}
                              </td>
                              <td>
                                {{ date( 'm-d-y', strtotime( $file->created_at) ) }}
                              </td>
                              <td>
                                 <div class="task-option">
                                   <ul class="list-inline">
                                     <li><a href="{{ url('/download/'. $file->id) }}" class="btn-brand btn-brand-icon btn-brand-success"><i class="fa fa-download"></i><span>Download</span></a></li>
                                   </ul>
                                </div>
                              </td>
                           </tr>   
                        @endforeach
                      @else
                          <tr><td colspan="5">No Data</td></tr>
                      @endif  
                  @endcomponent

          @endcomponent
      </div>
    </div>
    <div class="col-lg-4">
      <div id="fileSection">
        <div class="panel panel-default panel-brand">
            <div class="panel-heading">
              <h3>Top Downloaded Files</h3>
            </div>
            <div class="panel-body">
                @if($topDownloads)
                  <div class="top-downloads">
                    <div class="grid heading-grid">
                       <div class="download-item">Filename</div>
                       <div class="download-item">Downloads</div>
                    </div>
                    @foreach($topDownloads as $file)
                      <div class="grid">
                         <div class="download-item">{{ $file->filename }} </div>
                         <div class="download-item">{{ $file->downloads }} </div>
                      </div>
                    @endforeach
                  </div>
                @endif
                <div id="product_chart1"></div>
            </div>
        </div>
      </div>  
     </div>
</div>
