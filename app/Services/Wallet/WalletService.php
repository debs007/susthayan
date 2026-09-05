<?php

namespace App\Services\Wallet;

use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function getOrCreateForUser(User $user): Wallet
    {
        return Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);
    }

    /**
     * Balance update and ledger entry happen inside one transaction, with
     * a locked read on the wallet row first - two credits landing at
     * genuinely the same instant (a topup confirmation and a future
     * refund, say) must never race and silently drop one of them.
     */
    public function credit(User $user, float $amount, string $description, ?Order $order = null): void
    {
        DB::transaction(function () use ($user, $amount, $description, $order) {
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first()
                ?? Wallet::create(['user_id' => $user->id, 'balance' => 0]);

            $wallet->increment('balance', $amount);

            $wallet->transactions()->create([
                'type' => 'credit',
                'amount' => $amount,
                'description' => $description,
                'order_id' => $order?->id,
            ]);
        });
    }
}
