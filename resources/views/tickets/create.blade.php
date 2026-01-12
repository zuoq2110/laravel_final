<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Ticket') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('tickets.store') }}" class="space-y-6" enctype="multipart/form-data">
                        @csrf

                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" 
                                   name="title" 
                                   id="title" 
                                   value="{{ old('title') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('title') border-red-300 @enderror"
                                   placeholder="Brief description of your issue"
                                   required>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>


                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" 
                                      id="description" 
                                      rows="6"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('description') border-red-300 @enderror"
                                      placeholder="Please provide detailed information about your issue..."
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Priority -->
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                            <select name="priority" 
                                    id="priority" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('priority') border-red-300 @enderror"
                                    required>
                                <option value="">Select Priority</option>
                                <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                            </select>
                            @error('priority')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" 
                                    id="status" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('status') border-red-300 @enderror"
                                    required>
                                <option value="">Select Status</option>
                                <option value="open" {{ old('status') === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ old('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="closed" {{ old('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- File Upload -->
                        <div>
                            <label for="attachments" class="block text-sm font-medium text-gray-700">Attachments</label>
                            <p class="mt-1 text-xs text-gray-500">Upload relevant files (images, documents, etc.) - Max 10MB per file</p>
                            <input type="file" 
                                   name="attachments[]" 
                                   id="attachments" 
                                   multiple
                                   class="mt-1 block w-full text-sm text-gray-500
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-md file:border-0
                                          file:text-sm file:font-medium
                                          file:bg-indigo-50 file:text-indigo-700
                                          hover:file:bg-indigo-100
                                          focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
                                          @error('attachments') border-red-300 @enderror"
                                   accept=".pdf,.doc,.docx,.txt,.png,.jpg,.jpeg,.gif,.zip,.rar">
                            @error('attachments')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('attachments.*')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Labels</label>
                            <p class="mt-1 text-xs text-gray-500">Select relevant labels for your ticket (optional)</p>
                            <div class="mt-2 space-y-2">
                                @foreach($labels as $label)
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" 
                                               name="labels[]" 
                                               value="{{ $label->id }}"
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                               {{ in_array($label->id, old('labels', [])) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-700">{{ $label->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('labels')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Categories -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Categories</label>
                            <p class="mt-1 text-xs text-gray-500">Select relevant categories for your ticket (optional)</p>
                            <div class="mt-2 space-y-2">
                                @foreach($categories as $category)
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" 
                                               name="categories[]" 
                                               value="{{ $category->id }}"
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                               {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-700">{{ $category->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('categories')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <x-secondary-button onclick="window.location='{{ route('tickets.index') }}'">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            
                            <x-primary-button>
                                {{ __('Create Ticket') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>