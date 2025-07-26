<div class="edit-item-popup hide">
    <div class="popup-block">
        <div class="loading">
            <div><img src="{{ asset('images/loading.gif') }}" /></div>
        </div>
        <div class="popup-heading"><span>Edit Item</span><button id="btn-close">x</button></div>
        <div class="popup-body">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group row {{ $errors->has('serial_no') ? ' has-error' : '' }}">
                        <label for="input_sn" class="col-sm-12 col-form-label">Serial Number <span
                                class="req-input">*</span></label>
                        <div class="col-sm-12"><input class="required" type="text" name="serial_no" id="input_sn"
                                value="{{ old('serial_no') }}">
                            <input type="hidden" name="rma-id-input" id="rmaId" value="">
                            <span class="validate-feedback">
                                This field is required
                            </span>
                        </div>
                    </div>
                    {{-- <div class="form-group row {{ $errors->has('date_purchase_known') ? ' has-error' : '' }}">
                      <label for="date_purchase_known" class="col-sm-12 col-form-label">Date Purchase Known?</label>
                      <div class="col-sm-12"><input type="checkbox" name="date_purchase_known" id="date_purchase_known"> If tick, enter the date of purchase
                      <input type="date" class="form-control" name="original_order_date" id="original_order_date" /></div>
                    </div> --}}

                    <input type="hidden" name="user_id" id="userID" />
                    <input type="hidden" name="item_id" id="item_id" />
                    <input type="hidden" name="company_id" id="companyID" />

                    @if ($products)
                        <div class="form-group row {{ $errors->has('product') ? ' has-error' : '' }}">
                            <label for="input_pn" class="col-sm-12 col-form-label">Model <span
                                    class="req-input">*</span></label>
                            <div class="col-sm-12">
                                <select class="required" id="input_pn" name="product">
                                    <option value="">Select</option>

                                    @foreach ($products as $product)
                                        <option value="{{ $product->name }}">{{ $product->name }}</option>
                                    @endforeach

                                </select>
                                <span class="validate-feedback">
                                    This field is required
                                </span>
                            </div>
                        </div>
                    @endif


                </div>
                <div class="col-md-6">

                    @if ($issues)

                        <div class="form-group row {{ $errors->has('issue') ? ' has-error' : '' }}">
                            <label for="input_i" class="col-sm-12 col-form-label">Fault Category <span
                                    class="req-input">*</span></label>
                            <div class="col-sm-12">
                                <select id="selectIssue" class="selectedFaults required" name="issue"
                                    style="margin-bottom: 0;min-height:150px;" multiple>
                                    <option value=""></option>
                                    @foreach ($issues as $issue)
                                        <option value="{{ $issue->name }}">{{ $issue->name }}</option>
                                    @endforeach

                                </select>
                                <span class="validate-feedback">
                                    This field is required
                                </span><span class="text-sm text-dark text-fault-note"
                                    style="font-size:13px;display:block;margin-bottom:20px;">Hold CTR Key and Left click
                                    to select multiple faults</span>
                            </div>
                        </div>

                        {{-- customer comment --}}
                        <div class="form-group">
                            <strong>Please specify additional comment (E.g issues)</strong>
                            <div>
                                <textarea id="fault-comment" name="fault_described" value=""></textarea>
                            </div>
                            <span class="validate-feedback">
                                This field is required
                            </span>
                        </div>

                    @endif

                    <div class="form-group row ">
                        <label for="input_sn" class="col-sm-4 col-form-label"></label>
                        <div class="col-sm-12" style="display:flex;justify-content:flex-end;"><button
                                class="btn btn-primary" id="updateButtonCust">Update Fault Item</span></button></div>
                    </div>
                </div>
            </div>


        </div><!-- Pop up heading End -->
    </div><!-- Pop up block End -->
</div><!-- Add item End -->

<script>
    (function($) {
        const isOther = (selectField, faultComment) => {
            const selected = selectField.val();
            const hasIncluded = (selected.indexOf("Other") > -1);
            if (hasIncluded && !faultComment.val()) {
                faultComment.closest('validate-feedback').css("display", "block");
                // $(faultComment + ' .validate-feedback').css("display", "block");
                return true;
            } else {
                return false;
            }
        }

        $('#updateButtonCust').on('click', async (e) => {
            //const { item } = e.currentTarget.dataset;
            const item_id = $('.edit-item-popup #item_id').val();
            const serial_number = $('.edit-item-popup #input_sn').val();
            const model = $('.edit-item-popup #input_pn').val();
            const repair_cost = $('.edit-item-popup #repair_cost').val();
            //let date_purchased = $('.edit-item-popup #date_purchase_known').val();
            const fault_comment = $('.edit-item-popup #fault-comment').val();
            //let original_order_date = $('.edit-item-popup #original_order_date').val();
            const faults = $('.edit-item-popup #selectIssue').val();
            const _token = $('meta[name="csrf-token"]').attr('content');
            const rma_id = $('.edit-item-popup #rmaId').val();

            const otherSelected = isOther($('.edit-item-popup #selectIssue'), $(
                '.edit-item-popup #fault-comment'))

            if (!serial_number) {
                $('.edit-item-popup #input_sn').closest('.form-group').addClass('validate-error');
                $('.edit-item-popup #input_sn').closest('.form-group').find('.validate-feedback')
                    .addClass('show');
            } else {
                $('.edit-item-popup #input_sn').closest('.form-group').removeClass('validate-error');
                $('.edit-item-popup #input_sn').closest('.form-group').find('.validate-feedback')
                    .removeClass('show');
            }

            if (!model) {
                $('.edit-item-popup #input_pn').closest('.form-group').addClass('validate-error');
                $('.edit-item-popup #input_pn').closest('.form-group').find('.validate-feedback')
                    .addClass('show');
            } else {
                $('.edit-item-popup #input_pn').closest('.form-group').removeClass('validate-error');
                $('.edit-item-popup #input_pn').closest('.form-group').find('.validate-feedback')
                    .removeClass('show');
            }

            if (faults < 1) {
                $('.edit-item-popup #selectIssue').closest('.form-group').addClass('validate-error');
                $('.edit-item-popup #selectIssue').closest('.form-group').find('.validate-feedback')
                    .addClass('show');
            } else {
                $('.edit-item-popup #selectIssue').closest('.form-group').removeClass('validate-error');
                $('.edit-item-popup #selectIssue').closest('.form-group').find('.validate-feedback')
                    .removeClass('show');
            }

            if (!otherSelected) {
                $('.edit-item-popup #fault-comment').closest('.form-group').addClass('validate-error');
                $('.edit-item-popup #fault-comment').removeAttr('required', 'required');
            } else {
                $('.edit-item-popup #fault-comment').closest('.form-group').removeClass(
                    'validate-error');
            }

            if (!serial_number || !model || faults < 1) {
                return;
            } else {
                if (otherSelected == true && fault_comment == '') {
                    $('.edit-item-popup #fault-comment').closest('.form-group').addClass(
                        'validate-error');
                    return;
                } else {
                    $('.edit-item-popup #fault-comment').closest('.form-group').removeClass(
                        'validate-error');
                    $('.edit-item-popup .loading').addClass('show');
                }
            }




            if (serial_number && model && faults.length > 0) {
                const payload = {
                    _token,
                    rma_id,
                    serial_number,
                    model,
                    fault_comment,
                    faults: JSON.stringify(faults.map(fault => ({
                        fault,
                        rma_items_id: item_id
                    })))
                };

                $.ajax({
                    type: 'PUT',
                    url: '{{ URL::to('/rmaUpdatebyCust') }}' + `/${item_id}`,
                    data: {
                        ...payload
                    },
                    success(data) {
                        if (data.success) {
                            $('.edit-item-popup .loading').removeClass('show');
                            updateItem(data['item']);
                            clearField();
                            window.location.hash = "#addItem"
                            location.reload();

                            $('.edit-item-popup').addClass('hide');

                        }
                    },
                    error(data) {
                        console.log('Error:', data);
                    },
                });
            }
        });
    })(jQuery)
</script>
<style>
    .edit-item-popup .validate-error #fault-comment {
        border-color: red;
    }

    .validate-feedback {
        display: none;
    }

    .validate-error select,
    .validate-error input {
        border: 1px solid red;
    }

    input#original_order_date {
        margin-top: 10px;
    }

    .edit-item-popup .loading {
        width: 100%;
        height: 100%;
        position: absolute;
        z-index: 9999;
        background: rgba(0, 0, 0, 0.1);
        display: none;
    }

    .edit-item-popup .loading>div {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 999;
    }

    .edit-item-popup .loading img {
        width: 100px;
    }
</style>
