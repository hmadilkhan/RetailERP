<?php



use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SaloonServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            ['name' => 'Haircut', 'description' => 'Standard haircut service', 'price' => 25.00],
            ['name' => 'Shave', 'description' => 'Clean shave or beard trim', 'price' => 15.00],
            ['name' => 'Massage', 'description' => 'Full body massage (60 mins)', 'price' => 80.00],
            ['name' => 'Hair Coloring', 'description' => 'Full hair coloring', 'price' => 120.00],
        ];

        foreach ($services as $service) {
            \App\Models\SaloonService::firstOrCreate(['name' => $service['name']], $service);
        }
    }
}
