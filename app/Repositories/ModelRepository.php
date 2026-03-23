<?php

namespace App\Repositories;

use App\DTOs\BaseFieldDTO;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @template T of Model
 */
abstract class ModelRepository
{
    /** @var T */
    protected Model $model;

    /**
     * @param  T  $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param array<string> $with
     * @param array<string, string> $orderBy
     * @return Collection<int, T>
     */
    public function findByFields(BaseFieldDTO $data, array $with = [], array $orderBy = []): Collection
    {
        /** @var Collection<int, T> $collection */
        $collection = $this->findByFieldQuery($data, $with, $orderBy)->get();

        return $collection;
    }

    /**
     * @param array<string> $with
     * @param array<string, string> $orderBy
     * @return T|null
     */
    public function findFirstByFields(BaseFieldDTO $data, array $with = [], array $orderBy = []): ?Model
    {
        return $this->findByFieldQuery($data, $with, $orderBy)->first();
    }

    /**
     * @param array<string> $with
     * @param array<string, string> $orderBy
     * @return Builder<T>
     */
    private function findByFieldQuery(BaseFieldDTO $data, array $with = [], array $orderBy = []): Builder
    {
        /** @var Builder<T> $query */
        $query = $this->model::query();

        foreach ($data->toArray() as $field => $value) {
            $query->where($field, $value);
        }

        foreach ($with as $relation) {
            $query->with($relation);
        }

        foreach ($orderBy as $field => $direction) {
            $query->orderBy($field, $direction);
        }

        return $query;
    }
}
