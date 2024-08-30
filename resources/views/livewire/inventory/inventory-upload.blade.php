<form method='post' action='{{ url('uploadInventory') }}' enctype='multipart/form-data'>
    {{ csrf_field() }}
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="" class="checkbox-inline">Update to Retail Price</label>
                <br />
                <label for="" class="checkbox-inline">
                    <input type="checkbox" name="update" id="update" class="custom-control">
                </label>
                @if ($errors->has('file'))
                    <div class="form-control-feedback">Required field can not be blank.</div>
                @endif
            </div>
        </div>
    </div>
    <div class="row col-md-4 ">
        <div class="form-group {{ $errors->has('file') ? 'has-danger' : '' }} ">
            <label for="vdimg" class="form-control-label">Select File </label>
            <br />
            <label for="vdimg" class="custom-file">
                <input type="file" name="file" id="vdimg" class="custom-file-input">
                <span class="custom-file-control"></span>
            </label>
            @if ($errors->has('file'))
                <div class="form-control-feedback">Required field can not be blank.</div>
            @endif
        </div>
    </div>

    <div class="row col-md-2 ">
        <input type='submit' class="btn btn-primary m-l-5 m-t-35" name='submit' value='Import'>

    </div>
</form>
