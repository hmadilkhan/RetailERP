<section id="totaldiv" class="hidden grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
        <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Total Receipt Counts</div>
        <div id="totalreceipts" class="mt-4 text-3xl font-black text-erp-ink">0</div>
        <p class="mt-2 text-sm text-erp-mute">Receipt</p>
    </div>
    <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
        <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Total Items</div>
        <div id="totalorders" class="mt-4 text-3xl font-black text-erp-ink">0</div>
        <p class="mt-2 text-sm text-erp-mute">Items</p>
    </div>
    <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
        <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Total Amount</div>
        <div class="mt-4 text-3xl font-black text-erp-ink">{{ session('currency') }} <span id="totalamount">0</span></div>
        <p class="mt-2 text-sm text-erp-mute">Amount</p>
    </div>
</section>
