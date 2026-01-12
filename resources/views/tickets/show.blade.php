<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Ticket Details
                </h2>
                <p class="text-sm text-gray-600 mt-1">#{{{ $ticket->id }}} - {{ $ticket->title }}</p>
            </div>
            <div>
                <x-secondary-button onclick="window.location='{{ route('tickets.index') }}'">
                    ← Back to Tickets
                </x-secondary-button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Main Ticket Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif
                    <!-- Ticket Title and Description -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">{{ $ticket->title }}</h3>
                        <div class="text-gray-700 leading-relaxed mb-6">
                            {!! nl2br(e($ticket->description)) !!}
                        </div>
                    </div>

                    <!-- Ticket Metadata in 2 columns -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left metadata column -->
                        <div class="space-y-4">
                            <!-- Status -->
                            <div>
                                <label class="text-sm font-medium text-gray-500">Status</label>
                                <div class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($ticket->status === 'open') bg-green-100 text-green-800
                                        @elseif($ticket->status === 'in-progress') bg-yellow-100 text-yellow-800
                                        @elseif($ticket->status === 'closed') bg-gray-100 text-gray-800
                                        @else bg-blue-100 text-blue-800
                                        @endif">
                                        {{ ucfirst(str_replace('-', ' ', $ticket->status)) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Priority -->
                            <div>
                                <label class="text-sm font-medium text-gray-500">Priority</label>
                                <div class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($ticket->priority === 'high') bg-red-100 text-red-800
                                        @elseif($ticket->priority === 'medium') bg-orange-100 text-orange-800
                                        @elseif($ticket->priority === 'low') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($ticket->priority) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Created By -->
                            <div>
                                <label class="text-sm font-medium text-gray-500">Created By</label>
                                <div class="mt-1 text-gray-900">
                                    {{ $ticket->user->name }}
                                </div>
                            </div>
                        </div>

                        <!-- Right metadata column -->
                        <div class="space-y-4">
                            <!-- Assigned Agent -->
                            <div>
                                <label class="text-sm font-medium text-gray-500">Assignee</label>
                                <div class="mt-1 text-gray-900">
                                    @if($ticket->agent)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                            {{ $ticket->agent->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 italic">Unassigned</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Created Date -->
                            <div>
                                <label class="text-sm font-medium text-gray-500">Created</label>
                                <div class="mt-1 text-gray-900">
                                    {{ $ticket->created_at->format('M j, Y - g:i A') }}
                                </div>
                            </div>

                            <!-- Updated Date -->
                            @if($ticket->updated_at != $ticket->created_at)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Last Updated</label>
                                <div class="mt-1 text-gray-900">
                                    {{ $ticket->updated_at->format('M j, Y - g:i A') }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Categories and Labels -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Categories -->
                            <div>
                                <label class="text-sm font-medium text-gray-500 block mb-2">Categories</label>
                                @if($ticket->categories->count() > 0)
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($ticket->categories as $category)
                                            <span class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-blue-100 text-blue-800">
                                                {{ $category->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">No categories assigned</span>
                                @endif
                            </div>

                            <!-- Labels -->
                            <div>
                                <label class="text-sm font-medium text-gray-500 block mb-2">Labels</label>
                                @if($ticket->labels->count() > 0)
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($ticket->labels as $label)
                                            <span class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-purple-100 text-purple-800">
                                                {{ $label->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">No labels assigned</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Attachments -->
                    @if($ticket->attachments->count() > 0 )
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">
                            Attachments ({{ $ticket->attachments->count() }})
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($ticket->attachments as $attachment)
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-gray-300 transition-colors">
                                    <div class="flex items-start space-x-3">
                                        <!-- File Icon -->
                                        <div class="flex-shrink-0">
                                            @if(str_starts_with($attachment->file_type, 'image/'))
                                                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            @elseif($attachment->file_type === 'application/pdf')
                                                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                            @elseif(in_array($attachment->file_type, ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']))
                                                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            @elseif(in_array($attachment->file_type, ['application/zip', 'application/x-rar-compressed']))
                                                <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                </svg>
                                            @else
                                                <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            @endif
                                        </div>
                                        
                                        <!-- File Info -->
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate" title="{{ $attachment->filename }}">
                                                {{ $attachment->filename }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $attachment->formatted_file_size }}
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div>
                                         
                                   
                                    <div class="mt-3 flex space-x-2">
                                        @if(str_starts_with($attachment->file_type, 'image/'))
                                            <a href="{{ $attachment->cloudinary_secure_url }}" 
                                               target="_blank"
                                               class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                View
                                            </a>
                                        @endif
                                        
                                        
                                        <a href="{{ route('tickets.attachments.download', $attachment) }}"
                                           class="inline-flex items-center px-3 py-1 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Download
                                        </a>
                                    </div>
                                     </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                     
                    <!-- Admin Actions -->
                    @if(Auth::user()->isAdmin())
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Admin Actions</h4>
                        
                        <!-- Assign Agent Form -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <form method="POST" action="{{ route('tickets.assign', $ticket) }}" class="flex items-end space-x-4">
                                @csrf
                                @method('PATCH')
                                
                                <div class="flex-1">
                                    <label for="agent_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Assign to Agent
                                    </label>
                                    <select name="agent_id" id="agent_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">-- Unassign --</option>
                                        @foreach($agents as $agent)
                                            <option value="{{ $agent->id }}" {{ $ticket->agent_id == $agent->id ? 'selected' : '' }}>
                                                {{ $agent->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div>
                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                        Update Assignment
                                    </button>
                                </div>
                            </form>
                            
                            @error('agent_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Comments Section -->
            @if($ticket->comments->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">
                        Comments ({{ $ticket->comments->count() }})
                    </h4>
                    <div class="space-y-4">
                        @foreach($ticket->comments as $comment)
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
            </div>
            @endif

            <!-- Activity Log (only for ticket owner) -->
            @if(auth()->id() === $ticket->user_id && $ticket->logs->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">
                        Activity Log
                    </h4>
                    <div class="space-y-3">
                        @foreach($ticket->logs as $log)
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-2 h-2 bg-blue-400 rounded-full mt-2"></div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-700">{{ $log->description ?? $log->action }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Add Comment Form (if user is authorized) -->
            @if(auth()->check())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Add Comment</h4>
                    <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}">
                        @csrf
                        <div class="mb-4">
                            <x-input-label for="content" :value="__('Your Comment')" />
                            <textarea 
                                id="content" 
                                name="content" 
                                rows="4" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Enter your comment here..."
                                required>{{ old('content') }}</textarea>
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>
                        <div class="flex justify-end">
                            <x-primary-button>
                                Add Comment
                            </x-primary-button>
                        </div>
                    </form>
                    {{-- <form action="{{ route('tickets.comments.store', $ticket) }}" method="POST">
                        @csrf
                        <textarea name="content" id="" rows="4" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        <button type="submit" >Add comment</button>
                    </form> --}}
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
