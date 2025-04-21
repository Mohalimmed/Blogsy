<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Blog;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $query = Category::query();

        // Filter by ID if provided
        if ($request->has('category_id')) {
            $query->where('id', $request->category_id);
        }

        // Filter by name if provided
        if ($request->has('name')) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        $data = $query->get();

        if ($data->isNotEmpty()) {
            return ApiResponse::ResponseMessage(CategoryResource::collection($data), 'Categories Fetched Successfully', 200);
        }

        return ApiResponse::ResponseMessage([], 'Categories Not Found', 200);
    }
}
