<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        return \DB::transaction(function () use ($amount, $description) {
            if ($this->balance < $amount) {
                throw new \Exception('Insufficient balance.');
            }

            $this->update(['balance' => $this->balance - $amount]);

            $this->transactions()->create([
                'type' => 'debit',
                'amount' => $amount,
                'description' => $description,
            ]);

            return $this;
        });
    }
}