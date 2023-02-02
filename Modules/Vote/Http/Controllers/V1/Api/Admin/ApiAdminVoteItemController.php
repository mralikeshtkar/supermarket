<?php

namespace Modules\Vote\Http\Controllers\V1\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Vote\Entities\Vote;
use Modules\Vote\Entities\VoteItem;

class ApiAdminVoteItemController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        ApiResponse::init($request->all(), [
            'vote_id' => ['required', 'exists:' . Vote::class . ',id'],
            'title' => ['required', 'string'],
        ])->validate();
        VoteItem::init()->store($request);
        return ApiResponse::message(trans("The operation was done successfully"))->send();
    }

    /**
     * @param Request $request
     * @param $voteItem
     * @return JsonResponse
     */
    public function update(Request $request, $voteItem)
    {
        /** @var VoteItem $voteItem */
        $voteItem = VoteItem::init()->findOrFailById($voteItem);
        ApiResponse::init($request->all(), [
            'vote_id' => ['required', 'exists:' . Vote::class . ',id'],
            'title' => ['required', 'string'],
        ])->validate();
        $voteItem->updateRow($request);
        return ApiResponse::message(trans("The operation was done successfully"))->send();
    }

    /**
     * @param Request $request
     * @param $voteItem
     * @return JsonResponse
     */
    public function destroy(Request $request, $voteItem)
    {
        /** @var VoteItem $voteItem */
        $voteItem = VoteItem::init()->selectColumns(['id'])->findOrFailById($voteItem);
        $voteItem->destroyRow();
        return ApiResponse::message(trans("The operation was done successfully"))->send();
    }
}
