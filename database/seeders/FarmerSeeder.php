<?php

namespace Database\Seeders;

use App\Models\Farmer;
use Illuminate\Database\Seeder;

class FarmerSeeder extends Seeder
{
    public function run(): void
    {
        $farmers = [
            [
                'identifier'        => 'CI-2024-001',
                'firstname'         => 'Konan',
                'lastname'          => 'Kouassi',
                'phone_number'      => '+22507010001',
                'credit_limit_fcfa' => 100000,
            ],
            [
                'identifier'        => 'CI-2024-002',
                'firstname'         => 'Adjoua',
                'lastname'          => 'Bamba',
                'phone_number'      => '+22507010002',
                'credit_limit_fcfa' => 150000,
            ],
            [
                'identifier'        => 'CI-2024-003',
                'firstname'         => 'Koffi',
                'lastname'          => 'Assouman',
                'phone_number'      => '+22507010003',
                'credit_limit_fcfa' => 50000,
            ],
            [
                'identifier'        => 'CI-2024-004',
                'firstname'         => 'Amenan',
                'lastname'          => 'Coulibaly',
                'phone_number'      => '+22507010004',
                'credit_limit_fcfa' => 200000,
            ],
            [
                'identifier'        => 'CI-2024-005',
                'firstname'         => 'Yao',
                'lastname'          => "N'Goran",
                'phone_number'      => '+22507010005',
                'credit_limit_fcfa' => 500000,
            ],
        ];

        foreach ($farmers as $data) {
            Farmer::create($data);
        }
    }
}
