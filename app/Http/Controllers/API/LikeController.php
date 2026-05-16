<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Post;
use App\Models\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function toggle(Request $request,$post_id)
    {

       $post = Post::find($post_id );
        if(!$post){
            return response()->json([
                'status' => false,
                'message' => 'Post not found'
            ]);
        }

        $like = Like::where('post_id',$post_id)
            ->where('user_id', Auth::id())
            ->first();

        if ($like) {
            $like->delete();
            return response()->json([
                'status' => true,
                'message' => 'Unliked'
            ]);
        }
        Like::create([
            'post_id' =>$post_id,
            'user_id' => Auth::id()
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Liked'
        ]);
    }
}
