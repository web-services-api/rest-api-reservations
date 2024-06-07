<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CheckAdminRole
{
    public function handle(Request $request, Closure $next)
    {  
        if (!$request->hasHeader('Authorization')) {
            abort(401, 'Unauthorized');
        }


        $token = str_replace('Bearer ', '', $request->header('Authorization'));
        $exist = DB::table('tokens')->where('access_token', $token)->orderBy('created_at', 'desc')->first();

        if(!$exist){
            abort(404, 'Token not found');
        }
            
        $user = DB::table('users')->where('id', $exist->user_id)->first();
        if (!$user) {
            abort(404, 'User not found');
        }

        if($user->role_id != DB::table('roles')->where('name', 'ROLE_ADMIN')->first()->id && $user->role_id != DB::table('roles')->where('name', 'ROLE_OWNER')->first()->id){
            // abort(403, 'Unauthorized, you are not admin');
            return response()->json(['message' => 'Unauthorized, Access granted to admin.'], 403);
        }

        return $next($request);
    }
}

