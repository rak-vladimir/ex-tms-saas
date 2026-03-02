<?php

namespace App\Console\Commands;

use App\Models\Courier;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Str;


class SeedTenantsAndCouriers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:tenants-couriers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creating 2 tenants and 2 couriers for each';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        for ($i = 1; $i <= 2; $i++) {
            $tenant = Tenant::create([
                'name'    => "Tenant {$i}",
                'api_key' => Str::ulid()->toString(), // обязательное поле
            ]);

            $this->info("Создан tenant: {$tenant->name} (api_key: {$tenant->api_key})");

            for ($j = 1; $j <= 2; $j++) {
                $courier = Courier::create([
                    'tenant_id'    => $tenant->id,
                    'name'         => "Courier {$j} of Tenant {$i}",
                    'phone'        => "+70000000{$i}{$j}", // обязательное поле
                    'vehicle_type' => $j % 2 === 0 ? 'car' : 'bike', // обязательное поле
                    'active'       => true, // по умолчанию true
                ]);

                $this->info(" └─ Курьер: {$courier->name}, phone: {$courier->phone}, vehicle: {$courier->vehicle_type}");
            }
        }

        $this->info('Демо‑данные успешно созданы!');
    }
}
