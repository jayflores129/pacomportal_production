@extends('layouts.app')

@section('content')


    <div class="panel panel-default">
        <div class="panel-body">

                <div class="action action-delete">

                    <h4 class="text-info">Click the button to confirm delete</h4>

                    <p><span>Filename </span>: <span>{{ $file->filename }}</span></p>
                    <p><span>Date Created </span>: <span>{{ $file->created_at }}</span></p>

                    {!! Form::open([
                             'method' => 'delete',
                             'route' => ['files.destroy', $file->id]
                            ]) !!}
                         

                           <button type="submit" class="btn btn-danger">Delete</button>
                    {!! Form::close() !!}

                </div>
            
        </div>
    </div>


@endsection
