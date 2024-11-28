<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'balance'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function debit($amount, $description)
    {
        return $this->updateWallet($amount, 'debit', $description);
    }

    public function credit($amount, $description)
    {
        return $this->updateWallet($amount, 'credit', $description);
    }

    private function updateWallet($amount, $type, $description)
    {
        return \DB::transaction(function () use ($amount, $type, $description) {
            $newBalance = $type === 'debit' 
                ? $this->balance - $amount 
                : $this->balance + $amount;

            if ($newBalance < 0) {
                throw new \Exception("Insufficient funds");
            }

            $this->update(['balance' => $newBalance]);

            $this->transactions()->create([
                'type' => $type,
                'amount' => $amount,
                'description' => $description,
            ]);

            return $this;
        });
    }
}
