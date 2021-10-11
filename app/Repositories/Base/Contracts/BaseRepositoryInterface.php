<?php

namespace App\Repositories\Base\Contracts;

use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    /**
     * @param int $id
     *
     * @return Model
     */
    public function find(int $id): ?Model;

    /**
     * @param array $attributes
     *
     * @return Model
     */
    public function create(array $attributes): Model;
}
