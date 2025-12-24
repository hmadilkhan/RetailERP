<div>
    <div class="container-fluid">
        <!-- Header -->
        <div class="premium-header mb-4">
            <div class="header-content">
                <div class="header-left">
                    <div class="icon-wrapper">
                        <i class="mdi mdi-chart-line"></i>
                    </div>
                    <div>
                        <h2 class="mb-0">Sales Details</h2>
                        <p class="page-subtitle mb-0">View and manage all sales transactions</p>
                    </div>
                </div>
                <a href="{{ route('premium.dashboard') }}" class="btn btn-premium">
                    <i class="mdi mdi-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Tab Switcher -->
        <div class="card premium-card mb-4">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-10">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn {{ $selectedTab === 'active' ? 'btn-success' : 'btn-outline-success' }}"
                                wire:click="switchTab('active')">
                                <i class="mdi mdi-check-circle me-2"></i>Active Sales
                            </button>
                            <button type="button" class="btn {{ $selectedTab === 'closed' ? 'btn-success' : 'btn-outline-success' }}"
                                wire:click="switchTab('closed')">
                                <i class="mdi mdi-lock me-2"></i>Closed Sales
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2">
                        @if($selectedTab === 'closed')
                        <input type="date" class="form-control" wire:model="selectedDate" />
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Branches Grid -->
        <div class="branches-section mb-4">
            <div class="section-header mb-3">
                <h5 class="section-title">
                    <i class="mdi mdi-office-building"></i>
                    {{ $selectedTab === 'active' ? 'Active' : 'Closed' }} Branches
                </h5>
            </div>
            <div class="row g-3">
                @if($selectedTab === 'active')
                @foreach($branches as $branch)
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="branch-card"
                        onclick="getdetails('{{ session('roleId') == 2 ? $branch->branch_id : $branch->terminal_id }}','{{ addslashes($branch->identify) }}','open')">
                        <div class="branch-status-badge {{ $selectedTab === 'active' ? 'status-active' : 'status-closed' }}">
                            <i class="mdi mdi-circle"></i>
                        </div>
                        <div class="branch-logo-wrapper">
                            <img src="{{ asset('storage/images/branch/' . (!empty($branch->branch_logo) ? $branch->branch_logo : 'placeholder.jpg')) }}"
                                alt="Branch Logo">
                        </div>
                        <h6 class="branch-name">{{ session('roleId') == 2 ? $branch->branch_name : $branch->terminal_name }}</h6>
                        <div class="sales-info">
                            <div class="sales-amount">{{ session('currency') }} {{ number_format($branch->sales, 0) }}</div>
                            <div class="sales-label">Total Sales</div>
                        </div>
                        <div class="branch-action">
                            <i class="mdi mdi-arrow-right-circle"></i>
                        </div>
                    </div>
                </div>
                @endforeach
                @else
                @foreach($branchesClosedSales as $branch)
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="branch-card"
                        onclick="getdetails('{{ session('roleId') == 2 ? $branch->branch_id : $branch->terminal_id }}','{{ addslashes($branch->identify) }}','close')">
                        <div class="branch-status-badge {{ $selectedTab === 'active' ? 'status-active' : 'status-closed' }}">
                            <i class="mdi mdi-circle"></i>
                        </div>
                        <div class="branch-logo-wrapper">
                            <img src="{{ asset('storage/images/branch/' . (!empty($branch->branch_logo) ? $branch->branch_logo : 'placeholder.jpg')) }}"
                                alt="Branch Logo">
                        </div>
                        <h6 class="branch-name">{{ session('roleId') == 2 ? $branch->branch_name : $branch->terminal_name }}</h6>
                        <div class="sales-info">
                            <div class="sales-amount">{{ session('currency') }} {{ number_format($branch->sales, 0) }}</div>
                            <div class="sales-label">Total Sales</div>
                        </div>
                        <div class="branch-action">
                            <i class="mdi mdi-arrow-right-circle"></i>
                        </div>
                    </div>
                </div>
                @endforeach
                @endif
            </div>
        </div>

        <!-- Terminals Section -->
        <div class="card premium-card terminals-section mb-4">
            <div class="card-header terminals-header">
                <div class="header-content">
                    <div class="header-icon-badge">
                        <i class="mdi mdi-monitor"></i>
                    </div>
                    <div class="header-text">
                        <h5 class="card-title mb-0">Terminals</h5>
                        <p class="card-subtitle mb-0" id="terminal-subtitle">Select a terminal to view details</p>
                    </div>
                </div>
            </div>
            <div class="card-body terminals-body">
                <div class="terminal-tabs-container">
                    <div class="section-label">
                        <i class="mdi mdi-view-grid"></i>
                        <span>Available Terminals</span>
                    </div>
                    <div class="terminal-tabs" id="terminalTab"></div>
                </div>
                <div class="declaration-tabs-container" id="declarationContainer" style="display: none;">
                    <div class="section-label">
                        <i class="mdi mdi-calendar-clock"></i>
                        <span>Last Day Declarations</span>
                    </div>
                    <div class="declaration-tabs" id="declartionTab"></div>
                </div>
            </div>
        </div>

        <!-- Details Section -->
        <div class="card premium-card">
            <div class="card-body p-4">
                <div id="div_details">
                    <div class="text-center py-5">
                        <i class="mdi mdi-information-outline text-muted" style="font-size: 48px;"></i>
                        <p class="text-muted mt-3">Select a branch to view details</p>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <style>
        .premium-sales-wrapper {
            background: #f5f6fa;
            min-height: 100vh;
            padding: 2rem;
            margin: -2rem;
        }

        .premium-header {
            background: white;
            border-radius: 16px;
            padding: 1.5rem 2rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .icon-wrapper {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
        }

        .page-subtitle {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .btn-premium {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.625rem 1.25rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }

        .premium-card {
            border-radius: 20px;
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .section-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-title i {
            color: #667eea;
            font-size: 1.25rem;
        }

        .branch-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid #f0f0f0;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .branch-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .branch-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(102, 126, 234, 0.15);
            border-color: #667eea;
        }

        .branch-card:hover::before {
            transform: scaleX(1);
        }

        .branch-status-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .status-active {
            color: #28a745;
        }

        .status-closed {
            color: #dc3545;
        }

        .branch-logo-wrapper {
            width: 60px;
            height: 60px;
            margin: 0 auto 1rem;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .branch-logo-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .branch-name {
            font-size: 1rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 1rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sales-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 0.75rem;
        }

        .sales-amount {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 0.25rem;
        }

        .sales-label {
            font-size: 0.75rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .branch-action {
            color: #667eea;
            font-size: 1.5rem;
            opacity: 0;
            transform: translateX(-10px);
            transition: all 0.3s ease;
        }

        .branch-card:hover .branch-action {
            opacity: 1;
            transform: translateX(0);
        }

        /* Premium Terminals Section */
        .terminals-section {
            overflow: hidden;
        }

        .terminals-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 1.5rem 2rem;
            border-radius: 20px 20px 0 0;
        }

        .terminals-header .header-content {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-icon-badge {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .header-text .card-title {
            color: white;
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .header-text .card-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.875rem;
            font-weight: 400;
        }

        .terminals-body {
            padding: 2rem;
            background: white;
        }

        .terminal-tabs-container,
        .declaration-tabs-container {
            margin-bottom: 2rem;
        }

        .declaration-tabs-container {
            margin-bottom: 0;
        }

        .section-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .section-label i {
            color: #667eea;
            font-size: 1.125rem;
        }

        .section-label span {
            font-weight: 600;
            font-size: 0.9375rem;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .terminal-tabs-container .terminal-tabs,
        .declaration-tabs-container .declaration-tabs {
            overflow-x: auto;
            overflow-y: hidden;
            padding: 0.5rem 0;
        }

        .terminal-tabs-container::-webkit-scrollbar,
        .declaration-tabs-container::-webkit-scrollbar {
            height: 6px;
        }

        .terminal-tabs-container::-webkit-scrollbar-track,
        .declaration-tabs-container::-webkit-scrollbar-track {
            background: #f8f9fa;
            border-radius: 10px;
        }

        .terminal-tabs-container::-webkit-scrollbar-thumb,
        .declaration-tabs-container::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }

        .terminal-tabs,
        .declaration-tabs {
            display: flex;
            gap: 0.75rem;
            flex-wrap: nowrap;
        }

        .terminal-item,
        .declaration-item {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 0.875rem 1.25rem;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            color: #495057;
            position: relative;
            overflow: hidden;
        }

        .terminal-item::before,
        .declaration-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 3px;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .terminal-item:hover,
        .declaration-item:hover {
            border-color: #667eea;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }

        .terminal-item:hover::before,
        .declaration-item:hover::before {
            transform: scaleY(1);
        }

        .terminal-item i,
        .declaration-item i {
            font-size: 1.125rem;
            color: #667eea;
        }

        .terminal-btn-active,
        .declaration-active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .terminal-btn-active::before,
        .declaration-active::before {
            transform: scaleY(1);
            background: white;
        }

        .terminal-btn-active i,
        .declaration-active i {
            color: white;
        }

        .terminal-btn-active:hover,
        .declaration-active:hover {
            background: linear-gradient(135deg, #5568d3 0%, #65408b 100%);
            transform: translateY(-2px);
        }

        .bg-success-subtle {
            background-color: rgba(40, 167, 69, 0.1);
        }

        .bg-danger-subtle {
            background-color: rgba(220, 53, 69, 0.1);
        }

        @media (max-width: 768px) {
            .premium-sales-wrapper {
                padding: 1rem;
                margin: -1rem;
            }

            .premium-header {
                padding: 1rem;
            }

            .header-content {
                flex-direction: column;
                align-items: flex-start;
            }

            .header-left {
                width: 100%;
            }

            .btn-premium {
                width: 100%;
                justify-content: center;
            }

            .page-title {
                font-size: 1.25rem;
            }

            .icon-wrapper {
                width: 40px;
                height: 40px;
                font-size: 20px;
            }
        }
    </style>

    <script>
        var activeStatus = "";

        function getdetails(branch, status, branchstatus) {
            console.log(branch, status, branchstatus);
            if (branchstatus == "close") {
                getCloseTerminals(branch, status)
            } else {
                getTerminals(branch, status);
            }
            $('#div_details').empty();
        }

        getTerminals('{{ $branches[0]->branch_id ?? "" }}', 'open');

        function selectTerminal(element, id, type) {
            // Updated active state
            $('#terminalTab .terminal-item').removeClass('terminal-btn-active');
            $(element).addClass('terminal-btn-active');

            // Update Subtitle
            $('#terminal-subtitle').text($(element).text().trim());

            // Fetch details
            if (type === 'open') {
                getPartial(id);
            } else {
                getDeclarations(id);
            }
        }

        function getTerminals(branch, status) {
            showLoader("terminalTab");
            // Hide declaration container for active terminals
            $('#declarationContainer').hide();
            $('#declartionTab').empty();

            $.ajax({
                url: "{{ url('/getTerminals') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    branch: branch,
                    status: status
                },
                dataType: 'json',
                success: function(result) {
                    $('#terminalTab').empty();
                    $.each(result, function(index, value) {
                        let terminalDiv = `<button class="terminal-item" onclick="selectTerminal(this, '${value.terminal_id}', 'open')">
                            <i class="mdi mdi-monitor-dashboard"></i> ${value.terminal_name}
                        </button>`;
                        $('#terminalTab').append(terminalDiv);
                    });
                    // Auto-click first terminal
                    $('#terminalTab button:first').trigger('click');
                }
            });
        }

        function getCloseTerminals(branch, status) {
            showLoader("terminalTab");
            // Show declaration container for closed terminals
            $('#declarationContainer').show();
            $('#declartionTab').empty();
            $('#div_details').empty();

            $.ajax({
                url: "{{ url('/getTerminals') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    branch: branch,
                    status: status
                },
                dataType: 'json',
                success: function(result) {
                    $('#terminalTab').empty();
                    $.each(result, function(index, value) {
                        let terminalDiv = `<button class="terminal-item" onclick="selectTerminal(this, '${value.terminal_id}', 'close')">
                             <i class="mdi mdi-monitor-dashboard"></i> ${value.terminal_name}
                        </button>`;
                        $('#terminalTab').append(terminalDiv);
                    });
                    // Auto-click first terminal
                    $('#terminalTab button:first').trigger('click');
                }
            });
        }

        function getDeclarations(terminalId) {
            showLoader("declartionTab");
            let date = "{{ $selectedDate }}";
            $.ajax({
                url: "{{ url('/get-close-declarations') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    terminal: terminalId,
                    date: date
                },
                dataType: 'json',
                success: function(result) {
                    $('#declartionTab').empty();
                    $('#div_details').empty();
                    $.each(result, function(index, value) {
                        let DeclarationDiv = '<button class="btn btn-outline-primary" onclick="getLastDayPartial(' + terminalId + ',' + value.opening_id + ')">D#' + value.opening_id + '</button>';
                        $('#declartionTab').append(DeclarationDiv);
                    });
                }
            });
        }

        function getPartial(terminal) {
            showLoader("div_details");
            $.ajax({
                url: "{{ url('/get-terminal-details') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    terminal: terminal
                },
                success: function(result) {
                    $('#div_details').empty();
                    $('#div_details').html(result);
                }
            });
        }

        function getLastDayPartial(terminal, openingId) {
            showLoader("div_details");
            $.ajax({
                url: "{{ url('/last-day-heads') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    terminal: terminal,
                    openingId: openingId
                },
                success: function(result) {
                    $('#div_details').empty();
                    $('#div_details').html(result);
                }
            });
        }

        function showLoader(divName) {
            $('#' + divName).html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
        }
    </script>
</div>