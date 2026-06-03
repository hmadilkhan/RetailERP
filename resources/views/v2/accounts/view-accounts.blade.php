@extends('layouts.master-tailwind')

@section('title', 'Bank Accounts')
@section('page_title', 'Bank Accounts')
@section('page_subtitle', 'Manage branch bank accounts, deposits, and website bank links.')

@section('content')
    @php
        $accountCollection = collect($getaccounts ?? []);
        $websiteCollection = collect($website ?? []);
    @endphp

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Accounts</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($accountCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Bank accounts in current branch</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Website Links</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($accountCollection->whereNotNull('website_id')->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Linked website accounts</p>
            </div>
            <a href="{{ url('bankaccounts-details') }}" class="flex rounded-lg border border-erp bg-erp p-5 text-white shadow-sm transition hover:bg-erp-dark sm:col-span-2">
                <div class="self-end">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-white/70">Action</div>
                    <div class="mt-4 text-xl font-black">Create Account</div>
                    <p class="mt-2 text-sm text-white/75">Add a bank account</p>
                </div>
            </a>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Bank Account List</h2>
                    <p class="mt-1 text-sm text-erp-mute">Review accounts, create deposits, edit records, or manage website links.</p>
                </div>
                <input type="search" id="accountFilter" placeholder="Filter accounts..." class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Account</th>
                            <th class="px-5 py-3 text-left font-bold">Type</th>
                            <th class="px-5 py-3 text-left font-bold">Bank</th>
                            <th class="px-5 py-3 text-left font-bold">Branch</th>
                            <th class="px-5 py-3 text-left font-bold">Website</th>
                            <th class="px-5 py-3 text-right font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="accountRows" class="divide-y divide-slate-100">
                        @forelse($accountCollection as $value)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <img class="h-11 w-11 rounded-lg object-cover ring-1 ring-slate-200" src="{{ asset('assets/images/bank-account/' . (!empty($value->image) ? $value->image : 'placeholder.jpg')) }}" alt="{{ $value->account_title }}">
                                        <div>
                                            <div class="font-bold text-erp-ink">{{ $value->account_title }}</div>
                                            <div class="mt-1 text-xs text-erp-mute">{{ $value->account_no }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-erp-text">{{ $value->account_type }}</td>
                                <td class="px-5 py-4 font-semibold text-erp-ink">{{ $value->bank_name }}</td>
                                <td class="px-5 py-4 text-erp-text">{{ $value->branch_name }}</td>
                                <td class="px-5 py-4">
                                    @if(isset($value->website_id))
                                        <span class="rounded-md bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">{{ $value->website_name ?? 'Linked' }}</span>
                                    @else
                                        <span class="text-erp-mute">Not linked</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ url('/getaccountdetails') }}/{{ Crypt::encrypt($value->bank_account_id) }}" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100">Edit</a>
                                        <a href="{{ url('/create-deposit') }}/{{ Crypt::encrypt($value->bank_account_id) }}" class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-bold text-sky-700 transition hover:bg-sky-100">Deposit</a>
                                        @if($websiteCollection->isNotEmpty())
                                            <input type="hidden" id="bankAccountId{{ $value->bank_account_id }}" value="{{ Crypt::encrypt($value->bank_account_id) }}">
                                            <input type="hidden" id="websiteBankUniqueId{{ $value->bank_account_id }}" value="{{ Crypt::encrypt($value->website_bank_id) }}">
                                            <button type="button" onclick="websiteSetting(@js(isset($value->website_id) ? $value->website_id : 0), @js($value->bank_name), @js($value->bank_account_id))" class="rounded-lg border border-erp-line px-3 py-2 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">
                                                {{ isset($value->website_id) ? 'Unlink' : 'Link' }}
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-erp-mute">No bank accounts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div id="websiteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
        <div class="w-full max-w-lg rounded-lg bg-white shadow-menu">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Link To Website</h3>
                <button type="button" onclick="closeWebsiteModal()" class="rounded-lg px-2 py-1 text-xl leading-none text-erp-mute hover:bg-slate-100">x</button>
            </div>
            <div class="space-y-4 p-5">
                <input type="hidden" id="bank_md">
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Website</span>
                    <select id="website_md" data-placeholder="Select Website" class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Website</option>
                        @foreach($websiteCollection as $val)
                            <option value="{{ Crypt::encrypt($val->id) }}">{{ $val->name }}</option>
                        @endforeach
                    </select>
                </label>
                <div id="accountStatus" class="text-sm font-semibold text-erp-mute"></div>
            </div>
            <div class="flex justify-end border-t border-erp-line px-5 py-4">
                <button type="button" id="btnSubmit" class="rounded-lg bg-erp px-4 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Save</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function setAccountStatus(message, success = true) {
            const status = document.getElementById('accountStatus');
            status.textContent = message;
            status.className = 'text-sm font-semibold ' + (success ? 'text-emerald-700' : 'text-rose-700');
        }

        function openWebsiteModal() {
            document.getElementById('websiteModal').classList.remove('hidden');
            document.getElementById('websiteModal').classList.add('flex');
        }

        function closeWebsiteModal() {
            document.getElementById('websiteModal').classList.add('hidden');
            document.getElementById('websiteModal').classList.remove('flex');
        }

        function websiteSetting(value, bank, code) {
            if (value === 0) {
                document.getElementById('bank_md').value = document.getElementById('bankAccountId' + code).value;
                openWebsiteModal();
                return;
            }

            if (!confirm('Unlink website from this ' + bank + ' bank account?')) {
                return;
            }

            fetch("{{ route('bankUnLinkToWebsite') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: JSON.stringify({ uniqueId: document.getElementById('websiteBankUniqueId' + code).value })
            }).then(function (response) {
                if (response.ok) {
                    window.location = "{{ url('/view-accounts') }}";
                } else {
                    setAccountStatus('Unable to unlink website.', false);
                }
            }).catch(() => setAccountStatus('Unable to unlink website.', false));
        }

        document.getElementById('btnSubmit').addEventListener('click', function () {
            if (!document.getElementById('website_md').value) {
                setAccountStatus('Select website first.', false);
                return;
            }

            fetch("{{ route('bankLinkToWebsite') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: JSON.stringify({
                    website: document.getElementById('website_md').value,
                    bank: document.getElementById('bank_md').value
                })
            }).then(function (response) {
                if (response.ok) {
                    window.location = "{{ url('/view-accounts') }}";
                } else {
                    setAccountStatus('Unable to link website.', false);
                }
            }).catch(() => setAccountStatus('Unable to link website.', false));
        });

        document.getElementById('accountFilter').addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#accountRows tr').forEach(row => row.hidden = !row.textContent.toLowerCase().includes(term));
        });
    </script>
@endpush
