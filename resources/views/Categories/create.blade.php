@extends('layouts.dashboard')

@section('content')
<div class="bg-white shadow rounded-lg p-6 max-w-lg mx-auto">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Add New Category</h2>

    <form action="{{ route('categories.store') }}" method="POST" class="space-y-6">
        @csrf
        <div>
            <label class="block text-gray-700 font-medium mb-2">Category Name</label>
            <input type="text" name="name" value="{{ old('name') }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                   placeholder="Enter category name">
            @error('name')
                <p class="text-red-600 mt-1 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end">
            <a href="{{ route('categories.index') }}" class="px-4 py-2 bg-gray-300 rounded-lg mr-3 hover:bg-gray-400">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">Save</button>
        </div>
    </form>
</div>
@endsection
