@php
    $tabs = [
        ['route' => 'sales-returns.duplicate', 'label' => '1. Duplicate', 'url' => route('sales-returns.duplicate')],
        ['route' => 'sales-returns.edit', 'label' => '2. Edit / Recalculate', 'url' => route('sales-returns.duplicate') . '#after-duplicate'],
        ['route' => 'sales-returns.fbr', 'label' => '3. Send to FBR', 'url' => route('sales-returns.fbr')],
    ];
@endphp

<nav class="flex flex-wrap items-center gap-2 border-b border-erp-line pb-4">
    @foreach ($tabs as $tab)
        @php
            $active = request()->routeIs($tab['route']);
        @endphp
        <a href="{{ $tab['url'] }}"
           class="rounded-lg px-4 py-2 text-sm font-bold transition
                  {{ $active
                      ? 'border border-erp bg-erp text-white'
                      : 'border border-erp-line text-erp-text hover:border-erp hover:text-erp-dark' }}">
            {{ $tab['label'] }}
        </a>
    @endforeach
    <a href="{{ url('orders-view') }}"
       class="ml-auto text-sm font-bold text-erp-dark hover:text-erp">
        &larr; Back to Orders
    </a>
</nav>
