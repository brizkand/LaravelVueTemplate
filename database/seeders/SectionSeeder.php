<?php

namespace Database\Seeders;

use App\Models\System\Division;
use App\Models\System\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ETDD
        Section::updateOrCreate(
            ['code' => 'AdvancedMaterials', 'division_id' => Division::ETDD],
            ['name' => 'Advanced Materials']
        );
        Section::updateOrCreate(
            ['code' => 'ICTandAICreative', 'division_id' => Division::ETDD],
            ['name' => 'ICT and AI Creative']
        );
        Section::updateOrCreate(
            ['code' => 'SpaceTechApp', 'division_id' => Division::ETDD],
            ['name' => 'Space Tech App']
        );

        // EUSTDD
        Section::updateOrCreate(
            ['code' => 'ConstructionWater', 'division_id' => Division::EUSTDD],
            ['name' => 'Construction Water']
        );
        Section::updateOrCreate(
            ['code' => 'DisasterManagement', 'division_id' => Division::EUSTDD],
            ['name' => 'Disaster Management']
        );
        Section::updateOrCreate(
            ['code' => 'Energy', 'division_id' => Division::EUSTDD],
            ['name' => 'Energy']
        );
        Section::updateOrCreate(
            ['code' => 'Transport', 'division_id' => Division::EUSTDD],
            ['name' => 'Transport']
        );

        // FAD
        Section::updateOrCreate(
            ['code' => 'Accounting', 'division_id' => Division::FAD],
            ['name' => 'Accounting']
        );
        Section::updateOrCreate(
            ['code' => 'Budget', 'division_id' => Division::FAD],
            ['name' => 'Budget']
        );
        Section::updateOrCreate(
            ['code' => 'Cash', 'division_id' => Division::FAD],
            ['name' => 'Cash']
        );
        Section::updateOrCreate(
            ['code' => 'Motorpool', 'division_id' => Division::FAD],
            ['name' => 'Motorpool']
        );
        Section::updateOrCreate(
            ['code' => 'Personnel', 'division_id' => Division::FAD],
            ['name' => 'Personnel']
        );
        Section::updateOrCreate(
            ['code' => 'Procurement', 'division_id' => Division::FAD],
            ['name' => 'Procurement']
        );
        Section::updateOrCreate(
            ['code' => 'Property', 'division_id' => Division::FAD],
            ['name' => 'Property']
        );
        Section::updateOrCreate(
            ['code' => 'Records', 'division_id' => Division::FAD],
            ['name' => 'Records']
        );

        // HRIDD
        Section::updateOrCreate(
            ['code' => 'BSPandOtherts', 'division_id' => Division::HRIDD],
            ['name' => 'BSP and Others']
        );
        Section::updateOrCreate(
            ['code' => 'HRDPandOthers', 'division_id' => Division::HRIDD],
            ['name' => 'HRDP and Others']
        );

        // IG
        Section::updateOrCreate(
            ['code' => 'IG', 'division_id' => Division::IGD],
            ['name' => 'Information Group']
        );

        // ITDD
        Section::updateOrCreate(
            ['code' => 'CreativeIndustry', 'division_id' => Division::ITDD],
            ['name' => 'Creative Industry']
        );
        Section::updateOrCreate(
            ['code' => 'Environment', 'division_id' => Division::ITDD],
            ['name' => 'Environment']
        );
        Section::updateOrCreate(
            ['code' => 'Food', 'division_id' => Division::ITDD],
            ['name' => 'Food']
        );
        Section::updateOrCreate(
            ['code' => 'MetalsSector', 'division_id' => Division::ITDD],
            ['name' => 'Metals Sector']
        );
        Section::updateOrCreate(
            ['code' => 'Mining', 'division_id' => Division::ITDD],
            ['name' => 'Mining']
        );
        Section::updateOrCreate(
            ['code' => 'Process', 'division_id' => Division::ITDD],
            ['name' => 'Process']
        );

        // PCMD
        Section::updateOrCreate(
            ['code' => 'ITMU', 'division_id' => Division::PCMD],
            ['name' => 'ITMU']
        );
        Section::updateOrCreate(
            ['code' => 'Planning', 'division_id' => Division::PCMD],
            ['name' => 'Planning']
        );
        Section::updateOrCreate(
            ['code' => 'Policy', 'division_id' => Division::PCMD],
            ['name' => 'Policy']
        );
        Section::updateOrCreate(
            ['code' => 'TQM', 'division_id' => Division::PCMD],
            ['name' => 'TQM']
        );

        // RITDD
        Section::updateOrCreate(
            ['code' => 'FastracAndStartup', 'division_id' => Division::RITDD],
            ['name' => 'Fastrac And Startup']
        );
        Section::updateOrCreate(
            ['code' => 'TBIAndHeirit', 'division_id' => Division::RITDD],
            ['name' => 'TBIAndHeirit']
        );

        // HRIDD
        Section::updateOrCreate(
            ['code' => 'Direct', 'division_id' => Division::HRIDD],
            ['name' => 'Direct']
        );
    }
}
