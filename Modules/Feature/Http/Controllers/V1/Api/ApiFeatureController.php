<?php

namespace Modules\Feature\Http\Controllers\V1\Api;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Feature\Entities\Feature;
use Modules\Feature\Entities\FeatureOption;
use Modules\Feature\Rules\FeatureableRule;
use Modules\Feature\Transformers\V1\Api\Admin\AdminFeatureResource;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiFeatureController extends Controller
{
    /**
     * Store a feature with request data.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
//        ApiResponse::authorize($request->user()->can('store', Feature::class));
        $request->merge(['featureable' => [
            'id' => optional($request->featureable)['id'],
            'type' => Relation::getMorphedModel(strtolower(optional($request->featureable)['type'])),
        ]]);
        ApiResponse::init($request->all(), [
            'featureable' => ['bail', 'required', 'array:id,type', new FeatureableRule()],
            'parent_id' => ['bail', 'nullable', Rule::exists(Feature::class, 'id')->whereNull('parent_id')],
            'title' => ['bail', 'required', 'string'],
            'has_option' => ['bail', 'nullable', 'boolean'],
            'is_filter' => ['bail', 'nullable', 'boolean'],
        ], [], trans('feature::validation.attributes'))->validate();
        try {
            $featureable = $request->featureable['type']::init()->findByColumnOrFail($request->featureable['id']);
            $feature = $featureable->storeComment($request);
            return ApiResponse::message(trans('feature::messages.feature_was_created'))
                ->addData('feature', $feature)
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('feature::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * Update a feature.
     *
     * @param Request $request
     * @param $feature
     * @return JsonResponse
     */
    public function update(Request $request, $feature)
    {
//        ApiResponse::authorize($request->user()->can('update', Feature::class));
        ApiResponse::init($request->all(), [
            'parent_id' => ['bail', 'nullable', 'exists:' . Feature::class . ',id'],
            'title' => ['bail', 'required', 'string'],
            'has_option' => ['bail', 'nullable', 'boolean'],
            'is_filter' => ['bail', 'nullable', 'boolean'],
        ], [], trans('feature::validation.attributes'))->validate();
        $feature = Feature::init()->findByColumnOrFail($feature);
        try {
            $feature = Feature::init()->updateFeature($feature, $request);
            return ApiResponse::message(trans('feature::messages.feature_was_updated'))
                ->addData('feature', $feature)
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('feature::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * Show a feature.
     *
     * @param Request $request
     * @param $feature
     * @return JsonResponse
     */
    public function show(Request $request, $feature)
    {
        ApiResponse::authorize($request->user()->can('show', Feature::class));
        try {
            $feature = Feature::init()->findByColumnOrFail($feature);
            return ApiResponse::message(trans('feature::messages.received_information_successfully'))
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

    /**
     * @param Request $request
     * @param $feature
     * @return JsonResponse
     */
    public function destroy(Request $request, $feature)
    {
        $feature = Feature::init()->findByColumnOrFail($feature);
        $feature->destroyItem();
        return ApiResponse::message(trans('feature::messages.feature_was_deleted'))->send();
    }

    /**
     * Show feature's options.
     *
     * @param Request $request
     * @param $feature
     * @return JsonResponse
     */
    public function options(Request $request, $feature)
    {
        ApiResponse::authorize($request->user()->can('manage', Feature::class));
        try {
            $feature = Feature::init()->findByColumnOrFail($feature);
            $feature->load('options');
            return ApiResponse::message(trans('feature::messages.received_information_successfully'))
                ->addData('feature', new AdminFeatureResource($feature))
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendMessage(trans('feature::messages.feature_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('feature::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * Store an option for feature.
     *
     * @param Request $request
     * @param $feature
     * @return JsonResponse
     */
    public function storeOptions(Request $request, $feature)
    {
//        ApiResponse::authorize($request->user()->can('manage', Feature::class));
        ApiResponse::init($request->all(), [
            'option_value' => ['required', 'string'],
        ])->validate();
        try {
            $feature = Feature::init()->findHasOptionOrFail($feature);
            $option = $feature->storeOption($request);
            return ApiResponse::message(trans('feature::messages.feature_option_was_created'))
                ->addData('option', $option)
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendMessage(trans('feature::messages.feature_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('feature::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param Request $request
     * @param $feature
     * @param $option
     * @return JsonResponse
     */
    public function destroyOptions(Request $request, $feature, $option)
    {
        $option = FeatureOption::init()->findOrFailByIdAndFeatureId($option, $feature);
        $option->destroyItem();
        return ApiResponse::message(trans('feature::messages.attribute_option_was_deleted'))->send();
    }
}
