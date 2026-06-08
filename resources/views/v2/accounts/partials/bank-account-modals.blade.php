<div id="bank-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
    <div class="w-full max-w-md rounded-lg bg-white shadow-menu">
        <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
            <h3 class="text-base font-bold text-erp-ink">Add Bank</h3>
            <button type="button" onclick="closeModal('bank-modal')" class="rounded-lg px-2 py-1 text-xl leading-none text-erp-mute hover:bg-slate-100">x</button>
        </div>
        <div class="p-5">
            <label class="block">
                <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Bank Name</span>
                <input type="text" name="bankname" id="bankname" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
            </label>
        </div>
        <div class="flex justify-end border-t border-erp-line px-5 py-4">
            <button type="button" class="rounded-lg bg-erp px-4 py-2 text-sm font-bold text-white transition hover:bg-erp-dark" onclick="addbank()">Add Bank</button>
        </div>
    </div>
</div>

<div id="branch-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
    <div class="w-full max-w-md rounded-lg bg-white shadow-menu">
        <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
            <h3 class="text-base font-bold text-erp-ink">Add Branch</h3>
            <button type="button" onclick="closeModal('branch-modal')" class="rounded-lg px-2 py-1 text-xl leading-none text-erp-mute hover:bg-slate-100">x</button>
        </div>
        <div class="space-y-4 p-5">
            <label class="block">
                <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Bank</span>
                <select class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" data-placeholder="Select Bank" id="bankmodal" name="bankmodal">
                    <option value="">Select Bank</option>
                    @foreach($getbank as $value)
                        <option value="{{ $value->bank_id }}">{{ $value->bank_name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Branch Name</span>
                <input type="text" name="branchname" id="branchname" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
            </label>
        </div>
        <div class="flex justify-end border-t border-erp-line px-5 py-4">
            <button type="button" class="rounded-lg bg-erp px-4 py-2 text-sm font-bold text-white transition hover:bg-erp-dark" onclick="addbranch()">Add Branch</button>
        </div>
    </div>
</div>
