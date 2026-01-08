<?php



use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customer = \App\Models\Customer::first();
        $provider = \App\Models\ServiceProvider::first();
        $services = \App\Models\SaloonService::all();

        if ($customer && $provider && $services->count() > 0) {
            // Create a booking for today
            $booking = \App\Models\Booking::create([
                'customer_id' => $customer->id,
                'service_provider_id' => $provider->id,
                'service_date' => now()->format('Y-m-d'),
                'service_time' => '10:00:00',
                'status' => 'confirmed'
            ]);
            $booking->services()->attach($services->random(2));

            // Create a booking for tomorrow
            $booking2 = \App\Models\Booking::create([
                'customer_id' => $customer->id,
                'service_provider_id' => $provider->id,
                'service_date' => now()->addDay()->format('Y-m-d'),
                'service_time' => '14:00:00',
                'status' => 'pending'
            ]);
            $booking2->services()->attach($services->random(1));
        }
    }
}
