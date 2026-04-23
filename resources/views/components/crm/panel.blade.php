@props([
    'title' => null,
    'subtitle' => null,
    'class' => '',
    'contentClass' => '',
])

<section {{ $attributes->merge(['class' => 'rounded-[32px] border border-white/70 bg-white/90 p-6 shadow-crm ' . $class]) }}>
    @if ($title || $subtitle || isset($actions))
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                @if ($title)
                    <h2 class="text-xl font-semibold tracking-tight text-crm-ink">{{ $title }}</h2>
                @endif
                @if ($subtitle)
                    <p class="mt-1 text-sm text-crm-mute">{{ $subtitle }}</p>
                @endif
            </div>

            @if (isset($actions))
                <div>{{ $actions }}</div>
            @endif
        </div>
    @endif

    <div class="{{ $contentClass }}{{ ($title || $subtitle || isset($actions)) ? ' mt-6' : '' }}">
        {{ $slot }}
    </div>
</section>
