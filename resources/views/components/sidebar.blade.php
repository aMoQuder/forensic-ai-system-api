
<!-- resources/views/components/sidebar-dash.blade.php -->
<aside class="w-64 bg-white shadow-md min-h-screen transition-all duration-300">

    <nav class="mt-6">
        <ul class="space-y-2">
            <!-- Dashboard -->
            <li>
                <a href="{{ route('dashboard') }}"
                   class="flex items-center px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200
                   {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white font-semibold rounded-md' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6m-6 6v6h-6m0-6h6m6 0h-6"/>
                    </svg>
                    Dashboard
                </a>
            </li>

            <!-- Posts -->
            <li>
                <a href="{{ route('posts.index') }}"
                   class="flex items-center px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200
                   {{ request()->routeIs('posts.*') ? 'bg-blue-600 text-white font-semibold rounded-md' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M4 3h12a1 1 0 011 1v2H3V4a1 1 0 011-1z"/>
                        <path fill-rule="evenodd"
                              d="M3 9h14v8a1 1 0 01-1 1H4a1 1 0 01-1-1V9z"
                              clip-rule="evenodd"/>
                    </svg>
                    Posts
                </a>
            </li>

            <!-- Categories -->
            <li>
                <a href="{{ route('categories.index') }}"
                   class="flex items-center px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200
                   {{ request()->routeIs('categories.*') ? 'bg-blue-600 text-white font-semibold rounded-md' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M10 2a8 8 0 100 16 8 8 0 000-16zM9 7a1 1 0 012 0v4a1 1 0 01-2 0V7zm1 6a1.5 1.5 0 110 3 1.5 1.5 0 010-3z"
                              clip-rule="evenodd"/>
                    </svg>
                    Categories
                </a>
            </li>

            <!-- Comments -->
            <li>
                <a href="{{url('/')}}"
                   class="flex items-center px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200
                 ">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2z"
                              clip-rule="evenodd"/>
                    </svg>
                    welcome
                </a>
            </li>
        </ul>
    </nav>
</aside>
