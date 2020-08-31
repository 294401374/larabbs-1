<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TopicRequest;
use App\Http\Resources\TopicResource;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class TopicsController extends Controller
{
    /**
     * 帖子列表
     * @param Request $request
     * @param Topic $topic
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request, Topic $topic)
    {
        $query = $topic->query();
        if ($categoryId = $request->category_id){
            $query->where('category_id', $categoryId);
        }
        $topics = QueryBuilder::for(Topic::class)->allowedIncludes('user', 'category')
                                                 ->allowedFilters([
                                                     'title',
                                                     AllowedFilter::exact('category_id'),
                                                     AllowedFilter::scope('withOrder')->default('recentReplied'),
                                                 ])
                                                 ->paginate();
        return TopicResource::collection($topics);
    }
    
    /**
     * 用户帖子
     * @param Request $request
     * @param User $user
     */
    public function userIndex(Request $request, User $user)
    {
        $query = $user->topics()
                      ->getQuery();
        $topics = QueryBuilder::for($query)->allowedIncludes('user', 'category')
                                           ->allowedFilters([
                                               'title',
                                               AllowedFilter::exact('category_id'),
                                               AllowedFilter::scope('withOrder')->default('recentReplied'),
                                           ])
                                           ->paginate();
        return TopicResource::collection($topics);
    }
    
    /**
     * 帖子详情
     * @param $topicId
     * @return TopicResource
     */
    public function show($topicId)
    {
        
        $topic = QueryBuilder::for(Topic::class)->allowedIncludes('user', 'category')
                                                ->findOrFail($topicId);
        return new TopicResource($topic);
    }
    
    /**
     * 创建帖子
     * @param TopicRequest $request
     * @param Topic $topic
     * @return TopicResource
     */
    public function store(TopicRequest $request, Topic $topic)
    {
        $topic->fill($request->all());
        $topic->user_id = $request->user()->id;
        $topic->save();
        return new TopicResource($topic);
    }
    
    /**
     * 更新帖子
     * @param TopicRequest $request
     * @param Topic $topic
     * @return TopicResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(TopicRequest $request, Topic $topic)
    {
        $this->authorize('update', $topic);
        $topic->update($request->all());
        return new TopicResource($topic);
    }
    
    /**
     * 删除帖子
     * @param Topic $topic
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Topic $topic)
    {
        $this->authorize('destroy', $topic);
        $topic->delete();
        return response(null, 204);
    }
}
