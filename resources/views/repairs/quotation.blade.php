@extends('layouts.app')

@section('content')
    <div class="panel panel-top">
        <div class="row">
            <div class="col-sm-6">
                <button onclick="goBackTop({{ $repair->id }})" class="btn-brand btn-brand-icon btn-brand-primary"><i
                        class="fa fa-angle-left btn-icon"></i><span>View RMA</span></button>
            </div>
            <div class="col-sm-6 text-right">
            </div>
        </div>
    </div>
    <div class="panelx panel-default panel-brandx">

        <div class="panel-bodyx">
            @include('components/flash')
            @include('components/errors')
            @if ($repair->has_quotation)
                {{ Form::open(['url' => '/update-repair-status/' . $repair->id, 'method' => 'PUT']) }}
                <input type="hidden" name="selectStatus" value="Confirmed" />
                <div class="custom-wrap">
                    <div class="row">
                        <div class="col-md-12">
                            <h2>RMA: #{{ $repair->id }}</h2>
                        </div>
                        <div class="col-md-12">
                            <h3> {{ $repair->requester_company }}</h3>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="sub">
                                        {{ $repair->requester_name }}
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="sub">
                                        {{ $repair->requester_email }}
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="sub">
                                        {{ $repair->requester_phone }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="sub">
                                        {{ $repair->company_phone }}
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="sub">
                                        {{ $repair->company_address }}
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="sub">
                                        {{ $repair->country }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Serial No</th>
                                        <th>Model</th>
                                        <th>Details</th>
                                        <th>Repair Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        switch ($repair->currency) {
                                            case 'EURO':
                                                $cur_sign = '€';
                                                break;
                                            case 'GBP':
                                                $cur_sign = '£';
                                                break;
                                            case 'AUD':
                                                $cur_sign = 'A$';
                                                break;
                                            case 'CAD':
                                                $cur_sign = 'CA$';
                                                break;
                                            case 'YUAN':
                                                $cur_sign = '¥';
                                                break;
                                            default:
                                                $cur_sign = '$';
                                                break;
                                        }
                                    @endphp
                                    @if ($repair->items)
                                        @foreach ($repair->items as $item)
                                            <tr>
                                                <td>{{ $item->serial_number }}</td>
                                                <td>{{ $item->model }}</td>
                                                <td>
                                                    <ul>
                                                        <li><label>Root Cause: </label> {{ $item->root_cause_analysis }}
                                                        </li>
                                                        <li><label>Pacom Comment: </label> {{ $item->pacom_comment }}</li>
                                                    </ul>
                                                    {{-- @foreach ($item->faults as $fault)
                                                    {{ $fault->fault }}<br>
                                                @endforeach --}}
                                                </td>
                                                <td class="price">{{ $cur_sign . $item->repair_cost }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        No logs
                                    @endif
                                </tbody>
                            </table>
                            <div class="total">
                                <label>Total:</label>
                                <span class="count">

                                    @if ($repair->items)
                                        @php
                                            $total = 0;
                                            foreach ($repair->items as $item) {
                                                $total = $item->repair_cost + $total;
                                            }
                                        @endphp
                                        {!! $cur_sign . $total !!}
                                    @else
                                        {!! $cur_sign . 0 !!}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    {{-- {{ Auth::user()->id }}
                    {{ json_encode($repair) }} --}}
                    @if (!$repair->has_confirmed)
                        <div class="btn-wrap">
                            @if (Auth::user()->id == $repair->added_by || !Auth::user()->isAdmin())
                                <button class="btn btn-primary">Submit to confirm</button>
                            @else 
                                <div class="text text-danger">Only the customer can confirm this quotation</div>
                            @endif
                            {{-- @if (Auth::user()->isAdmin())
                                <div class="text text-danger">Only the customer can confirm this quotation</div>
                            @else
                                <button class="btn btn-primary">Submit to confirm</button>
                            @endif --}}
                        </div>
                    @else
                        <div class="btn-wrap">
                            <div class="text text-primary">Quotation has been confirmed already.</div>
                        </div>
                    @endif
                </div>
                
                {!! Form::close() !!}
            @else
                @component('components/panel')
                    @slot('title')
                    @endslot
                    <div class="text text-primary">
                        <h3>Sorry, no quotation was generated for this RMA</h3>
                    </div>
                @endcomponent

            @endif
        </div>
    </div>

@endsection

@section('js')
    <script>
        function goBack(id) {
            //e.preventDefault();
            window.location.href = "/repairs/" + id;
        }

        function goBackTop(id) {
            window.location.href = "/repairs/" + id;
        }

        $('#submitForm').on('click', function() {

            //$('button').attr('readonly', 'readonly');

            //$(this).after('<div class="form-spinner"><i class="fa fa-refresh fa-spin spin-loader" style="font-size:24px"></i></div>');



        })
    </script>
@endsection

@section('css')
    <style>
        .custom-wrap {
            background: #fff;
            width: 700px;
            max-width: 100%;
            padding: 40px;
            border-radius: 3px;
            position: relative;
            box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px;
            margin-bottom: 100px;
        }

        .custom-wrap table {
            margin: 30px 0 0 0;
        }

        .custom-wrap table .price {
            text-align: right;
        }

        .custom-wrap .total {
            margin-bottom: 15px;
            display: flex;
            justify-content: end;
            padding: 8px;
            font-weight: 800;
        }

        .custom-wrap .total label {
            padding-right: 15px;
        }

        .custom-wrap .btn-wrap {
            display: flex;
            justify-content: end;
        }

        .custom-wrap h3 {
            margin-bottom: 10px
        }

        .custom-wrap .sub {
            margin-bottom: 7px
        }

        ul.list-inline {
            display: flex;
            flex-wrap: wrap;
        }

        ul.list-inline li {
            padding: 5px 10px;
            background: #f5f5f5;
            margin-bottom: 5px;
            border-radius: 4px;
            margin-right: 5px;
        }

        ul.list-inline .radio {
            margin: 0;
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

        .form .radio {
            display: inline-block;
            margin: 10px 20px 10px 0;
        }

        .input-warranty input {
            margin-right: 10px;
        }

        .list-inline>li {
            margin-right: 20px;
        }

        .find-user label {
            font-weight: 400;
            color: #716e6e;
        }

        .find-user .heading,
        .item-list .heading {
            background: #d4edf9;
            padding: 7px 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #7fbddb;
        }

        .item-list .heading {
            margin-bottom: 10px;
        }

        .find-user .heading h4,
        .item-list .heading h4 {
            margin-bottom: 0;
            font-size: 16px;
            color: #156185;
        }

        .field {
            border-bottom: 0;
        }
    </style>
@stop
