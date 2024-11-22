<section>
    <div class="card">
        <div class="card-header">Add Order Items</div>
        <div class="card-body">
            <form wire:submit.prevent="addItems">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-control-label "><i class="icofont icofont-barcode"></i>
                                Products</label>
                            <select class="select2" id="products" wire:model="productId" wire:ignore>
                                <option value="">Select Products</option>
                                @if ($products)
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">
                                            {{ $product->item_code }} - {{ $product->product_name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-12">
                        <div id="itemcode" class="form-group">
                            <label class="form-control-label "><i class="icofont icofont-barcode"></i> Qty</label>
                            <input class="form-control" type="text" id="qty" wire:model="qty"
                                placeholder="Enter Qty" />
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-12">
                        <div id="itemcode" class="form-group">
                            <label class="form-control-label "><i class="icofont icofont-barcode"></i>Price</label>
                            <input class="form-control" type="text" wire:model="price" id="price"
                                placeholder="Enter Price " />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="button" id="submit-button" data-placement="bottom"
                            class="btn btn-success  waves-effect waves-light mt-4">Add Item</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@script
    <script>
        $(document).ready(function() {
            $(".select2").select2();
        });
        let productname = "";
        document.getElementById('submit-button').addEventListener('click', function() {
            console.log("Click Working");
            
            // Select all controls and buttons to disable
            let controls = document.querySelectorAll('input, select, button');

            // Disable controls and buttons
            controls.forEach(control => control.disabled = true);

            // Manually submit the form with parameters
            if ($("#products").val() != "") {
                productname = $("#products option:selected").text().replace(/\s+/g, " ");
            }

            // Pass parameters as an object/array
            Livewire.dispatch('addItems', {
                productId: $("#products").val(),
                productName: productname,
                qty: $("#qty").val(),
                price: $("#price").val()
            });

            // Re-enable controls after the Livewire request is complete
            Livewire.on('itemAdded', function() {
                controls.forEach(control => control.disabled = false);
            });

        });

        window.addEventListener('resetControls', event => {
            $("#products").val('').change();
            $("#qty").val('');
            $("#price").val('')
        });
    </script>
@endscript
