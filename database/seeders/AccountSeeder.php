<?php

namespace Database\Seeders;

use App\Models\Integration\HRMIS\v9\Employee\Profile as HRMISProfile;
use App\Models\System\Division;
use App\Models\System\Position;
use App\Models\System\Profile;
use App\Models\System\Section;
use App\Models\System\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $old_employee_profiles = HRMISProfile::all();

        // List of Employee Profiles and Accounts built from HRMIS
        $profiles = [];
        $accounts = [];

        // gather accounts first so we can upsert in one shot
        foreach ($old_employee_profiles as $old_profile) {
            $emp_number = (int) $old_profile->empNumber;

            if ($old_profile->account()->exists()) {
                $account = $old_profile->account;

                $accounts[] = [
                    'employee_number' => $emp_number,
                    'email' => Str::of($account->profile->email)->trim(),
                    'username' => $account->userName,
                    'password' => Hash::make($account->userPassword)
                ];
            }
        }

        // Upsert all accounts at once so users table is populated
        if (! empty($accounts)) {
            User::upsert($accounts, ['employee_number']);
        }

        // build users map with integer keys
        $users = User::pluck('id', 'employee_number')->mapWithKeys(fn($id, $emp) => [(int)$emp => $id])->toArray();

        // build profiles
        foreach ($old_employee_profiles as $old_profile) {
            $emp_number = (int) $old_profile->empNumber;

            if (! $old_profile->account()->exists() || ! isset($users[$emp_number])) {
                continue; // skip profiles without a user
            }

            $position_type = null;
            $division = null;
            $section = null;

            if ($old_profile->position()->exists()) {
                $position = $old_profile->position;
                $position_type = Position::ofCode($position->positionCode)->first();
                $division = $position->group2 === '' ? Division::ofCode($position->group1)->first() : Division::ofCode($position->group2)->first();
                $section = Section::ofCode($position->group3)->first();
            }

            $profiles[] = [
                'employee_number' => $emp_number,
                'user_id' => $users[$emp_number],
                'last_name' => Str::of($old_profile->surname)->squish()->trim(),
                'first_name' => Str::of($old_profile->firstname)->squish()->trim(),
                'middle_name' => Str::of($old_profile->middlename)->squish()->trim(),
                'division_id' => $division->id ?? null,
                'section_id' => $section->id ?? null,
                'position_id' => $position_type->id ?? null,
            ];
        }

        // Upsert Profiles
        Profile::upsert($profiles, ['employee_number']);
    }
}
