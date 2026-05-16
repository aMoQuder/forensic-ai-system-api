<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Evidence_Resource;
use App\Http\Resources\Usecase_Resource;
use App\Models\UseCase;
use App\Models\Evidences;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id() ;
        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek(Carbon::MONDAY);
        $startOfMonth = $now->copy()->startOfMonth();

        $activeCasesCount = UseCase::where('user_id', $userId)->where('status', 'active')->count();
        $newActiveThisWeek = UseCase::where('user_id', $userId)
            ->where('status', 'active')
            ->where('created_at', '>=', $startOfWeek)
            ->count();

        $completedCasesCount = UseCase::where('user_id', $userId)->where('status', 'complete')->count();
        $completedThisMonth = UseCase::where('user_id', $userId)
            ->where('status', 'complete')
            ->where('updated_at', '>=', $startOfMonth)
            ->count();

        $totalEvidencesCount = Evidences::whereHas('useCase', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();

        $casesThisWeek = UseCase::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('user_id', $userId)
            ->where('created_at', '>=', $startOfWeek)
            ->groupBy('date')
            ->pluck('count', 'date');

        $evidencesThisWeek = Evidences::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereHas('useCase', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('created_at', '>=', $startOfWeek)
            ->groupBy('date')
            ->pluck('count', 'date');

        $chartData = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i)->toDateString();
            $dayName = $startOfWeek->copy()->addDays($i)->format('D');

            $chartData[] = [
                'day' => $dayName,
                'cases' => $casesThisWeek[$date] ?? 0,
                'evidence' => $evidencesThisWeek[$date] ?? 0,
            ];
        }
        $activeCasesList = UseCase::where('user_id', $userId)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get(['id', 'name', 'description', 'created_at']);

        $completedCasesList = UseCase::where('user_id', $userId)
            ->where('status', 'complete')
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get(['id', 'name', 'description', 'created_at', 'updated_at']);

        $evidencesList = Evidences::with(['useCase' => function($q) {
                $q->select('id', 'name');
            }])
            ->whereHas('useCase', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get(['id', 'name', 'model_used', 'case_id', 'created_at']);

        return response()->json([
            'status' => 'success',
            'data' => [
                'overview' => [
                    'active_cases' => [
                        'total' => $activeCasesCount,
                        'new_this_week' => $newActiveThisWeek
                    ],
                    'evidences' => [
                        'total' => $totalEvidencesCount,
                        'pending_review' => 0
                    ],
                    'completed_cases' => [
                        'total' => $completedCasesCount,
                        'completed_this_month' => $completedThisMonth
                    ]
                ],
                'chart_data' => $chartData,
                'modals_data' => [
                    'active_cases_list' => $activeCasesList,
                    'evidences_list' => $evidencesList,
                    'completed_cases_list' => $completedCasesList
                ]
            ]
        ]);
    }

    public function DashboardFlutter(Request $request)
    {
        $userId = Auth::id() ;
        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek(Carbon::MONDAY);
        $startOfMonth = $now->copy()->startOfMonth();

        $activeCasesCount = UseCase::where('user_id', $userId)->where('status', 'active')->count();
        $newActiveThisWeek = UseCase::where('user_id', $userId)
            ->where('status', 'active')
            ->where('created_at', '>=', $startOfWeek)
            ->count();

        $completedCasesCount = UseCase::where('user_id', $userId)->where('status', 'complete')->count();
        $completedThisMonth = UseCase::where('user_id', $userId)
            ->where('status', 'complete')
            ->where('updated_at', '>=', $startOfMonth)
            ->count();

        $totalEvidencesCount = Evidences::whereHas('useCase', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();

        $casesThisWeek = UseCase::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('user_id', $userId)
            ->where('created_at', '>=', $startOfWeek)
            ->groupBy('date')
            ->pluck('count', 'date');

        $evidencesThisWeek = Evidences::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereHas('useCase', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('created_at', '>=', $startOfWeek)
            ->groupBy('date')
            ->pluck('count', 'date');

        $chartData = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i)->toDateString();
            $dayName = $startOfWeek->copy()->addDays($i)->format('D');

            $chartData[] = [
                'day' => $dayName,
                'cases' => $casesThisWeek[$date] ?? 0,
                'evidence' => $evidencesThisWeek[$date] ?? 0,
            ];
        }
        return response()->json([
            'status' => 'success',
            'data' => [
                'overview' => [
                    'active_cases' => [
                        'total' => $activeCasesCount,
                        'new_this_week' => $newActiveThisWeek
                    ],
                    'evidences' => [
                        'total' => $totalEvidencesCount,
                        'pending_review' => 0
                    ],
                    'completed_cases' => [
                        'total' => $completedCasesCount,
                        'completed_this_month' => $completedThisMonth
                    ]
                ],
                'chart_data' => $chartData,

            ]
        ]);
    }
        public function ACtiveCasesList(){
                $userId = Auth::id() ;

                $activeCasesList = UseCase::where('user_id', $userId)
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->take(15)
                ->get(['id', 'name', 'description', 'created_at']);

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'active_cases_list' => $activeCasesList
                    ]
                ]);
        }

        public function CompletedCasesList(){
                $userId = Auth::id() ;
            $completedCasesList = UseCase::where('user_id', $userId)
                ->where('status', 'complete')
                ->orderBy('updated_at', 'desc')
                ->take(10)
                ->get(['id', 'name', 'description', 'created_at', 'updated_at']);

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'completed_cases_list' => $completedCasesList
                    ]
                ]);
        }

        public function EvidenceList(){
                $userId = Auth::id() ;
               $evidencesList = Evidences::with(['useCase' => function($q) {
                $q->select('id', 'name');
            }])
            ->whereHas('useCase', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get(['id', 'name', 'model_used', 'case_id', 'created_at']);

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'active_cases_list' => $evidencesList
                    ]
                ]);
        }






}
