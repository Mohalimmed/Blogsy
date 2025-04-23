<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BlogRequest;
use App\Http\Resources\BlogsyResource;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
    public function singleBlog($blog_id)
    {
        $blog = Blog::with(['category', 'user', 'comments'])->find($blog_id);
        if ($blog) {
            return ApiResponse::ResponseMessage(new BlogsyResource($blog), 'Blog Fetched Successfully', 200);
        }
        return ApiResponse::ResponseMessage([], 'Blog Not Found', 404);
    }

    public function create(BlogRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->user()->id;
        if ($request->hasFile('image')) {
            $image = $request->image;
            $ImageNewName = time() . '-' . $image->getClientOriginalName();
            $image->storeAs('blogs', $ImageNewName, 'public');
            $data['image'] = $ImageNewName;
        }
        $blog = Blog::create($data);
        return ApiResponse::ResponseMessage(new BlogsyResource($blog), 'Blog Created Successfully', 201);
    }

    public function update(BlogRequest $request, $blog_id)
    {

        $blog = Blog::findOrFail($blog_id);
        if ($blog->user_id != auth()->user()->id) {
            return ApiResponse::ResponseMessage([], 'you are not allowed', 403);
        }
        $data = $request->validated();
        if ($request->hasFile('image')) {
            Storage::delete("public/blogs/$blog->image");
            $image = $request->image;
            $ImageNewName = time() . '-' . $image->getClientOriginalName();
            $image->storeAs('blogs', $ImageNewName, 'public');
            $data['image'] = $ImageNewName;
        }
        $updating =  $blog->update($data);
        if ($updating) return ApiResponse::ResponseMessage(new BlogsyResource($blog), 'Blog Updated Successfully', 200);
    }

    public function destroy($blog_id)
    {
        $blog = Blog::findOrFail($blog_id);
        if ($blog->user_id != auth()->user()->id) {
            return ApiResponse::ResponseMessage([], 'you are not allowed', 403);
        }
        Storage::delete("public/blogs/$blog->image");
        $blog->delete();
        return ApiResponse::ResponseMessage([], 'Blog Deleted Successfully', 200);
    }
    public function myblogs()
    {
        $blogs = Blog::where('user_id', auth()->user()->id)->latest()->get();
        if (count($blogs) > 0) {
            return ApiResponse::ResponseMessage(BlogsyResource::collection($blogs), 'My Blogs Fetched Successfully', 200);
        }
        return ApiResponse::ResponseMessage([], 'There are no Blogs found', 200);
    }
}
