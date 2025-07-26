@extends('layouts.app')

@section('content')
<section class="customer-section"> 
    @component('components/panel')
      @slot('title')
         MOnitoring Downloads
      @endslot
      
       
       @component('components/table')
          @slot('heading')
             <td><strong>Index</strong></td>
             <td><strong>Filename</strong></td>
             <td><strong>Company</strong></td>
             <td><strong>Name</strong></td>
             <td width="150"><strong>Date</strong></td>
          @endslot
              @php $count = 1; @endphp
              @foreach($downloads as $download)
                
                <tr>
                   <td>{{ $count++ }}</td>
                   <td>{{ DB::table('files')->where('id', $download->file_id )->value('filename') }}</td>
                   <td>{{ $download->company_name }}</td>
                   <td>{{ DB::table('users')->where('id', $download->user_id )->value('firstname')  . " " . DB::table('users')->where('id', $download->user_id )->value('lastname') }}</td>
                   <td>{{ $download->created_at }}</td>
                </tr>
              @endforeach

       @endcomponent

       @if (isset($pagination))
          <nav aria-label="Page navigation example">
              <ul class="pagination" style="margin: 0;">
                  @foreach ($pagination as $key => $link)
                      <li class="page-item">
                          @if ($key === 0)
                              <a class="page-link" href="{{ $link->url }}"><<</a>
                          @elseif (count($pagination) == $key + 1)
                              <a class="page-link" href="{{ $link->url }}">
                                  >>
                              </a>
                          @else
                              <a class="page-link" 
                                  style="{{ $link->active ? 'font-weight:bold;background:#3097d1;color:#fff;' : 'color:black;' }}" href="{{ $link->url }}"
                              >
                                  {{ $link->label }}
                              </a>
                          @endif
                      </li>
                  @endforeach
              </ul>
          </nav>
      @endif

    @endcomponent 
</section>
@endsection


@section('css')
<style>
.repair-log {
  max-height: 400px;
  overflow-y: scroll;
}
.list-of-options li {
    display: inline-block;
}
h4.label.label-success {
    font-size: 15px;
}
  .float-right {
    float: right;
  }
  .comment {
    padding: 10px;
    border: 1px solid #f5f3f3;
    margin-bottom: 5px;
    background: #f7f6f6;
  }
  .comment-form {
      margin-top: 20px;
      border-top: 1px solid #f3eded;
      padding-top: 10px;
  }
  .comment-form:after {
    content: '';
    clear: both;
    display: block;
  }
  .comment-form button[type="submit"] {
    border-bottom: 2px solid #2b8e2b !important;
    height: 30px;
    line-height: 1;
  }
  .comment .desc {
    font-weight: 600;
    color: #333;
  }
  h3.heading-rma {
    margin: 0;
    padding: 14px 10px;
    background: #2d2d2d;
    color: #fff;
    font-family: 'verdana','sans-serif';
    font-weight: 600;
    font-size: 16px;
}
.comment .date_created {
    color: #c5c4c4;
}
.form {
}
.form label {
    border-bottom: 1px solid #f1efef;
    color: #222;
    font-weight: 600;
    display: block;
}
.history-section .panel-body {
    height: 400px;
    overflow-y: scroll;
}
.form-group {
    margin-bottom: 15px;
    padding: 5px 0 15px;
    border-bottom: 1px solid #f9f5f5;
}
.btn-default {
  background: #f5f5f5;
  padding: 5px;
  line-height: 1;
  border-radius: 4px;
  display: inline-block;
}
.btn-default:hover {
  background-color: inherit;
}
.btn-open {
    background: #fb9210;
    color: #000;
}
.btn-ps {
  background: #004181;
  color: #fff;
}
.btn-cs {
    background: #2ba01c;
    color: #fff;
}
.btn-r {
    background: #dc3030;
    color: #fff;
}
.btn-rp {
    background: #ebef1a;
    color: #000;
}
.btn-rt {
    background: #ae68d2;
    color: #fff;
}
label {
  display: block;
} 
.form-group p {
  min-height: 20px;
}
</style>
@stop