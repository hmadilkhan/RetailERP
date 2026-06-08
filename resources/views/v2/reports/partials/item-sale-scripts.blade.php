<script>
    function setReportHtml(html) {
        const target = document.getElementById('itemSalesReport');
        target.innerHTML = html;
        target.querySelectorAll('script').forEach(function (script) {
            const next = document.createElement('script');
            next.text = script.textContent;
            document.body.appendChild(next);
            document.body.removeChild(next);
        });
    }

    function selectedValue(id) {
        return document.getElementById(id)?.value || '';
    }

    function buildExportUrl(baseUrl) {
        const params = new URLSearchParams({
            fromdate: selectedValue('fromdate'),
            todate: selectedValue('todate'),
            branch: selectedValue('branch'),
            terminal: selectedValue('terminal'),
            customer: selectedValue('customer'),
            paymentmode: selectedValue('paymentmode'),
            ordermode: selectedValue('ordermode'),
            product: selectedValue('product_name'),
            type: selectedValue('report_type'),
            declaration: selectedValue('declaration')
        });
        @if($includeDepartment)
            params.set('department', selectedValue('department'));
        @endif
        return baseUrl + '?' + params.toString();
    }

    function requireDateRange() {
        if (!selectedValue('fromdate') && !selectedValue('todate')) {
            document.getElementById('alert_fromdate').textContent = 'Please select the date';
            document.getElementById('fromdate').focus();
            return false;
        }
        document.getElementById('alert_fromdate').textContent = '';
        return true;
    }

    document.querySelector("button[name='btn_search_report']").addEventListener('click', function () {
        if (!requireDateRange()) return;

        setReportHtml('');
        document.getElementById('totaldiv').classList.add('hidden');
        document.getElementById('reportStatus').textContent = 'Loading report data...';

        const payload = {
            fromdate: selectedValue('fromdate'),
            todate: selectedValue('todate'),
            product: selectedValue('product_name'),
            branch: selectedValue('branch'),
            terminal: selectedValue('terminal'),
            customer: selectedValue('customer'),
            paymentmode: selectedValue('paymentmode'),
            ordermode: selectedValue('ordermode'),
            declaration: selectedValue('declaration')
        };
        @if($includeDepartment)
            payload.department = selectedValue('department');
        @endif

        fetch("{{ $searchRoute }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            body: JSON.stringify(payload)
        }).then(response => response.text()).then(function (html) {
            if (html.trim() && html.trim() !== '0') {
                setReportHtml(html);
                document.getElementById('reportStatus').textContent = 'Report loaded.';
            } else {
                document.getElementById('reportStatus').textContent = 'No sales found.';
            }
        }).catch(function () {
            document.getElementById('reportStatus').textContent = 'Unable to load report.';
        });
    });

    document.getElementById('branch').addEventListener('change', function () {
        fetch("{{ route('getTerminals') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            body: JSON.stringify({ branch: selectedValue('branch') })
        }).then(response => response.json()).then(function (result) {
            const terminal = document.getElementById('terminal');
            terminal.innerHTML = '<option value="">Select Terminal</option>';
            if (result && result.terminal) {
                result.terminal.forEach(item => terminal.add(new Option(item.terminal_name, item.terminal_id)));
            }
            if (window.jQuery) jQuery(terminal).trigger('change.select2');
        });
    });

    document.getElementById('btnExcel').addEventListener('click', function () {
        if (requireDateRange()) window.open(buildExportUrl("{{ $excelUrl }}"));
    });

    document.getElementById('btnPdf').addEventListener('click', function () {
        if (requireDateRange()) window.open(buildExportUrl("{{ $pdfUrl }}"));
    });
</script>
