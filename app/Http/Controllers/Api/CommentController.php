<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CommentRequest;
use App\Models\Comment;


class CommentController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(CommentRequest $request)
    {
        $data = $request->validated();

        $commentStore = Comment::create($data);
        if ($commentStore) {
            return ApiResponse::ResponseMessage($data, 'Comment Created Successfully', 201);
        }
        return ApiResponse::ResponseMessage($data, 'Comment Not Created', 200);
    }
}
