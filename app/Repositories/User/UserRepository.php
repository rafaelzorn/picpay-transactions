<?php

namespace App\Repositories\User;

use App\Repositories\Base\BaseRepository;
use App\Repositories\User\Contracts\UserRepositoryInterface;
use App\Models\User;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * @param User $user
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->model = $user;
    }
}
