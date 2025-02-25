<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        Storage::deleteDirectory('sunat');

        // User::factory(10)->create();
        $this->call([
            UserSeeder::class,
            DepartmentSeeder::class,
            ProvinceSeeder::class,
            DistrictSeeder::class,
            

            DocumentSeeder::class,
            OperationSeeder::class,
            IdentitySeeder::class,

            CurrencySeeder::class,

            UnitSeeder::class,

            AffectationSeeder::class,

            DetractionSeeder::class,

            PerceptionSeeder::class,

            PaymentMethodSeeder::class,

            TypeCreditNoteSeeder::class,
            TypeDebitNoteSeeder::class,

            ReasonTransferSeeder::class,

            CompanySeeder::class,
            ProductSeeder::class,

            ClientSeeder::class,

            CountrySeeder::class,

            PhoneCodeSeeder::class,
        ]);
    }
}
