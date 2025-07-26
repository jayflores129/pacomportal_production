@extends('layouts.app')

@section('content')


    @include('repairs/components/navigation')
    {{-- @include('repairs/components/advance-search') --}}
    <div class="row">
        <div class="col-sm-12">

            <div class="panel panel-default panel-brand">
                <div class="panel-heading">
                    <h3>All Repairs</h3>
                </div>
                <div class="panel-body">
                    @include('repairs/components/filter')
                    <div class="table-responsive">
                        <table class="table table-default-brand table-striped ">
                            <thead>
                                <tr>
                                    <th
                                        style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd; padding: 0;">
                                        <button class="sort-button" data-query="id">
                                            <span>RMA #</span>
                                            <i class="fa fa-sort"></i>
                                        </button>
                                    </th>
                                    <th
                                        style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd; padding: 0;">
                                        <button class="sort-button" data-query="requester_name">
                                            <span>Name</span>
                                            <i class="fa fa-sort"></i>
                                        </button>
                                    </th>
                                    <th
                                        style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd; padding: 0;">
                                        <button class="sort-button" data-query="requester_company">
                                            <span>Company</span>
                                            <i class="fa fa-sort"></i>
                                        </button>
                                    </th>
                                    <th
                                        style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd; padding: 0;">
                                        <button class="sort-button" data-query="status">
                                            <span>Status</span>
                                            <i class="fa fa-sort"></i>
                                        </button>
                                    </th>
                                    <th
                                        style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd; padding: 0;">
                                        <button class="sort-button" data-query="po_number">
                                            <span>PO Number</span>
                                            <i class="fa fa-sort"></i>
                                        </button>
                                    </th>
                                    <th
                                        style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd; padding: 5px;">
                                        Total Faulty Items</th>
                                    <th
                                        style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd; padding: 0;">
                                        <button class="sort-button" data-query="requested_date">
                                            <span>Date Requested</span>
                                            <i class="fa fa-sort"></i>
                                        </button>
                                    </th>
                                    <th
                                        style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd; padding: 0;padding: 5px;">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @if ($repairs != '') --}}
                                @if ($repairs->count() > 0)
                                    <?php $count = 0; ?>
                                    @foreach ($repairs as $repair)
                                        <?php
                                        $count++;
                                        switch ($repair->status) {
                                            case 'Under Reviewed':
                                                $class = 'btn-default btn-open';
                                                $rstatus = 'Open';
                                                break;
                                            case 'Open':
                                                $class = 'btn-default btn-open';
                                                $rstatus = 'Open';
                                                break;
                                            case 'Confirmed':
                                                $class = 'btn-default btn-cs';
                                                $rstatus = 'Confirmed';
                                                break;
                                            case 'Under Review':
                                                $class = 'btn-default btn-open';
                                                $rstatus = 'Open';
                                                break;
                                            case 'Received':
                                                $class = 'btn-default btn-r';
                                                $rstatus = 'Received';
                                                break;
                                            case 'To Be Confirmed':
                                                $class = 'btn-default btn-ps';
                                                $rstatus = 'To Be Confirmed';
                                                break;
                                        
                                            case 'Submitted':
                                                $class = 'btn-default btn-cs';
                                                $rstatus = 'Submitted';
                                                break;
                                        
                                            case 'Completed':
                                                $class = 'btn-default btn-r';
                                                $rstatus = 'Completed';
                                                break;
                                            case 'Partially Shipped':
                                                $class = 'btn-default btn-rp';
                                                $rstatus = 'Shipped';
                                                break;
                                            case 'Shipped':
                                                $class = 'btn-default btn-rp';
                                                $rstatus = 'Shipped';
                                                break;
                                        
                                            case 'Cancelled':
                                                $class = 'btn-default btn-rt';
                                                $rstatus = 'Cancelled';
                                                break;
                                        
                                            default:
                                                $class = '';
                                                break;
                                        } ?>
                                        <tr>
                                            <td>R{{ $repair->id }}</td>
                                            <td>{{ $repair->requester_name }}</td>
                                            <td>{{ $repair->requester_company }}</td>


                                            <td><span class="{{ $class }}">{{ $rstatus }}</span></td>
                                            <td><span class="">{{ $repair->po_number }}</span></td>
                                            <td>{{ $repair->items->count() }}</td>
                                            <td>{{ $repair->requested_date ? date('d/m/Y', strtotime(str_replace('/', '-', $repair->requested_date))) : 'N/A' }}
                                            </td>
                                            <td width="150">
                                                <a href="{{ route('repairs.show', $repair->id) }}"
                                                    class="btn btn-sm btn-primary"><span
                                                        class="fa fa-eye"></span> View Details</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8">No data found</td>
                                    </tr>

                                @endif

                                {{-- @endif --}}
                            </tbody>

                        </table>
                    </div>
                    <div class="grid justify-space-between grid-search-wrapper">
                        <div class="col">
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
                        </div>
                    </div>


                </div>
            </div>

        </div>
    </div>

    <script>
        let sort_by = '{{ isset($_GET['sort_by']) ? $_GET['sort_by'] : '' }}';

        document.querySelector('.pagination').querySelectorAll('ul li a').forEach(elem => {
            elem.addEventListener('click', (e) => {
                e.preventDefault();
                const page = e.target.getAttribute('href').split('page=')[1];
                const search = new URLSearchParams(location.search);
                search.set('page', page);
                location.href = "/repairs?" + search.toString();
            })
        });

        Array.from(document.querySelectorAll('.sort-button')).forEach(elem => {
            elem.addEventListener('click', (e) => {
                const {
                    query
                } = e.currentTarget.dataset;

                const search = new URLSearchParams(location.search);
                search.set('order_by', query);

                const sortDirection = search.get('sort_direction');

                const dir = {
                    desc: 'asc',
                    asc: 'desc'
                };

                if (!sortDirection) {
                    search.set('sort_direction', 'asc');
                } else {
                    search.set('sort_direction', dir[sortDirection]);
                }

                location.href = "/repairs?" + search.toString();

                //Get status sort order
                //get show results
                //Update pagination

            })
        });
    </script>

@endsection

@section('css')
    <style>
        .pagination-links {
            display: flex;
            column-gap: 10px;
        }

        .pagination {
            display: inline-block;
            padding-left: 0;
            margin: 0;
            border-radius: 4px;
        }

        .col.q-search-box {
            display: flex;
            align-items: flex-start;
            column-gap: 3px;
        }

        .col.q-search-box select {
            margin-bottom: 0;
        }

        select#quick-search-type {
            height: 34px;
            margin-top: 0;
        }

        .flash-message {
            position: fixed;
            bottom: 0;
            right: 27px;
            opacity: 0.9;
        }

        button#submitDelForm {
            position: absolute;
            top: 0;
            right: 15px;
            border-radius: 3px;
            background: #eb5c5c;
            z-index: 999;
        }

        .rma-group {
            display: flex;
            align-items: center;
            min-width: 450px;
            border: 1px solid #f3f3f3;
            margin-bottom: 15px;
        }

        .group-label {
            font-size: 14px;
            padding: 5px 10px;
        }

        .group-dropdown {
            width: calc(100% - 160px);
        }

        .group-dropdown select {
            margin: 0 !important;
            width: 100% !important;
            border: 0 !important;
            background: transparent !important;
            height: 36px !important;
            border-left: 1px solid #f3f3f3 !important;
            border-right: 1px solid #f3f3f3 !important;
            border-radius: 0 !important;
        }

        .group-edit {
            width: 100px;
        }

        .group-edit button {
            border: 0;
            padding: 8px 6px;
            background: transparent;
            font-size: 13px;
        }

        #clearBtn {
            width: 42px !important;
            min-width: auto !important;
        }

        .sort-button {
            width: 100%;
            padding: 0;
            border: 0;
            /* background: red; */
            display: flex;
            gap: 10px;
            justify-content: space-between;
            align-items: center;
            padding: 5px;
        }

        .repair-tab {
            min-height: auto;
        }

        .repair-tab .panel-header {
            margin: 0;
            padding: 10px 20px;
            background: #2680d0;
        }

        .repair-tab .panel-header .heading {
            font-size: 1.2em;
            background: #2680d0;
            border: 2px solid #2680d0;
            color: #fff;
            margin: 0;
        }

        .repair-tab.top-search {
            background: #fff;
            border-radius: 0;
            border: 0;
        }

        select#totalItems {
            padding: 5px;
            border: 1px solid #ddd;
            width: 50px;
            height: 31px;
        }

        input#search {
            height: 35px;
            margin-bottom: 7px;
            margin-right: 10px;
            min-width: 200px;
        }

        #advancedSearch {
            margin-top: 29px;
            width: 100%;
            height: 44px;
        }

        #advancedSearch i {
            height: 45px;
            line-height: 28px;
        }

        #advancedSearch span {
            font-size: 17px;
            height: 45px;
            line-height: 45px;
        }

        .btn-default {
            background: #f5f5f5;
            padding: 5px;
            line-height: 1;
            border-radius: 4px;
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

        .table-nav {
            margin-bottom: 10px;
        }

        .advanced-search:focus {
            outline: none;
        }

        .advanced-search label {
            display: block;
        }

        .advanced-search input,
        .advanced-search select {
            margin: 5px 0 10px 0;
        }

        .advanced-search .panel-body {
            padding: 10px 15px;
        }

        .repair-tab.top-search {
            background: rgba(60, 58, 58, 0.7);
            border-radius: 0;
            border: 0;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1000;
        }

        .repair-tab.top-search .panel-body {
            max-width: 500px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 100;
            background: #fff;
            box-shadow: 0 9px 17px #4e4c4c;
            padding: 40px 20px;
        }

        #closeAdvanced {
            position: absolute;
            top: 0;
            right: 0;
            background: transparent;
            border: 0;
            background: #fff;
            border-radius: 50%;
            padding: 10px;
            z-index: 10;
        }

        #closeAdvanced:focus {
            outline: none;
        }

        #clearBtn {
            margin-bottom: 5px;
            width: 120px;
            padding: 0 5px;
        }

        #clearBtn span {
            width: 100%;
        }

        .search-filter-group {
            background: #f7f7f7;
            padding: 10px;
            margin-bottom: 10px;
        }

        .search-rma-section select,
        .search-rma-section input,
        .search-rma-section select,
        .search-rma-section .form-group {
            margin-bottom: 0 !important;
            height: 30px !important;
        }

        .search-rma-section label {
            font-size: 13px;
            margin-top: 8px;
            margin-bottom: 0px;
        }

        .col-md-12.date-range span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        button#submitForm {
            margin-top: 10px;
        }

        .col-md-12.btn-filter-group {
            display: flex;
            justify-content: flex-end;
        }

        @media screen and (max-width: 767px) {
            .repair-tab.top-search .panel-body {
                padding: 10px;
                width: calc(100% - 20px);
            }
        }
    </style>
@stop
@section('js')

    <script>
        var status = '',
            product = '',
            value = '',
            company = '';

        //Product Change
        $('#repairProduct').on('change', function() {
            product = $(this).find('option:selected').text();

            if (product == 'Select') {
                product = '';
            }
        });

        //Repair Status Change
        $('#repairStatus').on('change', function() {
            status = $(this).prop('selected', true).val();
        });


        //Repair Status Change
        $('#groupFilter').on('change', function() {
            status = $(this).prop('selected', true).val();
            window.location = '{{ URL::to('repairs') }}?filter_id=' + $(this).val();
        });


        //Toggle show #groupEdit
        $('#groupEdit').on('click', function() {
            $('#editSearch').toggleClass('hide');
            $('#groupEdit .edit-filter').toggleClass('hide');
            $('#groupEdit .hide-filter').toggleClass('hide');
            $('#newSearch').addClass('hide');
        });
        //create new filter
        $('#createGroup').on('click', function(e) {
            e.preventDefault();
            $('#newSearch').toggleClass('hide');
            $('#editSearch').addClass('hide');
        });


        const RmaIDs = localStorage.setItem("rmaIDs", JSON.stringify({{ $rma_ids }}));
        //console.log(localStorage.getItem("rmaIDs"));

        $('#searchSubmit').on('click', function(e) {

            e.preventDefault();
            $('.pagination').hide();

            company = $('#companyName').val();

            //SearchController
            $.ajax({
                type: 'get',
                url: '{{ URL::to('searchRepair') }}',
                data: {
                    'search': '',
                    'status': status,
                    'product': product,
                    'company': company
                },
                success: function(data) {
                    console.log(data);

                    $('tbody').html(data);
                }
            });

        });

        $('#quick-search-type').on("change", function() {
            triggerSearch($(this).val())
        })
        $('#search').on('keyup', function() {
            triggerSearch($(this).val());
        });

        function triggerSearch(item) {
            return;
            var value = item;
            var items = $('#totalItems option:selected').val();
            var link =
                '{{ Auth::user()->isAdmin() === false ? URL::to('searchUserRepair') : URL::to('searchRepair') }}';
            var isRMAId = false;
            var search_type = $('#quick-search-type').val();

            //check if R is at first index
            if (value.charAt(0) == "R" || value.charAt(0) == "r") {
                var isRMAId = true;
            }
            if (isRMAId == true) {
                value = value.substring(1);
            }


            $.ajax({
                type: 'get',
                url: link,
                data: {
                    'search': value,
                    'items': items,
                    'search_type': search_type

                },
                success: function(data) {

                    var data = data['repairs'];
                    var total = data['data'].length;
                    var current_page = data['current_page'];
                    var last_page = data['last_page'];
                    var output = '';
                    var paginate = '';

                    if (total > 0) {

                        for (var x = 0; x < total; x++) {
                            output += listView(data['data'][x]);
                        }

                    } else {
                        output2 = '<tr><td colspan="8">No Data</td></tr>';
                    }


                    if (last_page > 1) {

                        paginate += '<ul class="pagination pagination-ajax">';

                        for (var a = 1; a <= last_page; a++) {

                            if (current_page === a) {
                                paginate += '<li class="active"><span id="' + a + '" class="page-link">' + a +
                                    '</span></li>';
                            } else {
                                paginate += '<li><a href="javascript:void(0)" id="' + a +
                                    '" class="page-link">' + a + '</a></li>';
                            }

                        }

                        paginate += '</div>';
                    }


                    $('tbody').html(output);
                    $('.pagination-links').html(paginate);
                }
            });


        }

        $('.pagination-links').on('click', '.page-link', function(e) {

            e.preventDefault();

            var page_id = $(this).attr('id');
            var value = $('#search').val();
            var items = $('#totalItems option:selected').val();
            var link =
                '{{ Auth::user()->isAdmin() === false ? URL::to('searchUserRepair') : URL::to('searchRepair') }}';
            var output2 = '';
            var isRMAId = false;

            //check if R is at first index
            if (value.charAt(0) == "R" || value.charAt(0) == "r") {
                var isRMAId = true;
            }
            if (isRMAId == true) {
                value = value.substring(1);
            }
            console.log(value);
            $.ajax({
                type: 'get',
                url: link,
                data: {
                    'search': value,
                    'items': items,
                    'page': page_id,

                },
                success: function(data) {
                    console.log(data);

                    var data = data['repairs'];
                    var total = data['data'].length;
                    var current_page = data['current_page'];
                    var last_page = data['last_page'];
                    var output = '';
                    var paginate = '';



                    if (total > 0) {

                        for (var x = 0; x < total; x++) {
                            output += listView(data['data'][x]);
                        }

                    } else {
                        output2 = '<tr><td colspan="8">No Data</td></tr>';
                    }


                    if (last_page > 1) {

                        paginate += '<ul class="pagination pagination-ajax">';

                        for (var a = 1; a <= last_page; a++) {

                            if (current_page === a) {
                                paginate += '<li class="active"><span id="' + a +
                                    '" class="page-link">' + a + '</span></li>';
                            } else {
                                paginate += '<li><a href="javascript:void(0)" id="' + a +
                                    '" class="page-link">' + a + '</a></li>';
                            }

                        }

                        paginate += '</div>';
                    }


                    $('tbody').html(output);
                    $('.pagination-links').html(paginate);
                }
            });

        });

        $('#clearBtn').on('click', function(e) {
            return;
            // remove input value
            $('#search').val('')

            var value = '';
            var items = $('#totalItems option:selected').val();
            var link =
                '{{ Auth::user()->isAdmin() === false ? URL::to('searchUserRepair') : URL::to('searchRepair') }}';
            var isRMAId = false;

            //check if R is at first index
            if (value.charAt(0) == "R" || value.charAt(0) == "r") {
                var isRMAId = true;
            }
            if (isRMAId == true) {
                value = value.substring(1);
            }

            $.ajax({
                type: 'get',
                url: link,
                data: {
                    'search': value,
                    'items': items,
                    'list': true

                },
                success: function(data) {
                    console.log(data);


                    var data = data['repairs'];
                    var total = data['data'].length;
                    var current_page = data['current_page'];
                    var last_page = data['last_page'];
                    var output = '';
                    var paginate = '';


                    if (total > 0) {

                        for (var x = 0; x < total; x++) {
                            output += listView(data['data'][x]);
                        }

                    } else {
                        output = '<tr><td colspan="8">No Data</td></tr>';
                    }


                    if (last_page > 1) {

                        paginate += '<ul class="pagination pagination-ajax">';

                        for (var a = 1; a <= last_page; a++) {

                            if (current_page === a) {
                                paginate += '<li class="active"><span id="' + a +
                                    '" class="page-link">' + a + '</span></li>';
                            } else {
                                paginate += '<li><a href="javascript:void(0)" id="' + a +
                                    '" class="page-link">' + a + '</a></li>';
                            }

                        }

                        paginate += '</div>';
                    }


                    $('tbody').html(output);
                    $('.pagination-links').html(paginate);
                }
            });
        });

        function listView(ticket = []) {



            var output = '';
            var id = ticket['id'];
            var requester_name = ticket['requester_name'];
            var requester_company = ticket['requester_company'];
            var requester_phone = ticket['requester_phone'];
            var po_number = ticket['po_number'];
            var totalItems = ticket['totalItems'];
            var status = ticket['status'];
            var requested_date = ticket['requested_date'];

            output = '<tr><td>R' + id + '</td>' +
                '<td>' + requester_name + '</td>' +
                '<td>' + requester_company + '</td>' +
                '<td>' + status + '</td>' +
                '<td><span class="">' + po_number + '</span></td>' +
                '<td>' + totalItems + '</td>' +
                '<td>' + requested_date + '</td>' +
                '<td><a href="' + '/repairs' + '/' + id +
                '" class="btn btn-sm btn-primary"><span class="fa fa-eye"></span> View Details</a></td></tr>';

            return output;
        }

        function showTotalResults() {
            $('#totalItems').on('change', function() {
                var totalItems = $(this).prop('selected', true).val();
                var url = '<?php echo url('repairs'); ?>';

                window.location.replace(url + '?' + 'items=' + totalItems);
            });
        }

        function toggleSearch() {
            $('#advanced-search-button').on('click', function(e) {

                e.preventDefault();


                $('.advanced-search').removeClass('hide').addClass('show');

            });
            $('#closeAdvanced').on('click', function() {
                $('.advanced-search').removeClass('show').addClass('hide');
            });
        }

        function statusColor(status) {
            switch (status) {
                case "open":
                    color = "btn-default btn-open";
                    break;

                case "Partially Shipped":
                    color = "btn-default btn-ps";
                    break;

                case "Completely Shipped":
                    color = "btn-default btn-cs";
                    break;

                case "received":
                    color = "btn-default btn-r";
                    break;

                case "repaired":
                    color = "btn-default btn-rp";
                    break;

                case "returned":
                    color = "btn-default btn-rt";
                    break;
                case "shipped":
                    color = "btn-default btn-rt";
                    break;

                default:
                    color = "";
                    break;
            }

            return color;
        }

        function advancedSearch() {

            $('#advancedSearch').on('click', function(e) {

                e.preventDefault();

                var main_url = '<?php echo url('repairs'); ?>';
                var url = '<?php echo url('search/repairs'); ?>';
                var param = '';
                var company = $('#companyName').val();

                if (company) {
                    param = 'company=' + company;
                }
                if (product) {
                    param = 'product=' + product;
                }
                if (status) {
                    param = 'status=' + status;
                }
                // if(totalItems) {
                //   param = 'items=' + totalItems;
                // }

                //product
                if (product && status && !company) {
                    param = 'status=' + status + '&product=' + product;
                }
                if (product && !status && company) {
                    param = '&product=' + product + '&company=' + company;
                }
                //status 
                if (!product && status && company) {
                    param = 'status=' + status + '&company=' + company;
                }

                if (product && status && company) {
                    param = 'status=' + status + '&product=' + product + '&company=' + company;
                }
                // if(product && status && company && totalItems) {
                //   param = 'status=' + status + '&product=' + product + '&company=' + company + '&items=' + $totalitems;
                // }

                if (product || status || company) {
                    window.location.replace(url + '?' + param);
                } else {
                    window.location.replace(main_url);
                }

            });
        }
        showTotalResults();
        toggleSearch();
        advancedSearch();
    </script>
@stop
