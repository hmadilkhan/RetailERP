<div class="premium-sales-wrapper">
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="page-title mb-1">Sales Details</h2>
                        <p class="text-muted mb-0">View and manage all sales transactions</p>
                    </div>
                    <a href="{{ route('premium.dashboard') }}" class="btn btn-premium">
                        <i class="mdi mdi-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
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
        <div class="card premium-card mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-3">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="mdi mdi-office-building me-2 text-primary"></i>
                    {{ $selectedTab === 'active' ? 'Active' : 'Closed' }} Branches
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    @if($selectedTab === 'active')
                        @foreach($branches as $branch)
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="branch-card" 
                                     onclick="getdetails('{{ session('roleId') == 2 ? $branch->branch_id : $branch->terminal_id }}','{{ $branch->identify }}','open')">
                                    <div class="branch-card-header">
                                        <div class="branch-logo">
                                            <img src="{{ asset('storage/images/branch/' . (!empty($branch->branch_logo) ? $branch->branch_logo : 'placeholder.jpg')) }}" 
                                                 alt="Branch Logo">
                                        </div>
                                        <div class="branch-info">
                                            <h6 class="branch-name">{{ session('roleId') == 2 ? $branch->branch_name : $branch->terminal_name }}</h6>
                                            <span class="badge bg-success-subtle text-success">
                                                <i class="mdi mdi-circle-small"></i>Active
                                            </span>
                                        </div>
                                    </div>
                                    <div class="branch-card-body">
                                        <div class="sales-amount">
                                            <span class="currency">{{ session('currency') }}</span>
                                            <span class="amount">{{ number_format($branch->sales, 0) }}</span>
                                        </div>
                                        <p class="sales-label">Total Sales</p>
                                    </div>
                                    <div class="branch-card-footer">
                                        <span class="view-details">View Details <i class="mdi mdi-arrow-right"></i></span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        @foreach($branchesClosedSales as $branch)
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="branch-card" 
                                     onclick="getdetails('{{ session('roleId') == 2 ? $branch->branch_id : $branch->terminal_id }}','{{ $branch->identify }}','close')">
                                    <div class="branch-card-header">
                                        <div class="branch-logo">
                                            <img src="{{ asset('storage/images/branch/' . (!empty($branch->branch_logo) ? $branch->branch_logo : 'placeholder.jpg')) }}" 
                                                 alt="Branch Logo">
                                        </div>
                                        <div class="branch-info">
                                            <h6 class="branch-name">{{ session('roleId') == 2 ? $branch->branch_name : $branch->terminal_name }}</h6>
                                            <span class="badge bg-danger-subtle text-danger">
                                                <i class="mdi mdi-circle-small"></i>Closed
                                            </span>
                                        </div>
                                    </div>
                                    <div class="branch-card-body">
                                        <div class="sales-amount">
                                            <span class="currency">{{ session('currency') }}</span>
                                            <span class="amount">{{ number_format($branch->sales, 0) }}</span>
                                        </div>
                                        <p class="sales-label">Total Sales</p>
                                    </div>
                                    <div class="branch-card-footer">
                                        <span class="view-details">View Details <i class="mdi mdi-arrow-right"></i></span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <!-- Terminals Section -->
        <div class="card premium-card mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-3">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="mdi mdi-monitor me-2 text-primary"></i>Terminals
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="terminal-tabs-wrapper mb-3">
                    <div class="terminal-tabs" id="terminalTab"></div>
                </div>
                <div class="declaration-tabs-wrapper">
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

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2c3e50;
        }

        .btn-premium {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
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

        .branch-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            height: 100%;
        }

        .branch-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
            border-color: #667eea;
        }

        .branch-card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f0f0f0;
        }

        .branch-logo {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .branch-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .branch-info {
            flex: 1;
        }

        .branch-name {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: #2c3e50;
        }

        .branch-card-body {
            text-align: center;
            padding: 1rem 0;
        }

        .sales-amount {
            font-size: 1.75rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 0.5rem;
        }

        .currency {
            font-size: 1rem;
            opacity: 0.7;
        }

        .sales-label {
            font-size: 0.875rem;
            color: #6c757d;
            margin: 0;
        }

        .branch-card-footer {
            text-align: center;
            padding-top: 1rem;
            border-top: 1px solid #f0f0f0;
        }

        .view-details {
            color: #667eea;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .terminal-tabs-wrapper, .declaration-tabs-wrapper {
            overflow-x: auto;
            overflow-y: hidden;
        }

        .terminal-tabs, .declaration-tabs {
            display: flex;
            gap: 0.75rem;
            padding: 0.5rem 0;
            flex-wrap: nowrap;
        }

        .terminal-tabs .btn, .declaration-tabs .btn {
            white-space: nowrap;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
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

            .page-title {
                font-size: 1.5rem;
            }

            .branch-card {
                padding: 1rem;
            }

            .sales-amount {
                font-size: 1.5rem;
            }
        }
    </style>

    <script>
        var activeStatus = "";

        function getdetails(branch, status, branchstatus) {
            if (branchstatus == "close") {
                getCloseTerminals(branch, status)
            } else {
                getTerminals(branch, status);
            }
            $('#div_details').empty();
        }

        getTerminals('{{ $branches[0]->branch_id ?? "" }}');

        function getTerminals(branch, status) {
            showLoader("terminalTab");
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
                        if (index == 0 && value.terminal_id != "") {
                            getPartial(value.terminal_id)
                        }
                        let terminalDiv = '<button class="btn btn-outline-success" onclick="getPartial(' + value.terminal_id + ')">' + value.terminal_name + '</button>';
                        $('#terminalTab').append(terminalDiv);
                    });
                }
            });
        }

        function getCloseTerminals(branch, status) {
            showLoader("terminalTab");
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
                        let terminalDiv = '<button class="btn btn-outline-success" onclick="getDeclarations(' + value.terminal_id + ')">' + value.terminal_name + '</button>';
                        $('#terminalTab').append(terminalDiv);
                    });
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
                url: "{{ url('/heads') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    terminal: terminal,
                    status: activeStatus
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
