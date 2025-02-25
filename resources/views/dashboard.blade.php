<x-layouts.app>
    <div class="p-6 space-y-6">
        <!-- Members Metrics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Members -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 flex items-center">
                <div class="p-3 bg-indigo-500 rounded-full">
                    <!-- Heroicon: Users -->
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 20h5v-2a4 4 0 00-5-3.6M9 20H4v-2a4 4 0 015-3.6M17 11a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 dark:text-gray-400">Total Members</p>
                    <p class="text-2xl font-semibold text-gray-700 dark:text-gray-300">{{ $totalMembers }}</p>
                </div>
            </div>
            <!-- New Members This Month -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 flex items-center">
                <div class="p-3 bg-green-500 rounded-full">
                    <!-- Heroicon: User Add -->
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M18 9v6m3-3h-6m-5 4a4 4 0 110-8 4 4 0 010 8zm7-8v4M13 2v4m3-3h4m-4 0H9" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 dark:text-gray-400">New This Month</p>
                    <p class="text-2xl font-semibold text-gray-700 dark:text-gray-300">{{ $newMembers }}</p>
                </div>
            </div>
            <!-- Active Members -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 flex items-center">
                <div class="p-3 bg-yellow-500 rounded-full">
                    <!-- Heroicon: Check Circle -->
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 12l2 2l4-4" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 dark:text-gray-400">Active Members</p>
                    <p class="text-2xl font-semibold text-gray-700 dark:text-gray-300">{{ $activeMembers }}</p>
                </div>
            </div>
            <!-- Inactive Members -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 flex items-center">
                <div class="p-3 bg-red-500 rounded-full">
                    <!-- Heroicon: User Remove (using a simple minus icon) -->
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M18 12H6" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 dark:text-gray-400">Inactive Members</p>
                    <p class="text-2xl font-semibold text-gray-700 dark:text-gray-300">{{ $inactiveMembers }}</p>
                </div>
            </div>
        </div>

        <!-- Detailed Panels -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Member Demographics Chart -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-4">Members by Age Group</h2>
                <!-- Chart Container -->
                <canvas id="demographicsChart" class="h-64"></canvas>
            </div>
            <!-- Recent Registrations Table -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-4">Recent Registrations</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">Email</th>
                                <th class="px-4 py-2">Joined</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($recentRegistrations as $member)
                                <tr>
                                    <td class="px-4 py-2">{{ $member->name }}</td>
                                    <td class="px-4 py-2">{{ $member->email }}</td>
                                    <td class="px-4 py-2">{{ $member->created_at->format('Y-m-d') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Chart.js from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('demographicsChart').getContext('2d');
        // Convert demographics data from PHP to JavaScript
        const demographicsData = @json($demographics);
        const labels = demographicsData.map(item => item.age_group + 's');
        const dataCounts = demographicsData.map(item => item.total);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Members Count',
                    data: dataCounts,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</x-layouts.app>
