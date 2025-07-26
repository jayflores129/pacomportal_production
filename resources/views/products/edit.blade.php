@extends('layouts.app')

@section('content')
    @if(Auth::user()->isAdmin())
      <div class="panel panel-top">
        <div class="row">
          <div class="col-sm-6">
            {!! Breadcrumbs::render('addProducts') !!} 
          </div>
          <div class="col-sm-6 text-right">
            
          </div>
        </div>
      </div> 
    @endif
    <div class="panel panel-default panel-brand">
        <div class="panel-heading">
          <h3>Edit Product</h3>
        </div>
        <div class="panel-body">
           <div class="form row">
              <div class="col-sm-10">
               @include('components/errors')
               
               @if($product)


                {!! Form::model($product, [
                                   'method' => 'patch',
                                   'route' => ['products.update', $product->id],

                     ]) !!}

                      <div class="form-group">
                        <label for="exampleInputEmail1">Product Name ( read-only )</label>
                        <input type="text" class="form-control" name="name"  value="{{ $product->name }}" required readonly="readonly" />
                      </div>
                      <div class="form-group">
                        <label for="exampleInputEmail1">Description</label>
                        <textarea type="text" name="description" rows="20" id="myeditorinstance">{!! $product->description !!}</textarea>
                      </div>

                  
                      <button type="submit" class="btn-brand btn-brand-icon btn-brand-success"><i class="fa fa-check btn-check"></i><span>Update Product</span></button>
                  {!! Form::close() !!}

                @endif 
               </div> 
           </div>
        </div>
    </div>

  <script src="https://cdn.tiny.cloud/1/lqy9t8jupdqf4113n0tg5g3sj2sa9s24j2tdrrf6fwyxor5x/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

  <script>
    tinymce.init({
      selector: 'textarea#myeditorinstance',
      plugins: [
        'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
        'checklist', 'mediaembed', 'casechange', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'editimage', 'advtemplate', 'ai', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown','importword', 'exportword', 'exportpdf'
      ],
      toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
      tinycomments_mode: 'embedded',
      tinycomments_author: 'Author name',
      mergetags_list: [
        { value: 'First.Name', title: 'First Name' },
        { value: 'Email', title: 'Email' },
      ],
      ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
      height: 800,
    });
  </script>

@endsection
