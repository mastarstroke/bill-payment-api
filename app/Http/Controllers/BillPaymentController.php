<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class BillPaymentController extends Controller
{
    public function purchaseAirtime(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'phone_number' => 'required|string',
        ]);

        $user = $request->user();
        $wallet = $user->wallet;

        if (!$wallet) {
            return response()->json(['error' => 'Wallet not found.'], 404);
        }

        try {
            // Deduct the amount from the wallet
            $wallet->debit($request->amount, 'Airtime Purchase');

            // Log the transaction
            $transaction = $wallet->transactions()->create([
                'type' => 'debit',
                'amount' => $request->amount,
                'description' => 'Airtime purchase for ' . $request->phone_number,
                'metadata' => json_encode([
                    'phone_number' => $request->phone_number,
                    'service' => 'airtime',
                ]),
            ]);

            return response()->json([
                'message' => 'Airtime purchase successful.',
                'transaction' => $transaction,
                'balance' => $wallet->balance,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
