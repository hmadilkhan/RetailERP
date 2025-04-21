<?php

namespace App\Livewire;

use App\Models\Inventory;
use App\Models\VehicleBrand;
use App\Models\VehicleInventory;
use App\Models\VehicleModel;
use Livewire\Attributes\Title;
use Livewire\Component;
// For file uploads
use Livewire\WithFileUploads;

class VehicleManager extends Component
{
    use WithFileUploads;

    #[Title("Vehicle Manager")]

    public $brands;
    public $models = [];
    public $selectedBrand = null;
    public $selectedModel = null;
    // Brand
    public $brandId = null;
    public $brandName = '';
    public $brandLogo = null;

    // Model
    public $modelId = null;
    public $modelName = '';
    public $modelLogo = null;

    // Control modals
    public $showBrandModal = false;
    public $showModelModal = false;
    public $showModelInventory = false;

    // Control Accordion
    public $selectedModelId = null;

    public function mount()
    {
        $this->brands = VehicleBrand::all();
    }

    public function selectBrand($brandId)
    {
        $this->selectedBrand = VehicleBrand::find($brandId);
        $this->models = $this->selectedBrand->models;
        $this->selectedModelId = null;
        $this->showModelInventory = false;
    }

    public function modelInventory($modelId)
    {
        $this->selectedModelId = $modelId;
        $this->showModelInventory = true;
        $this->selectedModel = VehicleModel::find($modelId);
    }

    // public function toggleModel($modelId)
    // {
    //     $this->selectedModelId = $this->selectedModelId === $modelId ? null : $modelId;
    // }

    public function getInventoriesProperty()
    {
        if (!$this->selectedModelId) return collect();

        return VehicleInventory::with("inventory")->where('vehicle_model_id', $this->selectedModelId)->get();

    }

    // BRAND
    public function addBrand()
    {
        $this->reset(['brandId', 'brandName', 'brandLogo']);
        $this->showBrandModal = true;
    }

    public function editBrand($id)
    {
        $brand = VehicleBrand::findOrFail($id);
        $this->brandId = $brand->id;
        $this->brandName = $brand->name;
        $this->showBrandModal = true;
    }

    public function saveBrand()
    {
        $this->validate([
            'brandName' => 'required|string',
            'brandLogo' => $this->brandId ? 'nullable|image|mimes:jpg,jpeg,png|max:1024' : 'required|image|mimes:jpg,jpeg,png|max:1024',
        ]);

        $data = ['name' => $this->brandName, 'company_id' => session('company_id')];

        if ($this->brandLogo) {
            $data['image'] = $this->brandLogo->store('images/vehicle-brands', 'public');
        }

        VehicleBrand::updateOrCreate(
            ['id' => $this->brandId],
            $data
        );

        $this->showBrandModal = false;
        $this->brands = VehicleBrand::all();
        $this->reset(['brandId', 'brandName', 'brandLogo']);
    }

    // // MODEL
    public function addModel()
    {
        $this->reset(['modelId', 'modelName', 'modelLogo']);
        $this->showModelModal = true;
    }

    public function editModel($id)
    {
        $model = VehicleModel::findOrFail($id);
        $this->modelId = $model->id;
        $this->modelName = $model->name;
        $this->showModelModal = true;
    }

    public function saveModel()
    {
        $this->validate([
            'modelName' => 'required|string',
            'modelLogo' => $this->modelId ? 'nullable|image|max:1024' : 'required|image|max:1024',
        ]);

        $data = [
            'name' => $this->modelName,
            'vehicle_brand_id' => $this->selectedBrand->id
        ];

        if ($this->modelLogo) {
            $data['image'] = $this->modelLogo->store('images/vehicle-models', 'public');
        }

        VehicleModel::updateOrCreate(
            ['id' => $this->modelId],
            $data
        );

        $this->showModelModal = false;
        $this->models = $this->selectedBrand->models;
        $this->reset(['modelId', 'modelName', 'modelLogo']);
    }


    public function render()
    {
        return view('livewire.vehicle-manager');
    }
}
