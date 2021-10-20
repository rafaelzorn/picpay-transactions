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

    /**
     * @param string $attribute
     * @param mixed $value
     *
     * @return Model
     */
    public function findByAttribute(string $attribute, mixed $value): ?Model;

    /**
     * @param array $attributes
     * @param array $values
     *
     * @return Model
     */
    public function updateOrCreate(array $attributes, array $values = []): Model;
}
