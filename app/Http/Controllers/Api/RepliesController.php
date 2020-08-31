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
        $reply->content = $request->input('content');
        $reply->topic()->associate($topic);
        $reply->user()->associate($request->user());
        $reply->save();
        return new ReplyResource($reply);
    }
    
    public function destroy(Topic $topic, Reply $reply)
    {
        if ($reply->topic_id != $topic->id){
            abort(404);
        }
        $this->authorize('destroy', $reply);
        $reply->delete();
        return response(null, 204);
    }
}