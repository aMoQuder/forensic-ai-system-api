<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Http\Resources\FeedResource;
use App\Models\Post;
use App\Models\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PostResource;
use App\Models\Comment;
use Dotenv\Validator;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class PostController extends Controller {

    public function feed( Request $request ) {
        $userId = Auth::id();

        $feeds = Post::with( [
            'user:id,name,image',
            'comments.user:id,name,image'
        ] )
        ->where( 'type', 'feeds' )
        ->withCount( [ 'likes', 'comments', 'views' ] )
        ->withExists( [
            'likes as is_liked' => function ( $q ) use ( $userId ) {
                $q->where( 'user_id', $userId );
            }
        ] )
        ->inRandomOrder()
        ->get();

        $posts = Post::with( [
            'user:id,name,image',
            'comments.user:id,name,image'
        ] )
        ->where( 'type', 'article' )
        ->withCount( [ 'likes', 'comments', 'views' ] )
        ->withExists( [
            'likes as is_liked' => function ( $q ) use ( $userId ) {
                $q->where( 'user_id', $userId );
            }
        ] )
        ->inRandomOrder()
        ->get();

        $myPosts = Post::with( [
            'user:id,name,image',
            'comments.user:id,name,image'
        ] )
        ->where( 'user_id', $userId )
        ->withCount( [ 'likes', 'comments', 'views' ] )
        ->withExists( [
            'likes as is_liked' => function ( $q ) use ( $userId ) {
                $q->where( 'user_id', $userId );
            }
        ] )
        ->get();

        $data = [];

        foreach ( $myPosts as $myPost ) {
            if ( $myPost->type == 'feeds' ) {
                $data[] = new FeedResource( $myPost );
            } else {
                $data[] = new PostResource( $myPost );
            }
        }

        return response()->json( [
            'status' => true,
            'data' => [
                'public_feed' => [
                    'data' => FeedResource::collection( $feeds ),
                ],
                'publication' => [
                    'data' => PostResource::collection( $posts ),
                ],
                'my_publications' => [
                    'data' => $data,
                ],
            ]
        ] );
    }

    public function index() {
        $posts = Post::with( [
            'user:id,name',
            'comments.user:id,name',
            'likes',
            'views'
        ] )
        ->latest()
        ->paginate( 10 );

        return response()->json( [
            'status' => true,
            'data' => $posts
        ] );
    }

    public function store( Request $request ) {
        $request->validate( [
            'content' => 'required|string|max:2000',
            'title' => 'required|string|max:500',
            'image' => 'nullable|image',
        ] );
        $imagePath = null;
        if ( $request->hasFile( 'image' ) ) {
            $imagePath = $request->file( 'image' )->store( 'posts', 'public' );
        }
        $post = Post::create( [
            'user_id' => Auth::id(),
            'title'   => $request->title,
            'content' => $request->content,
            'image'   => $imagePath,
            'type'   => 'article'
        ] );
        return response()->json( [
            'status' => true,
            'message' => 'created successfully',
            'data' => new PostResource( $post ),

        ] );
    }

    public function storefeed( Request $request ) {
        $request->validate( [
            'content' => 'required|string|max:2000',
        ] );
        $post = Post::create( [
            'user_id' => Auth::id(),
            'content' => $request->content,
            'type' => 'feeds',
        ] );
        return response()->json( [
            'status' => true,
            'message' => 'created successfully',
            'data' => new FeedResource( $post ),
        ] );
    }

    public function share( Request $request, $post_id ) {
        $post = Post::find( $post_id );
        // $view = View::where( 'post_id', $post_id )->where( 'user_id', Auth::id() )->first();
        // if ( !$view ) {
        //     // add view
        //     View::create( [
        //         'post_id' => $post_id,
        //         'user_id' => Auth::id(),
        //         'ip' => $request->ip()
        //     ] );
        // }

        return response()->json( [
            'status'  => true,
            'id'    => $post_id,
            'share_link' => url( '/view-'. $post->type.'/'.$post_id ),
        ] );

    }

    public function show( $id, Request $request ) {
        $userId = Auth::id();

        $view = View::where( 'post_id', $id )->where( 'user_id', Auth::id() )->first();
        if ( !$view ) {
            // add view
            View::create( [
                'post_id' => $id,
                'user_id' => Auth::id(),
                'ip' => $request->ip()
            ] );
        }

        $post = Post::with( 'user:id,name' )
        ->withCount( [ 'likes', 'comments', 'views' ] )
        ->withExists( [
            'likes as is_liked' => function ( $q ) use ( $userId ) {
                $q->where( 'user_id', $userId );
            }
        ] )->find( $id );

        if ( $post->type == 'feeds' ) {
            $data = new FeedResource( $post );
        } else {
            $data = new PostResource( $post );
        }
        $comments = Comment::where( 'post_id', $id )->get();
        return response()->json( [
            'status'  => true,
            'data'    => $data,
            'comments' => CommentResource::collection( $comments )
        ] );
    }

    public function update( Request $request, $id ) {

        $post = Post::where( 'user_id', Auth::id() )->find( $id );
        if ( !$post ) {
            return response()->json( [
                'status' => false,
                'message' => 'errore ! item not found'
            ] );
        }
        if ( $post->type == 'feeds' ) {
            $request->validate( [
                'content' => 'required|string|max:2000',
            ] );
        } else {
            $request->validate( [
                'content' => 'required|string|max:2000',
                'title' => 'required|string|max:500',
                'image' => 'nullable|image',
            ] );

        }

        $imagePath = $post->image;
        if ( $request->hasFile( 'image' ) ) {
            $imagePath = $request->file( 'image' )->store( 'posts', 'public' );
        }
        $post->update( [
            'title'   => $request->title,
            'content' => $request->content,
            'image'   => $imagePath
        ] );

        return response()->json( [
            'status' => true,
            'message' => 'updated successfully',
        ] );
    }

    public function destroy( $id ) {
        if ( Auth::user()->role == 'admin' ) {
            $post = Post::find( $id );
            $systemlog= new SystemLoglController();
            if ( $post->type == 'feeds' ) {
                $massage='Admin'.Auth::user()->name.'delete Feed ';
            } else {
                $massage='Admin'.Auth::user()->name.'delete Article ';
            }
            $systemlog->store(Auth::id(), $massage);
        }else {
            $post = Post::where( 'user_id', Auth::id() )->find( $id );
        }
        if ( !$post ) {
            return response()->json( [
                'status' => false,
                'message' => 'errore ! item not found'
            ] );
        }
        $post->comments()->delete();
        $post->delete();
        return response()->json( [
            'status' => true,
            'message' => ' deleted successfully',
        ] );
    }
}
