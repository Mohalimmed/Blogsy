<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBlogRequest;
use App\Http\Requests\UpdateBlogRequest;
use App\Models\Blog;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->only(['create', 'myBlogs', 'edit', 'update', 'destroy']);
    }

    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $categories = Category::get();
        return view('theme.blogs.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBlogRequest $request)
    {
        $data = $request->validated();
        $image = $request->image;
        $ImageNewName = time() . '-' . $image->getClientOriginalName();
        $image->storeAs('blogs', $ImageNewName, 'public');
        $data['image'] = $ImageNewName;
        $data['user_id'] = Auth::user()->id;
        Blog::create($data);
        return back()->with('blogCreateStatus', 'Blog Created Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Blog $blog)
    {
        return view('theme.single-blog', compact('blog'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Blog $blog)
    {
        if ($blog->user_id == Auth::user()->id) {
            $categories = Category::get();
            return view('theme.blogs.edit', compact('categories', 'blog'));
        }
        abort(403);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBlogRequest $request, Blog $blog)
    {
        if ($blog->user_id == Auth::user()->id) {
            $data = $request->validated();
            if ($request->hasFile('image')) {
                Storage::delete("public/blogs/$blog->image");
                $image = $request->image;
                $ImageNewName = time() . '-' . $image->getClientOriginalName();
                $image->storeAs('blogs', $ImageNewName, 'public');
                $data['image'] = $ImageNewName;
            }
            $blog->update($data);
            return back()->with('blogUpdatedStatus', 'Blog Updated Successfully');
        }
        abort(403);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {
        if ($blog->user_id == Auth::user()->id) {
            Storage::delete("public/blogs/$blog->image");
            $blog->delete();
            return back()->with('blogDeletedStatus', 'Blog Deleted Successfully');
        }
        abort(403);
    }
    public function myBlogs()
    {
        $blogs = Blog::where('user_id', Auth::user()->id)->paginate(8);
        return view('theme.blogs.my-blogs', compact('blogs'));
    }
}
