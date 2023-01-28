<?php

namespace Modules\Vote\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Vote\Entities\Vote;
use Modules\Vote\Transformers\Api\Admin\ApiAdminVoteResource;

class ApiVoteController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $votes = Vote::init()->selectColumns(['id', 'title', 'description','status', 'created_at'])
            ->withScopes(['itemUsersCount','active','selectedItemId'])
            ->withRelationships(['items' => function ($q) {
                
            }])->paginateAdmin($request);
        return ApiResponse::message(trans("Received information successfully"))
            ->addData('vote', ApiAdminVoteResource::collection($votes))
            ->send();
    }
}
