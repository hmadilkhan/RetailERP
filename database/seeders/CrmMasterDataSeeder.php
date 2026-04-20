<?php

namespace Database\Seeders;

use App\Models\Crm\LeadSource;
use App\Models\Crm\LeadStatus;
use App\Models\Crm\ProductType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CrmMasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedLeadSources();
        $this->seedProductTypes();
        $this->seedLeadStatuses();
    }

    private function seedLeadSources(): void
    {
        $items = [
            ['name' => 'Facebook', 'color' => '#2563eb'],
            ['name' => 'Instagram', 'color' => '#db2777'],
            ['name' => 'YouTube', 'color' => '#dc2626'],
            ['name' => 'Website', 'color' => '#0f766e'],
            ['name' => 'Google Search', 'color' => '#059669'],
            ['name' => 'Referral', 'color' => '#7c3aed'],
            ['name' => 'Walk-in', 'color' => '#ea580c'],
            ['name' => 'WhatsApp', 'color' => '#16a34a'],
            ['name' => 'Email', 'color' => '#0891b2'],
            ['name' => 'Call', 'color' => '#475569'],
        ];

        foreach ($items as $index => $item) {
            LeadSource::updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                $item + [
                    'slug' => Str::slug($item['name']),
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedProductTypes(): void
    {
        $items = [
            ['name' => 'Hardware', 'color' => '#1d4ed8'],
            ['name' => 'Software', 'color' => '#0f766e'],
            ['name' => 'Integration', 'color' => '#7c3aed'],
            ['name' => 'Service', 'color' => '#ea580c'],
            ['name' => 'Support', 'color' => '#0284c7'],
            ['name' => 'Subscription', 'color' => '#9333ea'],
            ['name' => 'POS', 'color' => '#2563eb'],
            ['name' => 'ERP', 'color' => '#1e40af'],
            ['name' => 'E-commerce', 'color' => '#0891b2'],
            ['name' => 'Custom Development', 'color' => '#475569'],
        ];

        foreach ($items as $index => $item) {
            ProductType::updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                $item + [
                    'slug' => Str::slug($item['name']),
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedLeadStatuses(): void
    {
        $items = [
            ['name' => 'New', 'color' => '#2563eb'],
            ['name' => 'Contacted', 'color' => '#0891b2'],
            ['name' => 'Follow-up', 'color' => '#7c3aed'],
            ['name' => 'Qualified', 'color' => '#0f766e'],
            ['name' => 'Proposal Sent', 'color' => '#ea580c'],
            ['name' => 'Negotiation', 'color' => '#f59e0b'],
            ['name' => 'Won', 'color' => '#16a34a'],
            ['name' => 'Lost', 'color' => '#dc2626'],
            ['name' => 'Junk', 'color' => '#64748b'],
        ];

        foreach ($items as $index => $item) {
            LeadStatus::updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                $item + [
                    'slug' => Str::slug($item['name']),
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }
    }
}
