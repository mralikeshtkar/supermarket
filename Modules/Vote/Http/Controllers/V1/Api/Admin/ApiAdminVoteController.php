<?php

namespace Modules\Vote\Http\Controllers\V1\Api\Admin;

use App\Http\Controllers\Controller;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Modules\User\Entities\User;
use Modules\Vote\Entities\Vote;
use Modules\Vote\Enums\VoteStatus;
use Modules\Vote\Transformers\Api\Admin\ApiAdminVoteResource;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiAdminVoteController extends Controller
{
    public function index(Request $request)
    {
        $votes = Vote::init()->selectColumns(['id','title','status','created_at'])
            ->withScopes(['itemUsersCount'])
            ->paginateAdmin($request);
        $resource = ApiPaginationResource::make($votes)->additional(['itemsResource' => ApiAdminVoteResource::class]);
        return ApiResponse::message(trans("The operation was done successfully"))
            ->addData('votes', $resource)
            ->addData('statuses', collect(VoteStatus::asArray())->map(function ($item){
                return ['value'=>$item,'title'=>VoteStatus::getDescription($item)];
            })->values()->toArray())
            ->send();
    }

    /**
     * @param Request $request
     * @param $vote
     * @return JsonResponse
     */
    public function show(Request $request, $vote)
    {
        $vote = Vote::init()->selectColumns(['id', 'user_id', 'title', 'description', 'status', 'created_at'])
            ->withScopes(['itemUsersCount'])
            ->withRelationships(['user:id,name,email', 'items' => function ($q) {
                $q->select(['id', 'vote_id', 'title'])->withCount('users');
            }])->findOrFailById($vote);
        return ApiResponse::message(trans("Received information successfully"))
            ->addData('vote', new ApiAdminVoteResource($vote))
            ->send();
    }

    /**
     * @param Request $request
     * @return JsonResponse|mixed
     */
    public function store(Request $request)
    {
        ApiResponse::init($request->all(), [
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'status' => ['required', new EnumValue(VoteStatus::class)],
            'items' => ['nullable', 'array', 'min:2'],
            'items.*' => ['required', 'string'],
        ])->validate();
        try {
            return DB::transaction(function () use ($request) {
                Vote::init()->store($request);
                return ApiResponse::message(trans("The operation was done successfully"), Response::HTTP_CREATED)->send();
            });
        } catch (Throwable $e) {
            return ApiResponse::sendError(trans("Internal server error"));
        }
    }

    /**
     * @param Request $request
     * @param $vote
     * @return JsonResponse
     */
    public function update(Request $request, $vote)
    {
        /** @var Vote $vote */
        $vote = Vote::init()->findOrFailById($vote);
        ApiResponse::init($request->all(), [
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'status' => ['required', new EnumValue(VoteStatus::class)],
        ])->validate();
        $vote->updateRow($request);
        return ApiResponse::message(trans("The operation was done successfully"), Response::HTTP_CREATED)->send();
    }

    public function destroy(Request $request,$vote)
    {
        /** @var Vote $vote */
        $vote = Vote::init()->selectColumns(['id'])->findOrFailById($vote);
        $vote->destroyRow();
        return ApiResponse::message(trans("The operation was done successfully"), Response::HTTP_CREATED)->send();
    }
}
