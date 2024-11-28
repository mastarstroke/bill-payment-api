<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\AirtimePurchase;
use Illuminate\Support\Facades\DB;

class BillPaymentController extends Controller
{
    public function purchaseAirtime(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'phone_number' => 'required|numeric|digits:10',
        ]);
    
        $wallet = auth()->user()->wallet;
    
        try {
            $wallet->debit($validated['amount'], 'Airtime purchase');

            // Log the purchase in the AirtimePurchase table
            AirtimePurchase::create([
                'user_id' => auth()->id(),
                'phone_number' => $validated['phone_number'],
                'amount' => $validated['amount'],
            ]);
    
            return response()->json(['message' => 'Airtime purchased successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    



}
