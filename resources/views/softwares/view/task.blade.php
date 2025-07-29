<h3 class="{{ $color }}">{{ $title }}</h3>
@if( !empty($lists) )
   <div class="inner-list">
     @foreach($lists as $list) 
       <a href="{{ url('admin/softwares') }}/{{ $list->id}}" id="{{ $list->id}}">
         <div class="single-status">
          <div class="col-left">
            <strong>Task #{{ $list->id }}</strong>
                    <p>{{ $list->summary }}</p>
                    {{ $list->product->name }}
          </div>
          <div class="col-right">
            <?php $photo = DB::table('user_details')->where('user_id', $list->assigned_to )->value('photo'); ?>
                @if( $photo )
                  <div class="photo">
                    <img src="{{ asset('images/uploads/' . $photo ) }}"  width="100%" />
                  </div>
                @else
                  <div class="photo">
                    <img src="{{ asset('/public/images//user-placeholder.png') }}" width="100%" />
                  </div>
                @endif
          </div>
        </div>  
      </a>  
     @endforeach
   </div>
@endif 