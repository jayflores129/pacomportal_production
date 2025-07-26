@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/virtual-select.min.css') }}">
    <script src="{{ asset('js/virtual-select.min.js') }}"></script>

    <div class="panel panel-top">
        <div class="row">
            <div class="col-md-4">
                {!! Breadcrumbs::render('showRepairs') !!}
            </div>
            <div class="col-sm-12 col-md-8 text-right">

                <ul style="display: inline-block;text-align: right;">
                    @if (Auth::user()->isAdmin())
                        <li style="display: inline-block;text-align: right;">
                            <a href="{{ route('repairs.edit', $repair->id) }}"
                                class="btn-brand btn-brand-icon btn-brand-primary"><i
                                    class="fa fa-edit btn-icon"></i><span>Edit</span></a>
                        </li>
                    @endif
                    @if (
                        !Auth::user()->isAdmin() &&
                            Auth::user()->company_id == $repair->company_id &&
                            ($repair->cust_can_edit != 0 && $repair->has_confirmed != 1) &&
                            $repair->has_quotation != true)
                        <li style="display: inline-block;text-align: right;">
                            <a href="{{ route('repairs.customer-edit', $repair->id) }}"
                                class="btn-brand btn-brand-icon btn-brand-primary"><i
                                    class="fa fa-edit btn-icon"></i><span>Edit</span></a>
                        </li>
                    @endif
                    <li style="display: inline-block;text-align: right;">
                        <a href="#" class="btn-brand btn-brand-icon btn-brand-success print-window"><i
                                class="fa fa-print btn-icon"></i><span>Print</span></a>
                    </li>
                    @if (Auth::user()->isAdmin() ||
                            (Auth::user()->company_id == $repair->company_id &&
                                ($repair->cust_can_edit != 0 && $repair->has_confirmed != 1 && $repair->has_quotation != true)))
                        <li style="display: inline-block;text-align: right;">
                            {!! Form::open(['method' => 'delete', 'route' => ['repairs.destroy', $repair->id]]) !!}
                            <button type="submit" id="delMainRMA" class="btn-brand btn-brand-icon btn-brand-danger"><i
                                    class="fa fa-trash btn-icon"></i><span> Delete</span></button>
                            {!! Form::close() !!}
                        </li>
                    @endif

                </ul>

            </div>
        </div>
    </div>
    @include('components/flash')

    <div style="position: relative;">
        @component('components/panel')
            @slot('title')
                RMA Details
            @endslot
            @include('repairs/show/rma-details')
        @endcomponent
        <div class="nav-button">
            <button class="btn-brand btn-brand-primary" data-type="prev">Previous</button>
            <button class="btn-brand btn-brand-primary" data-type="next">Next</button>
        </div>
    </div>

    @if (Auth::user()->isAdmin())
        @include('repairs/components/add-item')
    @else
        @include('repairs/show/customer/fault-item-add')
    @endif

    @if (Auth::user()->isAdmin())
        @include('repairs/components/edit-item')
    @else
        @include('repairs/show/customer/fault-item-edit')
    @endif

    @include('repairs/components/update-status')
    @include('repairs/components/add-comment')
    @include('repairs/show/fault-item-view')
    @component('components/panel')
        @slot('title')
            <span>Repair / Request Details</span>
            @if ($repair->has_quotation == true && $repair->has_confirmed != 1)
                <a href="/rma-quotation/{!! $repair->id !!}" class="btn btn-primary">Confirm Quotation</a>
            @elseif($repair->has_quotation == true && $repair->has_confirmed == 1)
                <a href="/rma-quotation/{!! $repair->id !!}" class="btn btn-primary">View Quotation</a>
            @else
            @endif
        @endslot
        @include('repairs/show/fault-items')
    @endcomponent

    @component('components/panel')
        @slot('title')
            <span>RMA Status Details</span>
        @endslot
        @include('repairs/components/rma-status')
    @endcomponent
    <div class="for-printing">
        @component('components/panel')
            @slot('title')
                <span>Comments</span>
                @if (Auth::user()->isAdmin())
                    <button id="addComment" class="btn btn-primary">Add Comment</button>
                @endif
            @endslot
            @include('repairs/components/comment')
        @endcomponent
    </div>
    @component('components/panel')
        @slot('title')
            Repair Log
        @endslot
        @include('repairs/components/repair-log')
    @endcomponent

    </div>


@endsection

<style>
    select#selectStatus {
        margin-right: 5px;
    }

    .inner-wrap .btn {
        font-size: 14px;
        text-transform: uppercase;
        padding: 2px 7px;
        height: 36px;
    }

    .nav-button button {
        padding: 3px 14px;
    }

    .nav-button {
        position: absolute;
        top: 9px;
        right: 10px;
    }

    .ck.ck-content.ck-editor__editable.ck-rounded-corners.ck-editor__editable_inline {
        min-height: 150px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>

@section('js')
    <script>
        let options = <?= json_encode($products) ?>;

        VirtualSelect.init({
        ele: '#input_pn',
        options: options.map((item) => ({ label: item.name, value: item.name })),
        });

        function goBack() {
            window.history.back();
        }

        const repairId = {{ $repair->id }};
        const RmaIDs = localStorage.getItem('rmaIDs');
        const rma_ids = JSON.parse(RmaIDs);

        const currentIdIndex = Array.isArray(rma_ids) ? rma_ids.findIndex(item => item == repairId) : -1;

        if (currentIdIndex === 0) {
            document.querySelector('.nav-button button[data-type="prev"]').style.display = 'none';
        }

        if (Array.isArray(rma_ids) && rma_ids.length - 1 === currentIdIndex) {
            document.querySelector('.nav-button button[data-type="next"]').style.display = 'none';
        }

        if (currentIdIndex == -1) {
            document.querySelectorAll('.nav-button button')[0].style.display = 'none';
        }

        Array.from(document.querySelectorAll('.nav-button button')).forEach(elem => {
            if (!RmaIDs) {
                elem.style.display = 'none';
            }

            elem.addEventListener('click', (e) => {
                const {
                    type
                } = e.currentTarget.dataset;

                // console.log(currentIdIndex);return;

                let _curr = currentIdIndex;

                if (type == 'prev') {
                    _curr -= 1;
                }

                if (type == 'next') {
                    _curr += 1;
                }

                if (_curr > -1 && rma_ids[_curr]) {
                    location.href = '/repairs/' + rma_ids[_curr];
                }
            });
        });

        //When fault category in use
        $(".edit-item-popup #selectIssue").change((e) => {
            hasOther($(".edit-item-popup #selectIssue"), $(".edit-item-popup #fault-comment"))
        })
        $(".add-item-popup #selectIssue").change((e) => {
            hasOther($(".add-item-popup #selectIssue"), $(".add-item-popup #fault-comment"))
        })


        const isOther = (selectField, faultComment) => {
            const selected = selectField.val();
            const hasIncluded = (selected.indexOf("Other") > -1);
            if (hasIncluded && !faultComment.val()) {
                faultComment.closest('validate-feedback').css("display", "block");
                //faultComment.closest('.fault-block').find('.validate-feedback').css("display", "block");
                // $(faultComment + ' .validate-feedback').css("display", "block");
                return true;
            } else {
                return false;
            }
        }

        //Add Item
        $('#addItemButton').on('click', async (e) => {
            e.preventDefault();

            var model = $('#input_pn').val(),
                serial_number = $('#input_sn').val(),
                fault_comment = $('#fault-comment').val(),
                companyID = $('#companyID').val(),
                original_order_date = $('#original_order_date').val(),
                repair_cost = $('#repair_cost').val(),
                rma_id = $('#rma_id').val(),
                faults = $('.add-item-popup #selectIssue').val();
            isCustomer = $('.add-item-popup #isCustomer').val();

            let date_purchase_known = $('#date_purchase_known').prop('checked') ? 1 : 0;

            // let warranty_flag_no = $('input[name="warranty_flag_no"]:checked').val() == undefined ? "" : $('input[name="warranty_flag_no"]:checked').val()
            // let warranty_flag_yes= $('input[name="warranty_flag"]:checked').val() == undefined ? "" : $('input[name="warranty_flag"]:checked').val()
            let warranty_flag = $('input[name="warranty_flag"]:checked').val() == undefined ? "" : $(
                'input[name="warranty_flag"]:checked').val();

            if (warranty_flag == "") {
                warranty_flag = "";
            } else if (warranty_flag == "0") {
                warranty_flag = 0;
            } else if (warranty_flag == "1") {
                warranty_flag = 1;
            }

            let invalid_serial_number = $('#invalid_serial').prop('checked') ? 1 : 0;
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

            const otherSelected = isOther($('.add-item-popup #selectIssue'), $(
                '.add-item-popup #fault-comment'));

            $('.add-item-popup #fault-comment').closest('.form-group').removeClass('validate-error');
            $('#input_sn').closest('.form-group').removeClass('validate-error');
            $('#input_pn').closest('.form-group').removeClass('validate-error');
            $('#selectIssue').closest('.form-group').removeClass('validate-error');

            if (faults.includes('Other') && !fault_comment) {
                $('.add-item-popup #fault-comment').closest('.form-group').addClass('validate-error');
                return;
            }

            if (!serial_number) {
                $('#input_sn').closest('.form-group').addClass('validate-error');
                return;
            }

            if (!model) {
                $('#input_pn').closest('.form-group').addClass('validate-error');
                return;
            }

            if (faults.length < 1) {
                $('#selectIssue').closest('.form-group').addClass('validate-error');
                return;
            }

            const payload = {
                _token: CSRF_TOKEN,
                model,
                serial_number,
                fault_comment,
                original_order_date,
                repair_cost,
                rma_id,
                faults,
                date_purchase_known,
                warranty_flag,
                invalid_serial_number,
                isCustomer
            };

            $('.add-item-popup .loading').addClass('show');

            $.ajax({
                type: 'POST',
                url: '{{ URL::to('/rma/addItemRMA') }}',
                data: {
                    ...payload
                },
                success(data) {
                    //console.log(data);
                    if (data.success) {
                        //let faultJSON = JSON.stringify(data['faults']);
                        //addItemTable(data['item'], faultJSON);
                        $('.add-item-popup .loading').removeClass('show');
                        window.location.hash = "#addItem"
                        location.reload();
                        clearField();
                        $('.add-item-popup').addClass('hide');
                    }
                },
                error(data) {
                    console.log('Error:', data);
                },
            });
        });

        $('.inner-content').on('click', '.EditBTN', function(e) {

            let rma_id = $(this).attr('data-rma-id');
            let serial_no = $(this).attr('data-serial');
            let model = $(this).attr('data-model');
            let repair_cost = $(this).attr('data-repair-cost');
            let order_date = $(this).attr('data-order-date');
            let under_warranty = $(this).attr('data-under-warranty');
            let invalid_serial = $(this).attr('data-invalid-serial');
            let status = $(this).attr('data-status');
            let date_purchased = $(this).attr('data-purchase-known');

            let item_id = $(this).attr('data-id');
            let fault_described = $(this).attr('data-fault-described');
            let faults = $(this).attr('data-faults') ? JSON.parse($(this).attr('data-faults')) : "";
            let root_cause_analysis = $(this).attr('data-root-cause-analysis');
            let pacom_fault_description = $(this).attr('data-pacom-fault-description');
            let pacom_comment = $(this).attr('data-pacom-comment');
            let received_date = $(this).attr('data-received-date');
            let repaired_date = $(this).attr('data-repaired-date');
            let fault_item_id = $(this).attr('data-id');

            if (faults.length > 0) {
                faults.forEach(function(item, index) {
                    // console.log($('.selectedFaults').find('option[value="'+ item.fault +'"]'));
                    $('.selectedFaults').find('option[value="' + item.fault + '"]').attr('selected',
                        'selected');
                })
            }
            if (date_purchased) {
                $('.edit-item-popup #original_order_date').addClass('show');
            }
            $('.edit-item-popup #original_order_date').val(order_date);
            $('.edit-item-popup #rmaId').val(rma_id);
            $('.edit-item-popup #status').val(status);
            $('.edit-item-popup #item_id').val(fault_item_id);
            $('.edit-item-popup #input_sn').val(serial_no);
            document.querySelector('.edit-item-popup #input_pn').setValue(model);
            $('.edit-item-popup #repair_cost').val(repair_cost);
            $('.edit-item-popup #fault-comment').val(fault_described);
            $('.edit-item-popup #date_purchase_known').prop('checked', date_purchased == "1" ? true : false);
            $('.edit-item-popup #invalid_serial').prop('checked', invalid_serial == "1" ? true : false);


            let warrantyYes = $('.edit-item-popup #warranty_flag');
            let warrantyNo = $('.edit-item-popup #warranty_flag_no');
            warrantyYes.prop('checked', under_warranty == "1" ? true : "");
            warrantyNo.prop('checked', under_warranty == "0" ? true : ""); // New added

            //switching between yes no
            warrantyYes.change(function() {
                if ($(this).prop('checked') == true && warrantyNo.prop("checked") == true) {
                    warrantyNo.prop("checked", false);
                };
            });
            warrantyNo.change(function() {
                if ($(this).prop('checked') == true && warrantyYes.prop("checked") == true) {
                    warrantyYes.prop("checked", false);
                };
            });

            //root cause analysis desc show and hide

            if (root_cause_analysis != "other" || !root_cause_analysis) {
                $(".pacom_fault_desc-wrap").removeClass('show');
            }
            $('.edit-item-popup #root_cause_analysis').change(function() {
                let root_cause_analysis_text = $(this).find(":selected").val()
                if (root_cause_analysis_text == "other") {
                    $(".pacom_fault_desc-wrap").addClass('show');
                } else {
                    $(".pacom_fault_desc-wrap").removeClass('show');
                }
            })


            $('.edit-item-popup #original_order_date').val(dayjs(order_date).format('YYYY-MM-DD'));



            $('.edit-item-popup #root_cause_analysis').val(root_cause_analysis);



            $('.edit-item-popup #pacom_fault_desc').val(pacom_fault_description);
            $('.edit-item-popup #pacom_fault_comment').val(pacom_comment);

            $('.edit-item-popup #received_date').val(received_date);
            $('.edit-item-popup #repaired_date').val(repaired_date);


            $('.edit-item-popup').removeClass('hide');

        })

        $('.inner-content').on('click', '.itemViewBTN', function() {

            let root_cause = $(this).attr('data-root-cause-analysis');
            let fault_described = $(this).attr('data-fault-described');
            let pacom_fault_desc = $(this).attr('data-pacom-fault-description');
            let pacom_comment = $(this).attr('data-pacom-comment');
            let received_date = $(this).attr('data-received-date');
            let repaired_date = $(this).attr('data-repaired-date');
            let serial_no = $(this).attr('data-serial');
            let model = $(this).attr('data-model');
            let repair_cost = $(this).attr('data-repair-cost');
            let order_date = $(this).attr('data-order-date') == "" ? "" : $(this).attr('data-order-date');
            let under_warranty = $(this).attr('data-under-warranty');
            let invalid_serial = $(this).attr('data-invalid-serial');
            let status = $(this).attr('data-status');
            let date_purchased = $(this).attr('data-purchase-known');
            let item_id = $(this).attr('data-id');
            let faults = $(this).attr('data-faults') ? $(this).attr('data-faults') : "";

            //console.log(faults);
            if (faults.length > 0) {
                let outputFaults = '';
                faults = JSON.parse(faults);
                faults.forEach(function(item, index) {
                    outputFaults = outputFaults + '<div class="item-single">' + item.fault + '</div><br />';
                })
                $('#itemFaultCategory').html(outputFaults);
            }

            $('.view-item-popup #itemSerialNumber').html(serial_no);
            $('.view-item-popup #itemModel').html(model);
            $('.view-item-popup #itemStatus').html(status);
            $('.view-item-popup #itemRepairCost').html(checkEmpty(repair_cost));
            $('.view-item-popup #itemDatePurchased').html(date_purchased == 1 ? 'Yes' : '');
            $('.view-item-popup #itemInvalidSerialNumber').html(invalid_serial == 1 ? 'Yes' : '');
            $('.view-item-popup #itemUnderWarranty').html(under_warranty == 1 ? 'Yes' : '');
            if (order_date != '') {
                $('.view-item-popup #itemOriginalOrderDate').html(dayjs(order_date).format('YYYY-MM-DD'));
            }
            $('.view-item-popup #itemRootCauseAnalysis').html(checkEmpty(root_cause))
            $('.view-item-popup #itemPacomFaultDesc').html(checkEmpty(pacom_fault_desc))
            $('.view-item-popup #itemPacomComment').html(checkEmpty(pacom_comment))
            $('.view-item-popup #itemFaultDescByCustomer').html(checkEmpty(fault_described))
            $('.view-item-popup #itemReceivedDate').html(checkEmpty(received_date))
            $('.view-item-popup #itemRepairedDate').html(checkEmpty(repaired_date))



            $('.view-item-popup').removeClass('hide');

        })


        const hasOther = (selectField, faultComment) => {
            const selected = selectField.val()
            const hasIncluded = (selected.indexOf("Other") > -1);
            if (hasIncluded) {
                faultComment.attr('required', 'required');
                faultComment.addClass('required');
                return true;
            } else {
                faultComment.removeAttr('required');
                faultComment.removeClass('required');
                faultComment.closest('validate-feedback').css("display", "none")
                // $(faultComment + ' .validate-feedback').css("display", "none");
                faultComment.closest('.form-group').removeClass('validate-error');
                return false;
            }
        }

        $('#updateButton').on('click', async (e) => {
            //const { item } = e.currentTarget.dataset;
            const rma_id = $('.edit-item-popup #rmaId').val();
            const item_id = $('.edit-item-popup #item_id').val();
            const serial_number = $('.edit-item-popup #input_sn').val();
            const model = $('.edit-item-popup #input_pn').val();
            const repair_cost = $('.edit-item-popup #repair_cost').val();
            const original_order_date = $('.edit-item-popup #original_order_date').val();
            const fault_comment = $('.edit-item-popup #fault-comment').val();
            const status = $('.edit-item-popup #status').val();

            // console.log({rma_id});return;

            const root_cause_analysis = $('.edit-item-popup #root_cause_analysis').val();

            const pacom_fault_description = $('.edit-item-popup #pacom_fault_desc').val();
            const pacom_comment = $('.edit-item-popup #pacom_fault_comment').val();

            const received_date = $('.edit-item-popup #received_date').val();
            const repaired_date = $('.edit-item-popup #repaired_date').val();

            const date_purchased = $('.edit-item-popup #date_purchase_known').prop("checked") == true ? 1 : 0;
            const invalid_serial_number = $('.edit-item-popup #invalid_serial').prop("checked") == true ? 1 : 0;

            let underWarranty = $('.edit-item-popup #warranty_flag').prop("checked");
            let underWarrantyNo = $('.edit-item-popup #warranty_flag_no').prop("checked");

            let under_warranty = "";

            if (underWarranty == false && underWarrantyNo == false) {
                under_warranty = null;
            } else {
                if (underWarranty == true) {
                    under_warranty = 1;
                } else {
                    under_warranty = 0;
                }
            }
            const faults = $('.edit-item-popup #selectIssue').val();
            const _token = $('meta[name="csrf-token"]').attr('content');

            const otherSelected = isOther($('.edit-item-popup #selectIssue'), $(
                '.edit-item-popup #fault-comment'))

            $('.edit-item-popup #input_sn').closest('.form-group').removeClass('validate-error');
            $('.edit-item-popup #input_pn').closest('.form-group').removeClass('validate-error');
            $('.edit-item-popup #selectIssue').closest('.form-group').removeClass('validate-error');
            $('.edit-item-popup #selectIssue').closest('.form-group').find('.validate-feedback').removeClass(
                'show');
            $('.edit-item-popup #fault-comment').closest('.form-group').removeClass('validate-error');
            $('.edit-item-popup #fault-comment').attr('required', 'required')


            if (!serial_number) {
                $('.edit-item-popup #input_sn').closest('.form-group').addClass('validate-error');
                return;
            }
            if (!model) {
                $('.edit-item-popup #input_pn').closest('.form-group').addClass('validate-error');
                return
            }

            if (faults.length < 1) {
                $('.edit-item-popup #selectIssue').closest('.form-group').addClass('validate-error');
                $('.edit-item-popup #selectIssue').closest('.form-group').find('.validate-feedback').addClass(
                    'show');
                return;
            }

            if (faults.includes('Other') && !fault_comment) {
                $('.edit-item-popup #fault-comment').closest('.form-group').addClass('validate-error');
                return;
            }

            if (!faults.includes('Other')) {
                $('.edit-item-popup #fault-comment').removeAttr('required', 'required')
            }

            $('.edit-item-popup .loading').addClass('show');

            const payload = {
                _token,
                rma_id,
                serial_number,
                model,
                repair_cost,
                original_order_date,
                fault_comment,
                date_purchased,
                invalid_serial_number,
                under_warranty,
                root_cause_analysis,
                pacom_fault_description,
                pacom_comment,
                received_date,
                repaired_date,
                status,
                faults: JSON.stringify(faults.map(fault => ({
                    fault,
                    rma_items_id: item_id
                })))
            };

            $.ajax({
                type: 'PUT',
                url: '{{ URL::to('/rma') }}' + `/${item_id}`,
                data: {
                    ...payload
                },
                success(data) {
                    if (data.success) {
                        updateItem(data['item']);
                        clearField();
                        window.location.hash = "#addItem"
                        location.reload();

                        ('.edit-item-popup .loading').removeClass('show');
                        $('.edit-item-popup').addClass('hide');

                    }
                },
                error(data) {
                    console.log('Error:', data);
                },
            });



        });




        function updateItem(item) {
            let el_raw = $('#rma_items_table tr[data-tr-id="' + item.id + '"]');
            let output = '';
            let faults = item.faults;
            let itemFaultsOutput = '';

            var repair_cost = "";
            faults.forEach(function(fault, index) {
                itemFaultsOutput += fault.fault + '<br>';

            })

            if (item.repair_cost === null || item.repair_cost === "") {
                repair_cost = "";
            } else {
                repair_cost = item.repair_cost;
            }
            if (item.under_warranty === 0 || item.under_warranty === "") {
                item.under_warranty = "No";
            } else {
                item.under_warranty = "Yes";
            }
            output = `
        <td>` + item.serial_number + ` </td>
        <td>` + item.model + ` </td>
        <td>` + itemFaultsOutput + ` </td>
        <td>` + repair_cost + ` </td>
        <td>` + item.original_order_date + `</td>
        <td>` + item.under_warranty + ` </td>
        <td>
          <button class="itemViewBTN btn btn-secondary" 
            data-id="` + item.id + `" 
            data-serial="` + item.serial_number + `" 
            data-model="` + item.model + `" 
            data-repair-cost="` + checkEmpty(repair_cost) + `" 
            data-order-date="` + item.original_order_date + `" 
            data-under-warranty="` + checkEmpty(item.under_warranty) + `" 
            data-invalid-serial="` + item.invalid_serial_number + `" 
            data-status="` + item.status + `" 
            data-purchase-known="` + item.date_purchased + `"
            data-pacom-fault-description="` + checkEmpty(item.pacom_fault_description) + `"
            data-pacom-comment="` + checkEmpty(item.pacom_comment) + `"
            data-received-date="` + checkEmpty(item.received_date) + `"
            data-repaired-date="` + checkEmpty(item.repaired_date) + `"
            data-fault="` + item.faults + `"
            data-root-cause-analysis = "` + checkEmpty(item.root_cause_analysis) + `"
            data-fault-described = "` + checkEmpty(item.fault_described_by_customer) + `"
            ><span class="fa fa-eye"></span>
          </button>
          <button class="EditBTN btn btn-primary" 
            data-id="` + item.id + `" 
            data-serial="` + item.serial_number + `" 
            data-model="` + item.model + `" 
            data-repair-cost="` + checkEmpty(repair_cost) + `" 
            data-order-date="` + item.original_order_date + `" 
            data-under-warranty="` + checkEmpty(item.under_warranty) + `" 
            data-invalid-serial="` + item.invalid_serial_number + `" 
            data-status="` + item.status + `" 
            data-purchase-known="` + item.date_purchased + `"
            data-pacom-fault-description="` + checkEmpty(item.pacom_fault_description) + `"
            data-pacom-comment="` + checkEmpty(item.pacom_comment) + `"
            data-received-date="` + checkEmpty(item.received_date) + `"
            data-repaired-date="` + checkEmpty(item.repaired_date) + `"
            data-fault="` + item.faults + `"
            data-root-cause-analysis = "` + checkEmpty(item.root_cause_analysis) + `"
            data-fault-described = "` + checkEmpty(item.fault_described_by_customer) + `"
            ><span class="fa fa-edit"></span>
          </button> <button class="DelBTN btn btn-danger" data-serial="` + item.serial_number + `"  data-id="` + item
                .id + `"><span class="fa fa-trash"></span></button>
        </td>`;

            el_raw.html(output);

        }

        function addItemTable(item, faults = '') {

            let el_raw = $('#rma_items_table tr:first-child');
            let output = '';
            let itemFaultsOutput = '';

            if (faults != '') {
                faults = JSON.parse(faults);
                faults.forEach(function(fault, index) {
                    itemFaultsOutput += fault + '<br>';
                })
            }

            if (item.repair_cost === null || item.repair_cost === "") {
                item.repair_cost = "";
            } else {
                item.repair_cost = item.repair_cost;
            }
            if (item.under_warranty === 0 || item.under_warranty === "") {
                item.under_warranty = "No";
            } else {
                item.under_warranty = "Yes";
            }
            if (item.original_order_date === null || item.original_order_date === "") {
                item.original_order_date = "";
            } else {
                item.original_order_date = item.original_order_date;
            }

            //let dataFaults = JSON.stringify(faults);
            //console.log(item);


            output = `
        <tr data-tr-id=` + item.id + ` >
          <td>` + item.serial_number + `</td>
          <td>` + item.model + `</td>
          <td>` + itemFaultsOutput + `</td>
          <td>` + item.repair_cost + `</td>
          <td>` + item.original_order_date + `</td>
          <td>` + item.under_warranty + `</td>
          <td>
              <button class="itemViewBTN btn btn-secondary" 
              data-id="` + item.id + `" 
              data-serial="` + item.serial_number + `" 
              data-model="` + item.model + `" 
              data-repair-cost="` + item.repair_cost + `" 
              data-order-date="` + item.original_order_date + `" 
              data-under-warranty="` + item.under_warranty + `" 
              data-invalid-serial="` + item.invalid_serial_number + `" 
              data-status="` + checkEmpty(item.status) + `" 
              data-purchase-known="` + item.date_purchased + `"
              data-pacom-fault-description="` + checkEmpty(item.pacom_fault_description) + `"
              data-pacom-comment="` + checkEmpty(item.pacom_comment) + `"
              data-received-date="` + checkEmpty(item.received_date) + `"
              data-repaired-date="` + checkEmpty(item.repaired_date) + `"
              data-faults="` + faults + `"
              data-root-cause-analysis = "` + checkEmpty(item.root_cause_analysis) + `"
              data-fault-described = "` + item.fault_described_by_customer + `"
              ><span class="fa fa-eye"></span>
            </button>
            <button class="EditBTN btn btn-primary" 
              data-id="` + item.id + `" 
              data-serial="` + item.serial_number + `" 
              data-model="` + item.model + `" 
              data-repair-cost="` + item.repair_cost + `" 
              data-order-date="` + item.original_order_date + `" 
              data-under-warranty="` + item.under_warranty + `" 
              data-invalid-serial="` + item.invalid_serial_number + `" 
              data-status="` + checkEmpty(item.status) + `" 
              data-purchase-known="` + item.date_purchased + `"
              data-pacom-fault-description="` + checkEmpty(item.pacom_fault_description) + `"
              data-pacom-comment="` + checkEmpty(item.pacom_comment) + `"
              data-received-date="` + checkEmpty(item.received_date) + `"
              data-repaired-date="` + checkEmpty(item.repaired_date) + `"
              data-faults="` + faults + `"
              data-root-cause-analysis = "` + checkEmpty(item.root_cause_analysis) + `"
              data-fault-described = "` + checkEmpty(item.fault_described_by_customer) + `"
              ><span class="fa fa-edit"></span>
            </button> <button class="DelBTN btn btn-danger" data-serial="` + item.serial_number + `" data-id="` + item
                .id + `"><span class="fa fa-trash"></span></button>
          </td>
        </tr>`;


            el_raw.before(output);

        }

        function checkEmpty($item) {
            if ($item == '' || $item == undefined || $item == null || $item == 'null') {
                return $item = '';
            }
            return $item;
        }


        $('#addItem').on('click', function(e) {
            e.preventDefault();
            $('.add-item-popup').removeClass('hide');
        })

        $('#btn-add-close').on('click', function(e) {
            e.preventDefault();
            $('.add-item-popup').addClass('hide');
            $('.form-group.validate-error').removeClass('validate-error');

            clearField();

            const options = document.querySelectorAll('#selectIssue option');
            Array.from(options).forEach(option => option.removeAttribute('selected'));
        });

        $('#btn-close').on('click', function(e) {
            e.preventDefault();
            $('.edit-item-popup').addClass('hide');

            clearField();

            const options = document.querySelectorAll('#selectIssue option');
            Array.from(options).forEach(option => option.removeAttribute('selected'));
        });

        $('#addStatus').on('click', function(e) {
            e.preventDefault();
            $('.add-status-popup').removeClass('hide');
            $('.add-status-popup').removeClass('hide');
        });

        $('#addComment').on('click', function(e) {
            e.preventDefault();
            $('.add-comment-popup').removeClass('hide');
        });

        $('.btn-add-close').on('click', function(e) {
            e.preventDefault();
            $('.popup-form').addClass('hide');
        });

        $('.main-content').on('click', '.itemViewBTN', function(e) {
            e.preventDefault();

            $('.view-item-popup').removeClass('hide');
        });

        $('#btn-view-item-close').on('click', function(e) {
            e.preventDefault();
            $('.view-item-popup').addClass('hide');

        });

        function clearViewFaults() {
            $('.view-item-popup').find('span').html('');
        }

        function clearField() {
            $('#input_sn').val('');
            $('#input_pn').val('');
            $('#repair_cost').val('');
            $('#date_purchase_known').prop('checked', false);
            $('#invalid_serial').prop('checked', false);
            $('#warranty_flag').prop('checked', false);
            $('#original_order_date').val('');
            $('#fault-comment').val('');
        }
        $('.main-content').on('click', '.DelBTN', function(e) {
            e.preventDefault();
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            item_id = $(this).attr('data-id');
            serial = $(this).attr('data-serial');
            rma_id = $(this).attr('data-rma-id');
            let delConfirm = confirm("Are you sure you want to delete?")

            if (delConfirm !== true) {
                return;
            }
            $.ajax({
                type: 'POST',
                url: '{{ URL::to('/rma/delete') }}',
                data: {
                    '_token': CSRF_TOKEN,
                    'id': item_id,
                    'serial': serial,
                    'rma_id': rma_id
                },
                success(data) {
                    window.location.hash = "#addItem"
                    location.reload();
                    //$('#rma_items_table tr[data-tr-id="' + item_id + '"]').remove();
                }

            });

        });

        $('.main-content').on('click', '#delMainRMA', function(e) {
            //e.preventDefault();
            let delConfirm = confirm("Are you sure you want to delete?")

            if (delConfirm !== true) {
                e.preventDefault();
                return;
            }


        });

        $('#selectStatus').on('change', function() {
            let selected = $(this).val();

            if (selected == 'Shipped') {
                $('.add-status-popup').removeClass('hide');
            }


        });
        $('#rma_status').on('change', function() {
            let curRMAStatus = $(this).val();
            if (curRMAStatus == 'Goods have been shipped on ') {
                $('#shipInfo').removeClass('hide');
            } else {
                $('#shipInfo').addClass('hide');
                $('#rma_courier').val('');
                $('#consignment_note').val('');
            }
        });

        $('.main-content').on('click', '.delRMAStatus', function(e) {
            e.preventDefault();
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            let item_id = $(this).attr('data-id');
            let rma_id = $(this).attr('data-rma-id');
            let rma_status = $(this).attr('data-rma-status')
            let colSelect = $(this).closest('tr');
            let delConfirm = confirm("Are you sure you want to delete?")

            if (delConfirm !== true) {
                return;
            }
            $.ajax({
                type: 'POST',
                url: '{{ URL::to('/deleteRMAStatus') }}',
                data: {
                    '_token': CSRF_TOKEN,
                    'id': item_id,
                    'rma_id': rma_id,
                    'rma_status': rma_status
                },
                success(data) {
                    colSelect.remove();
                    location.reload();
                }

            });

        });

        $('.main-content').on('click', '.delRMAComments', function(e) {
            e.preventDefault();
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            let item_id = $(this).attr('data-id');
            let rma_id = $(this).attr('data-rma-id');
            let comment = $(this).attr('data-comment');

            let colSelect = $(this).closest('tr');
            let delConfirm = confirm("Are you sure you want to delete?")

            if (delConfirm !== true) {
                return;
            }
            $.ajax({
                type: 'POST',
                url: '{{ URL::to('/deleteRMAComments') }}',
                data: {
                    '_token': CSRF_TOKEN,
                    'id': item_id,
                    'comment': comment,
                    'rma_id': rma_id
                },
                success(data) {
                    colSelect.remove();
                    location.reload();
                }

            });

        });

        $('.print-window').click(function(e) {
            e.preventDefault();
            window.print();
        });

        $('button#updateStatus').on('click', function(e) {
            e.preventDefault();
            $(this).closest('.status-view').hide();
            $('.rma-field form').show();

        })
        $('button#cancelEditStatus').on('click', function(e) {
            e.preventDefault();
            $('.status-view').show();
            $('.rma-field form').hide();

        })
    </script>
@endsection
@section('css')
    <style>
        textarea#fault-comment {
            padding: 10px;
            min-height: 60px !important;
        }

        .commentBTN {
            background: #c1c772;
            color: #fff;
        }

        .commentBTN:hover {
            background: #abb156;
            color: #fff;
        }

        button#EditItem {

            border: 0;
            color: #fff;
            border-radius: 4px;
            text-transform: uppercase;

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

        .field .rma-field {
            float: right;
            width: calc(100% - 150px);
            word-break: break-word;
        }

        .field .rma-field .group-field {
            margin-top: 10px;
        }

        .field .rma-field form {
            display: none;
        }

        .rma-field select,
        .rma-field button {
            height: 30px;
        }

        .rma-field button {
            line-height: 1;
            font-size: 12px !important;
        }

        button#updateStatus,
        button#cancelEditStatus {
            margin-left: 6px;
            border: 0;
            padding: 5px;
            height: 27px;
            width: 30px;
            border-radius: 3px;
            border-bottom: 2px solid #ddd;
        }

        #cancelEditStatus {
            width: 35px;
        }

        .panel-body input,
        .panel-body select {
            height: 36px;
            background: #fff;
            margin-bottom: 0px;
        }

        button#addItem {
            margin-bottom: 10px;
            border: 0;
            background: #007cbc;
            color: #fff;
            padding: 5px 10px;
            border-radius: 4px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1px;
            font-size: 12px;
        }

        a.a-as-btn {
            margin-bottom: 10px;
            border: 0;
            background: #f2f2f2;
            color: #0a5584;
            padding: 5px 10px;
            border-radius: 4px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1px;
            font-size: 12px;
            display: inline-block;
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

        .form-control-view span {
            display: block;
            min-height: 41px;
            margin-bottom: 10px;
            padding: 5px;
            align-items: center;
            justify-content: left;
            display: flex;
            border: 1px solid #f0f0f0;
            border-radius: 3px;
        }

        .view-item-popup .popup-block {
            width: 100% !important;
            max-width: 800px;

        }

        .add-item-popup,
        .popup-form,
        .edit-item-popup,
        .view-item-popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
            z-index: 999;
        }

        .add-item-popup .popup-block,
        .popup-form .popup-block,
        .edit-item-popup .popup-block,
        .view-item-popup .popup-block {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            width: 800px;
        }

        .req-input {
            color: red;
        }

        .add-item-popup .popup-block .popup-heading,
        .popup-form .popup-block .popup-heading,
        .edit-item-popup .popup-block .popup-heading,
        .view-item-popup .popup-block .popup-heading {
            padding: 8px 10px;
            background: #f5f5f5;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #000;
        }

        .add-item-popup .popup-block .popup-body,
        .popup-form .popup-block .popup-body,
        .edit-item-popup .popup-block .popup-body,
        .view-item-popup .popup-block .popup-body {
            padding: 20px;
        }

        .find-user .heading,
        .item-list .heading {
            background: #f9f9f9;
            padding: 7px 10px;
            margin-bottom: 20px;
            border-bottom: 1px solid #c2ccd1;
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

        .panel-brand .panel-heading {
            border-bottom: 1px solid #f6fcff !important;
            background-color: #e1f5ff !important;

        }

        .panel-brand .panel-heading h3 {
            color: #156185 !important;
        }

        .panel-brand .panel-heading h3 {
            display: flex;
            justify-content: space-between;
            align-items: center;


            /* Add item validation */
            .validate-feedback {
                display: none;
            }

            .validate-error .validate-feedback {
                display: block;
                color: #ef7140;
                font-size: 13px;
                margin-bottom: 15px;
            }

            .validate-error .required {
                /* border-color: #ef7140; */
                border: 1px solid #ef7140 !important;
                margin-bottom: 0;
            }

            .item-single {
                background: #ededed;
                margin-right: 5px;
                padding: 5px 8px;
                border-radius: 3px;
            }

            .view-item-popup .popup-block,
            .edit-item-popup .popup-block {
                width: calc(100% - 60px);
                min-width: 90%;
            }

            .edit-item-popup .popup-block {
                width: 600px;
                max-width: 90%;
            }

            .view-item-popup .popup-heading {
                background: #688ee7 !important;
                color: #fff !important;
            }

            .edit-item-popup .popup-heading {
                background: #8cd3c0 !important;
                color: #fff !important;
            }

            .view-item-popup #btn-view-item-close,
            .edit-item-popup #btn-close {
                border: 0;
                background: #19375d;
                font-size: 16px;
                padding: 2px 10px;
                border-radius: 3px;
            }

            .view-item-popup label,
            .edit-item-popup label {
                color: #312f2f;
            }

            .edit-item-popup input,
            .edit-item-popup select,
            .edit-item-popup textarea {
                /* border: none !important; */
                border: none;
                background: #f3f3f3;
                box-shadow: none;
                /* border: 0 !important; */
                padding: 11px 10px;
            }

            div#logPagination,
            #commentPagination {
                text-align: right;
            }

            div#logPagination a,
            #commentPagination a {
                display: inline-block;
                margin-left: 7px;
            }

            .table-m-height {
                max-height: inherit;
            }

            /* UTILS */
            .field-with-sub {
                margin: 0;
            }

            .sub-label {
                font-size: 12px;
            }

            .b-g-15 {
                height: 15px;
            }

            #fault-comment {
                margin: 0;
            }

            /* message alert move to bottom left */
            .flash-message {
                position: fixed;
                bottom: 0;
                right: 0;
                z-index: 1;
            }

            .flash-message .close {
                margin-left: 15px;
            }

            /* Yes No Switch */
            .yesno-switch-cover {
                display: table-cell;
                position: relative;
                box-sizing: border-box;
            }

            .yesno-switch-cover .button {
                background-color: #fff;
                border-radius: 4px;
                position: relative;
                width: 74px;
                height: 36px;
                overflow: hidden;
            }

            .yesno-switch-cover .button-cover:before {
                counter-increment: button-counter;
                content: counter(button-counter);
                position: absolute;
                right: 0;
                bottom: 0;
                color: #d7e3e3;
                font-size: 12px;
                line-height: 1;
                padding: 5px;
            }

            .yesno-switch-cover .button-cover,
            .yesno-switch-cover .knobs,
            .yesno-switch-cover .layer {
                position: absolute;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
            }

            .yesno-switch-cover .checkbox {
                position: relative;
                width: 100%;
                height: 100%;
                padding: 0;
                margin: 0;
                opacity: 0;
                cursor: pointer;
                z-index: 3;
            }

            .yesno-switch-cover .knobs {
                z-index: 2;
            }

            .yesno-switch-cover .layer {
                width: 100%;
                background-color: #ebf7fc;
                transition: 0.3s ease all;
                z-index: 1;
            }

            .yesno-switch-cover .button.r,
            .yesno-switch-cover .button.r .layer {
                border-radius: 100px;
            }

            .yesno-switch-cover #button-3 .knobs:before {
                content: "NO";
                position: absolute;
                top: 3px;
                left: 4px;
                width: 30px;
                height: 30px;
                color: #fff;
                font-size: 10px;
                font-weight: bold;
                text-align: center;
                line-height: 1;
                padding: 9px 4px;

                background-color: #f44336;
                border-radius: 50%;
                transition: 0.3s ease all, left 0.3s cubic-bezier(0.18, 0.89, 0.35, 1.15);
            }

            .yesno-switch-cover #button-3 .checkbox:active+.knobs:before {
                width: 46px;
                border-radius: 100px;
            }

            .yesno-switch-cover #button-3 #date_purchase_known:checked:active+.knobs:before {
                margin-left: -26px;
            }

            .yesno-switch-cover #button-3 #date_purchase_known:checked+.knobs:before {
                content: "YES";
                left: 42px;
                background-color: #03a9f4;
            }

            .yesno-switch-cover #button-3 #warranty_flag:checked:active+.knobs:before {
                margin-left: -26px;
            }

            .yesno-switch-cover #button-3 #warranty_flag:checked+.knobs:before {
                content: "YES";
                left: 42px;
                background-color: #03a9f4;
            }

            .yesno-switch-cover #button-3 #invalid_serial:checked:active+.knobs:before {
                margin-left: -26px;
            }

            .yesno-switch-cover #button-3 #invalid_serial:checked+.knobs:before {
                content: "YES";
                left: 42px;
                background-color: #03a9f4;
            }


            /* .yesno-switch-cover #button-3 .checkbox:checked:active + .knobs:before {
                margin-left: -26px;
              } */
            /* .yesno-switch-cover #button-3 .checkbox:checked + .knobs:before {
                content: "YES";
                left: 42px;
                background-color: #03a9f4;
              } */

            .yesno-switch-cover #button-3 .checkbox:checked~.layer {
                background-color: #d9effa;
            }

            .yesno-switch-cover #button-3 .checkbox~.layer {
                background-color: #f8e3e3;
            }

            /* root cause analysis  */
            .pacom_fault_desc-wrap {
                display: none;
            }

            .pacom_fault_desc-wrap.show {
                display: block;
            }
    </style>
@endsection
