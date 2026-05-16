<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'NewLand') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    <!-- Navbar -->
    <nav class="bg-white shadow sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ url('/') }}" class="text-2xl font-bold text-blue-600">ITI Interships</a>
            <div class="space-x-6">
                <a href="{{ route('posts-page') }}" class="text-gray-700 hover:text-blue-600 font-medium">All Posts</a>
                <a href="{{ url('/') }}" class="text-gray-700 hover:text-blue-600 font-medium">Home</a>
                @auth
                    <span class="text-gray-900 font-semibold">Hello, {{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button class="text-red-600 hover:text-red-800 font-medium ml-2">Logout</button>

                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-blue-600 font-medium">Dashboard</a>
                        {{-- @if (Auth::role=="admin")

                        @endif --}}
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 font-medium">Login</a>
                    <a href="{{ route('register') }}" class="ml-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow">
                        Register
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 text-center py-6 mt-10">
        <p>© {{ date('Y') }} <span class="font-bold text-white">ITI interships</span>. All rights reserved.</p>
    </footer>

</body>
</html>
