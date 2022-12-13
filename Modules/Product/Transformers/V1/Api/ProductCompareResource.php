<?php

namespace Modules\Product\Transformers\V1\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductCompareResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource->map(function ($item) {
            return [
                'id' => $item['id'],
                'title' => $item['title'],
                'children' => collect($item['children'])->map(function ($child) {
                    return [
                        'id' => $child['id'],
                        'parent_id' => $child['parent_id'],
                        'title' => $child['title'],
                        'is_filter' => $child['is_filter'],
                        'has_option' => $child['has_option'],
                        'attributes' => collect($child['attributes'])
                            ->map(function ($item) use ($child) {
                                return [
                                    'attributable_id' => $item->attributable_id,
                                    'value' => $child['has_option'] ? $item->option->value : $item->value,
                                ];
                            })->groupBy("attributable_id"),
                    ];
                }),
            ];
        });
    }
}
