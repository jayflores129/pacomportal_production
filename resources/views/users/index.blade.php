@extends('layouts.app')

@section('content')

    @if (Auth::user()->isAdmin())
        <div class="panel panel-top">

            <div class="row">

                <div class="col-sm-6">

                    {!! Breadcrumbs::render('allusers') !!}

                </div>

                <div class="col-sm-6 text-right">

                </div>

            </div>

        </div>
    @endif

    @include('components/flash')


    <section class="user-page">

        <div class="row">

            <div class="col-sm-12">

                <div class="panel panel-default panel-brand">

                    <div class="panel-heading">

                        <h3>All Users</h3>

                    </div>

                    <div class="panel-body">

                        @if (Auth::user()->spainTeam(Auth::user()->id) != true)
                            <div class="search-section" style="margin-bottom: 20px;">
                                <p>Quick Search </p>
                                <form style="display: flex;" action="">
                                    <input type="text" id="search" class="placeholder" name="search"
                                    value="{{ request('search') }}" />
                                    <select type="text" name="role" id="role-input" class="form-control"
                                    tyle="margin: 0;height: 35px;">
                                        <option value="">Select</option>
                                        @if ($roles)
                                            @foreach($roles as $role)
                                                <option value="{{ $role->name }}" {{ ( request('role') == $role->name ) ? 'selected': '' }}>{{ $role->text}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <button type="submit" class="btn-brand btn-brand-primary btn-brand-icon"><i class="btn-icon fa fa-refresh"></i>
                                    <span>Search</span></button>
                                    
                                </form>
                            </div>
                        @endif
                        <div class="table-responsive">

                            <table class="table table-default-brand">

                                <thead>

                                    <tr>

                                        <th
                                            style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd;">
                                            Name

                                            <div style="display: inline-block;margin-left: 10px;float: right;"
                                                class="sortTable">

                                                <a href="{{ route('users.index') }}?sortby=firstname&sort=ASC"
                                                    target="_self" style="display: block;"><span class="fa fa-sort-up"
                                                        style="height: 8px;line-height: 1;display: block;"></span></a>
                                                <a href="{{ route('users.index') }}?sortby=firstname&sort=DESC"
                                                    target="_self" style="display: block;"><span class="fa fa-sort-down"
                                                        style="height: 8px;line-height: 1;display: block;"></span></a>
                                            </div>

                                        </th>

                                        <th
                                            style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd;">
                                            Email</th>

                                        <th
                                            style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd">
                                            Company

                                            <div style="display: inline-block;margin-left: 10px;float: right;"
                                                class="sortTable">

                                                <a href="{{ route('users.index') }}?sortby=company&sort=ASC" target="_self"
                                                    style="display: block;"><span class="fa fa-sort-up"
                                                        style="height: 8px;line-height: 1;display: block;"></span></a>
                                                <a href="{{ route('users.index') }}?sortby=company&sort=DESC" target="_self"
                                                    style="display: block;"><span class="fa fa-sort-down"
                                                        style="height: 8px;line-height: 1;display: block;"></span></a>
                                            </div>

                                        </th>

                                        <th
                                            style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd;">
                                            Role</th>

                                        <th
                                            style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd;">
                                            Status

                                            <div style="display: inline-block;margin-left: 10px;float: right;"
                                                class="sortTable">

                                                <a href="{{ route('users.index') }}?sortby=status&sort=ASC" target="_self"
                                                    style="display: block;"><span class="fa fa-sort-up"
                                                        style="height: 8px;line-height: 1;display: block;"></span></a>
                                                <a href="{{ route('users.index') }}?sortby=status&sort=DESC" target="_self"
                                                    style="display: block;"><span class="fa fa-sort-down"
                                                        style="height: 8px;line-height: 1;display: block;"></span></a>

                                            </div>

                                        </th>

                                        <th
                                            style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd;">
                                            Blocked?</th>

                                        <th
                                            style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd;">
                                            Date Added</th>

                                        <th style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd;"
                                            width="350">Action</th>

                                    </tr>

                                </thead>

                                @if (count($users) == 0)
                                    <tr>
                                        <td colspan="8" align="center">No Users found</td>
                                    </tr>
                                @endif

                                @foreach ($users as $user)
                                    <?php
                                    // $role_id = DB::table('role_user')->where('user_id', $user->id)->value('role_id');
                                    // $role_name = DB::table('roles')->where('id', $role_id )->value('name');
                                    $company = DB::table('companies')->where('id', $user->company_id)->first();
                                    ?>

                                    <tr>
                                        <td>{{ $user->firstname . ' ' . $user->lastname }}</td>

                                        <td>{{ $user->email }}</td>

                                        <td>{{ $company->name ?? '-' }}</td>

                                        <td class="text-capitalize">{{ $user->getUserRole() }}</td>

                                        <td>{!! $user->status
                                            ? '<label class="label label-primary" style="font-size: 13px;font-weight: normal;">Approved</label>'
                                            : '' !!} {!! !$user->status && $user->approval_status
                                            ? '<label class="label label-danger" style="font-size: 13px;font-weight: normal;">Disapproved</label>'
                                            : '' !!}</td>

                                        <td>{!! $user->blocked != null || $user->blocked != 0 ? '<span class="label label-danger">Blocked User</span>' : '' !!}</td>

                                        <td>{{ date('F d, Y', strtotime($user->created_at)) }}</td>

                                        <td>

                                            <ul class="list-inline">

                                                <li><a href="{{ url('admin/users') }}/{{ $user->id }}"
                                                        class="btn-brand btn-brand-icon btn-brand-success"><i
                                                            class="fa fa-eye"></i><span>View</span></a></li>

                                                <li><a href="{{ route('users.edit', $user->id) }}"
                                                        class="btn-brand btn-brand-icon btn-brand-primary"><i
                                                            class="fa fa-pencil"></i><span>Edit</span></a></li>

                                                @if (Auth::user()->id != $user->id)
                                                    <li><a href="{{ route('users.confirm-delete', $user->id) }}"
                                                            class="btn-brand btn-brand-icon btn-brand-{!! $user->blocked != null || $user->blocked != 0 ? 'info' : 'danger' !!}"><i
                                                                class="fa fa-trash"></i><span>{!! $user->blocked != null || $user->blocked != 0 ? 'Unblock' : 'Block' !!}</span></a>
                                                    </li>
                                                @endif
                                            </ul>

                                        </td>

                                    </tr>
                                @endforeach

                            </table>

                        </div>

                        @if (isset($pagination))
                            <nav aria-label="Page navigation example">
                                <ul class="pagination">
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

                    </div>

                </div>

            </div>

        </div>

    </section>

@endsection


@section('css')
    <style>
        .option-nav ul {
            padding: 0;
        }

        .panel .panel-header {
            padding: 10px;
            background: #2d2d2d;
        }

        .panel .panel-header .heading {
            margin: 0;
            font-size: 1.2em;
            color: #fff;
        }

        .legend-list {
            padding: 0 0 0 10px;

        }

        .legend-list li {
            margin-bottom: 10px;
        }

        .form .radio {
            display: inline-block;
            margin: 10px 20px 10px 0;
        }

        .input-warranty input {
            margin-right: 10px;
        }

        .list-inline {
            margin-left: 0;
            display: flex;
        }
        .search-section form input,
        .search-section form select {
            margin-bottom: 0;
            height: 32px;
        }

        .search-section form input[type="text"] {
            width: 300px;
        }

        .search-section form select {
            width: 150px;
        }
        .search-section form {
            gap: 10px;
        }
    </style>
@stop

@section('js')

@stop
