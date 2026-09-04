@php
    $timingCollection = collect($timings ?? []);
    $dayLabels = [
        'Mon' => 'Monday',
        'Tue' => 'Tuesday',
        'Wed' => 'Wednesday',
        'Thu' => 'Thursday',
        'Fri' => 'Friday',
        'Sat' => 'Saturday',
        'Sun' => 'Sunday',
    ];
    $shiftsByDay = $timingCollection->groupBy('day');
    $hasTimings = $timingCollection->count() > 0;
@endphp

<form id="timingForm" method="post" action="{{ route('branchTimingStore') }}">
    @csrf
    <input type="hidden" name="website_id" value="{{ $websiteId }}">
    <input type="hidden" name="branch_id" value="{{ $branchId }}">
    <input type="hidden" name="mode" value="{{ $hasTimings ? 'update' : 'insert' }}">

    <div class="flex flex-wrap items-center gap-2 border-b border-erp-line bg-erp-soft px-5 py-3">
        <span class="mr-1 text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Quick fill</span>
        <button type="button" data-preset-open="09:00" data-preset-close="18:00"
            class="rounded-lg border border-erp-line bg-white px-3 py-1.5 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">09:00 &ndash; 18:00</button>
        <button type="button" data-preset-open="10:00" data-preset-close="22:00"
            class="rounded-lg border border-erp-line bg-white px-3 py-1.5 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">10:00 &ndash; 22:00</button>
        <button type="button" id="btnCopyFirst"
            class="rounded-lg border border-erp-line bg-white px-3 py-1.5 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Copy first open day to all</button>
        <button type="button" id="btnCloseAll"
            class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Mark all closed</button>
    </div>

    <div id="dayList">
        @foreach ($days as $day)
            @php
                $shifts = $shiftsByDay->get($day, collect())->values();
            @endphp
            <div class="day-block border-b border-erp-line last:border-b-0" data-day="{{ $day }}">
                <div class="flex flex-wrap items-center justify-between gap-3 px-5 pt-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="w-24 text-sm font-bold text-erp-ink">{{ $dayLabels[$day] ?? $day }}</span>
                        <span data-day-status></span>
                        <span data-day-total class="text-xs font-bold text-erp-mute"></span>
                    </div>
                    <button type="button" data-add-shift
                        class="rounded-lg border border-erp-line px-3 py-1.5 text-xs font-bold text-erp-dark transition hover:border-erp hover:bg-emerald-50">+ Add Shift</button>
                </div>

                <div data-shifts class="space-y-2 px-5 pb-4 pt-3">
                    @forelse ($shifts as $shift)
                        <div class="shift-row flex flex-wrap items-center gap-2">
                            <input type="hidden" name="dayname[]" value="{{ $day }}">
                            <input type="hidden" name="id[]" value="{{ $shift->id }}">
                            <input type="time" name="starttime[]" value="{{ $shift->opening_time ? date('H:i', strtotime($shift->opening_time)) : '' }}"
                                class="day-time day-open h-10 w-36 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                            <span class="text-xs font-bold text-erp-mute">to</span>
                            <input type="time" name="endtime[]" value="{{ $shift->closing_time ? date('H:i', strtotime($shift->closing_time)) : '' }}"
                                class="day-time day-close h-10 w-36 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                            <span data-duration class="w-24 text-sm font-bold text-erp-text">&mdash;</span>
                            <span data-shift-status></span>
                            <button type="button" data-remove-shift
                                class="ml-auto rounded-lg border border-erp-line px-3 py-2 text-xs font-bold text-erp-text transition hover:border-rose-300 hover:bg-rose-50 hover:text-rose-700">Remove</button>
                        </div>
                    @empty
                        <div class="shift-row flex flex-wrap items-center gap-2">
                            <input type="hidden" name="dayname[]" value="{{ $day }}">
                            <input type="hidden" name="id[]" value="">
                            <input type="time" name="starttime[]" value=""
                                class="day-time day-open h-10 w-36 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                            <span class="text-xs font-bold text-erp-mute">to</span>
                            <input type="time" name="endtime[]" value=""
                                class="day-time day-close h-10 w-36 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                            <span data-duration class="w-24 text-sm font-bold text-erp-text">&mdash;</span>
                            <span data-shift-status></span>
                            <button type="button" data-remove-shift
                                class="ml-auto rounded-lg border border-erp-line px-3 py-2 text-xs font-bold text-erp-text transition hover:border-rose-300 hover:bg-rose-50 hover:text-rose-700">Remove</button>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    <div class="flex flex-col gap-3 border-t border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
        <p id="timingFormMessage" class="text-sm font-bold text-erp-mute">
            A day with no times is saved as closed. Add a second shift for a split day such as 13:00&ndash;15:00 and 20:00&ndash;00:00.
        </p>
        <button id="btnSubmit" type="submit"
            class="h-10 rounded-lg bg-erp px-6 text-sm font-bold text-white transition hover:bg-erp-dark">
            {{ $hasTimings ? 'Update Schedule' : 'Save Schedule' }}
        </button>
    </div>

    <template id="shiftTemplate">
        <div class="shift-row flex flex-wrap items-center gap-2">
            <input type="hidden" name="dayname[]" value="">
            <input type="hidden" name="id[]" value="">
            <input type="time" name="starttime[]" value=""
                class="day-time day-open h-10 w-36 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
            <span class="text-xs font-bold text-erp-mute">to</span>
            <input type="time" name="endtime[]" value=""
                class="day-time day-close h-10 w-36 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
            <span data-duration class="w-24 text-sm font-bold text-erp-text">&mdash;</span>
            <span data-shift-status></span>
            <button type="button" data-remove-shift
                class="ml-auto rounded-lg border border-erp-line px-3 py-2 text-xs font-bold text-erp-text transition hover:border-rose-300 hover:bg-rose-50 hover:text-rose-700">Remove</button>
        </div>
    </template>
</form>

<script type="text/javascript">
    (function () {
        var form = document.getElementById('timingForm');
        if (!form) {
            return;
        }

        var template = document.getElementById('shiftTemplate');
        var message = document.getElementById('timingFormMessage');

        function dayBlocks() {
            return Array.prototype.slice.call(form.querySelectorAll('.day-block'));
        }

        function shiftRows(block) {
            return Array.prototype.slice.call(block.querySelectorAll('.shift-row'));
        }

        function toMinutes(value) {
            if (!value) {
                return null;
            }
            var parts = value.split(':');
            return (parseInt(parts[0], 10) * 60) + parseInt(parts[1], 10);
        }

        function badge(text, classes) {
            return '<span class="rounded-md px-2 py-1 text-xs font-bold ring-1 ' + classes + '">' + text + '</span>';
        }

        // A shift that ends at or before it starts runs past midnight, so it
        // covers two ranges on the clock face.
        function rangesOf(open, close) {
            var start = toMinutes(open);
            var end = toMinutes(close);

            if (end <= start) {
                return [[start, 1440], [0, end]];
            }
            return [[start, end]];
        }

        function overlaps(a, b) {
            return a[0] < b[1] && b[0] < a[1];
        }

        function setMessage(text, tone) {
            message.innerHTML = text;
            message.className = 'text-sm font-bold ' + (tone || 'text-erp-mute');
        }

        function addShift(block, open, close) {
            var row = template.content.firstElementChild.cloneNode(true);
            row.querySelector('input[name="dayname[]"]').value = block.getAttribute('data-day');
            row.querySelector('.day-open').value = open || '';
            row.querySelector('.day-close').value = close || '';
            block.querySelector('[data-shifts]').appendChild(row);
            return row;
        }

        function clearDay(block) {
            var rows = shiftRows(block);
            rows.forEach(function (row, index) {
                if (index === 0) {
                    row.querySelector('.day-open').value = '';
                    row.querySelector('.day-close').value = '';
                } else {
                    row.remove();
                }
            });
        }

        function refresh() {
            var openDays = 0;
            var totalMinutes = 0;
            var hasOverlap = false;

            dayBlocks().forEach(function (block) {
                var dayMinutes = 0;
                var dayShifts = 0;
                var incomplete = 0;
                var ranges = [];

                shiftRows(block).forEach(function (row) {
                    var open = row.querySelector('.day-open').value;
                    var close = row.querySelector('.day-close').value;
                    var durationCell = row.querySelector('[data-duration]');
                    var statusCell = row.querySelector('[data-shift-status]');

                    if (!open && !close) {
                        durationCell.innerHTML = '&mdash;';
                        statusCell.innerHTML = '';
                        return;
                    }

                    if (!open || !close) {
                        incomplete += 1;
                        durationCell.innerHTML = '&mdash;';
                        statusCell.innerHTML = badge('Incomplete', 'bg-amber-50 text-amber-700 ring-amber-200');
                        return;
                    }

                    var minutes = toMinutes(close) - toMinutes(open);
                    var overnight = minutes <= 0;
                    if (overnight) {
                        minutes += 24 * 60;
                    }

                    dayShifts += 1;
                    dayMinutes += minutes;
                    ranges.push({ row: row, parts: rangesOf(open, close) });

                    durationCell.textContent = Math.floor(minutes / 60) + 'h ' + (minutes % 60) + 'm';
                    statusCell.innerHTML = overnight
                        ? badge('Overnight', 'bg-sky-50 text-sky-700 ring-sky-200')
                        : badge('Open', 'bg-emerald-50 text-emerald-700 ring-emerald-200');
                });

                // Two shifts on the same day must not cover the same minute.
                for (var i = 0; i < ranges.length; i++) {
                    for (var j = i + 1; j < ranges.length; j++) {
                        var clash = ranges[i].parts.some(function (a) {
                            return ranges[j].parts.some(function (b) {
                                return overlaps(a, b);
                            });
                        });

                        if (clash) {
                            hasOverlap = true;
                            [ranges[i].row, ranges[j].row].forEach(function (row) {
                                row.querySelector('[data-shift-status]').innerHTML =
                                    badge('Overlaps', 'bg-rose-50 text-rose-700 ring-rose-200');
                            });
                        }
                    }
                }

                var statusCell = block.querySelector('[data-day-status]');
                var totalCell = block.querySelector('[data-day-total]');

                if (dayShifts > 0) {
                    openDays += 1;
                    totalMinutes += dayMinutes;
                    statusCell.innerHTML = badge(
                        dayShifts > 1 ? dayShifts + ' shifts' : 'Open',
                        'bg-emerald-50 text-emerald-700 ring-emerald-200'
                    );
                    totalCell.textContent = Math.floor(dayMinutes / 60) + 'h ' + (dayMinutes % 60) + 'm total';
                } else if (incomplete > 0) {
                    statusCell.innerHTML = badge('Incomplete', 'bg-amber-50 text-amber-700 ring-amber-200');
                    totalCell.textContent = '';
                } else {
                    statusCell.innerHTML = badge('Closed', 'bg-slate-100 text-slate-600 ring-slate-200');
                    totalCell.textContent = '';
                }
            });

            if (typeof window.branchTimingStats === 'function') {
                window.branchTimingStats(openDays, totalMinutes);
            }

            return hasOverlap;
        }

        function applyToAll(open, close) {
            dayBlocks().forEach(function (block) {
                clearDay(block);
                var first = shiftRows(block)[0];
                first.querySelector('.day-open').value = open;
                first.querySelector('.day-close').value = close;
            });
            refresh();
        }

        form.addEventListener('input', function (event) {
            if (event.target.classList.contains('day-time')) {
                refresh();
            }
        });

        form.addEventListener('click', function (event) {
            var preset = event.target.closest('[data-preset-open]');
            if (preset) {
                applyToAll(preset.getAttribute('data-preset-open'), preset.getAttribute('data-preset-close'));
                return;
            }

            var add = event.target.closest('[data-add-shift]');
            if (add) {
                addShift(add.closest('.day-block'), '', '').querySelector('.day-open').focus();
                refresh();
                return;
            }

            var remove = event.target.closest('[data-remove-shift]');
            if (remove) {
                var row = remove.closest('.shift-row');
                var block = remove.closest('.day-block');

                // Always leave one row behind so the day stays editable.
                if (shiftRows(block).length > 1) {
                    row.remove();
                } else {
                    row.querySelector('.day-open').value = '';
                    row.querySelector('.day-close').value = '';
                }
                refresh();
            }
        });

        document.getElementById('btnCloseAll').addEventListener('click', function () {
            applyToAll('', '');
        });

        // Copies the whole shift set of the first configured day, so a split
        // day is repeated across the week in one click.
        document.getElementById('btnCopyFirst').addEventListener('click', function () {
            var source = null;

            dayBlocks().some(function (block) {
                var pairs = shiftRows(block)
                    .map(function (row) {
                        return [row.querySelector('.day-open').value, row.querySelector('.day-close').value];
                    })
                    .filter(function (pair) {
                        return pair[0] && pair[1];
                    });

                if (pairs.length) {
                    source = { block: block, pairs: pairs };
                    return true;
                }
                return false;
            });

            if (!source) {
                setMessage('Set one day first, then copy it to the rest.', 'text-amber-600');
                return;
            }

            dayBlocks().forEach(function (block) {
                if (block === source.block) {
                    return;
                }
                clearDay(block);
                source.pairs.forEach(function (pair, index) {
                    if (index === 0) {
                        var first = shiftRows(block)[0];
                        first.querySelector('.day-open').value = pair[0];
                        first.querySelector('.day-close').value = pair[1];
                    } else {
                        addShift(block, pair[0], pair[1]);
                    }
                });
            });

            refresh();
        });

        form.addEventListener('submit', function (event) {
            var incomplete = null;

            dayBlocks().forEach(function (block) {
                shiftRows(block).forEach(function (row) {
                    var open = row.querySelector('.day-open').value;
                    var close = row.querySelector('.day-close').value;
                    if (!incomplete && ((open && !close) || (!open && close))) {
                        incomplete = row;
                    }
                });
            });

            if (incomplete) {
                event.preventDefault();
                setMessage('Every shift needs both an opening and a closing time.', 'text-rose-600');
                incomplete.querySelector('.day-open').focus();
                return;
            }

            if (refresh()) {
                event.preventDefault();
                setMessage('Two shifts on the same day overlap. Fix the highlighted rows first.', 'text-rose-600');
                return;
            }

            var button = document.getElementById('btnSubmit');
            button.disabled = true;
            button.classList.add('opacity-60');
            button.textContent = 'Saving...';
        });

        refresh();
    })();
</script>
