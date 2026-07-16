<div class="mb-3">
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('sales-returns.duplicate') ? 'active' : '' }}"
               href="{{ route('sales-returns.duplicate') }}">1. Duplicate Orders</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('sales-returns.edit') ? 'active' : '' }}"
               href="{{ route('sales-returns.duplicate') }}#edit-hint">2. Edit / Recalculate</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('sales-returns.fbr') ? 'active' : '' }}"
               href="{{ route('sales-returns.fbr') }}">3. Send to FBR</a>
        </li>
        <li class="nav-item ms-auto">
            <a class="nav-link" href="{{ url('orders-view') }}">Back to Orders</a>
        </li>
    </ul>
</div>
