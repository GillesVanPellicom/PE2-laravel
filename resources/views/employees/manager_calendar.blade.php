<x-app-layout>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: 'rgb(59, 130, 246)',
                            '50': 'rgb(239, 246, 255)',
                            '100': 'rgb(219, 234, 254)',
                            '200': 'rgb(191, 219, 254)',
                            '300': 'rgb(147, 197, 253)',
                            '400': 'rgb(96, 165, 250)',
                            '500': 'rgb(59, 130, 246)',
                            '600': 'rgb(37, 99, 235)',
                            '700': 'rgb(29, 78, 216)',
                            '800': 'rgb(30, 64, 175)',
                            '900': 'rgb(30, 58, 138)',
                            '950': 'rgb(23, 37, 84)'
                        }
                    },
                    borderRadius: {
                        'xl': '0.75rem',
                        '2xl': '1rem',
                    }
                }
            }
        }
    </script>
    <style>
        /* Base styles */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }
        
        .dark {
            color-scheme: dark;
        }
        
        /* Custom Card Styles */
        .dashboard-card {
            @apply bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden transition-all hover:shadow-xl;
        }
        
        .card-header {
            @apply p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between;
        }
        
        /* Status Indicators */
        .status-badge {
            @apply text-xs font-medium px-2.5 py-0.5 rounded-full;
        }
        
        .status-available {
            @apply bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400;
        }
        
        .status-unavailable {
            @apply bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400;
        }
        
        .status-holiday {
            @apply bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400;
        }
        
        /* Calendar styles */
        .fc .fc-button-primary {
            @apply bg-blue-600 border-blue-600 hover:bg-blue-700 hover:border-blue-700;
        }
        
        .fc .fc-button-primary:not(:disabled).fc-button-active,
        .fc .fc-button-primary:not(:disabled):active {
            @apply bg-blue-700 border-blue-700 shadow-inner;
        }
        
        .fc .fc-daygrid-day.fc-day-today {
            @apply bg-blue-50 dark:bg-blue-900/20;
        }
        
        .fc .fc-col-header-cell-cushion,
        .fc .fc-daygrid-day-number {
            @apply text-gray-700 dark:text-gray-300;
        }
        
        /* Custom tooltip */
        .calendar-tooltip {
            position: fixed;
            max-width: 300px;
            z-index: 1000;
        }

        /* Employee item styles */
        .employee, .sick-employee, .holiday-employee {
            @apply p-3 my-1 bg-white/70 dark:bg-gray-800/70 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700/40 flex items-center;
        }
    </style>

    <!-- Header -->
    <header class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-30">
        <div class="container mx-auto px-4 flex justify-between items-center h-16">
            <!-- Logo and Title -->
            <div class="flex items-center space-x-3">
                <div class="bg-blue-100 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 5H7C5.89543 5 5 5.89543 5 7V19C5 20.1046 5.89543 21 7 21H17C18.1046 21 19 20.1046 19 19V7C19 5.89543 18.1046 5 17 5H15M9 5C9 6.10457 9.89543 7 11 7H13C14.1046 7 15 6.10457 15 5M9 5C9 3.89543 9.89543 3 11 3H13C14.1046 3 15 3.89543 15 5" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                        <path d="M12 12H15M12 16H15M9 12H9.01M9 16H9.01" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                    </svg>
                </div>
                <h1 class="text-xl font-bold tracking-tight">
                    <span class="text-blue-600">Employee</span> Manager
                </h1>
            </div>

            <!-- Right side controls -->
            <div class="flex items-center space-x-4">
                <!-- Dark mode toggle -->
                <button id="darkModeToggle" class="p-2 rounded-md text-gray-600 hover:bg-gray-100 focus:outline-none" aria-label="Toggle dark mode">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-6">
        <div class="flex flex-wrap gap-6">
            <!-- Sidebar -->
            <div class="w-full lg:w-1/4 dashboard-card">
                <div class="card-header">
                    <div class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                        <div class="flex items-center gap-2">
                            <div class="bg-blue-100 dark:bg-blue-900/30 p-1.5 rounded-md">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <span>Team Status</span>
                        </div>
                    </div>
                </div>
                
                <div class="px-5 py-2 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/40">
                    <div class="text-sm font-medium text-gray-600 dark:text-gray-400">
                        Status for
                    </div>
                    <div class="text-blue-600 dark:text-blue-400 font-medium" id="currentDate">
                        <!-- Date will be populated by JS -->
                    </div>
                </div>
                
                <div class="p-5 space-y-6">
                    <!-- Available Section -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-base font-medium text-green-600 dark:text-green-400 flex items-center gap-2">
                                <span class="text-lg">👨‍💼</span> Available
                            </h3>
                            <span class="status-badge status-available" id="availableCount">0</span>
                        </div>
                        
                        <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg shadow-sm min-h-[50px] border border-gray-100 dark:border-gray-700/40">
                            <div id="employeeList" class="space-y-2"></div>
                        </div>
                    </div>

                    <!-- Sick Section -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-base font-medium text-red-600 dark:text-red-400 flex items-center gap-2">
                                <span class="text-lg">🤒</span> Out Sick
                            </h3>
                            <span class="status-badge status-unavailable" id="sickCount">0</span>
                        </div>
                        
                        <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg shadow-sm min-h-[50px] border border-gray-100 dark:border-gray-700/40">
                            <div id="sickEmployeeList" class="space-y-2"></div>
                        </div>
                    </div>

                    <!-- Holiday Section -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-base font-medium text-yellow-600 dark:text-yellow-400 flex items-center gap-2">
                                <span class="text-lg">🏖️</span> On Holiday
                            </h3>
                            <span class="status-badge status-holiday" id="holidayCount">0</span>
                        </div>
                        
                        <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg shadow-sm min-h-[50px] border border-gray-100 dark:border-gray-700/40">
                            <div id="holidayEmployeeList" class="space-y-2"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1 space-y-6">
                <!-- Notification Icons -->
                <div class="flex justify-end space-x-6">
                    <!-- Holiday Notifications -->
                    <div class="notification-dropdown-container relative">
                        <button class="flex items-center justify-center w-10 h-10 rounded-full hover:bg-gray-100 text-amber-500 dark:hover:bg-gray-700 dark:text-amber-400" onclick="toggleNotifications()">
                            <span class="text-2xl">🔔</span>
                            <span id="notificationBadge" class="absolute -top-1 -right-1 bg-amber-500 text-white flex items-center justify-center w-5 h-5 p-0 text-xs font-bold shadow-lg animate-pulse rounded-full hidden">0</span>
                        </button>

                        <div id="notificationDropdown" class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 shadow-xl p-4 rounded-xl z-50 border border-gray-200 dark:border-gray-700 transition-all duration-200 transform origin-top-right hidden">
                            <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Holiday Requests</h3>
                                <button onclick="toggleNotifications()" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <div class="space-y-3 max-h-[60vh] overflow-y-auto pr-1">
                                <!-- Holiday notifications will be populated here -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sick Leave Notifications -->
                    <div class="notification-dropdown-container relative">
                        <button class="flex items-center justify-center w-10 h-10 rounded-full hover:bg-gray-100 text-red-500 dark:hover:bg-gray-700 dark:text-red-400" onclick="toggleSickLeaveNotifications()">
                            <span class="text-2xl">🤒</span>
                            <span id="sickLeaveNotificationBadge" class="absolute -top-1 -right-1 bg-red-500 text-white flex items-center justify-center w-5 h-5 p-0 text-xs font-bold shadow-lg animate-pulse rounded-full hidden">0</span>
                        </button>

                        <div id="sickLeaveNotificationDropdown" class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 shadow-xl p-4 rounded-xl z-50 border border-gray-200 dark:border-gray-700 transition-all duration-200 transform origin-top-right hidden">
                            <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Sick Leave Notifications</h3>
                                <button onclick="toggleSickLeaveNotifications()" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <div class="space-y-3 max-h-[60vh] overflow-y-auto pr-1">
                                <!-- Sick leave notifications will be populated here -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart Section -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <div class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                            <div class="flex items-center gap-2">
                                <div class="bg-blue-100 dark:bg-blue-900/30 p-1.5 rounded-md">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0-01-2-2z" />
                                    </svg>
                                </div>
                                <span>Team Availability Trends</span>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button id="7daysButton" class="text-xs px-2 py-1 rounded bg-blue-100 text-blue-600 font-medium hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50">7 Days</button>
                            <button id="1monthButton" class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-600 font-medium hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">1 Month</button>
                            <button id="3monthsButton" class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-600 font-medium hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">3 Months</button>
                        </div>
                    </div>
                    
                    <div class="px-6 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/40 grid grid-cols-3 gap-4">
                        <div class="bg-white dark:bg-gray-800 rounded-md p-3 shadow-sm border border-gray-100 dark:border-gray-700">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Available</div>
                            <div class="text-xl font-bold text-green-600 dark:text-green-400" id="availablePercentage">0%</div>
                            <div class="mt-1 flex items-center">
                                <span class="text-xs font-medium text-green-600 dark:text-green-400">+5.2%</span>
                                <svg class="w-3 h-3 ml-1 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                </svg>
                                <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">vs last period</span>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-md p-3 shadow-sm border border-gray-100 dark:border-gray-700">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Sick Leave</div>
                            <div class="text-xl font-bold text-red-600 dark:text-red-400" id="sickPercentage">0%</div>
                            <div class="mt-1 flex items-center">
                                <span class="text-xs font-medium text-red-600 dark:text-red-400">-2.3%</span>
                                <svg class="w-3 h-3 ml-1 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                                <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">vs last period</span>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-md p-3 shadow-sm border border-gray-100 dark:border-gray-700">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Holiday</div>
                            <div class="text-xl font-bold text-yellow-600 dark:text-yellow-400" id="holidayPercentage">0%</div>
                            <div class="mt-1 flex items-center">
                                <span class="text-xs font-medium text-green-600 dark:text-green-400">+1.7%</span>
                                <svg class="w-3 h-3 ml-1 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                </svg>
                                <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">vs last period</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:space-x-6 mb-6 space-y-4 md:space-y-0">
                            <div class="flex items-center space-x-2">
                                <label for="startDatePicker" class="text-sm font-medium text-gray-600 dark:text-gray-300">
                                    Start Date:
                                </label>
                                <input 
                                    type="date" 
                                    id="startDatePicker" 
                                    class="border border-gray-300 dark:border-gray-600 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-gray-200 text-sm"
                                />
                            </div>

                            <div class="flex items-center space-x-2">
                                <label for="endDatePicker" class="text-sm font-medium text-gray-600 dark:text-gray-300">
                                    End Date:
                                </label>
                                <input 
                                    type="date" 
                                    id="endDatePicker"
                                    class="border border-gray-300 dark:border-gray-600 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-gray-200 text-sm"
                                />
                            </div>
                        </div>

                        <div class="relative h-[380px] w-full">
                            <div id="chartLoading" class="absolute inset-0 flex flex-col items-center justify-center gap-3 z-10 bg-white dark:bg-gray-800 bg-opacity-80 dark:bg-opacity-80">
                                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600 dark:border-blue-400"></div>
                                <p class="text-gray-500 dark:text-gray-400 text-sm">Loading chart data...</p>
                            </div>
                            <canvas id="availabilityChart"></canvas>
                        </div>
                        
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-4 text-center" id="chartDaysInfo">
                            Showing employee availability data for <span id="totalDays">0</span> days
                        </div>
                    </div>
                </div>

                <!-- Holiday Requests Section -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <div class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                            <div class="flex items-center gap-2">
                                <div class="bg-yellow-100 dark:bg-yellow-900/30 p-1.5 rounded-md">
                                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <span>Holiday Requests</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-5 space-y-4">
                        <!-- Search Bar -->
                        <div class="relative">
                            <input 
                                type="text" 
                                id="searchHolidayRequests" 
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-gray-200 text-sm" 
                                placeholder="Search by employee name..."
                                oninput="filterHolidayRequests()"
                            />
                        </div>
                        <!-- Holiday Requests List -->
                        <div id="holidayRequests" class="space-y-3">
                            <!-- Holiday requests will be dynamically populated here -->
                        </div>
                    </div>
                </div>

                <!-- Calendar Section -->
                <div class="dashboard-card">
                    <div class="p-4 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <div class="flex items-center space-x-6">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Holiday</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Sick Leave</span>
                            </div>
                        </div>

                        <a href="employees/calendar" class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md px-4 py-2 font-medium dark:bg-blue-700 dark:hover:bg-blue-800">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            View Full Calendar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const notificationDropdown = document.getElementById('notificationDropdown');
            const sickLeaveNotificationDropdown = document.getElementById('sickLeaveNotificationDropdown');

            // Fix malformed SVG path
            const svgPaths = document.querySelectorAll('svg path');
            svgPaths.forEach(path => {
                if (path.getAttribute('d')?.includes('0-01-2')) {
                    path.setAttribute('d', path.getAttribute('d').replace('0-01-2', '0 0 1-2'));
                }
            });

            // Toggle notifications dropdown
            window.toggleNotifications = function () {
                notificationDropdown.classList.toggle('hidden');
            };

            // Toggle sick leave notifications dropdown
            window.toggleSickLeaveNotifications = function () {
                sickLeaveNotificationDropdown.classList.toggle('hidden');
            };

            // Close dropdowns when clicking outside
            document.addEventListener('click', function (event) {
                if (!event.target.closest('#notificationDropdown') &&
                    !event.target.closest('[onclick="toggleNotifications()"]')) {
                    notificationDropdown.classList.add('hidden');
                }

                if (!event.target.closest('#sickLeaveNotificationDropdown') &&
                    !event.target.closest('[onclick="toggleSickLeaveNotifications()"]')) {
                    sickLeaveNotificationDropdown.classList.add('hidden');
                }
            });

            const availableList = document.getElementById('employeeList');
            const sickList = document.getElementById('sickEmployeeList');
            const holidayList = document.getElementById('holidayEmployeeList');
            const availableCount = document.getElementById('availableCount');
            const sickCount = document.getElementById('sickCount');
            const holidayCount = document.getElementById('holidayCount');
            const currentDateElement = document.getElementById('currentDate');
            const chartLoading = document.getElementById('chartLoading');
            let availabilityChart;

            // Fetch employee data for a specific day
            function fetchEmployeeData(date) {
                fetch(`/workspace/get-unavailable-employees?date=${date}`)
                    .then(response => response.json())
                    .then(data => {
                        // Clear existing lists
                        availableList.innerHTML = '';
                        sickList.innerHTML = '';
                        holidayList.innerHTML = '';

                        // Populate available employees
                        if (data.available && data.available.length > 0) {
                            data.available.forEach(employee => {
                                availableList.innerHTML += `<div class="employee">${employee.name}</div>`;
                            });
                        } else {
                            availableList.innerHTML = '<div class="text-gray-500 text-sm">No available employees</div>';
                        }

                        // Populate sick employees
                        if (data.sick && data.sick.length > 0) {
                            data.sick.forEach(employee => {
                                sickList.innerHTML += `<div class="sick-employee">${employee.name}</div>`;
                            });
                        } else {
                            sickList.innerHTML = '<div class="text-gray-500 text-sm">No sick employees</div>';
                        }

                        // Populate employees on holiday
                        if (data.holiday && data.holiday.length > 0) {
                            data.holiday.forEach(employee => {
                                holidayList.innerHTML += `<div class="holiday-employee">${employee.name}</div>`;
                            });
                        } else {
                            holidayList.innerHTML = '<div class="text-gray-500 text-sm">No employees on holiday</div>';
                        }

                        // Update counts
                        availableCount.textContent = data.available ? data.available.length : 0;
                        sickCount.textContent = data.sick ? data.sick.length : 0;
                        holidayCount.textContent = data.holiday ? data.holiday.length : 0;

                        // Update the current date display
                        currentDateElement.textContent = new Date(date).toLocaleDateString('en-US', {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                        });
                    })
                    .catch(error => console.error("Error fetching employee data:", error));
            }

            // Update the chart with fetched data
            function updateChart(data) {
                const ctx = document.getElementById('availabilityChart').getContext('2d');
                const labels = data.map(day => new Date(day.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
                const availableData = data.map(day => day.available);
                const onHolidayData = data.map(day => day.onHoliday);
                const sickData = data.map(day => day.sick);

                if (availabilityChart) {
                    availabilityChart.destroy();
                }

                availabilityChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [
                            { label: 'Available', data: availableData, backgroundColor: 'rgba(34, 197, 94, 0.7)' },
                            { label: 'On Holiday', data: onHolidayData, backgroundColor: 'rgba(234, 179, 8, 0.7)' },
                            { label: 'Sick Leave', data: sickData, backgroundColor: 'rgba(239, 68, 68, 0.7)' }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'top' },
                            tooltip: { mode: 'index', intersect: false }
                        },
                        scales: {
                            x: { stacked: true },
                            y: { stacked: true, beginAtZero: true }
                        },
                        onClick: (event, elements) => {
                            if (elements.length > 0) {
                                const index = elements[0].index;
                                const date = data[index].date;
                                fetchEmployeeData(date); // Fetch and display employee data for the clicked date
                            }
                        }
                    }
                });
            }

            // Fetch availability data
            function fetchAvailabilityData(startDate, endDate) {
                chartLoading.style.display = 'flex';

                fetch(`/workspace/get-availability-data?start_date=${startDate}&end_date=${endDate}`)
                    .then(response => response.json())
                    .then(data => {
                        updateChart(data);

                        // Calculate percentages
                        const totalDays = data.length;
                        const totalAvailable = data.reduce((sum, day) => sum + day.available, 0);
                        const totalOnHoliday = data.reduce((sum, day) => sum + day.onHoliday, 0);
                        const totalSick = data.reduce((sum, day) => sum + day.sick, 0);
                        const totalEmployees = totalAvailable + totalOnHoliday + totalSick;

                        document.getElementById('availablePercentage').textContent = `${Math.round((totalAvailable / (totalEmployees || 1)) * 100)}%`;
                        document.getElementById('holidayPercentage').textContent = `${Math.round((totalOnHoliday / (totalEmployees || 1)) * 100)}%`;
                        document.getElementById('sickPercentage').textContent = `${Math.round((totalSick / (totalEmployees || 1)) * 100)}%`;

                        chartLoading.style.display = 'none';
                    })
                    .catch(error => {
                        console.error('Error fetching availability data:', error);
                        chartLoading.style.display = 'none';
                    });
            }

            // Update the date range when buttons are clicked
            document.getElementById('7daysButton').addEventListener('click', () => {
                const endDate = new Date();
                const startDate = new Date();
                startDate.setDate(endDate.getDate() - 7);

                startDatePicker.value = startDate.toISOString().split('T')[0];
                endDatePicker.value = endDate.toISOString().split('T')[0];
                fetchAvailabilityData(startDatePicker.value, endDatePicker.value);
            });

            document.getElementById('1monthButton').addEventListener('click', () => {
                const endDate = new Date();
                const startDate = new Date();
                startDate.setMonth(endDate.getMonth() - 1);

                startDatePicker.value = startDate.toISOString().split('T')[0];
                endDatePicker.value = endDate.toISOString().split('T')[0];
                fetchAvailabilityData(startDatePicker.value, endDatePicker.value);
            });

            document.getElementById('3monthsButton').addEventListener('click', () => {
                const endDate = new Date();
                const startDate = new Date();
                startDate.setMonth(endDate.getMonth() - 3);

                startDatePicker.value = startDate.toISOString().split('T')[0];
                endDatePicker.value = endDate.toISOString().split('T')[0];
                fetchAvailabilityData(startDatePicker.value, endDatePicker.value);
            });

            // Update notification counts for holiday requests and sick leave notifications
            function updateNotificationCounts() {
                fetch('/workspace/pending-vacations')
                    .then(response => response.json())
                    .then(data => {
                        const holidayBadge = document.getElementById('notificationBadge');
                        holidayBadge.textContent = data.length;
                        holidayBadge.classList.toggle('hidden', data.length === 0);
                    })
                    .catch(error => console.error('Error fetching holiday notifications:', error));

                fetch('/workspace/sick-leave-notifications')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        const sickLeaveBadge = document.getElementById('sickLeaveNotificationBadge');
                        sickLeaveBadge.textContent = data.length;
                        sickLeaveBadge.classList.toggle('hidden', data.length === 0);
                    })
                    .catch(error => console.error('Error fetching sick leave notifications:', error));
            }

            // Call updateNotificationCounts periodically
            setInterval(updateNotificationCounts, 30000); // Update every 30 seconds
            updateNotificationCounts(); // Initial call

            // Fetch pending vacation notifications
            function fetchPendingVacationNotifications() {
                fetch('/workspace/pending-vacations')
                    .then(response => response.json())
                    .then(data => {
                        const container = document.querySelector('#notificationDropdown .space-y-3');
                        const badge = document.getElementById('notificationBadge');
                        container.innerHTML = '';

                        if (data.length === 0) {
                            container.innerHTML = '<div class="text-gray-500 text-sm">No pending holiday requests</div>';
                            badge.classList.add('hidden');
                            return;
                        }

                        badge.textContent = data.length;
                        badge.classList.remove('hidden');

                        data.forEach(notification => {
                            const vacationType = notification.vacation_type || 'Holiday';
                            const dayType = notification.day_type || 'Whole Day';

                            const notificationItem = document.createElement('div');
                            notificationItem.className = 'p-3 bg-gray-50 dark:bg-gray-700 rounded-lg shadow-sm border border-gray-100 dark:border-gray-600';
                            notificationItem.innerHTML = `
                                <p class="text-gray-800 dark:text-gray-200 font-medium">
                                    ${notification.employee_name} requested ${vacationType} (${dayType})
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    From: ${notification.start_date} To: ${notification.end_date || notification.start_date}
                                </p>
                                <div class="flex space-x-2 mt-2">
                                    <button class="px-3 py-1 bg-green-500 text-white text-xs font-medium rounded-md hover:bg-green-600 transition" onclick="updateVacationStatus(${notification.id}, 'Approved')">Approve</button>
                                    <button class="px-3 py-1 bg-red-500 text-white text-xs font-medium rounded-md hover:bg-red-600 transition" onclick="updateVacationStatus(${notification.id}, 'Rejected')">Reject</button>
                                </div>
                            `;
                            container.appendChild(notificationItem);
                        });
                    })
                    .catch(error => console.error('Error fetching pending vacation notifications:', error));
            }

            // Make updateVacationStatus globally accessible
            window.updateVacationStatus = function (vacationId, status) {
                fetch(`/workspace/vacations/${vacationId}/update-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({ status }),
                })
                    .then(response => response.json())
                    .then(data => {
                        alert(data.message || 'Vacation status updated successfully.');
                        fetchPendingVacationNotifications(); // Refresh the notifications
                    })
                    .catch(error => console.error('Error updating vacation status:', error));
            };

            // Fetch sick leave notifications
            function fetchSickLeaveNotifications() {
                fetch('/workspace/sick-leave-notifications')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        const container = document.querySelector('#sickLeaveNotificationDropdown .space-y-3');
                        container.innerHTML = '';

                        if (data.length === 0) {
                            container.innerHTML = '<div class="text-gray-500 text-sm">No sick leave notifications</div>';
                            return;
                        }

                        data.forEach(notification => {
                            const notificationItem = document.createElement('div');
                            notificationItem.className = 'p-3 bg-gray-50 dark:bg-gray-700 rounded-lg shadow-sm border border-gray-100 dark:border-gray-600';
                            notificationItem.innerHTML = `
                                <p class="text-gray-800 dark:text-gray-200 font-medium">
                                    ${notification.message}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Submitted: ${new Date(notification.created_at).toLocaleString()}
                                </p>
                                <button class="mt-2 px-3 py-1 bg-blue-500 text-white text-xs font-medium rounded-md hover:bg-blue-600 transition" onclick="markSickLeaveAsRead(${notification.id})">Mark as Read</button>
                            `;
                            container.appendChild(notificationItem);
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching sick leave notifications:', error);
                        const container = document.querySelector('#sickLeaveNotificationDropdown .space-y-3');
                        container.innerHTML = '<div class="text-red-500 text-sm">Failed to load sick leave notifications.</div>';
                    });
            }

            // Make markSickLeaveAsRead globally accessible
            window.markSickLeaveAsRead = function (notificationId) {
                fetch(`/workspace/sick-leave-notifications/${notificationId}/mark-as-read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                })
                    .then(response => response.json())
                    .then(data => {
                        alert(data.message || 'Notification marked as read.');
                        fetchSickLeaveNotifications(); // Refresh the notifications
                    })
                    .catch(error => console.error('Error marking sick leave notification as read:', error));
            };

            // Fetch holiday requests
            function fetchHolidayRequests() {
                fetch('/workspace/pending-vacations')
                    .then(response => response.json())
                    .then(data => {
                        const container = document.getElementById('holidayRequests');
                        const searchInput = document.getElementById('searchHolidayRequests');
                        container.innerHTML = '';

                        if (data.length === 0) {
                            container.innerHTML = '<div class="text-gray-500 text-sm">No holiday requests found</div>';
                            return;
                        }

                        const renderRequests = (filteredData) => {
                            container.innerHTML = '';
                            filteredData.forEach(request => {
                                const vacationType = request.vacation_type || 'Holiday';
                                const dayType = request.day_type || 'Whole Day';

                                const requestItem = document.createElement('div');
                                requestItem.className = 'p-3 bg-gray-50 dark:bg-gray-700 rounded-lg shadow-sm border border-gray-100 dark:border-gray-600';
                                requestItem.innerHTML = `
                                    <p class="text-gray-800 dark:text-gray-200 font-medium">
                                        ${request.employee_name} requested ${vacationType} (${dayType})
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        From: ${request.start_date} To: ${request.end_date || request.start_date}
                                    </p>
                                    <div class="flex space-x-2 mt-2">
                                        <button class="px-3 py-1 bg-green-500 text-white text-xs font-medium rounded-md hover:bg-green-600 transition" onclick="updateVacationStatus(${request.id}, 'Approved')">Approve</button>
                                        <button class="px-3 py-1 bg-red-500 text-white text-xs font-medium rounded-md hover:bg-red-600 transition" onclick="updateVacationStatus(${request.id}, 'Rejected')">Reject</button>
                                    </div>
                                `;
                                container.appendChild(requestItem);
                            });
                        };

                        renderRequests(data);

                        searchInput.addEventListener('input', () => {
                            const searchTerm = searchInput.value.toLowerCase();
                            const filteredData = data.filter(request =>
                                request.employee_name.toLowerCase().includes(searchTerm)
                            );
                            renderRequests(filteredData);
                        });
                    })
                    .catch(error => console.error('Error fetching holiday requests:', error));
            }

            // Initialize the page
            const startDatePicker = document.getElementById('startDatePicker');
            const endDatePicker = document.getElementById('endDatePicker');
            const initialStartDate = new Date();
            initialStartDate.setDate(initialStartDate.getDate() - 7);
            startDatePicker.value = initialStartDate.toISOString().split('T')[0];
            endDatePicker.value = new Date().toISOString().split('T')[0];

            fetchAvailabilityData(startDatePicker.value, endDatePicker.value);

            startDatePicker.addEventListener('change', () => fetchAvailabilityData(startDatePicker.value, endDatePicker.value));
            endDatePicker.addEventListener('change', () => fetchAvailabilityData(startDatePicker.value, endDatePicker.value));

            document.querySelector('[onclick="toggleNotifications()"]').addEventListener('click', fetchPendingVacationNotifications);
            document.querySelector('[onclick="toggleSickLeaveNotifications()"]').addEventListener('click', fetchSickLeaveNotifications);

            // Fetch holiday requests on page load
            fetchHolidayRequests();
        });
    </script>
</x-app-layout>



