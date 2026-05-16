<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller {

    public function store( Request $request, $post_id ) {
        $request->validate( [
            'comment' => 'required|string|max:1000'
        ] );
        $comment = Comment::create( [
            'post_id' => $post_id,
            'user_id' => Auth::id(),
            'comment' => $request->comment
        ] );

        return response()->json( [
            'status' => true,
            'data' => $comment
        ] );
    }
    public function destroy( $id ) {
        if ( Auth::user()->role == 'admin' ) {
            $comment = Comment::find( $id );
            $systemlog= new SystemLoglController();
            $massage=Auth::user()->name.'  admin delete comment';
            $systemlog->store(Auth::id(), $massage);
        }else {
            $comment = Comment::where( 'user_id', Auth::id() )->find( $id );
        }
        if (!$comment ) {
            return response()->json( [
                'status' => false,
                'message' => 'comment not found'
            ] );
        }
        $comment->delete();
        return response()->json( [
            'status' => true,
            'message' => 'Comment deleted'
        ] );
    }

    public function update ( Request $request, $id ) {
        $comment = Comment::where( 'user_id', Auth::id() )->find( $id );
        if ( !$comment ) {
            return response()->json( [
                'status' => false,
                'message' => 'comment not found'
            ] );
        }
        $request->validate( [
            'comment' => 'required|string|max:1000'
        ] );
        $comment->update( [
            'comment' => $request->comment
        ] );
        return response()->json( [
            'status' => true,
            'data' => $comment
        ] );
    }

}
