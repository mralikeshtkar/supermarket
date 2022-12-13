<?php

namespace Modules\Feature\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\Request;
use Modules\Comment\Traits\HasComment;
use Modules\Feature\Entities\Attribute;
use Modules\Feature\Entities\Feature;

trait HasAttribute
{
    use HasRelationships;

    /**
     * @return MorphMany
     */
    public function attributes(): MorphMany
    {
        return $this->morphMany(Attribute::class, 'attributable');
    }

    /**
     * Initialize class.
     *
     * @return $this
     */
    public static function init(): static
    {
        return new self();
    }

    /**
     * @param $feature
     * @param $option_id
     * @param $attribute_value
     * @return Model
     */
    public function storeAttribute($feature, $option_id, $attribute_value): Model
    {
        return $this->attributes()
            ->create([
                'user_id' => auth()->id(),
                'feature_id' => $feature,
                'option_id' => $option_id,
                'value' => $attribute_value,
            ]);
    }
}
