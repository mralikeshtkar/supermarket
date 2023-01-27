<?php

namespace Modules\Vote\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Vote\Entities\VoteItem;
use Symfony\Component\HttpFoundation\Response;

class ApiVoteItemController extends Controller
{
    /**
     * @param Request $request
     * @param $voteItem
     * @return JsonResponse
     */
    public function store(Request $request, $voteItem)
    {
        /** @var VoteItem $voteItem */
        $voteItem = VoteItem::init()
            ->withRelationships(['vote','vote.itemUsers:users.id'])
            ->findOrFailById($voteItem);
        if ($voteItem->vote->itemUsers->contains($request->user()->id))
            return ApiResponse::sendError(trans("You have already participated in this survey"),Response::HTTP_BAD_REQUEST);
        $voteItem->attachUser($request->user()->id);
        return ApiResponse::message(trans("The operation was done successfully"))->send();
    }
}
