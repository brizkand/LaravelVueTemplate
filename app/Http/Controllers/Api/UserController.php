<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthUserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){
        $users = User::orderBy("id","desc")->paginate(10);
        return response()->json($users);
    }

    public function show(Request $request){
        $user = $request->user();

        return AuthUserResource::make($user);
    }
}
