<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HRMIS\LoginRequest;
use App\Models\System\Position;
use App\Models\Integration\HRMIS\v9\Employee\Account as HRMISAccount;
use App\Models\System\Division;
use App\Models\System\Profile;
use App\Models\System\Section;
use App\Models\System\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $md5_password = md5($request->password);

        $hrmisAccount = HRMISAccount::with('profile', 'profile.position')
        ->where('userName', $request->username)
        ->where('userPassword', $md5_password)
        ->first();


        if ($hrmisAccount) {
            $profile = $hrmisAccount->profile;
            $position = $profile->position;

            $emp_number = (int) $profile->empNumber;

            $division = Division::ofCode($position->group2)->first();

            $position_type = Position::ofCode($position->positionCode)->first();

            if ($position->group2 == '') {
                $division = Division::ofCode($position->group1)
                ->first();
            } else {
                $division = Division::ofCode($position->group2)
                ->first();
            }

            $section = Section::ofCode($position->group3)->first();

            // Save/Update User to Portal's Database
            $user = User::updateOrCreate(
                [
                    'employee_number' => $emp_number,
                ],
                [
                    'email' => $profile->email,
                    'username' => $hrmisAccount->userName,
                    'password' => Hash::make($hrmisAccount->userPassword),
                ]
            );

            Profile::updateOrCreate(
                [
                    'employee_number' => $emp_number,
                ],
                [
                    'user_id' => $user->id,
                    'division_id' => $division->id ?? null,
                    'section_id' => $section->id ?? null,
                    'position_id' => $position_type->id ?? null,
                    'last_name' => Str::of($profile->surname)->squish()->trim(),
                    'first_name' => Str::of($profile->firstname)->squish()->trim(),
                    'middle_name' => Str::of($profile->middlename)->squish()->trim()
                ]
            );

            $token = $user->createToken(name: config('sanctum.token_name'))->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ],200);
        }
        else {
            return response()->json([
                'errors' => [
                    'username' => ["These credentials didn't match our records."]
                ]
            ], 401);
        }
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out'], 200);
    }
}
