<?php

namespace Modules\Permission\Entities;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use LaravelIdea\Helper\Modules\Permission\Entities\_IH_Permission_C as _IH_Permission_CAlias;
use LaravelIdea\Helper\Spatie\Permission\Models\_IH_Permission_C;

class Permission extends \Spatie\Permission\Models\Permission
{
    #region Constance

    private array $selected_columns = ['*'];

    private array $with_relationships = [];

    private array $with_scopes = [];

    #endregion

    #region Methods

    /**
     * Initialize class.
     *
     * @return Permission
     */
    public static function init(): Permission
    {
        return new self();
    }

    /**
     * @return Collection
     */
    public function getAll(): Collection
    {
        return self::query()->select($this->selected_columns)
            ->scopes($this->with_scopes)
            ->with($this->with_relationships)
            ->get();
    }

    /**
     * @return array|string|Translator|Application|null
     */
    public function getTranslatedName(): array|string|Translator|Application|null
    {
        return trans('permission::permissions.' . $this->name);
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function selectColumns(array $columns): static
    {
        $this->selected_columns = $columns;
        return $this;
    }

    /**
     * @param array $relations
     * @return $this
     */
    public function withRelationships(array $relations): static
    {
        $this->with_relationships = $relations;
        return $this;
    }

    /**
     * @param array $scopes
     * @return $this
     */
    public function withScopes(array $scopes): static
    {
        $this->with_scopes = $scopes;
        return $this;
    }

    #endregion
}
