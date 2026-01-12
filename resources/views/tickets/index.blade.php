<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Tickets') }}
            </h2>
            @can('create')
            <x-primary-button onclick="window.location='{{ route('tickets.create') }}'">
                Create New Ticket
            </x-primary-button>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Tickets Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($tickets->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Title
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Priority
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Categories
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Comments
                                        </th>
                                        @if(Auth::user()->isAdmin())
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Agent
                                        </th>
                                        @endif
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Created
                                        </th>
                                        @can('agent-or-admin')
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($tickets as $ticket)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ route('tickets.show', $ticket) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 font-medium">
                                                    {{ $ticket->title }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    @if ($ticket->status === 'open') bg-red-100 text-red-800
                                                    @elseif($ticket->status === 'in_progress') bg-yellow-100 text-yellow-800
                                                    @else bg-green-100 text-green-800 @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    @if ($ticket->priority === 'high') bg-red-100 text-red-800
                                                    @elseif($ticket->priority === 'medium') bg-yellow-100 text-yellow-800
                                                    @else bg-green-100 text-green-800 @endif">
                                                    {{ ucfirst($ticket->priority) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($ticket->categories->count() > 0)
                                                    <div class="flex flex-wrap gap-1">
                                                        @foreach ($ticket->categories as $category)
                                                            <span
                                                                class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded">
                                                                {{ $category->name }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-gray-500 text-sm">No categories</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $ticket->comments->count() }}
                                            </td>
                                            @if(Auth::user()->isAdmin())
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if($ticket->agent)
                                                    <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded">
                                                        {{ $ticket->agent->name }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-500 text-sm">Unassigned</span>
                                                @endif
                                            </td>
                                            @endif
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $ticket->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    @can('update', $ticket)
                                                    <a href="{{ route('tickets.edit', $ticket) }}"
                                                        class="text-indigo-600 hover:text-indigo-900">
                                                        Edit
                                                    </a>
                                                    @endcan
                                                    @can('delete', $ticket)
                                                    <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" class="inline-block"
                                                        onsubmit="return confirm('Are you sure you want to delete this ticket?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-red-600 hover:text-red-900">
                                                            Delete
                                                        </button>
                                                    </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $tickets->links() }}
                        </div>


                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
