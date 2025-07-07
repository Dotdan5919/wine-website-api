<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\Validator;
use App\Models\Blog;
use App\Models\Comment;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{
    //
public function index()
    {
        try {
            $comments = Comment::latest()->paginate(10);
            return response()->json([
                'success' => true,
                'data' => $comments->items(),
                'pagination' => [
                    'current_page' => $comments->currentPage(),
                    'last_page' => $comments->lastPage(),
                    'per_page' => $comments->perPage(),
                    'total' => $comments->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch comments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created Comment
     */
    public function store(Request $request)
    {
        

        try {
            $comment = new Comment();
            $comment->content= $request->content;
            $comment->author_name = $request->author_name;
            $comment->user_id = $request->user_id;
            $comment->commentable_type = $request->commentable_type;
           


           

            
            $comment->save();

            return response()->json([
                'success' => true,
                'message' => 'comment post created successfully',
                'data' => $comment
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create comment post: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified blog post
     */
    public function show($id)
    {
        try {
            $comment = Comment::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $comment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'comment post not found'
            ], 404);
        }
    }

    /**
     * Update the specified blog post
     */
    public function update(Request $request, $id)
    {
       
        try {
             $comment = new Comment();
            $comment->content= $request->content;
            $comment->author_name = $request->author_name;
            $comment->user_id = $request->user_id;
            $comment->comment_type = $request->comment_type;

            
            $comment->save();

            return response()->json([
                'success' => true,
                'message' => 'comment post updated successfully',
                'data' => $comment
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update comment post: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified blog post
     */
    public function destroy($id)
    {
        try {
            $comment = Comment::findOrFail($id);

            // Delete associated image
            if ($comment->featured_image) {
                $this->deleteFileFromStorage($comment->featured_image);
            }

          

            // Delete the comment post
            $comment->delete();

            return response()->json([
                'success' => true,
                'message' => 'comment post deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete comment post: ' . $e->getMessage()
            ], 500);
        }
    }




   

}
