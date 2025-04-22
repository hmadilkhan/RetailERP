<div>
    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Add / Edit Vehicle Brand</h5>
            </div>
            <div class="card-block">
                <div class=" py-4">
                    <div class="row">
                        <!-- Left Column: Brands -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header fw-bold">
                                    Vehicle Brands
                                </div>
                                <ul class="list-group list-group-flush">
                                    @foreach ($brands as $brand)
                                        <li class="list-group-item d-flex justify-content-between align-items-center @if ($selectedBrand && $selectedBrand->id === $brand->id) bg-light @endif"
                                            wire:click="selectBrand({{ $brand->id }})" style="cursor: pointer;">
                                            <div>
                                                @if ($brand->image)
                                                    <img src="{{ asset('storage/' . $brand->image) }}"
                                                        alt="{{ $brand->name }}" height="50" class="me-2">
                                                @endif
                                                {{ $brand->name }}
                                            </div>
                                            <div>
                                                <button class="btn btn-sm btn-outline-primary"
                                                    wire:click.stop="editBrand({{ $brand->id }})">✎</button>
                                                <button class="btn btn-sm btn-outline-danger"
                                                    wire:click.stop="deleteBrand({{ $brand->id }})">🗑️</button>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="card-footer" wire:ignore>
                                    <button type="button" class="btn btn-primary w-100" wire:click="addBrand()">+ Add
                                        Brand</button>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Models -->
                        <div class="col-md-8">
                            @if ($selectedBrand)
                                <div class="card">
                                    <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                                        Models for Selected Brand
                                        <span>
                                            Brand:
                                            @if ($selectedBrand->image)
                                                <img src="{{ asset('storage/' . $selectedBrand->image) }}"
                                                    alt="{{ $selectedBrand->name }}" height="20">
                                            @endif
                                            {{ $selectedBrand->name }}
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <button class="btn btn-primary mb-3" wire:click="addModel">+ Add Model</button>
                                        <ul class="list-group">
                                            @foreach ($models as $model)
                                                <li
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        @if ($model->image)
                                                            <img src="{{ asset('storage/' . $model->image) }}"
                                                                alt="{{ $model->name }}" height="50"
                                                                class="me-2">
                                                        @endif
                                                        {{ $model->name }}
                                                    </div>
                                                    <div>
                                                        <button class="btn btn-sm btn-outline-success"
                                                            wire:click="modelInventory({{ $model->id }})">👁</button>
                                                        <button class="btn btn-sm btn-outline-primary"
                                                            wire:click="editModel({{ $model->id }})">✎</button>
                                                        <button class="btn btn-sm btn-outline-danger"
                                                            wire:click="deleteModel({{ $model->id }})">🗑️</button>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    Please select a brand to view or manage its models.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if ($showBrandModal)
            <div class="modal fade in show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ $brandId ? 'Edit Brand' : 'Add Brand' }}</h5>
                            <button type="button" class="btn-close"
                                wire:click="$set('showBrandModal', false)"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Brand Name</label>
                                <input type="text" class="form-control" wire:model.defer="brandName">
                                @error('brandName')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label>Brand Logo</label>
                                <input type="file" class="form-control" wire:model="brandLogo">
                                @error('brandLogo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" wire:click="$set('showBrandModal', false)">Cancel</button>
                            <button class="btn btn-primary" wire:click="saveBrand">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if ($showModelModal)
            <div class="modal fade in show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"> {{ $modelId ? 'Edit Model' : 'Add Model' }}</h5>
                            <button type="button" class="btn-close"
                                wire:click="$set('showModelModal', false)"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Model Name</label>
                                <input type="text" class="form-control" wire:model.defer="modelName">
                                @error('modelName')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label>Model Logo</label>
                                <input type="file" class="form-control" wire:model="modelLogo">
                                @error('modelLogo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" wire:click="$set('showModelModal', false)">Cancel</button>
                            <button class="btn btn-primary" wire:click="saveModel">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>
    @if ($showModelInventory)
        <section class="panels-wells">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h5 class="card-header-text m-0">
                        Vehicle Inventories
                        <span class="ms-2">
                            Model:
                            @if ($selectedModel->image)
                                <img src="{{ asset('storage/' . $selectedModel->image) }}"
                                    alt="{{ $selectedModel->name }}" height="20">
                            @endif
                            {{ $selectedModel->name }}
                        </span>
                    </h5>

                    <button type="button" class="btn btn-primary btn-sm ms-auto" wire:click="addBrand()">+ Add
                        Inventory</button>
                </div>

                <div class="card-block">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-light ">
                                    <h5 class="mb-0">
                                        <i class="icofont icofont-search"></i> Search and Add Inventory
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label">
                                                    <i class="icofont icofont-barcode"></i> Search Products
                                                </label>
                                                <select id="productId" wire:ignore.self class="form-control select2">
                                                    <option value="">Type to search products...</option>
                                                </select>
                                                {{-- <small wire:ignore.self class="form-text text-muted">
                                                    Start typing to search for products
                                                </small> --}}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            {{-- <div class="form-group">
                                                <label class="form-control-label">
                                                    <i class="icofont icofont-info-circle"></i> Selected Product
                                                </label>
                                                <div id="selectedProduct" class="p-2 bg-light rounded">
                                                    <p class="mb-0 text-muted">No product selected</p>
                                                </div>
                                            </div> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header border-0 py-3">
                                    <div class=" d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0 fw-bold">
                                            <i class="bi bi-box-seam me-2"></i>Current Inventory
                                        </h5>
                                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                                            <i class="bi bi-box me-1"></i>{{ count($this->inventories) }} Items
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="row g-3 p-3">
                                        @forelse ($this->inventories as $inventory)
                                            <div class="col-md-6 col-lg-3">
                                                <div class="card h-100 border-0 shadow-sm hover-shadow">
                                                    <div class="position-relative">
                                                        @if($inventory->inventory->image)
                                                            <img src="{{ asset('storage/images/products/' . $inventory->inventory->image) }}" 
                                                                 alt="{{ $inventory->inventory->product_name }}"
                                                                 class="card-img-top"
                                                                 style="height: 200px; object-fit: cover;">
                                                        @else
                                                            <div class="bg-light d-flex align-items-center justify-content-center"
                                                                 style="height: 200px;">
                                                                <i class="bi bi-box text-muted" style="font-size: 3rem;"></i>
                                                            </div>
                                                        @endif
                                                        <div class="position-absolute top-0 end-0 m-2">
                                                            <span class="badge bg-success bg-opacity-90">
                                                                <i class="bi bi-check-circle me-1"></i>In Stock
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <h5 class="card-title text-primary fw-bold mb-2">
                                                            {{ $inventory->inventory->product_name }}
                                                        </h5>
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span class="badge bg-info bg-opacity-10 text-info">
                                                                <i class="icofont icofont-barcode me-1"></i>
                                                                {{ $inventory->inventory->item_code }}
                                                            </span>
                                                        </div>
                                                        @if($inventory->inventory->description)
                                                            <p class="card-text text-muted small mb-3">
                                                                <i class="icofont icofont-info-circle me-1"></i>
                                                                {{ Str::limit($inventory->inventory->description, 100) }}
                                                            </p>
                                                        @endif
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <small class="text-muted">
                                                                <i class="icofont icofont-clock-time me-1"></i>
                                                                Added {{ $inventory->created_at->diffForHumans() }}
                                                            </small>
                                                            <div class="btn-group">
                                                                <button class="btn btn-sm btn-outline-primary" 
                                                                        wire:click="viewDetails({{ $inventory->id }})">
                                                                    <i class="icofont icofont-eye"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-outline-danger" 
                                                                        wire:click="confirmDelete({{ $inventory->id }})">
                                                                    <i class="icofont icofont-ui-delete"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12">
                                                <div class="text-center py-5">
                                                    <div class="mb-4">
                                                        <i class="icofont icofont-box text-muted" style="font-size: 4rem;"></i>
                                                    </div>
                                                    <h4 class="text-muted mb-3">No Inventory Items</h4>
                                                    <p class="text-muted mb-4">Your inventory is currently empty. Start by searching and adding products.</p>
                                                    <button class="btn btn-primary">
                                                        <i class="icofont icofont-search me-2"></i>Search Products
                                                    </button>
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
    <!-- Delete Confirmation Modal -->
    <div class="modal fade in" id="deleteInventoryModal" tabindex="-1" aria-labelledby="deleteInventoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteInventoryModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to remove this inventory item? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" wire:click="removeInventory({{ $deleteId }})">Delete</button>
                </div>
            </div>
        </div>
    </div>
    <div>
        @script
            <script>
                // Define the initialization function in the global scope
                window.initializeSelect2 = function() {
                    // Check if Select2 is already initialized
                    if ($('#productId').hasClass('select2-hidden-accessible')) {
                        return; // Exit if already initialized
                    }

                    // Initialize Select2
                    $('#productId').select2({
                        ajax: {
                            url: "{{ route('search-inventory') }}",
                            type: 'GET',
                            dataType: 'json',
                            delay: 250,
                            data: function(params) {
                                return {
                                    q: params.term,
                                    page: params.page || 1
                                };
                            },
                            processResults: function(data, params) {
                                if (data === 0) {
                                    return {
                                        results: []
                                    };
                                }

                                if (data && data.items) {
                                    return {
                                        results: $.map(data.items, function(item) {
                                            return {
                                                id: item.id,
                                                text: item.product_name + " | " + item.item_code,
                                                description: item.description || 'No description available'
                                            };
                                        })
                                    };
                                }

                                return {
                                    results: []
                                };
                            },
                            error: function(xhr, status, error) {
                                // Handle error silently
                            },
                            cache: true
                        },
                        placeholder: 'Type to search products...',
                        minimumInputLength: 1,
                        width: '100%',
                        templateResult: formatProduct,
                        templateSelection: formatProductSelection
                    });

                    // Format the product display in dropdown
                    function formatProduct(product) {
                        if (!product.id) return product.text;
                        return $('<span><strong>' + product.text + '</strong><br><small class="text-muted">' +
                            (product.description || 'No description available') + '</small></span>');
                    }

                    // Format the selected product
                    function formatProductSelection(product) {
                        if (!product.id) return product.text;
                        return product.text;
                    }

                    // Handle selection
                    $('#productId').on('select2:select', function(e) {
                        let selectedData = e.params.data;

                       

                        // Add to inventory
                        @this.addInventory(selectedData.id);

                        // Clear selection after a short delay
                        setTimeout(() => {
                            $(this).val('').trigger('change');
                        }, 100);
                    });
                };

                // Initialize when document is ready
                $(document).ready(function() {
                    window.initializeSelect2();
                });

                // Handle Livewire updates
                Livewire.hook('morph.updating', () => {
                    if ($('#productId').hasClass('select2-hidden-accessible')) {
                        $('#productId').select2('destroy');
                    }
                });

                Livewire.hook('morph.updated', () => {
                    // Only reinitialize if the element exists and is visible
                    if ($('#productId').length && $('#productId').is(':visible')) {
                        window.initializeSelect2();
                    }
                });

                // Handle delete confirmation
                window.addEventListener('showDeleteModal', event => {
                    const modal = new bootstrap.Modal(document.getElementById('deleteInventoryModal'), {
                        backdrop: 'static',
                        keyboard: false
                    });
                    modal.show();
                });

                // Handle modal closing
                window.addEventListener('hideDeleteModal', event => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteInventoryModal'));
                    if (modal) {
                        modal.hide();
                    }
                });

                // Handle successful deletion
                window.addEventListener('inventoryDeleted', event => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteInventoryModal'));
                    if (modal) {
                        modal.hide();
                    }
                });
            </script>
        @endscript
    </div>


<style>
    .hover-shadow {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .card-img-top {
        border-top-left-radius: 0.375rem;
        border-top-right-radius: 0.375rem;
    }
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }
</style>
</div>