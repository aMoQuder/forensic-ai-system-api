@extends('layouts.dashboard')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Categories</h2>
        <a href="{{ route('categories.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition-all">
            + Add Category
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <table class="w-full border-collapse bg-white shadow rounded-lg overflow-hidden">
        <thead>
            <tr class="bg-gray-100 text-left">
                <th class="px-6 py-3 font-medium text-gray-600">#</th>
                <th class="px-6 py-3 font-medium text-gray-600">Name</th>
                <th class="px-6 py-3 font-medium text-gray-600">Created At</th>
                <th class="px-6 py-3 font-medium text-gray-600 text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($Categories as $category)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-6 py-3">{{ $category->id }}</td>
                    <td class="px-6 py-3">{{ $category->name }}</td>
                    <td class="px-6 py-3">{{ $category->created_at}}</td>
                    <td class="px-6 py-3 text-center">
                        <a href="{{ route('categories.edit', $category->id) }}"
                           class="text-blue-600 hover:underline mr-3">Edit</a>
                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-red-600 hover:underline"
                                    onclick="return confirm('Are you sure you want to delete this category?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center py-6 text-gray-500">No categories found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-6">
        {{ $Categories->links() }}
    </div>
</div>
@endsection
