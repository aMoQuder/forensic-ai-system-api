<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\Category;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $postsCount = Post::count();
        $commentsCount = Comment::count();
        $categoriesCount = Category::count();

        $postLabels = Post::selectRaw('DATE(created_at) as date')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('date')
            ->map(fn($d) => date('M d', strtotime($d)));

        $postData = Post::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count');

        $commentLabels = Comment::selectRaw('DATE(created_at) as date')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('date')
            ->map(fn($d) => date('M d', strtotime($d)));

        $commentData = Comment::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count');

        return view('dashboard', compact(
            'postsCount',
            'commentsCount',
            'categoriesCount',
            'postLabels',
            'postData',
            'commentLabels',
            'commentData'
        ));
    }
}
