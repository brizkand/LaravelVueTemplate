<?php

namespace Database\Seeders;

use App\Models\Integration\HRMIS\v9\Position as HRMISPosition;
use App\Models\System\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $old_positions = HRMISPosition::all();

        $positions = $old_positions->map(function ($item) {
            return [
            'code' => $item->positionCode,
            'name' => $item->positionDesc
            ];
        })->toArray();

        foreach ($positions as $position) {
            Position::updateOrCreate(
            ['code' => $position['code']],
            ['name' => $position['name']]
            );
        }
    }
}
