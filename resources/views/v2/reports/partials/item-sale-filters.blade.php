<label class="block md:col-span-3">
    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Product Name</span>
    <select id="product_name" name="product_name" class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" data-placeholder="Select Product Name">
        <option value="">Select Product Name</option>
        @foreach($inventory as $value)
            <option value="{{ $value->id }}">{{ $value->item_code . ' | ' . $value->product_name }}</option>
        @endforeach
    </select>
</label>

<label class="block md:col-span-3">
    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">From Date</span>
    <input type="date" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" id="fromdate" name="fromdate">
</label>

<label class="block md:col-span-3">
    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">To Date</span>
    <input type="date" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" id="todate" name="todate">
</label>

<label class="block md:col-span-3">
    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Customer</span>
    <select id="customer" name="customer" data-placeholder="Select Customer" class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
        <option value="">Select Customer</option>
        @foreach($customer as $value)
            <option value="{{ $value->id }}">{{ $value->name }}</option>
        @endforeach
    </select>
</label>

@if($includeDepartment)
    <label class="block md:col-span-3">
        <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Departments</span>
        <select id="department" name="department" class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" data-placeholder="Select Departments">
            <option value="">Select Department</option>
            @foreach($departments as $department)
                <option value="{{ $department->department_id }}">{{ $department->department_name }}</option>
            @endforeach
        </select>
    </label>
@endif

<label class="block md:col-span-3">
    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Payment Mode</span>
    <select id="paymentmode" name="paymentmode" class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" data-placeholder="Select Payment Mode">
        <option value="">Select Payment Mode</option>
        @foreach($paymentMode as $value)
            <option value="{{ $value->payment_id }}">{{ $value->payment_mode }}</option>
        @endforeach
    </select>
</label>

<label class="block md:col-span-3">
    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Mode</span>
    <select id="ordermode" name="ordermode" data-placeholder="Select Mode" class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
        <option value="">Select Mode</option>
        @foreach($mode as $value)
            <option value="{{ $value->order_mode_id }}">{{ $value->order_mode }}</option>
        @endforeach
    </select>
</label>

<label class="block md:col-span-3">
    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Branch</span>
    <select id="branch" name="branch" data-placeholder="Select Branch" class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
        <option value="all">All Branch</option>
        @foreach($branch as $value)
            <option value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>
        @endforeach
    </select>
</label>

<label class="block md:col-span-3">
    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Terminal</span>
    <select id="terminal" name="terminal" data-placeholder="Select Terminal" class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
        <option value="">Select Terminal</option>
    </select>
</label>

<label class="block md:col-span-3">
    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Report Type</span>
    <select id="report_type" name="report_type" data-placeholder="Select Report Type" class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
        <option value="consolidated">Consolidated</option>
        <option value="seperate">Separate</option>
    </select>
</label>

<label class="block md:col-span-3">
    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Declaration Type</span>
    <select id="declaration" name="declaration" data-placeholder="Select Declaration Type" class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
        <option value="declaration">Declaration</option>
        <option value="datewise">Datewise</option>
    </select>
</label>
