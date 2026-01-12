<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('User Details: ') . $user->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Users
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-4">User Information</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">ID</label>
                                    <div class="mt-1 text-sm text-gray-900">{{ $user->id }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Name</label>
                                    <div class="mt-1 text-sm text-gray-900">{{ $user->name }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <div class="mt-1 text-sm text-gray-900">{{ $user->email }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Role</label>
                                    <div class="mt-1">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($user->role === 'admin') bg-red-100 text-red-800 
                                            @elseif($user->role === 'agent') bg-green-100 text-green-800 
                                            @else bg-blue-100 text-blue-800 @endif">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email Verified At</label>
                                    <div class="mt-1 text-sm text-gray-900">
                                        {{ $user->email_verified_at ? $user->email_verified_at->format('M d, Y H:i:s') : 'Not verified' }}
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Created At</label>
                                    <div class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('M d, Y H:i:s') }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Updated At</label>
                                    <div class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('M d, Y H:i:s') }}</div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-4">User Statistics</h3>
                            
                            <div class="space-y-4">
                                @if($user->tickets())
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Total Tickets</label>
                                        <div class="mt-1 text-sm text-gray-900">{{ $user->tickets()->count() }}</div>
                                    </div>
                                @endif

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Account Status</label>
                                    <div class="mt-1">
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('users.edit', $user) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Edit User
                            </a>
                            
                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                            onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                        Delete User
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>