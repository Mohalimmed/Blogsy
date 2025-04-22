<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\BlogsyResource;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogsController extends Controller
{
    public function index(Request $request)
    {
        $query = Blog::query();

        // 🔍 Filter by search term
        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where('name', 'LIKE', '%' . $request->search . '%');
        });
        $data = $query->latest()->paginate(5)->appends(request()->query());


        if ($data) {
            if ($data->total() > $data->perPage()) {
                $blogs = [
                    'records' => BlogsyResource::collection($data),
                    'Pagination_links' => [
                        'next_page' => $data->nextPageUrl(),
                        'previous_page' => $data->previousPageUrl(),
                        'current_page' => $data->currentPage(),
                        'total_records' => $data->total(),
                        'links' => [
                            'first_page' => $data->url(1),
                            'last_page' => $data->url($data->lastPage()),
                        ],
                    ],
                ];
            } else {
                $blogs =  BlogsyResource::collection($data);
            }
            return ApiResponse::ResponseMessage($blogs, 'Blogs Fetched Successfully', 200);
        }
        return ApiResponse::ResponseMessage([], 'Blogs Not Found', 200);
    }
}
