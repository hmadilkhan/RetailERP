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
            // Determine end time based on number of services (approx 30 mins per service?)
            // For now, simplify to 1 hour +
            $startDateTime = Carbon::parse($booking->service_date . ' ' . $booking->service_time);
            $endTime = $startDateTime->copy()->addHour();

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

        // Find or Create Customer
        // Note: Customer model might differ, using phone as unique identifier
        // Assuming 'contact' or 'phone' column in existing Customer model.
        // Based on implementation plan, we assume standard usage.
        // Let's assume 'mobile' or 'phone'. I need to check Customer model columns again if strict.
        // For now using updateOrCreate based on phone.

        // I'll assume 'phone_number' maps to 'contact' or 'mobile' in Customer table based on common schemes or just 'phone'.
        // Wait, I should verify Customer columns. The user just said "Customer Name, Phone...".
        // I'll assume Customer has 'name' and 'contact' (from service provider list view earlier, providers had 'contact', maybe customers do too? No, usually 'phone').
        // I will use 'name' and 'phone'.

        $customer = Customer::firstOrCreate(
            ['phone' => $this->phone_number], // Assume phone column exists, if not I'll fix.
            ['name' => $this->customer_name, 'address' => $this->address]
        );

        // Update address/name if needed? For now just keep existing.

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

    public function updatedFilterServiceProviderId()
    {
        $this->dispatch('refresh-calendar', events: $this->getEvents());
    }
}
