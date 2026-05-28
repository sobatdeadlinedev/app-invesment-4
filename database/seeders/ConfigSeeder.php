<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            [
                'key' => 'app_name',
                'value' => ['value' => 'My Website'],
            ],
            [
                'key' => 'app_logo',
                'value' => ['value' => 'Website logo here'],
            ],
            [
                'key' => 'app_announcement',
                'value' => ['value' => ''],
            ],
            // Wallet TRC20
            [
                'key' => 'app_wallet_trc20',
                'value' => [
                    'name' => 'TRON Network (TRC20)',
                    'address' => ''
                ],
            ],
            // Wallet BEP20
            [
                'key' => 'app_wallet_bep20',
                'value' => [
                    'name' => 'Binance Smart Chain (BEP20)',
                    'address' => ''
                ],
            ],
            [
                'key' => 'app_email',
                'value' => ['value' => 'jameb6377@gmail.com'],
            ],
        ];

        foreach ($configs as $config) {
            Config::updateOrCreate(
                ['key' => $config['key']],
                ['value' => $config['value']]
            );
        }
    }
}
