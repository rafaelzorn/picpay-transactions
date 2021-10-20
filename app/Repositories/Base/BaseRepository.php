<?php

namespace App\Repositories\Base;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\Base\Contracts\BaseRepositoryInterface;

abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param int $id
     *
     * @return Model
     */
    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * @param array $attributes
     *
     * @return Model
     */
    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    /**
     * @param string $attribute
     * @param mixed $value
     *
     * @return Model
     */
    public function findByAttribute(string $attribute, mixed $value): ?Model
    {
        return $this->model->where($attribute, $value)->first();
    }

    /**
     * @param Model $model
     * @param array $attributes
     *
     * @return bool
     */
    public function update(Model $model, array $attributes): bool
    {
        return $model->update($attributes);
    }

    /**
     * @param array $attributes
     * @param array $values
     *
     * @return Model
     */
    public function updateOrCreate(array $attributes, array $values = []): Model
    {
        return $this->model->updateOrCreate($attributes, $values);
    }
}
