<div 
    x-data="{ show: @entangle($attributes->wire('model')) }" 
    x-show="show" 
    x-transition 
    @keydown.escape.window="show = false"
    class="modal fade show d-block" 
    tabindex="-1" 
    style="display: none; background: rgba(0,0,0,0.5)">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $title }}</h5>
                <button type="button" class="btn-close" @click="show = false"></button>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            <div class="modal-footer">
                {{ $footer }}
            </div>
        </div>
    </div>
</div>
