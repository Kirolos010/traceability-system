<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Fresh Meat Suppliers Inc.',
                'code' => 'SUP-001',
                'contact_person' => 'John Smith',
                'email' => 'john@freshmeat.com',
                'phone' => '+1-555-0101',
                'address' => '123 Farm Road, Texas, USA',
                'is_active' => true,
            ],
            [
                'name' => 'Quality Pharmaceuticals Ltd.',
                'code' => 'SUP-002',
                'contact_person' => 'Sarah Johnson',
                'email' => 'sarah@qualitypharma.com',
                'phone' => '+1-555-0102',
                'address' => '456 Medical Street, New York, USA',
                'is_active' => true,
            ],
            [
                'name' => 'Global Retail Supplies',
                'code' => 'SUP-003',
                'contact_person' => 'Mike Brown',
                'email' => 'mike@globalretail.com',
                'phone' => '+1-555-0103',
                'address' => '789 Commerce Blvd, California, USA',
                'is_active' => true,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
