// Packing UOM (jaise Carton) ke liye stock-entry helper.
// Stock aur DB hamesha primary UOM (Pcs) me rehte hain; Carton sirf entry ke waqt chuna jata hai
// aur submit se pehle primary me convert hota hai: qty x pack_qty, per-unit rate / pack_qty.
// Jis product pe pack_qty set nahi (baqi sab companies), dropdown chhupa rehta hai aur kuch convert nahi hota.
window.PackUom = (function ($) {
    function round(value, digits) {
        return Number(Number(value || 0).toFixed(digits));
    }

    // /get-uom-id ka row -> { qty, name, primary } ya null
    function fromRow(row) {
        if (!row || !(Number(row.pack_qty) > 1) || !row.pack_uom_name || row.pack_uom_name === row.uom_name) {
            return null;
        }
        return { qty: Number(row.pack_qty), name: row.pack_uom_name, primary: row.uom_name || 'Unit' };
    }

    // Unit dropdown bharo; har naye product pe primary pe reset hota hai (saved values primary me hoti hain)
    function bind($select, row) {
        var pack = fromRow(row);
        $select.empty();
        if (pack) {
            $select.append($('<option>', { value: 'primary', text: pack.primary }));
            $select.append($('<option>', { value: 'pack', text: pack.name }));
        }
        $select.data('pack', pack).val('primary').toggle(!!pack);
        return pack;
    }

    // Carton chuna ho to pack, warna null
    function active($select) {
        var pack = $select.data('pack');
        return pack && $select.val() === 'pack' ? pack : null;
    }

    function toPrimaryQty(qty, pack) {
        var value = parseFloat(qty);
        return pack && !isNaN(value) ? round(value * pack.qty, 4) : qty;
    }

    function toPrimaryRate(rate, pack) {
        var value = parseFloat(rate);
        return pack && !isNaN(value) ? round(value / pack.qty, 6) : rate;
    }

    // "= 3000 Pcs @ 0.83 (1 Carton = 1000 Pcs)"
    function hint(pack, isActive, qty, rate) {
        if (!pack) {
            return '';
        }
        var text = '1 ' + pack.name + ' = ' + round(pack.qty, 4) + ' ' + pack.primary;
        var q = parseFloat(qty);
        if (!isActive || isNaN(q)) {
            return text;
        }
        var r = parseFloat(rate);
        return '= ' + round(q * pack.qty, 4) + ' ' + pack.primary + (isNaN(r) ? '' : ' @ ' + round(r / pack.qty, 2)) + ' (' + text + ')';
    }

    return { fromRow: fromRow, bind: bind, active: active, toPrimaryQty: toPrimaryQty, toPrimaryRate: toPrimaryRate, hint: hint };
})(jQuery);
