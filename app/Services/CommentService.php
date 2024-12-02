<?php

namespace App\Services;

use App\Models\Comment;
use Cache;

class CommentService
{
    protected $comment;

    public function __construct()
    {
        $this->comment = new comment();
    }

    /**
     * Handle get comments
     */
    public function getComments(int $event_id)
    {
        return Cache::remember('comment_list_' . $event_id, 86400, function () use($event_id) {
            return $this->comment->where('event_id', $event_id)->orderBy('created_at', 'desc')->take(10)->get();
        });
    }

    /**
     * Handle create comments
     */
    public function createComment(array $data)
    {
        Cache::forget('comment_list_' . $data['event_id']);

        return $this->comment->create([
            'user_id' => auth()->id(),
            'event_id' => $data['event_id'],
            'comment' => $data['comment'],
        ]);
    }
}
