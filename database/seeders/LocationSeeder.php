<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Main Warehouse',
                'code' => 'WH-001',
                'type' => 'warehouse',
                'address' => '100 Storage Avenue, Industrial District',
                'is_active' => true,
            ],
            [
                'name' => 'Downtown Shop',
                'code' => 'SHOP-001',
                'type' => 'shop',
                'address' => '200 Main Street, Downtown',
                'is_active' => true,
            ],
            [
                'name' => 'Production Facility',
                'code' => 'PROD-001',
                'type' => 'production',
                'address' => '300 Factory Road, Industrial Zone',
                'is_active' => true,
            ],
            [
                'name' => 'Retail Store North',
                'code' => 'SHOP-002',
                'type' => 'shop',
                'address' => '400 North Avenue, Shopping District',
                'is_active' => true,
            ],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}
