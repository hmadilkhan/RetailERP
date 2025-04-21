<section class="panels-wells">
    <div class="card">
        <div class="card-header">
            <h5 class="card-header-text">Vehicle Manager</h5>
        </div>
        <div class="card-block">
            <div class=" py-4">
                <h4 class="mb-4">Add / Edit Vehicle Brand</h4>
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
                                            @if ($brand->logo)
                                                <img src="{{ asset('storage/' . $brand->logo) }}"
                                                    alt="{{ $brand->name }}" height="20" class="me-2">
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
                            <div class="card-footer">
                                <button type="button" class="btn btn-primary w-100" id="submit-button">+ Add
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
                                        @if ($selectedBrand->logo)
                                            <img src="{{ asset('storage/' . $selectedBrand->logo) }}"
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
                                                    @if ($model->logo)
                                                        <img src="{{ asset('storage/' . $model->logo) }}"
                                                            alt="{{ $model->name }}" height="20" class="me-2">
                                                    @endif
                                                    {{ $model->name }}
                                                </div>
                                                <div>
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

    <div wire:ignore.self class="modal fade in" id="brandModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

            </div>
        </div>
    </div>
    <!-- Brand Modal -->
    <x-modal wire:model="showBrandModal">
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
    </x-modal>
</section>
@script
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
@endscript
