<section class="panels-wells">

    <div class="card">
        <div class="card-header">
            <h5 class="card-header-text" id="title-hcard">Create Expense</h5>
        </div>
        <div class="card-block">
            <form id="expenseform" method="POST" class="form-horizontal">
                @csrf
                <input type="hidden" id="hidd_amt" name="hidd_amt">
                <input type="hidden" id="hidd_id" name="hidd_id" value="0">
                <div class="row">
                    <div class="col-lg-4 col-sm-12">
                        <div class="form-group">
                            <label class="form-control-label">Expense Category</label>
                            <i id="btn_exp_cat" class="icofont icofont-plus f-right text-success" data-toggle="tooltip"
                                data-placement="top" title="Add Expense Category"></i>
                            <select class="select2" data-placeholder="Select Category" id="exp_cat" name="exp_cat">
                                <option value="">Select Category</option>

                            </select>
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-12">
                        <div class="form-group">
                            <label class="form-control-label">Date</label>
                            <input type='text' class="form-control" id="expensedate" name="expensedate"
                                placeholder="DD-MM-YYYY" />
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <!-- Expense Details -->
                    <div class="col-lg-4 col-sm-12">
                        <div class="form-group">
                            <label class="form-control-label">Amount</label>
                            <input type="number" id="amount" name="amount" class="form-control" value="0"
                                min="1" />
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-9">
                        <div class="form-group">
                            <label class="form-control-label">Narration</label>
                            <textarea id="details" name="details" class="form-control "></textarea>
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <button type="button" id="btn_clear" class="btn btn-danger btn-circle f-right  m-l-10"
                            data-toggle="tooltip" data-placement="top" title="" data-original-title="Clear"
                            style="margin-top: 30px;">
                            <i class="icofont icofont-error"></i>&nbsp; Clear</button>

                        <button type="button" id="btn_save" class="btn btn-success btn-circle f-right"
                            data-toggle="tooltip" data-placement="top" title="" data-original-title="Save"
                            style="margin-top: 30px;"> <i class="icofont icofont-plus"></i>&nbsp; Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
