<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Wallet;

class UserObserver
{
    public function created(User $user)
    {
        Wallet::create([
            'user_id' => $user->id,
            'balance' => 0,
        ]);
    }
}
