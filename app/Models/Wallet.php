<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'balance'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function credit($amount, $description = null)
    {
        return \DB::transaction(function () use ($amount, $description) {
            $this->update(['balance' => $this->balance + $amount]);

            $this->transactions()->create([
                'type' => 'credit',
                'amount' => $amount,
                'description' => $description,
            ]);

            return $this;
        });
    }

    public function debit($amount, $description = null)
    {
        return DB::transaction(function () use ($amount, $description) {
            // Lock the row for the wallet to prevent race conditions
            $wallet = $this->lockForUpdate()->find($this->id);
    
            if ($wallet->balance < $amount) {
                throw new \Exception('Insufficient balance');
            }
    
            // Deduct the amount
            $wallet->balance -= $amount;
            $wallet->save();
    
            // Record the transaction
            $wallet->transactions()->create([
                'amount' => $amount,
                'type' => 'debit',
                'description' => $description,
            ]);
        });
    }
    
}