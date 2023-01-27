<?php

namespace Modules\Feature\Http\Controllers\V1\Api;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Feature\Entities\Attribute;
use Modules\Feature\Entities\Feature;
use Modules\Feature\Rules\AttributableRule;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiAttributeController extends Controller
{
    public function store(Request $request,$feature)
    {
        ApiResponse::authorize($request->user()->can('manage', Attribute::class));
        $request->merge(['attributable' => [
            'id' => optional($request->attributable)['id'],
            'type' => Relation::getMorphedModel(strtolower(optional($request->attributable)['type'])),
        ]]);
        ApiResponse::init($request->all(), [
            'attributable' => ['bail', 'required', 'array:id,type', new AttributableRule()],
            'option_id' => ['bail', Rule::requiredIf(Feature::init()->checkHasOptionByColumn($feature))],
            'attribute_value' => ['bail', Rule::requiredIf(Feature::init()->checkDoesntHasOptionByColumn($feature))],
        ],[],trans('feature::validation.attributes'))->validate();
        try {
            $feature = Feature::init()->findByColumnOrFail($feature);
            $attributable = $request->attributable['type']::init()->findOrFail($request->attributable['id']);
            $attribute = $attributable->storeAttribute($feature,$request);
            return ApiResponse::message(trans('feature::messages.attribute_option_was_created'))
                ->addData('attributable', $attributable)
                ->addData('attribute', $attribute)
                ->addData('feature', $feature)
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendMessage(trans('feature::messages.feature_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('feature::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }
}
