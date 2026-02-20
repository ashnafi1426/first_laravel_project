<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // READ ALL
    public function index()
    {
        return Post::latest()->get();
    }

    // CREATE
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required'
        ]);

        $post = Post::create($request->all());

        return response()->json($post, 201);
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required'
        ]);

        $post = Post::findOrFail($id);
        $post->update($request->all());

        return response()->json($post);
    }

    // DELETE
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully'
        ]);
    }
}
