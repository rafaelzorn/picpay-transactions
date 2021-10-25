<?php

use App\Models\User;
use App\Models\Wallet;

class UserHelper
{
    /**
     * @param string $type
     * @param float $balance
     *
     * @return User $user
     */
    public static function createUserWithWallet(string $type, float $balance): User
    {
        $user = User::factory()->type($type)->create();

        Wallet::factory()->userId($user->id)->balance($balance)->create();

        return $user;
    }
}
