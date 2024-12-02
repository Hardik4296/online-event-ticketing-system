<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Services\CommentService;
use App\Services\EventService;
use App\Traits\Common;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    use Common;

    protected $commentService;

    protected $EventService;

    public function __construct(CommentService $commentService, EventService $EventService)
    {
        $this->commentService = $commentService;
        $this->EventService = $EventService;
    }

    /**
     * Handle comment list
     */
    public function index(string $id)
    {
        try {
            if (!app()->environment('testing') && !request()->ajax() || empty($id)) {
                Log::error('Invalid request at comment list');

                abort(404);
            }

            $eventId = $this->decryptId($id);
            if (!$this->EventService->getEventDetails($eventId)) {
                Log::error('Invalid request at comment list - event not found for id : ' . $eventId);

                return response()->json([
                    'success' => false,
                    'message' => 'Event not found'
                ], 404);
            }
            $comments = $this->commentService->getComments($eventId);
            if (app()->environment('testing')) {

                return response()->json([
                    'success' => true,
                    'comments' => $comments
                ]);
            }

            return view('partials.comment.list', compact('comments'));
        } catch (DecryptException $e) {
            Log::error('Decryption failed at comment list', ['exception' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ], 404);
        } catch (Exception $e) {
            Log::error(' Error at comment list', ['message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ], 404);
        }
    }

    /**
     * Handle store comment
     */
    public function store(StoreCommentRequest $request)
    {
        try {

            if (!app()->environment('testing') && !request()->ajax()) {
                abort(404);
            }

            $validatedData = $request->validated();
            $this->commentService->createComment($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
            ]);
        } catch (Exception $e) {
            Log::error('Error storing comment', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ], 404);
        }
    }
}

