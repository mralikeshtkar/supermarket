<?php

namespace Modules\Core\Traits;

trait EloquentHelper
{



    /**
     * @param $id
     * @return mixed
     */
    public function findOrFailByIdColumn($id): mixed
    {
        return self::query()->findOrFail($id);
    }

    /**
     * @return mixed
     */
    public function destroyItem(): mixed
    {
        return $this->delete();
    }

    /**
     * @param $relations
     * @return $this
     */
    public function withRelations($relations): static
    {
        self::query()->with($relations);
        return $this;
    }

    /**
     * @param $columns
     * @return $this
     */
    public function selectColumns($columns): static
    {
        self::query()->select($columns);
        return $this;
    }
}
