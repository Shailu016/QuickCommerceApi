<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Notifications\sendToken;
use DB;
use Spatie\Permission\Models\Permission;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'firstName'       =>    'required|string|max:255',
                'lastName'       =>    'required|string|max:255',
                'email'      =>    'required|string|email|max:255|unique:users',
                'password'   =>    'required|string|confirmed|min:6'
              
            ]);
            
            $user = User::create([
                'firstName'       =>    $validatedData['firstName'],
                'lastName'       =>    $validatedData['lastName'],
                'email'      =>    $validatedData['email'],
                'password'   =>    Hash::make($validatedData['password'])
                
            ]);
            
            $token = $user->createToken('auth_token')->plainTextToken;
            
            // if user site is not empty then make him admin
    
            // if ($user->site != null) {
            //     //  $role = Role::create(['name' => 'admin']);
            //     $permission = Permission::where('name', 'admin')->first();
            //     if (! $permission) {
            //         $permission = Permission::create(['name' => 'admin']);
            //     }
               
            //     $user->givePermissionTo('admin');
            //     $user->update(['is_admin' => 1]);
            //     //  $user->assignRole('admin');
            // }
    
            return response()->json([
                'message' => "User registered successfully",
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'message' => "User logged in successfully",
        ]);
    }

    public function profile(Request $request)
    {
        return Auth::user();
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        
        return response()->json(['message' => 'User successfully signed out']);
    }


    public function forgetPassword(Request $request)
    {
        $validatedData = $request->validate(['email' => 'required|string|email|max:255']);
        $user = User::where('email', $validatedData['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token
        ]);
        // $user->notify(new sendToken($token));
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',

        ]);
    }

    public function reset(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'email' => 'required|string|email|max:255',
                'password' => 'required|string|confirmed|min:6',
            ]);
    
            $token = $request->bearerToken();
    
            $tokenData = DB::table('password_resets')
            ->where('token', $token)->first();
    
            $user = User::where('email', $tokenData->email)->firstOrFail();
    
            $user->update([
                'password' => Hash::make($validatedData['password']),
                'token' => $token,
            ]);
    
            return response()->json([
                'message' => 'Password updated successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function userProfile(Request $request)
    {
        try {
            $user = User::where('id', Auth::user()->id)->firstOrFail();

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            return response()->json([
                'message' => 'User updated successfully',
                'data' =>   $user
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }
    // //check email is register or not
    // public function checkEmail(Request $request)
    // {
    //     $email = $request->email;
    //     $user = User::where('email', $email)->first();
    //     if (!$user) {
    //         return response()->json([
    //             "is_email_taken" => 0,            ]);
    //     } else {
    //         return response()->json([
    //             "is_email_taken" => 1,
    //         ]);
    //     }
    // }
}
