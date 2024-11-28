<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function balance(Request $request)
    {
        $wallet = $request->user()->wallet;

        if (!$wallet) {
            return response()->json([
                'error' => 'Wallet not found. Please contact support.',
            ], 404);
        }
    
        return response()->json([
            'balance' => $wallet->balance,
        ], 200);
    }

    public function fund(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $wallet = $request->user()->wallet;

        $wallet->credit($request->amount, 'Wallet funding');

        return response()->json([
            'message' => 'Wallet funded successfully.',
            'balance' => $wallet->balance,
        ], 200);
    }

    public function deduct(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $wallet = $request->user()->wallet;

        try {
            $wallet->debit($request->amount, 'Transaction deduction');

            return response()->json([
                'message' => 'Transaction successful.',
                'balance' => $wallet->balance,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}