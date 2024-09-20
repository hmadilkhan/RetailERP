<section class="panels-wells">
    <div class="card">
        <div class="card-header">
            <h5 class="card-header-text">Upload Inventory</h5>
            <a href="{{ route('create-invent') }}" data-toggle="tooltip" data-placement="bottom" title=""
                data-original-title="Create Inventory"
                class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i
                    class="icofont icofont-plus m-r-5"></i> CREATE INVENTORY</a>

            <a href="{{ url('get-sample-csv') }}" data-toggle="tooltip" data-placement="bottom" title=""
                data-original-title="Download Sample"
                class="btn btn-success waves-effect waves-light f-right d-inline-block m-r-10"> <i
                    class="icofont icofont-plus m-r-5"></i> Download Sample</a>
        </div>
        <div class="card-block">
            <livewire:Inventory.inventory-upload wire:key="{{ str()->random(10) }}"/>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <livewire:Inventory.inventory-filter lazy wire:key="{{ str()->random(10) }}"/>
        </div>
        <div class="card-block">
            <livewire:Inventory.inventory-list lazy wire:key="{{ str()->random(10) }}"/>
        </div>
    </div>
</section>
