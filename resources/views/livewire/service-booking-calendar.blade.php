<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Service Booking Calendar</h5>
            <div class="d-flex align-items-center gap-3">
                <select wire:model.live="filter_service_provider_id" class="form-select form-select-sm" style="width: 200px;">
                    <option value="">All Service Men</option>
                    @foreach($providers as $provider)
                    <option value="{{ $provider->id }}">{{ $provider->provider_name }}</option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#bookingModal">
                    Book Service
                </button>
            </div>
        </div>
        <div class="card-body">
            <div id="calendar" wire:ignore></div>
        </div>
    </div>

    <!-- Booking Modal -->
    <div class="modal fade in" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingModalLabel">Book New Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="saveBooking">
                    <div class="modal-body">
                        <!-- Customer Details -->
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control @error('phone_number') is-invalid @enderror" wire:model="phone_number" placeholder="Enter phone to search/create">
                            @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Customer Name</label>
                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror" wire:model="customer_name">
                            @error('customer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" wire:model="address">
                        </div>

                        <hr>

                        <!-- Service Details -->
                        <div class="mb-3">
                            <label class="form-label">Service Man</label>
                            <select class="form-select @error('service_provider_id') is-invalid @enderror" wire:model="service_provider_id">
                                <option value="">Select Service Man</option>
                                @foreach($providers as $provider)
                                <option value="{{ $provider->id }}">{{ $provider->provider_name }}</option>
                                @endforeach
                            </select>
                            @error('service_provider_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Services</label>
                            <select class="form-select @error('selected_services') is-invalid @enderror" wire:model="selected_services" multiple style="height: 100px;">
                                @foreach($saloon_services as $service)
                                <option value="{{ $service->id }}">{{ $service->name }} ({{ number_format($service->price, 2) }})</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
                            @error('selected_services') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date</label>
                                <input type="date" class="form-control @error('service_date') is-invalid @enderror" wire:model="service_date">
                                @error('service_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Time</label>
                                <input type="time" class="form-control @error('service_time') is-invalid @enderror" wire:model="service_time">
                                @error('service_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Book Service</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Event Detail Modal (triggered via JS) -->
    <div class="modal fade" id="eventDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Booking Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Customer:</strong> <span id="detail-customer"></span></p>
                    <p><strong>Phone:</strong> <span id="detail-phone"></span></p>
                    <p><strong>Service Man:</strong> <span id="detail-provider"></span></p>
                    <p><strong>Services:</strong> <span id="detail-services"></span></p>
                    <p><strong>Time:</strong> <span id="detail-time"></span></p>
                </div>
            </div>
        </div>
    </div>

    @assets
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <style>
        /* Small styling fix for header alignment */
        .fc-header-toolbar {
            margin-bottom: 1rem !important;
        }
    </style>
    @endassets

    @script
    <script>
        document.addEventListener('livewire:initialized', () => {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: @json($events),
                eventClick: function(info) {
                    // Populate and show detail modal
                    document.getElementById('detail-customer').textContent = info.event.title;
                    document.getElementById('detail-phone').textContent = info.event.extendedProps.phone || 'N/A';
                    document.getElementById('detail-provider').textContent = info.event.extendedProps.provider;
                    document.getElementById('detail-services').textContent = info.event.extendedProps.services;
                    document.getElementById('detail-time').textContent = info.event.start.toLocaleString();

                    var modal = new bootstrap.Modal(document.getElementById('eventDetailModal'));
                    modal.show();
                }
            });
            calendar.render();

            // Refresh events when Livewire updates
            Livewire.on('refresh-calendar', ({
                events
            }) => {
                calendar.removeAllEvents();
                calendar.addEventSource(events);
            });

            // Close modal on successful booking
            Livewire.on('booking-saved', () => {
                var bookingModallEl = document.getElementById('bookingModal');
                var modal = bootstrap.Modal.getInstance(bookingModallEl);
                if (modal) {
                    modal.hide();
                }
            });
        });
    </script>
    @endscript
</div>