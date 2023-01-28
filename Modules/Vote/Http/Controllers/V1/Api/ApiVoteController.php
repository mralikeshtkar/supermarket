<?php

namespace Modules\Vote\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Modules\Vote\Entities\Vote;
use Modules\Vote\Transformers\Api\Admin\ApiAdminVoteResource;
use Modules\Vote\Transformers\Api\ApiVoteResource;

class ApiVoteController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $votes = Vote::init()->selectColumns(['id', 'title', 'description', 'created_at'])
            ->withScopes(['itemUsersCount'])
            ->withRelationships(['items' => function ($q) {
                $q->select(['id', 'vote_id', 'title'])->withCount('users');
            }])->paginateAdmin($request);
        $resource = ApiPaginationResource::make($votes)->additional(['itemsResource' => ApiVoteResource::class]);
        return ApiResponse::message(trans("Received information successfully"))
            ->addData('votes', $resource)
            ->send();
    }
}
