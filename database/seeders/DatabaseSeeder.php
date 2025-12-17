<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed in order of dependencies
        $this->call([
            SupplierSeeder::class,
            ProductSeeder::class,
            LocationSeeder::class,
            BatchSeeder::class,
            ProductionSeeder::class,
            TransferSeeder::class,
            SaleSeeder::class,
        ]);
    }
}
