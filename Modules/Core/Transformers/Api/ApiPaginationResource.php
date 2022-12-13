<?php

namespace Modules\Core\Transformers\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

class ApiPaginationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'per_page' => $this->resource->perPage(),
            'current_page' => $this->resource->currentPage(),
            'last_page' => $this->resource->lastPage(),
            'total' => $this->resource->total(),
            'links' => $this->resource->linkCollection(),
            'onFirstPage' => $this->resource->onFirstPage(),
            'data'=>$this->additional['itemsResource']::collection($this->resource->items()),
        ];
    }
}
