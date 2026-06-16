@extends('layouts.master-tailwind')

@section('title', 'Sales Details')

@section('content')
<div class="space-y-5">
    <input type="hidden" id="terminalID">
    <input type="hidden" id="openingID">

    <header class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-black text-erp-ink">Sales Details</h1>
            <p class="mt-1 text-sm text-erp-mute">Review active and closed branch sales from one workspace.</p>
        </div>
        <a href="{{ route('home') }}" class="inline-flex h-10 items-center justify-center rounded-lg border border-erp-line bg-white px-4 text-sm font-bold text-erp-text shadow-sm transition hover:border-erp hover:text-erp-dark">
            Back to Dashboard
        </a>
    </header>

    <section class="grid gap-4 lg:grid-cols-[360px_minmax(0,1fr)]">
        <aside class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line p-4">
                <div class="grid grid-cols-2 gap-2">
                    <button id="active-tab" type="button" class="sales-tab h-10 rounded-lg bg-erp text-sm font-bold text-white shadow-sm" onclick="switchSalesTab('open')">Active</button>
                    <button id="closed-tab" type="button" class="sales-tab h-10 rounded-lg border border-erp-line bg-white text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark" onclick="switchSalesTab('close')">Closed</button>
                </div>
                <input type="date" id="dateselection" class="mt-3 hidden h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" value="{{ date('Y-m-d', strtotime('-1 days')) }}">
            </div>

            <div class="max-h-[calc(100vh-235px)] overflow-auto p-3">
                <div id="tab-active" class="space-y-2">
                    @forelse ($branches as $value)
                        <button type="button" class="branch-button w-full rounded-lg border border-erp-line bg-white p-3 text-left transition hover:border-erp hover:bg-green-50"
                            onclick="getdetails(this, '{{ session('roleId') == 2 ? $value->branch_id : $value->terminal_id }}', '{{ addslashes($value->identify) }}', 'open')">
                            <span class="flex items-center gap-3">
                                <img class="h-11 w-11 rounded-lg border border-erp-line object-cover" src="{{ asset('storage/images/branch/' . (!empty($value->branch_logo) ? $value->branch_logo : 'placeholder.jpg')) }}" alt="">
                                <span class="min-w-0">
                                    <span class="branch-name block break-words text-sm font-black text-erp-ink">{{ session('roleId') == 2 ? $value->branch_name : $value->terminal_name }}</span>
                                    <span class="mt-1 block text-sm font-black text-erp-dark">{{ session('currency') }} {{ number_format($value->sales, 0) }}</span>
                                </span>
                            </span>
                        </button>
                    @empty
                        <div class="rounded-lg border border-dashed border-erp-line p-6 text-center text-sm text-erp-mute">No active sales found.</div>
                    @endforelse
                </div>

                <div id="tab-closed" class="hidden space-y-2">
                    @forelse ($branchesClosedSales as $value)
                        <button type="button" class="branch-button w-full rounded-lg border border-erp-line bg-white p-3 text-left transition hover:border-erp hover:bg-green-50"
                            onclick="getdetails(this, '{{ session('roleId') == 2 ? $value->branch_id : $value->terminal_id }}', '{{ addslashes($value->identify) }}', 'close')">
                            <span class="flex items-center gap-3">
                                <img class="h-11 w-11 rounded-lg border border-erp-line object-cover" src="{{ asset('storage/images/branch/' . (!empty($value->branch_logo) ? $value->branch_logo : 'placeholder.jpg')) }}" alt="">
                                <span class="min-w-0">
                                    <span class="branch-name block break-words text-sm font-black text-erp-ink">{{ session('roleId') == 2 ? $value->branch_name : $value->terminal_name }}</span>
                                    <span class="mt-1 block text-sm font-black text-erp-dark">{{ session('currency') }} {{ number_format($value->sales, 0) }}</span>
                                </span>
                            </span>
                        </button>
                    @empty
                        <div class="rounded-lg border border-dashed border-erp-line p-6 text-center text-sm text-erp-mute">No closed sales found.</div>
                    @endforelse
                </div>
            </div>
        </aside>

        <main class="min-w-0 rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 id="workspace-title" class="text-base font-black text-erp-ink">Terminal Workspace</h2>
                    <p id="workspace-subtitle" class="mt-1 text-sm text-erp-mute">Choose a branch to load terminals.</p>
                </div>
            </div>

            <div class="space-y-4 border-b border-erp-line bg-slate-50 px-5 py-4">
                <div>
                    <div class="text-xs font-black uppercase tracking-[0.16em] text-erp-mute">Terminals</div>
                    <div id="terminalTab" class="mt-3 flex gap-2 overflow-x-auto pb-1"></div>
                </div>
                <div id="declarationWrap" class="hidden">
                    <div class="text-xs font-black uppercase tracking-[0.16em] text-erp-mute">Declarations</div>
                    <div id="declartionTab" class="mt-3 flex gap-2 overflow-x-auto pb-1"></div>
                </div>
            </div>

            <div id="div_details" class="min-h-[420px] bg-slate-50 p-4">
                <div class="flex min-h-[300px] flex-col items-center justify-center rounded-lg border border-dashed border-erp-line bg-white p-6 text-center text-sm text-erp-mute">
                    <div class="text-base font-black text-erp-ink">No terminal selected</div>
                    <div class="mt-1">Sales information will appear here after a terminal is loaded.</div>
                </div>
            </div>
        </main>
    </section>
</div>
@endsection

@section('scriptcode_three')
<script>
    var activeStatus = 'open';
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.addEventListener('DOMContentLoaded', function() {
        var firstBranch = document.querySelector('#tab-active .branch-button');
        if (firstBranch) {
            firstBranch.click();
        }

        document.getElementById('dateselection').addEventListener('change', function() {
            var activeTerminal = document.querySelector('#terminalTab .selector-button-active');
            if (activeStatus === 'close' && activeTerminal) {
                activeTerminal.click();
            }
        });
    });

    window.toggleCategory = function(categoryId) {
        var content = document.getElementById(categoryId);
        var icon = document.getElementById(categoryId + '-icon');
        if (!content) return;
        content.classList.toggle('hidden');
        if (icon) icon.classList.toggle('rotate-180');
    };

    function switchSalesTab(status) {
        activeStatus = status;
        setTabState('active-tab', status === 'open');
        setTabState('closed-tab', status === 'close');
        document.getElementById('tab-active').classList.toggle('hidden', status !== 'open');
        document.getElementById('tab-closed').classList.toggle('hidden', status !== 'close');
        document.getElementById('dateselection').classList.toggle('hidden', status !== 'close');
        document.getElementById('terminalTab').innerHTML = '';
        document.getElementById('declartionTab').innerHTML = '';
        document.getElementById('declarationWrap').classList.add('hidden');
        setDetailsEmpty('Choose a branch to load terminals.');
    }

    function setTabState(id, active) {
        var button = document.getElementById(id);
        button.className = active
            ? 'sales-tab h-10 rounded-lg bg-erp text-sm font-bold text-white shadow-sm'
            : 'sales-tab h-10 rounded-lg border border-erp-line bg-white text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark';
    }

    function getdetails(element, branch, status, branchstatus) {
        document.querySelectorAll('.branch-button').forEach(function(button) {
            button.classList.remove('border-erp', 'bg-green-50', 'ring-2', 'ring-green-100');
        });
        element.classList.add('border-erp', 'bg-green-50', 'ring-2', 'ring-green-100');

        document.getElementById('workspace-title').textContent = element.querySelector('.branch-name').textContent.trim();
        document.getElementById('workspace-subtitle').textContent = branchstatus === 'close' ? 'Closed terminals for selected date' : 'Active terminals';

        if (branchstatus === 'close') {
            getCloseTerminals(branch, status);
        } else {
            getTerminals(branch, status);
        }
        setDetailsEmpty('Select a terminal to view sales details.');
    }

    function getTerminals(branch, status) {
        showLoader('terminalTab');
        document.getElementById('declarationWrap').classList.add('hidden');
        document.getElementById('declartionTab').innerHTML = '';

        postJson("{{ url('/getTerminals') }}", { branch: branch, status: status })
            .then(function(result) {
                renderTerminalButtons(result, 'open');
                var first = document.querySelector('#terminalTab .selector-button');
                if (first) first.click();
            })
            .catch(function() {
                setSelectorMessage('terminalTab', 'Unable to load terminals.');
            });
    }

    function getCloseTerminals(branch, status) {
        showLoader('terminalTab');
        document.getElementById('declarationWrap').classList.remove('hidden');
        document.getElementById('declartionTab').innerHTML = '';

        postJson("{{ url('/getTerminals') }}", { branch: branch, status: status })
            .then(function(result) {
                renderTerminalButtons(result, 'close');
                var first = document.querySelector('#terminalTab .selector-button');
                if (first) first.click();
            })
            .catch(function() {
                setSelectorMessage('terminalTab', 'Unable to load terminals.');
            });
    }

    function renderTerminalButtons(result, type) {
        var target = document.getElementById('terminalTab');
        target.innerHTML = '';

        if (!result || !result.length) {
            setSelectorMessage('terminalTab', 'No terminals found.');
            return;
        }

        result.forEach(function(value) {
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'selector-button shrink-0 rounded-lg border border-erp-line bg-white px-4 py-2 text-sm font-bold text-erp-text shadow-sm transition hover:border-erp hover:text-erp-dark';
            button.textContent = value.terminal_name;
            button.onclick = function() {
                selectTerminal(button, value.terminal_id, type);
            };
            target.appendChild(button);
        });
    }

    function selectTerminal(element, id, type) {
        document.querySelectorAll('#terminalTab .selector-button').forEach(function(button) {
            button.className = 'selector-button shrink-0 rounded-lg border border-erp-line bg-white px-4 py-2 text-sm font-bold text-erp-text shadow-sm transition hover:border-erp hover:text-erp-dark';
            button.classList.remove('selector-button-active');
        });
        element.className = 'selector-button selector-button-active shrink-0 rounded-lg bg-erp px-4 py-2 text-sm font-bold text-white shadow-sm';
        document.getElementById('workspace-subtitle').textContent = element.textContent.trim();

        if (type === 'open') {
            document.getElementById('declarationWrap').classList.add('hidden');
            document.getElementById('declartionTab').innerHTML = '';
            getPartial(id);
        } else {
            document.getElementById('declarationWrap').classList.remove('hidden');
            getDeclarations(id);
        }
    }

    function getDeclarations(terminalId) {
        var date = document.getElementById('dateselection').value;
        if (!date) {
            setSelectorMessage('declartionTab', 'Select a date first.');
            return;
        }

        showLoader('declartionTab');
        postJson("{{ url('/get-close-declarations') }}", { terminal: terminalId, date: date })
            .then(function(result) {
                var target = document.getElementById('declartionTab');
                target.innerHTML = '';
                setDetailsEmpty('Select a declaration to view closed sales.');

                if (!result || !result.length) {
                    setSelectorMessage('declartionTab', 'No declarations found.');
                    return;
                }

                result.forEach(function(value) {
                    var button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'declaration-button shrink-0 rounded-lg border border-erp-line bg-white px-4 py-2 text-sm font-bold text-erp-text shadow-sm transition hover:border-erp hover:text-erp-dark';
                    button.textContent = 'D#' + value.opening_id;
                    button.onclick = function() {
                        document.querySelectorAll('#declartionTab .declaration-button').forEach(function(item) {
                            item.className = 'declaration-button shrink-0 rounded-lg border border-erp-line bg-white px-4 py-2 text-sm font-bold text-erp-text shadow-sm transition hover:border-erp hover:text-erp-dark';
                        });
                        button.className = 'declaration-button shrink-0 rounded-lg bg-erp px-4 py-2 text-sm font-bold text-white shadow-sm';
                        getLastDayPartial(terminalId, value.opening_id);
                    };
                    target.appendChild(button);
                });
            })
            .catch(function() {
                setSelectorMessage('declartionTab', 'Unable to load declarations.');
            });
    }

    function getPartial(terminal) {
        showLoader('div_details');
        postHtml("{{ url('/get-terminal-details-v2') }}", { terminal: terminal })
            .then(function(html) {
                document.getElementById('div_details').innerHTML = html;
            })
            .catch(function() {
                setDetailsEmpty('Unable to load terminal details.');
            });
    }

    function getLastDayPartial(terminal, openingId) {
        showLoader('div_details');
        postHtml("{{ url('/get-terminal-details-v2') }}", { terminal: terminal, openingId: openingId })
            .then(function(html) {
                document.getElementById('div_details').innerHTML = html;
            })
            .catch(function() {
                setDetailsEmpty('Unable to load closed terminal details.');
            });
    }

    function cashDetails(opening, terminal, mode) {
        window.open("{{ url('sales-show') }}/" + opening + "/" + terminal + "/" + mode);
    }

    function openTerminal(branchId, terminalId) {
        var amount = prompt('Please enter amount to open');
        if (!amount) return;

        postText("{{ url('/open-terminal') }}", { terminal: terminalId, branch: branchId, amount: amount })
            .then(function(result) {
                if (String(result).trim() == '1') location.reload();
            });
    }

    function closedTerminal(openingId, terminalId) {
        var amount = prompt('Please enter the amount to close this terminal', '');
        if (!amount) return;

        showLoader('div_details');
        postText("{{ url('/close-terminal') }}", { terminal: terminalId, opening: openingId, amount: amount })
            .then(function(result) {
                if (String(result).trim() == '1') location.reload();
            });
    }

    function postJson(url, data) {
        return fetch(url, postOptions(data)).then(function(response) { return response.json(); });
    }

    function postHtml(url, data) {
        return fetch(url, postOptions(data)).then(function(response) { return response.text(); });
    }

    function postText(url, data) {
        return fetch(url, postOptions(data)).then(function(response) { return response.text(); });
    }

    function postOptions(data) {
        data._token = csrfToken;
        return {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'text/html, application/json, */*',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(data)
        };
    }

    function showLoader(divName) {
        document.getElementById(divName).innerHTML = '<div class="flex min-h-[96px] items-center justify-center rounded-lg border border-dashed border-erp-line bg-white p-6 text-sm font-bold text-erp-mute"><span class="mr-3 h-5 w-5 animate-spin rounded-full border-2 border-erp-line border-t-erp"></span>Loading</div>';
    }

    function setDetailsEmpty(message) {
        document.getElementById('div_details').innerHTML = '<div class="flex min-h-[300px] items-center justify-center rounded-lg border border-dashed border-erp-line bg-white p-6 text-center text-sm font-bold text-erp-mute">' + message + '</div>';
    }

    function setSelectorMessage(target, message) {
        document.getElementById(target).innerHTML = '<div class="rounded-lg border border-dashed border-erp-line bg-white px-4 py-2 text-sm font-bold text-erp-mute">' + message + '</div>';
    }
</script>
@endsection
