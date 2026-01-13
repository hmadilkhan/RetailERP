<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Rule;
use App\Models\Booking;
use App\Models\SaloonService;
use App\Models\ServiceProvider;
use App\Models\Customer;
use Carbon\Carbon;

class ServiceBookingCalendar extends Component
{
    // Filter
    public $filter_service_provider_id = '';

    // Booking Form
    #[Rule('required|min:3')]
    public $customer_name = '';

    #[Rule('required')]
    public $phone_number = '';

    #[Rule('nullable')]
    public $address = '';

    #[Rule('required|date')]
    public $service_date = '';

    #[Rule('required')]
    public $service_time = '';

    #[Rule('required|exists:service_provider_details,id')]
    public $service_provider_id = '';

    #[Rule('required|array|min:1')]
    public $selected_services = [];

    // UI State
    public $isModalOpen = false;
    public $searchingCustomer = false;
    public $customerFound = false;

    public function render()
    {
        return view('livewire.service-booking-calendar', [
            'events' => $this->getEvents(),
            'providers' => ServiceProvider::all(),
            'saloon_services' => SaloonService::all()
        ]);
    }

    public function getEvents()
    {
        $query = Booking::with(['customer', 'serviceProvider', 'services']);

        if ($this->filter_service_provider_id) {
            $query->where('service_provider_id', $this->filter_service_provider_id);
        }

        $bookings = $query->get();

        return $bookings->map(function ($booking) {
            // Calculate duration based on number of services (30 mins per service)
            $serviceCount = $booking->services->count();
            $durationMinutes = $serviceCount > 0 ? $serviceCount * 30 : 60; // Default 1 hour if no services

            $startDateTime = Carbon::parse($booking->service_date . ' ' . $booking->service_time);
            $endTime = $startDateTime->copy()->addMinutes($durationMinutes);

            return [
                'id' => $booking->id,
                'title' => $booking->customer ? $booking->customer->name : 'Unknown Customer',
                'start' => $booking->service_date . 'T' . $booking->service_time,
                'end' => $endTime->toIso8601String(),
                'extendedProps' => [
                    'provider' => $booking->serviceProvider->provider_name ?? 'Unassigned',
                    'services' => $booking->services->pluck('name')->join(', '),
                    'servicesArray' => $booking->services->map(fn($s) => ['name' => $s->name, 'price' => $s->price])->toArray(),
                    'serviceCount' => $booking->services->count(),
                    'phone' => $booking->customer->phone ?? '',
                    'address' => $booking->customer->address ?? '',
                    'status' => $booking->status,
                    'statusBadge' => ucfirst($booking->status),
                    'formattedDate' => $startDateTime->format('l, F j, Y'),
                    'formattedTime' => $startDateTime->format('g:i A') . ' - ' . $endTime->format('g:i A'),
                ],
                'backgroundColor' => $this->getColorForStatus($booking->status),
                'borderColor' => $this->getColorForStatus($booking->status),
            ];
        });
    }

    private function getColorForStatus($status)
    {
        return match ($status) {
            'confirmed' => '#28a745', // Green
            'pending' => '#ffc107', // Yellow
            'cancelled' => '#dc3545', // Red
            default => '#007bff', // Blue
        };
    }

    public function saveBooking()
    {
        $this->validate();

        // Check for time conflicts before creating the booking
        if ($this->hasTimeConflict()) {
            $this->addError('service_time', 'This service provider is already booked during the selected time slot. Please choose a different time.');
            return;
        }

        // Find or Create Customer
        $customer = Customer::firstOrCreate(
            ['phone' => $this->phone_number],
            ['name' => $this->customer_name, 'address' => $this->address]
        );

        $booking = Booking::create([
            'customer_id' => $customer->id,
            'service_provider_id' => $this->service_provider_id,
            'service_date' => $this->service_date,
            'service_time' => $this->service_time,
            'status' => 'pending' // Default status
        ]);

        $booking->services()->attach($this->selected_services);

        $this->reset(['customer_name', 'phone_number', 'address', 'service_date', 'service_time', 'service_provider_id', 'selected_services']);

        $this->dispatch('booking-saved'); // Trigger JS to close modal/refresh calendar
        $this->dispatch('refresh-calendar', events: $this->getEvents());
    }

    /**
     * Check if the selected time slot conflicts with existing bookings
     * for the same service provider
     */
    private function hasTimeConflict()
    {
        // Calculate the duration based on number of services (30 mins per service)
        $serviceCount = count($this->selected_services);
        $durationMinutes = $serviceCount * 30; // 30 minutes per service

        // Parse the requested start time
        $requestedStart = Carbon::parse($this->service_date . ' ' . $this->service_time);
        $requestedEnd = $requestedStart->copy()->addMinutes($durationMinutes);

        // Get all bookings for this service provider on the same date
        $existingBookings = Booking::where('service_provider_id', $this->service_provider_id)
            ->where('service_date', $this->service_date)
            ->whereIn('status', ['pending', 'confirmed']) // Only check active bookings
            ->with('services')
            ->get();

        // Check each existing booking for time overlap
        foreach ($existingBookings as $booking) {
            $existingStart = Carbon::parse($booking->service_date . ' ' . $booking->service_time);

            // Calculate existing booking duration based on services
            $existingServiceCount = $booking->services->count();
            $existingDurationMinutes = $existingServiceCount > 0 ? $existingServiceCount * 30 : 60; // Default 1 hour if no services
            $existingEnd = $existingStart->copy()->addMinutes($existingDurationMinutes);

            // Check for overlap
            // Two time ranges overlap if: (StartA < EndB) AND (EndA > StartB)
            if ($requestedStart->lt($existingEnd) && $requestedEnd->gt($existingStart)) {
                return true; // Conflict found
            }
        }

        return false; // No conflict
    }

    public function updatedFilterServiceProviderId()
    {
        $this->dispatch('refresh-calendar', events: $this->getEvents());
    }

    public function updatedPhoneNumber($value)
    {
        $this->searchingCustomer = true;
        $this->customerFound = false;
        
        if (!empty($value)) {
            $customer = Customer::where('phone', $value)->first();

            if ($customer) {
                $this->customer_name = $customer->name;
                $this->address = $customer->address ?? '';
                $this->customerFound = true;
            } else {
                $this->customer_name = '';
                $this->address = '';
            }
        }
        
        $this->searchingCustomer = false;
    }

    public function updateBookingStatus($bookingId, $status)
    {
        $booking = Booking::find($bookingId);
        if ($booking) {
            $booking->update(['status' => $status]);
            $this->dispatch('refresh-calendar', events: $this->getEvents());
            $this->dispatch('booking-status-updated');
        }
    }
}
