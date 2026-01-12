<div>
    <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; overflow: hidden;">
        <div class="card-header border-0 d-flex justify-content-between align-items-center" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); padding: 1.5rem;">
            <div>
                <h5 class="mb-1" style="color: #1a202c; font-weight: 700; font-size: 1.5rem;">
                    <i class="fas fa-calendar-alt me-2" style="color: #667eea;"></i>Service Booking Calendar
                </h5>
                <p class="mb-0 text-muted small">Manage your appointments and bookings</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <select wire:model.live="filter_service_provider_id" class="form-select form-select-sm shadow-sm" style="width: 220px; border-radius: 8px; border: 2px solid #e2e8f0; font-weight: 500;">
                    <option value="">🔍 All Service Men</option>
                    @foreach($providers as $provider)
                    <option value="{{ $provider->id }}">{{ $provider->provider_name }}</option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#bookingModal" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; padding: 0.5rem 1.25rem; font-weight: 600; transition: all 0.3s ease;">
                    <i class="fas fa-plus-circle me-1"></i> Book Service
                </button>
            </div>
        </div>
        <div class="card-body" style="background: white; padding: 2rem;">
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

    <!-- Event Detail Modal (triggered via JS) - Premium Design -->
    <div class="modal fade" id="eventDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.5rem 2rem;">
                    <div>
                        <h5 class="modal-title text-white mb-1" style="font-weight: 700; font-size: 1.5rem;">
                            <i class="fas fa-calendar-check me-2"></i>Booking Details
                        </h5>
                        <p class="mb-0 text-white-50 small">Complete appointment information</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 2rem; background: #f7fafc;">
                    <!-- Status Badge -->
                    <div class="mb-4 text-center">
                        <span id="detail-status-badge" class="badge px-4 py-2 shadow-sm" style="font-size: 0.9rem; font-weight: 600; border-radius: 20px;"></span>
                    </div>

                    <!-- Date & Time Card -->
                    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
                        <div class="card-body" style="padding: 1.25rem;">
                            <div class="d-flex align-items-center mb-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <i class="fas fa-clock text-white"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0" style="color: #2d3748; font-weight: 600;">Date & Time</h6>
                                </div>
                            </div>
                            <div class="ms-5">
                                <p class="mb-1" style="color: #4a5568; font-size: 0.95rem;"><strong>Date:</strong> <span id="detail-date"></span></p>
                                <p class="mb-0" style="color: #4a5568; font-size: 0.95rem;"><strong>Time:</strong> <span id="detail-time"></span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Information Card -->
                    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
                        <div class="card-body" style="padding: 1.25rem;">
                            <div class="d-flex align-items-center mb-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0" style="color: #2d3748; font-weight: 600;">Customer Information</h6>
                                </div>
                            </div>
                            <div class="ms-5">
                                <p class="mb-1" style="color: #4a5568; font-size: 0.95rem;"><strong>Name:</strong> <span id="detail-customer"></span></p>
                                <p class="mb-1" style="color: #4a5568; font-size: 0.95rem;"><strong>Phone:</strong> <span id="detail-phone"></span></p>
                                <p class="mb-0" style="color: #4a5568; font-size: 0.95rem;"><strong>Address:</strong> <span id="detail-address"></span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Service Provider Card -->
                    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
                        <div class="card-body" style="padding: 1.25rem;">
                            <div class="d-flex align-items-center mb-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);">
                                    <i class="fas fa-user-tie text-white"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0" style="color: #2d3748; font-weight: 600;">Service Provider</h6>
                                </div>
                            </div>
                            <div class="ms-5">
                                <p class="mb-0" style="color: #4a5568; font-size: 0.95rem;" id="detail-provider"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Services Card -->
                    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-body" style="padding: 1.25rem;">
                            <div class="d-flex align-items-center mb-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);">
                                    <i class="fas fa-cut text-white"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0" style="color: #2d3748; font-weight: 600;">Services</h6>
                                    <small class="text-muted" id="detail-service-count"></small>
                                </div>
                            </div>
                            <div class="ms-5" id="detail-services-container">
                                <!-- Services will be dynamically inserted here -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0" style="background: #f7fafc; padding: 1.5rem 2rem;">
                    <button type="button" class="btn shadow-sm" data-bs-dismiss="modal" style="background: #e2e8f0; color: #2d3748; border: none; border-radius: 8px; padding: 0.5rem 1.5rem; font-weight: 600;">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    @assets
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <style>
        /* Premium Calendar Styling */
        .fc-header-toolbar {
            margin-bottom: 1.5rem !important;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 12px;
        }

        .fc .fc-button-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            border: none !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            padding: 0.5rem 1rem !important;
            transition: all 0.3s ease !important;
        }

        .fc .fc-button-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4) !important;
        }

        .fc-theme-standard td,
        .fc-theme-standard th {
            border-color: #e2e8f0 !important;
        }

        .fc .fc-daygrid-day.fc-day-today {
            background: rgba(102, 126, 234, 0.1) !important;
        }

        /* Premium Event Cards */
        .fc-daygrid-event {
            white-space: normal !important;
            height: auto !important;
            align-items: flex-start !important;
            border: none !important;
            border-radius: 8px !important;
            border-left: 4px solid !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08) !important;
            margin: 3px 4px !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            position: relative !important;
            overflow: hidden !important;
        }

        .fc-daygrid-event::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0) 100%);
            pointer-events: none;
        }

        .fc-daygrid-event:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15), 0 3px 6px rgba(0, 0, 0, 0.1) !important;
            transform: translateY(-2px) scale(1.02);
            cursor: pointer;
            z-index: 10 !important;
        }

        .fc-event-main {
            overflow: hidden;
            padding: 6px 8px !important;
        }

        .fc-daygrid-event-dot {
            display: none !important;
        }

        /* Time Grid View Styling */
        .fc-timegrid-event {
            border-radius: 6px !important;
            border-left-width: 4px !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
            transition: all 0.3s ease !important;
        }

        .fc-timegrid-event:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
            transform: translateX(2px);
        }

        /* Button hover effect */
        button[data-bs-target="#bookingModal"]:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4) !important;
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
                eventContent: function(arg) {
                    // Premium Event Content with Status Indicator
                    let timeText = arg.event.start.toLocaleTimeString([], {
                        hour: 'numeric',
                        minute: '2-digit',
                        hour12: true
                    });
                    if (arg.event.end) {
                        timeText += ' - ' + arg.event.end.toLocaleTimeString([], {
                            hour: 'numeric',
                            minute: '2-digit',
                            hour12: true
                        });
                    }

                    let customer = arg.event.title;
                    let provider = arg.event.extendedProps.provider || 'Unassigned';
                    let serviceCount = arg.event.extendedProps.serviceCount || 0;

                    let contentEl = document.createElement('div');
                    contentEl.className = 'd-flex flex-column';
                    contentEl.style.cssText = 'gap: 2px; line-height: 1.3; position: relative; z-index: 1;';

                    // Time display with icon
                    let timeEl = document.createElement('div');
                    timeEl.style.cssText = 'font-size: 10.5px; font-weight: 700; color: rgba(0,0,0,0.9); letter-spacing: 0.3px; text-transform: uppercase;';
                    timeEl.innerHTML = `<i class="fas fa-clock" style="font-size: 9px; margin-right: 3px;"></i>${timeText}`;

                    // Customer name - prominent with icon
                    let customerEl = document.createElement('div');
                    customerEl.style.cssText = 'font-size: 13px; font-weight: 600; color: rgba(0,0,0,0.95); margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;';
                    customerEl.innerHTML = `<i class="fas fa-user" style="font-size: 10px; margin-right: 4px; opacity: 0.7;"></i>${customer}`;

                    // Provider name with icon
                    let providerEl = document.createElement('div');
                    providerEl.style.cssText = 'font-size: 11px; font-weight: 500; color: rgba(0,0,0,0.65); margin-top: 1px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;';
                    providerEl.innerHTML = `<i class="fas fa-user-tie" style="font-size: 9px; margin-right: 4px; opacity: 0.6;"></i>${provider}`;

                    // Service count badge
                    if (serviceCount > 0) {
                        let serviceEl = document.createElement('div');
                        serviceEl.style.cssText = 'font-size: 10px; font-weight: 600; color: rgba(0,0,0,0.6); margin-top: 2px;';
                        serviceEl.innerHTML = `<i class="fas fa-cut" style="font-size: 8px; margin-right: 3px;"></i>${serviceCount} service${serviceCount > 1 ? 's' : ''}`;
                        contentEl.appendChild(timeEl);
                        contentEl.appendChild(customerEl);
                        contentEl.appendChild(providerEl);
                        contentEl.appendChild(serviceEl);
                    } else {
                        contentEl.appendChild(timeEl);
                        contentEl.appendChild(customerEl);
                        contentEl.appendChild(providerEl);
                    }

                    return {
                        domNodes: [contentEl]
                    };
                },
                eventClick: function(info) {
                    // Populate premium modal with all details
                    const props = info.event.extendedProps;

                    // Status Badge
                    const statusBadge = document.getElementById('detail-status-badge');
                    const status = props.status || 'pending';
                    const statusColors = {
                        'confirmed': {
                            bg: '#48bb78',
                            text: 'white'
                        },
                        'pending': {
                            bg: '#ed8936',
                            text: 'white'
                        },
                        'cancelled': {
                            bg: '#f56565',
                            text: 'white'
                        },
                        'completed': {
                            bg: '#4299e1',
                            text: 'white'
                        }
                    };
                    const statusColor = statusColors[status] || statusColors['pending'];
                    statusBadge.textContent = props.statusBadge || 'Pending';
                    statusBadge.style.background = statusColor.bg;
                    statusBadge.style.color = statusColor.text;

                    // Date & Time
                    document.getElementById('detail-date').textContent = props.formattedDate || info.event.start.toLocaleDateString();
                    document.getElementById('detail-time').textContent = props.formattedTime || info.event.start.toLocaleString();

                    // Customer Information
                    document.getElementById('detail-customer').textContent = info.event.title;
                    document.getElementById('detail-phone').textContent = props.phone || 'N/A';
                    document.getElementById('detail-address').textContent = props.address || 'N/A';

                    // Service Provider
                    document.getElementById('detail-provider').textContent = props.provider;

                    // Services - Create individual service cards
                    const servicesContainer = document.getElementById('detail-services-container');
                    const servicesArray = props.servicesArray || [];
                    const serviceCount = props.serviceCount || 0;

                    document.getElementById('detail-service-count').textContent = `${serviceCount} service${serviceCount !== 1 ? 's' : ''} selected`;

                    servicesContainer.innerHTML = '';
                    if (servicesArray.length > 0) {
                        servicesArray.forEach((service, index) => {
                            const serviceCard = document.createElement('div');
                            serviceCard.className = 'mb-2';
                            serviceCard.style.cssText = 'background: #f7fafc; padding: 0.75rem; border-radius: 8px; border-left: 3px solid #4299e1;';
                            serviceCard.innerHTML = `
                                <div class="d-flex justify-content-between align-items-center">
                                    <span style="color: #2d3748; font-weight: 500;">${index + 1}. ${service.name}</span>
                                    <span style="color: #4a5568; font-weight: 600;">$${parseFloat(service.price).toFixed(2)}</span>
                                </div>
                            `;
                            servicesContainer.appendChild(serviceCard);
                        });
                    } else {
                        servicesContainer.innerHTML = '<p class="mb-0 text-muted">No services listed</p>';
                    }

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