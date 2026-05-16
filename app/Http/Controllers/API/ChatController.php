<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\ForensicAiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatController extends Controller {
    protected ForensicAiService $aiService;

    public function __construct( ForensicAiService $aiService ) {
        $this->aiService = $aiService;
    }

    public function getConversations( Request $request ) {
        $conversations = $request->user()->conversations()
        ->orderBy( 'updated_at', 'desc' )
        ->get();

        return response()->json( [ 'data' => $conversations ] );
    }

    public function getMessages( Conversation $conversation ) {
        if ( $conversation->user_id !== Auth::id() ) {
            return response()->json( [ 'error' => 'Unauthorized' ], 403 );
        }

        $messages = $conversation->messages()->orderBy( 'created_at', 'asc' )->get();
        return response()->json( [ 'data' => $messages ] );
    }

    public function sendMessage( Request $request ) {
        $request->validate( [
            'conversation_id' => 'nullable|exists:conversations,id',
            'query' => 'required|string|max:1000',
            'case_context' => 'nullable|string'
        ] );

        $user = $request->user();
        $query = $request->input( 'query' );
        $caseContext = $request->input( 'case_context', 'string' );

        $conversationId = $request->input( 'conversation_id' );
        if ( !$conversationId ) {
            $title = Str::words( $query, 4, '...' );
            $conversation = $user->conversations()->create( [
                'title' => $title
            ] );
        } else {
            $conversation = Conversation::find( $conversationId );
            $conversation->touch();
        }

        $userMessage = $conversation->messages()->create( [
            'sender' => 'user',
            'content' => $query,
        ] );

        $aiResponse = $this->aiService->askForensicAssistant( $query, $caseContext );

        if ( !$aiResponse ) {
            return response()->json( [
                'error' =>'sorry !may be the assistant is busy now, please try again later',
            ], 500 );
        }

        $assistantMessage = $conversation->messages()->create( [
            'sender' => 'assistant',
            'content' => $aiResponse[ 'answer' ] ?? 'لا يوجد رد.',
            'metadata' => [
                'language' => $aiResponse[ 'language' ] ?? 'ar',
                'used_tavily' => $aiResponse[ 'used_tavily' ] ?? false,
                'used_pubmed' => $aiResponse[ 'used_pubmed' ] ?? false,
                'pubmed_query' => $aiResponse[ 'pubmed_query' ] ?? null,
            ]
        ] );

        return response()->json( [
            'conversation_id' => $conversation->id,
            'user_message' => $userMessage,
            'assistant_message' => $assistantMessage
        ] );
    }

    // public function sendINConversation( Request $request, Conversation $conversation ) {
    //     $request->validate( [
    //         'query' => 'required|string|max:1000',
    //         'case_context' => 'nullable|string'
    //     ] );

    //     $user = $request->user();
    //     $query = $request->input( 'query' );
    //     $caseContext = $request->input( 'case_context', 'string' );
    //     if ( !$conversation ) {
    //         return response()->json( [ 'error' => 'Conversation not found' ], 404 );
    //     }
    //     $conversation = Conversation::find( $conversation );

    //     $userMessage = $conversation->messages()->create( [
    //         'sender' => 'user',
    //         'content' => $query,
    //     ] );

    //     $aiResponse = $this->aiService->askForensicAssistant( $query, $caseContext );

    //     if ( !$aiResponse ) {
    //         return response()->json( [
    //             'error' =>'sorry !may be the assistant is busy now, please try again later',
    //         ], 500 );
    //     }

    //     $assistantMessage = $conversation->messages()->create( [
    //         'sender' => 'assistant',
    //         'content' => $aiResponse[ 'answer' ] ?? 'لا يوجد رد.',
    //         'metadata' => [
    //             'language' => $aiResponse[ 'language' ] ?? 'ar',
    //             'used_tavily' => $aiResponse[ 'used_tavily' ] ?? false,
    //             'used_pubmed' => $aiResponse[ 'used_pubmed' ] ?? false,
    //             'pubmed_query' => $aiResponse[ 'pubmed_query' ] ?? null,
    //         ]
    //     ] );

    //     return response()->json( [
    //         'conversation_id' => $conversation->id,
    //         'user_message' => $userMessage,
    //         'assistant_message' => $assistantMessage
    //     ] );
    // }
}
