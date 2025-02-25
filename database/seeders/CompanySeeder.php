<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company = Company::create([
            'ruc' => '20609278235',
            'razonSocial' => 'Coders Free SAC',
            'nombreComercial' => 'Coders Free',
            'direccion' => 'Psj. San Nicolas 126',
            'ubigeo' => '150113',
        ]);

        $company->users()->attach(1);

        $branch = $company->branches()->create([
            'name' => 'Principal',
            'code' => '0000',
            'ubigeo' => '150113',
            'address' => 'Psj. San Nicolas 126',
        ]);

        $branch->documents()->attach("01", [
            'serie' => 'F001',
            'correlativo' => '0001',
            'company_id' => $company->id,
        ]);
        $branch->documents()->attach("03", [
            'serie' => 'B001',
            'correlativo' => '0001',
            'company_id' => $company->id,
        ]);
        $branch->documents()->attach("07", [
            'serie' => 'FC01',
            'correlativo' => '0001',
            'company_id' => $company->id,
        ]);
        $branch->documents()->attach("07", [
            'serie' => 'BC01',
            'correlativo' => '0001',
            'company_id' => $company->id,
        ]);
        $branch->documents()->attach("08", [
            'serie' => 'FD01',
            'correlativo' => '0001',
            'company_id' => $company->id,
        ]);
        $branch->documents()->attach("08", [
            'serie' => 'BD01',
            'correlativo' => '0001',
            'company_id' => $company->id,
        ]);
        $branch->documents()->attach("09", [
            'serie' => 'T001',
            'correlativo' => '0001',
            'company_id' => $company->id,
        ]);

        $branch->users()->attach(1, [
            'company_id' => $company->id,
        ]);
    }
}
