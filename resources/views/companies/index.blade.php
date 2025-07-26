@extends('layouts.app')

@section('content')

    @if (Auth::user()->isAdmin())
        <div class="panel panel-top">

            <div class="row">

                <div class="col-sm-6">

                    {!! Breadcrumbs::render('allCompany') !!}

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

                        <h3>All Companies</h3>

                    </div>

                    <div class="panel-body">

                        <div class="search-section" style="margin-bottom: 20px;max-width: 400px;">

                            <p>Quick Search </p>

                            <form style="display: flex;justify-content: space-between;">

                                <div style="width: calc(100% - 140px);margin: 0;">

                                    <input type="text" id="search" name="search" class="placeholder"
                                        value="{{ request('search') }}" style="margin: 0;height: 35px;" />

                                </div>

                                <button type="submit" class="btn-brand btn-brand-primary btn-brand-icon"
                                    style="width: 135px;"><i class="btn-icon fa fa-refresh"></i>
                                    <span>Search</span></button>

                            </form>

                        </div>

                        <div class="table-responsive">

                            <table class="table table-default-brand">

                                <thead>

                                    <tr>

                                        <th width="400"
                                            style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd;">
                                            Name

                                            <div style="display: inline-block;margin-left: 10px;float: right;"
                                                class="sortTable">

                                                <a href="{{ route('companies.index') }}?sortby=name&sort=ASC" target="_self"
                                                    style="display: block;"><span class="fa fa-sort-up"
                                                        style="height: 8px;line-height: 1;display: block;"></span></a>
                                                <a href="{{ route('companies.index') }}?sortby=name&sort=DESC"
                                                    target="_self" style="display: block;"><span class="fa fa-sort-down"
                                                        style="height: 8px;line-height: 1;display: block;"></span></a>

                                            </div>

                                        </th>

                                        <th
                                            style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd;">
                                            Description</th>



                                        <th
                                            style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd;">
                                            Telephone No</th>

                                        <th
                                            style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd;">
                                            Total users</th>

                                        <th
                                            style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd;">
                                            Date Added</th>

                                        <th style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd;"
                                            width="250">Action</th>

                                    </tr>

                                </thead>

                                @foreach ($companies as $company)
                                    <tr>

                                        <td>{{ $company->name }}</td>

                                        <td>{{ $company->description }}</td>

                                        <td>{{ $company->telephone_no }}</td>

                                        <td>{{ DB::table('users')->where('company_id', '=', $company->id)->count() }}</td>

                                        <td>{{ date('F d, Y', strtotime($company->created_at)) }}</td>

                                        <td>

                                            <ul class="list-inline">
                                                <li><a href="{{ url('admin/companies') }}/{{ $company->id }}"
                                                        class="btn-brand btn-brand-icon btn-brand-success"><i
                                                            class="fa fa-eye"></i><span>View</span></a></li>
                                                <li><a href="{{ route('companies.edit', $company->id) }}"
                                                        class="btn-brand btn-brand-icon btn-brand-primary"><i
                                                            class="fa fa-pencil"></i><span>Edit</span></a></li>
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
        ..table-default-brand>thead>tr>th {
            background: #f5f5f5;
        }

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
    </style>
@stop

@section('js')

    <script>
        $('.sortTable a').on('click', function(e) {

            console.log('test');

            e.preventdefault();

            //var link = $(this).attr('href');

            //window.location.href = link;


        });


        $('#search').on('keyup', function() {
            return;
            var value = $(this).val();
            var link = '{{ URL::to('admin/search-companies') }}';

            $('.sortTable').hide();

            $.ajax({
                type: 'get',
                url: link,
                data: {
                    'search': value
                },
                success: function(data) {

                    console.log(data);
                    var contacts = data['contacts'];
                    var total_items = data['contacts'].length;
                    var output = '';

                    if (value != '') {

                        if (total_items > 0) {

                            for (var x = 0; x < total_items; x++) {

                                output += listView(data['contacts'][x]);
                            }

                        } else {

                            output = '<tr><td colspan="6">No data found</td></tr>';

                        }

                        $('tbody').html(output);
                        $('.pagination').hide();

                    } else {

                        var total_items = data['contacts']['data'].length;
                        if (total_items > 0) {

                            $('.sortTable').show();

                            for (var x = 0; x < total_items; x++) {

                                output += listView(data['contacts']['data'][x]);
                            }

                        } else {

                            output = '<tr><td colspan="5">No data found</td></tr>';

                        }

                        $('tbody').html(output);
                        $('.pagination').show();

                    }

                }

            });

        });

        function listView(contact = []) {

            var output = '';
            var id = contact['id'];
            var date = contact['created_at'];
            var name = contact['name'];
            var description = (contact['description']) ? contact['description'] : '';
            var phone = (contact['telephone_no']) ? contact['telephone_no'] : '';
            var users = contact['users'];
            var address = (contact['address']) ? contact['address'] : '';



            var view_url = '{{ url('admin/companies') }}' + '/' + id;
            var view_edit = '{{ url('admin/companies') }}' + '/' + id + '/edit';

            output += '<tr>';
            output += '<td>' + name + '</td>';
            output += '<td>' + description + '</td>';
            output += '<td>' + phone + '</td>';
            output += '<td>' + users + '</td>';
            output += '<td>' + date + '</td>';
            output += '<td>';
            output += '<ul class="list-inline">';

            output += '<li><a href="' + view_url +
                '" class="btn-brand btn-brand-icon btn-brand-success"><i class="fa fa-eye"></i><span>View</span></a></li>';

            output += '<li><a href="' + view_edit +
                '" class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-pencil"></i><span>Edit</span></a></li>';

            output += '</ul>';
            output += '<td>';
            output += '</tr>';


            return output;
        }
    </script>
@stop
