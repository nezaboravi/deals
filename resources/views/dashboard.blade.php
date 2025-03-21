<x-layouts.app.sidebar :title="__('Dashboard')">
    <flux:main>
    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <!-- Stats Card - Total Deals -->
                        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 rounded-md bg-indigo-100 p-3 dark:bg-indigo-900/30">
                                        <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Total Deals</dt>
                                            <dd>
                                                <div class="text-lg font-medium text-gray-900 dark:text-white">{{ Auth::user()->deals()->count() }}</div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-5 py-3 dark:bg-gray-700">
                                <div class="text-sm">
                                    <a href="{{ route('dashboard.deals.index') }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-500 dark:hover:text-indigo-400">View all</a>
                                </div>
                            </div>
                        </div>

                        <!-- Stats Card - Published Deals -->
                        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 rounded-md bg-green-100 p-3 dark:bg-green-900/30">
                                        <svg class="h-6 w-6 text-green-600 dark:text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Published Deals</dt>
                                            <dd>
                                                <div class="text-lg font-medium text-gray-900 dark:text-white">{{ Auth::user()->deals()->whereNotNull('published_at')->count() }}</div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-5 py-3 dark:bg-gray-700">
                                <div class="text-sm">
                                    <a href="{{ route('dashboard.deals.index', ['status' => 'published']) }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-500 dark:hover:text-indigo-400">View published</a>
                                </div>
                            </div>
                        </div>

                        <!-- Stats Card - Total Votes -->
                        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 rounded-md bg-amber-100 p-3 dark:bg-amber-900/30">
                                        <svg class="h-6 w-6 text-amber-600 dark:text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.5c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75A2.25 2.25 0 0116.5 4.5c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 01-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 00-1.423-.23H5.904M14.25 9h2.25M5.904 18.75c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 01-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 10.203 4.167 9.75 5 9.75h1.053c.472 0 .745.556.5.96a8.958 8.958 0 00-1.302 4.665c0 1.194.232 2.333.654 3.375z" />
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Total Votes</dt>
                                            <dd>
                                                <div class="text-lg font-medium text-gray-900 dark:text-white">
                                                    {{ Auth::user()->deals()->sum('vote_count') }}
                                                </div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-5 py-3 dark:bg-gray-700">
                                <div class="text-sm">
                                    <a href="{{ route('dashboard.deals.index', ['sort' => 'votes']) }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-500 dark:hover:text-indigo-400">View by votes</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- My Deals Component -->
                    <livewire:dashboard.deals.index />
                </div>
            </div>
        </div>
    </div>
    </flux:main>
</x-layouts.app.sidebar>
