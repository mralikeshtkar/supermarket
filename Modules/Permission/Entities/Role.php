<?php

namespace Modules\Permission\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelIdea\Helper\Modules\Permission\Entities\_IH_Role_C;
use Modules\Permission\Enums\Roles;
use Modules\User\Entities\User;
use Spatie\Permission\PermissionRegistrar;

class Role extends \Spatie\Permission\Models\Role
{
    #region Constants

    private array $selected_columns = ['*'];

    private array $with_relationships = [];

    private array $with_scopes = [];

    #endregion

    #region Methods

    /**
     * Initialize class.
     *
     * @return Role
     */
    public static function init(): Role
    {
        return new self();
    }

    public function findOrFailById($role)
    {
        return self::query()
            ->with(['users', 'permissions'])
            ->whereNot('name', Roles::SUPER_ADMIN['name_en'])
            ->findOrFail($role);
    }

    public function allRoles()
    {
        return self::query()->get();
    }

    /**
     * @return bool|null
     */
    public function deleteRole(): ?bool
    {
        return $this->delete();
    }

    /**
     * @param Request $request
     * @return array|LengthAwarePaginator|_IH_Role_C|\LaravelIdea\Helper\Spatie\Permission\Models\_IH_Role_C
     */
    public function getAdminIndexPaginate(Request $request): array|LengthAwarePaginator|_IH_Role_C|\LaravelIdea\Helper\Spatie\Permission\Models\_IH_Role_C
    {
        return self::query()
            ->with('permissions:id,name')
            ->when($request->filled('name'), function (Builder $builder) use ($request) {
                $builder->where('name', 'LIKE', '%' . $request->name . '%')
                    ->orWhere('name_fa', 'LIKE', '%' . $request->name . '%');
            })->paginate();
    }

    /**
     * Find a role with slug if exists return this else return not found error.
     *
     * @param $role
     * @return Model|Collection|Builder|array|null
     */
    public function findOrFailWithId($role): Model|Collection|Builder|array|null
    {
        return self::query()->findOrFail($role);
    }

    /**
     * Update a role with request data.
     *
     * @param Request $request
     * @return mixed
     */
    public function updateRole(Request $request): mixed
    {
        $this->update([
            'name' => $request->name,
            'name_fa' => $request->name_fa,
        ]);
        $this->syncPermissions($request->get('permissions', []));
        return $this->refresh()->load('permissions');
    }

    /**
     * Create a role
     *
     * @param Request $request
     * @return Model|Builder
     */
    public function store(Request $request): Model|Builder
    {
        $role = self::query()->create([
            'name' => $request->name,
            'name_fa' => $request->name_fa,
        ]);
        $role->syncPermissions($request->get('permissions', []));
        return $role->load('permissions');
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

    /**
     * Get users has role.
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->morphedByMany(
            User::class,
            'model',
            config('permission.table_names.model_has_roles'),
            PermissionRegistrar::$pivotRole,
            config('permission.column_names.model_morph_key')
        );
    }

    #endregion

    #region Mutators

    /**
     * @return bool
     */
    public function getIsSuperAdminAttribute(): bool
    {
        return $this->name == Roles::SUPER_ADMIN['name_en'];
    }

    #endregion
}
