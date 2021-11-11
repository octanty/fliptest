<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function top_transactions_per_user(Request $request){
        $balance = Transaction::select('username','amount')
                    ->where('username', $request->user()->username)
                    ->orderBy('amount','desc')->take(10)->get();
    
        return $balance;
    }   

    public function top_users(Request $request){

        $balance = Transaction::Where('type','debits')
                    ->select(['username',DB::raw("SUM(-(amount)) as transacted_value")])
                    ->groupBy('username')
                    ->orderByRaw('SUM(-(amount)) DESC')->take(10)->get();

        return $balance;
    }   
}
