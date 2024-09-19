<form method='post' action='{{ url('uploadInventory') }}' enctype='multipart/form-data'>
    {{ csrf_field() }}
    <div class="row">
        <div class="col-md-12">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" name="update" id="update">
                <label class="form-check-label" for="update">
                    Only Update Pricing
                </label>
            </div>
            {{-- <div class="form-group">
                <label for="" class="checkbox-inline">Update to Retail Price</label>
                <br />
                <label for="" class="checkbox-inline">
                    <input type="checkbox" name="update" id="update" class="custom-control">
                </label>
                @if ($errors->has('file'))
                    <div class="form-control-feedback">Required field can not be blank.</div>
                @endif
            </div> --}}
        </div>
        <div class="row col-md-12 mt-2">
            {{-- <div class="form-group {{ $errors->has('file') ? 'has-danger' : '' }} ">
                <label for="vdimg" class="form-control-label">Select File </label>
                <br />
                <label for="vdimg" class="custom-file">
                    <input type="file" name="file" id="vdimg" class="custom-file-input">
                    <span class="custom-file-control"></span>
                </label>
                @if ($errors->has('file'))
                    <div class="form-control-feedback">Required field can not be blank.</div>
                @endif
            </div> --}}
            <div class="col-auto">
                <label for="vdimg" class="custom-file">
                    <input type="file" name="file" id="vdimg" class="custom-file-input">
                    <span class="custom-file-control"></span>
                </label>
                @if ($errors->has('file'))
                    <div><small class="text-danger">Required field can not be blank.</small></div>
                @endif
            </div>

            <div class="col-auto">
                <button type="submit" class="btn btn-primary mb-3">Import</button>
            </div>
        </div>

        {{-- <div class="row col-md-2 ">
            <input type='submit' class="btn btn-primary m-l-5 m-t-35" name='submit' value='Import'>
        </div> --}}
    </div>
</form>
