<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UseCase;
use App\Models\Post;
use App\Models\ModelAi;
use App\Models\Comment;
use App\Models\Conversation;
use App\Models\model_ai;
use App\Models\SystemLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminPanalController extends Controller {
    /**
    * 1. Dashboard Page
    */

    public function dashboard() {
        $totalDoctors = User::where( 'role', 'doctor' )->count();
        $activeCases = UseCase::where( 'status', 'active' )->count();
        $totalFeedsPosts = Post::where( 'type', 'feeds' )->count();

        $topDoctors = User::where( 'role', 'doctor' )->select( 'id', 'name', 'image', 'created_at' )
        ->withCount( 'UseCases as cases_count' )
        ->orderByDesc( 'cases_count' )
        ->take( 5 )
        ->get( [ 'id', 'name', 'image' ] );

        $aiModelsChartData = model_ai::select( 'models', DB::raw( 'count(*) as total_used' ) )
        ->groupBy( 'models' )
        ->get();

        return response()->json( [
            'status' => true,
            'data' => [
                'statistics' => [
                    'total_doctors' => $totalDoctors,
                    'active_cases' => $activeCases,
                    'total_feeds_posts' => $totalFeedsPosts,
                ],
                'top_doctors' => $topDoctors,
                'chart_data' => $aiModelsChartData,
            ]
        ] );
    }

    /**
    * 2. Doctor Hub Page
    */

    public function doctors() {
        $doctors = User::where( 'role', 'doctor' )->select( 'id', 'name', 'national_id', 'created_at', 'status' )
        ->paginate( 10 );

        return response()->json( [
            'status' => true,
            'data' => $doctors
        ] );
    }

    /**
    * 3. Doctor Profile Page ( With Pop Screens Data )
    */

    public function doctorProfile( $id ) {
        $doctor = User::withCount( [
            'UseCases as total_cases',
            'posts as total_articles' => function ( $query ) {
                $query->where( 'type', 'article' );
            }
        ] )->find( $id );

        $cases = UseCase::where( 'user_id', $id )
        ->select( 'id', 'user_id', 'status', 'created_at' )
        ->withCount( 'evidences' )
        ->paginate( 5, [ '*' ], 'cases_page' );

        $articles = Post::where( 'user_id', $id )
        ->where( 'type', 'article' )
        ->select( 'id', 'user_id', 'title', 'created_at' )
        ->withCount( 'views' )
        ->paginate( 5, [ '*' ], 'articles_page' );

        return response()->json( [
            'status' => true,
            'data' => [
                'doctor_info' => [
                    'id' => $doctor->id,
                    'name' => $doctor->name,
                    'image' => $doctor->image,
                    'email' => $doctor->email,
                    'national_id' => $doctor->national_id,
                    'total_cases' => $doctor->total_cases,
                    'total_articles' => $doctor->total_articles,
                ],
                'modals_data' => [
                    'cases_modal' => $cases,
                    'articles_modal' => $articles,
                ]
            ]
        ] );
    }

    public function assignAdmin( $id ) {
        $doctor = User::find( $id );
        if ( $doctor->role == 'doctor' ) {
            $systemlog = new SystemLoglController();
            $massage = 'admin'. Auth::user()->name.' assign'.$doctor->name.' admin';
            $systemlog->store( Auth::id(), $massage );
            $doctor->role = 'admin';
            $doctor->save();
            return response()->json( [
                'status' => true,
                'message' => 'assigned as admin successfully',
            ] );
        } else {
            return response()->json( [
                'status' => false,
                'message' => 'User is not a doctor',
            ] );
        }
    }

    /**
    * 4. Case Audit Page
    */

    public function casesAudit() {
        $cases = UseCase::select( 'id', 'user_id', )->with( [ 'user:id,name' ] )
        ->withCount( 'evidences' )
        ->latest()
        ->paginate( 10 );

        return response()->json( [
            'status' => true,
            'data' => $cases
        ] );
    }
    /**
    * 5. Community Page
    */

    public function community() {
        $articles = Post::where( 'type', 'article' )->select( 'id', 'title', 'user_id' )
        ->with( 'user:id,name' )
        ->latest()
        ->paginate( 10, [ '*' ], 'articles_page' );

        $feeds = Post::where( 'type', 'feeds' )->select( 'id', 'content', 'user_id' )
        ->with( 'user:id,name' )
        ->latest()
        ->paginate( 10, [ '*' ], 'feeds_page' );

        $comments = Comment::select( 'id', 'comment', 'user_id' )->with( 'user:id,name' )
        ->latest()
        ->paginate( 10, [ '*' ], 'comments_page' );

        return response()->json( [
            'status' => true,
            'data' => [
                'articles' =>  $articles,
                'feeds' => $feeds,
                'comments' => $comments,
            ]
        ] );
    }
    /**
    * 6. Chat Page
    * */

    public function chatMange() {
        $conversations = Conversation::select( 'id', 'title', 'created_at' )
        ->withCount( 'messages' )
        ->latest()
        ->paginate( 10, [ '*' ], 'chat_page' );

        return response()->json( [
            'status' => true,
            'msg'=>'succesfully fetched data',
            'data' => [
                'Conversation' =>  $conversations,

            ]
        ] );
    }

    /**
    *7. toggle to make user active or block
    */

    public function toggleUser( $id ) {

        $user = User::find( $id );
        if ( !$user ) {
            return response()->json( [ 'status' => false, 'message' => 'Error! User not found' ], 403 );
        }
        $newStatus = ( $user->status === 'active' ) ? 'block ' : 'active';
        $user->update( [
            'status' => $newStatus,
        ] );
        $user->save();
        $systemlog = new SystemLoglController();
        $massage = 'admin'. Auth::user()->name.' Make this user '.$user->name.' an '.$newStatus;
        $systemlog->store( Auth::id(), $massage );
        $message = ( $newStatus === 'active' ) ? 'active now' : 'block now';

        return response()->json( [
            'success' => true,
            'status'  => $newStatus,
            'message' => $message
        ] );

    }

    public function SystwmLog() {
        $systemlog = SystemLog::select( 'id', 'name', 'created_at', 'massage' )
        ->paginate( 10 );

        return response()->json( [
            'status' => true,
            'data' => $systemlog
        ] );
    }

    /*
    * some of data in excel file  of file pdf
    */

    public function getGlobalReportData() {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $userActivity = User::whereBetween( 'updated_at', [ $startOfMonth, $endOfMonth ] )
        ->select( 'name', 'role', 'updated_at' )
        ->take( 100 )
        ->get();

        $caseStats = [
            'total' => UseCase::whereBetween( 'created_at', [ $startOfMonth, $endOfMonth ] )->count(),
            'active' => UseCase::where( 'status', 'active' )->count(),
            'completed' => UseCase::where( 'status', 'complete' )->count(),
        ];

        $aiPerformance = model_ai::select( 'models', DB::raw( 'count(*) as usage_count' ) )
        ->whereBetween( 'created_at', [ $startOfMonth, $endOfMonth ] )
        ->groupBy( 'models' )
        ->get();

        $securityLogs = DB::table( 'activity_log' )
        ->whereBetween( 'created_at', [ $startOfMonth, $endOfMonth ] )
        ->latest()
        ->take( 50 )
        ->get();

        $communityStats = [
            'articles' => Post::where( 'type', 'article' )->whereBetween( 'created_at', [ $startOfMonth, $endOfMonth ] )->count(),
            'feeds' => Post::where( 'type', 'feeds' )->whereBetween( 'created_at', [ $startOfMonth, $endOfMonth ] )->count(),
            'comments' => Comment::whereBetween( 'created_at', [ $startOfMonth, $endOfMonth ] )->count(),
        ];

        return response()->json( [
            'status' => true,
            'metadata' => [
                'period' => now()->format( 'F Y' ),
                'generated_at' => now()->toDateTimeString(),
            ],
            'data' => [
                'user_activity' => $userActivity,
                'case_statistics' => $caseStats,
                'ai_performance' => $aiPerformance,
                'security_logs' => $securityLogs,
                'community_engagement' => $communityStats
            ]
        ] );
    }

}
