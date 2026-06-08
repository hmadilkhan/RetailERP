@extends('layouts.master-tailwind')

@section('title', 'Create Bank')
@section('page_title', 'Create Bank')
@section('page_subtitle', 'Add a bank master record with an optional logo.')

@section('content')
    <div class="space-y-6">
        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Bank Details</h2>
                    <p class="mt-1 text-sm text-erp-mute">This bank will be available for accounts and bank discounts.</p>
                </div>
                <a href="{{ url('get-banks') }}" class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Back</a>
            </div>

            <form method="post" action="{{ url('save-bank') }}" enctype="multipart/form-data" class="grid gap-5 p-5 lg:grid-cols-12">
                @csrf
                @method('post')

                <label class="block lg:col-span-5">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Bank Name</span>
                    <input class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" type="text" required name="bankname" id="bankname">
                </label>

                <div class="lg:col-span-4">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Bank Logo</span>
                    <div class="mt-2 flex items-center gap-4">
                        <img id="vdpimg" src="{{ asset('assets/images/placeholder.jpg') }}" class="h-24 w-24 rounded-lg object-cover ring-1 ring-slate-200" alt="Bank logo preview">
                        <label class="flex-1">
                            <input type="file" name="vdimg" id="vdimg" accept="image/*" class="block w-full text-sm text-erp-text file:mr-4 file:rounded-lg file:border-0 file:bg-erp file:px-4 file:py-2 file:text-sm file:font-bold file:text-white hover:file:bg-erp-dark">
                            @if ($errors->has('vdimg'))
                                <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $errors->first('vdimg') }}</span>
                            @endif
                        </label>
                    </div>
                </div>

                <div class="flex items-end lg:col-span-3">
                    <button type="submit" id="btnsubmit" class="h-10 rounded-lg bg-erp px-5 text-sm font-bold text-white transition hover:bg-erp-dark">Create Bank</button>
                </div>
            </form>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('vdimg').addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = event => document.getElementById('vdpimg').src = event.target.result;
            reader.readAsDataURL(file);
        });
    </script>
@endpush
