<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Ticket') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form action="{{ route('tickets.update', $ticket->id) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title"
                                :value="old('title', $ticket->title)" required autofocus autocomplete="title" />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                name="description" rows="4" required>{{ old('description', $ticket->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="priority" :value="__('Priority')" />
                            <select id="priority"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                name="priority" required>
                                <option value="low"
                                    {{ old('priority', $ticket->priority) === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium"
                                    {{ old('priority', $ticket->priority) === 'medium' ? 'selected' : '' }}>Medium
                                </option>
                                <option value="high"
                                    {{ old('priority', $ticket->priority) === 'high' ? 'selected' : '' }}>High</option>
                            </select>
                            <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                name="status" required>
                                <option value="open"
                                    {{ old('status', $ticket->status) === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress"
                                    {{ old('status', $ticket->status) === 'in_progress' ? 'selected' : '' }}>In Progress
                                </option>
                                <option value="closed"
                                    {{ old('status', $ticket->status) === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="categories" :value="__('Categories')" />
                            <div class="mt-2 space-y-2">
                                @foreach ($categories as $category)
                                    <div class="flex items-center">
                                        <input id="category_{{ $category->id }}" type="checkbox" name="categories[]"
                                            value="{{ $category->id }}"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            {{ in_array($category->id, old('categories', $ticket->categories->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <label for="category_{{ $category->id }}" class="ml-2 text-sm text-gray-700">
                                            {{ $category->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('categories')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="labels" :value="__('Labels')" />
                            <div class="mt-2 space-y-2">
                                @foreach ($labels as $label)
                                    <div class="flex items-center">
                                        <input id="label_{{ $label->id }}" type="checkbox" name="labels[]"
                                            value="{{ $label->id }}"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            {{ in_array($label->id, old('labels', $ticket->labels->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <label for="label_{{ $label->id }}" class="ml-2 text-sm text-gray-700">
                                            {{ $label->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('labels')" class="mt-2" />
                        </div>

                        
                        @if ($ticket->comments->count() > 0)
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                    <x-input-label for="comment" :value="__('Comments') . ' (' . $ticket->comments->count() . ')'" />
                                    {{-- <h4 class="text-md text-gray-800 mb-4">
                                        Comments ({{ $ticket->comments->count() }})
                                    </h4> --}}
                                    <div class="space-y-4">
                                        @foreach ($ticket->comments as $comment)
                                            <div class="border border-gray-200 rounded-lg p-4">
                                                <div class="flex justify-between items-start mb-2">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $comment->user->name }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $comment->created_at->format('M j, Y - g:i A') }}
                                                    </div>
                                                </div>
                                                <div class="text-gray-700 leading-relaxed">
                                                    {!! nl2br(e($comment->content)) !!}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                
                            </div>
                        @endif


                        <div>
                            <x-input-label for="comment" :value="__('Add Comment')" />
                            <textarea id="comment"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                name="comment" rows="3" placeholder="Add new comment for the ticket...">{{ old('comment') }}</textarea>
                            <x-input-error :messages="$errors->get('comment')" class="mt-2" />
                            <p class="mt-1 text-sm text-gray-600">This comment will be added to the ticket after updating.
                            </p>
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('tickets.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Cancel') }}
                            </a>

                            <x-primary-button class="ms-4">
                                {{ __('Edit Ticket') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
