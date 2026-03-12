<?php

namespace Database\Seeders;

use App\Models\System\Division;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Division::updateOrCreate([
            'code' => 'PCMD',
            'name' => 'Policy Coordination and Monitoring Division'
        ]);
        Division::updateOrCreate([
            'code' => 'FAD',
            'name' => 'Finance and Administrative Division'
        ]);
        Division::updateOrCreate([
            'code' => 'ETDD',
            'name' => 'Emerging Technology Development Division'
        ]);
        Division::updateOrCreate([
            'code' => 'EUSTDD',
            'name' => 'Energy & Utilities Systems Technology Division'
        ]);
        Division::updateOrCreate([
            'code' => 'ITDD',
            'name' => 'Industrial Technology Development Division'
        ]);
        Division::updateOrCreate([
            'code' => 'RITTD',
            'name' => 'Research Information & Technology Transfer Division'
        ]);
        Division::updateOrCreate([
            'code' => 'HRIDD',
            'name' => 'Human Resource and Institution Development Division'
        ]);
        Division::updateOrCreate([
            'code' => 'IGD',
            'name' => 'Information Group'
        ]);
        Division::updateOrCreate([
            'code' => 'ODED',
            'name' => 'Office of the Deputy Executive Director'
        ]);
        Division::updateOrCreate([
            'code' => 'OED',
            'name' => 'Office of the Executive Director'
        ]);
    }
}
