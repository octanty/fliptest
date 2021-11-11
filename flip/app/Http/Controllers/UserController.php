<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Rules\Password;

class UserController extends Controller
{
    public function create_user(Request $request)
    {
    try{
        $validator = Validator::make($request->all(),[
            'name' => ['required', 'string'],
            'username' => ['required', 'string', 'unique:users'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string']
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $validator->errors()->all()
            ], 'Username Already Exist', 409);
        }

        
        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'username' => $request->username,
            'balance' => 0,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);


        $tokenResult = $user->createToken('authToken')->plainTextToken;

        return ResponseFormatter::success([
            'access_token' => $tokenResult,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 'User Registered');
        } catch (Exception $error){
            return ResponseFormatter::error([
            'message' => 'Something went wrong',
            'error' => $error
            ], 'Bad Request', 400);
        }
    }

    public function balance_read(Request $request){
        $balance = User::select('balance')
                    ->where('username', $request->user()->username)
                    ->first();
        
        return $balance;
    }

    public function balance_topup(Request $request){
        if($request->amount > 0 && $request->amount <10000000){
            User::where('username', $request->user()->username)
            ->increment('balance',$request->amount);
            
            return ResponseFormatter::success(
                '',
                'Top Up Successful',
                204
            );
        }
        else{
            return ResponseFormatter::error(
                null,
                'Invalid Top Amount',
                400
            );           
        }
    }

    public function transfer(Request $request){
        $to_username = User::select('username')
        ->where('username', $request->to_username)
        ->first();

        if($to_username){

            $user = User::where('username', $request->user()->username)
            ->first();

            if($user->balance < $request->amount){
                return ResponseFormatter::error(
                    null,
                    'Insufficient balance',
                    400
                );        
            }
            else{
                User::where('username', $request->user()->username)
                ->decrement('balance',$request->amount);
                
                User::where('username', $request->to_username)
                ->increment('balance',$request->amount);

                Transaction::create([
                    'username' => $request->to_username,
                    'amount' => $request->amount,
                    'type' => 'credits',
                ]);

                Transaction::create([
                    'username' => $request->user()->username,
                    'amount' => -($request->amount),
                    'type' => 'debits',
                ]);
    
    
                return ResponseFormatter::success(
                '',
                'Transfer success',
                204
                );

            }
        }
        else{
            return ResponseFormatter::error(
                null,
                'Destination user not found',
                404
            );       
        }

    }


}


