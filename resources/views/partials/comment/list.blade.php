<div>
    @if($comments->isEmpty())
        <div class="container">No comments..</div>
    @endif

    @php
        $authUserId = auth()->id();
    @endphp

    @foreach ($comments as $comment)
        <div class="comment">
            <div class="comment-header">
                <img src="https://ui-avatars.com/api/?name={{$comment->user->name}}&background=random&size=64" alt="User Avatar"
                    class="comment-avatar" />
                <div class="comment-user-info">
                    <span class="comment-user-name">{{ $authUserId == $comment->user_id ?? "" ? "You" : $comment->user->name }}</span>
                    <span class="comment-timestamp">{{$comment->created_at_human}}</span>
                </div>
            </div>
            <div class="comment-body">
                <p>{{$comment->comment}}</p>
            </div>
        </div>
    @endforeach
<div>

