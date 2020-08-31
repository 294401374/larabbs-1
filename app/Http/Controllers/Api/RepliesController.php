<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Topic;
use App\Models\Reply;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReplyResource;
use App\Http\Requests\Api\ReplyRequest;

class RepliesController extends Controller
{
    /**
     * 新增评论
     * @param ReplyRequest $request
     * @param Topic $topic
     * @param Reply $reply
     * @return ReplyResource
     */
    public function store(ReplyRequest $request, Topic $topic, Reply $reply)
    {
        $reply->content = $request->content;
        $reply->topic()->associate($topic);
        $reply->user()->associate($request->user());
        $reply->save();
        return new ReplyResource($reply);
    }
}
