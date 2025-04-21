<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\BlogsyResource;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogsyController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $data = Blog::find(1);
        if ($data) {
            return ApiResponse::ResponseMessage(new BlogsyResource($data), 'Blogs Fetched Successfully', 200);
        }
        return ApiResponse::ResponseMessage([], 'Blogs Not Found', 200);
    }
}
