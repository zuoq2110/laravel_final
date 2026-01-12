<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ticket Activity Logs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Filters</h3>
                    <form method="GET" action="{{ route('logs.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <!-- Ticket Filter -->
                        <div>
                            <x-input-label for="ticket_id" :value="__('Ticket')" />
                            <select id="ticket_id" name="ticket_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">All Tickets</option>
                                @foreach($tickets as $ticket)
                                    <option value="{{ $ticket->id }}" {{ request('ticket_id') == $ticket->id ? 'selected' : '' }}>
                                        #{{ $ticket->id }} - {{ Str::limit($ticket->title, 30) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- User Filter -->
                        <div>
                            <x-input-label for="user_id" :value="__('User')" />
                            <select id="user_id" name="user_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Action Filter -->
                        <div>
                            <x-input-label for="action" :value="__('Action')" />
                            <x-text-input id="action" class="block mt-1 w-full" type="text" name="action" :value="request('action')" placeholder="e.g. created, updated" />
                        </div>

                        <!-- Date From -->
                        <div>
                            <x-input-label for="date_from" :value="__('Date From')" />
                            <x-text-input id="date_from" class="block mt-1 w-full" type="date" name="date_from" :value="request('date_from')" />
                        </div>

                        <!-- Date To -->
                        <div>
                            <x-input-label for="date_to" :value="__('Date To')" />
                            <x-text-input id="date_to" class="block mt-1 w-full" type="date" name="date_to" :value="request('date_to')" />
                        </div>

                        <!-- Filter Button -->
                        <div class="md:col-span-5 flex items-end space-x-2">
                            <x-primary-button type="submit">
                                {{ __('Filter') }}
                            </x-primary-button>
                            <a href="{{ route('logs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Clear') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Logs Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date & Time
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ticket
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        User
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Action
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Description
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($logs as $log)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div class="flex flex-col">
                                                <span class="font-medium">{{ $log->created_at ? $log->created_at->format('M d, Y') : 'N/A' }}</span>
                                                <span class="text-xs text-gray-500">{{ $log->created_at ? $log->created_at->format('H:i:s') : 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($log->ticket)
                                                <div class="flex flex-col">
                                                    <a href="{{ route('tickets.show', $log->ticket) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                                        #{{ $log->ticket->id }}
                                                    </a>
                                                    <span class="text-xs text-gray-500">{{ Str::limit($log->ticket->title, 40) }}</span>
                                                </div>
                                            @else
                                                <span class="text-gray-500">Ticket Deleted</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($log->user)
                                                <div class="flex flex-col">
                                                    <span class="font-medium">{{ $log->user->name }}</span>
                                                    <span class="text-xs text-gray-500">{{ $log->user->email }}</span>
                                                </div>
                                            @else
                                                <span class="text-gray-500">User Deleted</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                @if(Str::contains($log->action, ['created', 'opened'])) bg-green-100 text-green-800
                                                @elseif(Str::contains($log->action, ['updated', 'modified', 'changed'])) bg-yellow-100 text-yellow-800
                                                @elseif(Str::contains($log->action, ['closed', 'completed'])) bg-blue-100 text-blue-800
                                                @elseif(Str::contains($log->action, ['deleted', 'removed'])) bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $log->action }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                @if(Str::contains($log->action, ['created', 'opened'])) bg-green-100 text-green-800
                                                @elseif(Str::contains($log->action, ['updated', 'modified', 'changed'])) bg-yellow-100 text-yellow-800
                                                @elseif(Str::contains($log->action, ['closed', 'completed'])) bg-blue-100 text-blue-800
                                                @elseif(Str::contains($log->action, ['deleted', 'removed'])) bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $log->description }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                            No logs found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $logs->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>