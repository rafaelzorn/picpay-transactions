<?php

use App\Models\User;
use App\Models\Wallet;

class UserHelper
{
    /**
     * @param string $type
     *
     * @return User $user
     */
    public static function createUserWithWallet(string $type, $balance): User
    {
        $user = User::factory()->type($type)->create();

        Wallet::factory()->userId($user->id)->balance($balance)->create();

        return $user;
    }
}
