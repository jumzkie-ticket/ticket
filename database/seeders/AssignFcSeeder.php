<?php

namespace Database\Seeders;

use App\Models\AssignFc;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssignFcSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $values = [
            'FC-001',
            'FC-002',
            'FC-003',
            'FC-004',
        ];

        foreach ($values as $value) {
            AssignFc::query()->firstOrCreate(['assign_fc' => $value]);
        }
    }
}
