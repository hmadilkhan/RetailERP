{{-- Packing UOM (Carton) wale product pe Rec. Quantity ki unit. Sirf <select>/<small> - row ko "input,label" position se padha jata hai, isliye yahan input/label mat dalo. --}}
@php $packUomName = !empty($value->pack_uom) ? ($packUomNames[$value->pack_uom] ?? null) : null; @endphp
@if ((float) ($value->pack_qty ?? 0) > 1 && $packUomName && $packUomName != $value->unitName)
    <select class="form-control pack-unit" data-pack="{{ (float) $value->pack_qty }}" data-rec="rec{{ $value->item_code }}" style="margin-top:4px">
        <option value="primary">{{ $value->unitName }}</option>
        <option value="pack">{{ $packUomName }}</option>
    </select>
    <small class="text-muted">1 {{ $packUomName }} = {{ (float) $value->pack_qty }} {{ $value->unitName }}</small>
@endif
