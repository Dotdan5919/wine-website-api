<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Blog;
use App\Models\Comment;
use App\Http\Controllers\Controller;

class BlogController extends Controller
{
    /**
     * Display a listing of blog posts
     */
    public function index()
    {
        try {
            $blogs = Blog::latest()->paginate(10);
            return response()->json([
                'success' => true,
                'data' => $blogs->items(),
                'pagination' => [
                    'current_page' => $blogs->currentPage(),
                    'last_page' => $blogs->lastPage(),
                    'per_page' => $blogs->perPage(),
                    'total' => $blogs->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch blogs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created blog post
     */
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $blog = new Blog();
            $blog->title = $request->title;
            $blog->content = $request->content;
            $blog->excerpt = $request->excerpt;
            $blog->author_id = Auth::id(); // Use actual user ID
            $blog->slug = $this->generateSlug($request->title);

            // Handle file upload
                if ($request->hasFile('featured_image')) {
                    $image = $request->file('featured_image');
                    $filename = time() . '_' . $image->getClientOriginalName();
                    $path = $image->storeAs('uploads', $filename, 'public');
                    $blog->featured_image = $filename;
                }

            $blog->save();

            return response()->json([
                'success' => true,
                'message' => 'Blog post created successfully',
                'data' => $blog
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create blog post: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified blog post
     */
    public function show($id)
    {
        try {
            $blog = Blog::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $blog
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Blog post not found'
            ], 404);
        }
    }

    /**
     * Update the specified blog post
     */
    public function update(Request $request, $id)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $blog = Blog::findOrFail($id);
            
            $blog->title = $request->title;
            $blog->content = $request->content;
            $blog->excerpt = $request->excerpt;
            $blog->slug = $this->generateSlug($request->title);

            // Handle file upload
            if ($request->hasFile('featured_image')) {
                // Delete old image if exists
                if ($blog->featured_image) {
                    $this->deleteFileFromStorage($blog->featured_image);
                }

                $image = $request->file('featured_image');
                $filename = time() . '_' . $image->getClientOriginalName();
                $path = $image->storeAs('uploads', $filename, 'public');
                $blog->featured_image = $filename;
            }

            $blog->save();

            return response()->json([
                'success' => true,
                'message' => 'Blog post updated successfully',
                'data' => $blog
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update blog post: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified blog post
     */
    public function destroy($id)
    {
        try {
            $blog = Blog::findOrFail($id);

            // Delete associated image
            if ($blog->featured_image) {
                $this->deleteFileFromStorage($blog->featured_image);
            }

            // Delete associated comments
            Comment::where('commentable_id', $id)->delete();

            // Delete the blog post
            $blog->delete();

            return response()->json([
                'success' => true,
                'message' => 'Blog post deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete blog post: ' . $e->getMessage()
            ], 500);
        }
    }




    /**
     * Generate a unique slug from title
     */
    private function generateSlug($title)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $originalSlug = $slug;
        $count = 1;

        while (Blog::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }





    /**
     * Delete file from storage
     */
    private function deleteFileFromStorage($filename)
    {
        try {
            if (Storage::disk('public')->exists('uploads/' . $filename)) {
                Storage::disk('public')->delete('uploads/' . $filename);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}