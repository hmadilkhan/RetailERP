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
                                        {{-- <div class="accordion" id="modelsAccordion" x-data="{ selectedModelId: @entangle('selectedModelId') }">
                                            @foreach ($models as $model)
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading{{ $model->id }}">
                                                        <button
                                                            class="accordion-button"
                                                            :class="{ 'collapsed': selectedModelId !== {{ $model->id }} }"
                                                            type="button"
                                                            @click="selectedModelId === {{ $model->id }} ? selectedModelId = null : selectedModelId = {{ $model->id }}"
                                                            aria-expanded="selectedModelId === {{ $model->id }}"
                                                            aria-controls="collapse{{ $model->id }}">
                                                            @if ($model->image)
                                                                <img src="{{ asset('storage/' . $model->image) }}"
                                                                    alt="{{ $model->name }}" height="30"
                                                                    class="me-2">
                                                            @endif
                                                            {{ $model->name }}
                                                        </button>
                                                    </h2>
                                                    <div id="collapse{{ $model->id }}"
                                                        class="accordion-collapse collapse"
                                                        :class="{ 'show': selectedModelId === {{ $model->id }} }"
                                                        aria-labelledby="heading{{ $model->id }}"
                                                        data-bs-parent="#modelsAccordion">
                                                        <div class="accordion-body">
                                                            
                                                            @if ($selectedModelId === $model->id)
                                                                @forelse ($this->inventories as $inventory)
                                                                    <div class="mb-2 p-2 border rounded">
                                                                        <strong>{{ $inventory->name }}</strong><br>
                                                                        {{ $inventory->description ?? 'No description' }}
                                                                    </div>
                                                                @empty
                                                                    <p class="text-muted">No inventories found for this model.</p>
                                                                @endforelse
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div> --}}

                                        {{-- <div class="accordion" id="modelsAccordion">
                                            @foreach ($models as $model)
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading{{ $model->id }}">
                                                        <button
                                                            class="accordion-button {{ $selectedModelId === $model->id ? '' : 'collapsed' }}"
                                                            type="button"
                                                            wire:click="toggleModel({{ $model->id }})"
                                                            aria-expanded="{{ $selectedModelId === $model->id ? 'true' : 'false' }}"
                                                            aria-controls="collapse{{ $model->id }}">
                                                            @if ($model->image)
                                                                <img src="{{ asset('storage/' . $model->image) }}"
                                                                    alt="{{ $model->name }}" height="30"
                                                                    class="me-2">
                                                            @endif
                                                            {{ $model->name }}
                                                        </button>
                                                    </h2>
                                                    <div id="collapse{{ $model->id }}"
                                                        class="accordion-collapse collapse {{ $selectedModelId === $model->id ? 'show' : '' }}"
                                                        aria-labelledby="heading{{ $model->id }}"
                                                        data-bs-parent="#modelsAccordion">
                                                        <div class="accordion-body">
                                                            @if ($selectedModelId === $model->id)
                                                                @forelse ($this->inventories as $inventory)
                                                                    <div class="mb-2 p-2 border rounded">
                                                                        <strong>{{ $inventory->name }}</strong><br>
                                                                        {{ $inventory->description ?? 'No description' }}
                                                                    </div>
                                                                @empty
                                                                    <p class="text-muted">No inventories found for this
                                                                        model.</p>
                                                                @endforelse
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div> --}}

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
                
                    <button type="button" class="btn btn-primary btn-sm ms-auto" wire:click="addBrand()">+ Add Inventory</button>
                </div>
                
                <div class="card-block">
                    <table class="table table-striped datatable">
                        <thead>
                            <th>Name</th>
                            <th>Descrition</th>
                        </thead>
                        <tbody>
                            @forelse ($this->inventories as $inventory)
                                <tr>
                                    <td>{{ $inventory->inventory->product_name }}</td>
                                    <td>{{ $inventory->inventory->description ?? 'No description' }}</td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="2" class="text-center">
                                        No inventories found for this model
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    @endif
    <!-- Brand Modal -->
    {{-- <x-modal wire:model="showBrandModal">
    <x-slot name="title">
        {{ $brandId ? 'Edit Brand' : 'Add Brand' }}
    </x-slot>

    <div>
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

    <x-slot name="footer">
        <button class="btn btn-secondary" wire:click="$set('showBrandModal', false)">Cancel</button>
        <button class="btn btn-primary" wire:click="saveBrand">Save</button>
    </x-slot>
</x-modal>

<!-- Model Modal -->
<x-modal wire:model="showModelModal">
    <x-slot name="title">
        {{ $modelId ? 'Edit Model' : 'Add Model' }}
    </x-slot>

    <div>
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

    <x-slot name="footer">
        <button class="btn btn-secondary" wire:click="$set('showModelModal', false)">Cancel</button>
        <button class="btn btn-primary" wire:click="saveModel">Save</button>
    </x-slot>
</x-modal> --}}
    <div>
        {{-- @script
    <script>
        $(document).ready(function() {
            $('#submit-button').on('click', function(e) {
                alert();
                e.preventDefault();
                document.addEventListener('livewire:load', () => {
                    Livewire.on('open-brand-modal', () => {
                        const modal = new bootstrap.Modal(document.getElementById(
                            'brandModal'));
                        modal.show();
                    });
                });
            });
        });
    </script>
@endscript --}}
