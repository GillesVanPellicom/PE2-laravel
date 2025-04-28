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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
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
            // Dark mode toggle
            const darkModeToggle = document.getElementById('darkModeToggle');
            const htmlElement = document.documentElement;
            const isDarkMode = localStorage.getItem('darkMode') === 'true';
            
            if (isDarkMode) {
                htmlElement.classList.add('dark');
                updateDarkModeButton(true);
            }
            
            darkModeToggle.addEventListener('click', function() {
                const isDark = htmlElement.classList.toggle('dark');
                localStorage.setItem('darkMode', isDark);
                updateDarkModeButton(isDark);
            });
            
            function updateDarkModeButton(isDark) {
                if (isDark) {
                    darkModeToggle.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>`;
                } else {
                    darkModeToggle.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>`;
                }
            }

            // Notifications
            const notificationDropdown = document.getElementById('notificationDropdown');
            const notificationBadge = document.getElementById('notificationBadge');
            const sickLeaveNotificationDropdown = document.getElementById('sickLeaveNotificationDropdown');
            const sickLeaveNotificationBadge = document.getElementById('sickLeaveNotificationBadge');

            window.toggleNotifications = function () {
                notificationDropdown.classList.toggle('hidden');
            };

            window.toggleSickLeaveNotifications = function () {
                sickLeaveNotificationDropdown.classList.toggle('hidden');
            };

            function fetchHolidayRequestNotifications() {
                fetch('/pending-vacations')
                    .then(response => response.json())
                    .then(data => {
                        notificationDropdown.innerHTML = '';
                        let unreadCount = 0;

                        if (data.length === 0) {
                            notificationDropdown.innerHTML = `
                                <div class="py-10 text-center">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full mx-auto flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400">No holiday requests available</p>
                                </div>
                            `;
                        } else {
                            const container = document.createElement('div');
                            container.className = "space-y-3 max-h-[60vh] overflow-y-auto pr-1";
                            
                            data.forEach(request => {
                                const requestElement = document.createElement('div');
                                requestElement.className = "p-4 bg-gray-50 dark:bg-gray-700 rounded-lg shadow border border-gray-100 dark:border-gray-600 hover:shadow-md transition-shadow";
                                requestElement.innerHTML = `
                                    <div class="flex items-start gap-3">
                                        <div class="bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 p-2 rounded-full">
                                            <span class="text-xl">🏖️</span>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">${request.employee_name}</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                                Requested holiday on ${request.start_date} 
                                                <span class="ml-1 px-1.5 py-0.5 bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-400 rounded text-xs">
                                                    ${request.day_type || 'Full Day'}
                                                </span>
                                            </p>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 mb-3">
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                                                    <span class="relative flex h-2 w-2 mr-1">
                                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-500 opacity-75"></span>
                                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-600"></span>
                                                    </span>
                                                    Pending
                                                </span>
                                            </div>
                                            <div class="flex gap-2">
                                                <button 
                                                    class="px-3 py-1 bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 text-white text-sm font-medium rounded"
                                                    onclick="updateVacationStatus(${request.id}, 'approved', this.parentNode.parentNode.parentNode)"
                                                >
                                                    Approve
                                                </button>
                                                <button 
                                                    class="px-3 py-1 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800 hover:bg-red-50 dark:hover:bg-red-900/30 text-sm font-medium rounded"
                                                    onclick="updateVacationStatus(${request.id}, 'rejected', this.parentNode.parentNode.parentNode)"
                                                >
                                                    Reject
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                container.appendChild(requestElement);
                                unreadCount++;
                            });
                            
                            notificationDropdown.appendChild(container);
                        }

                        notificationBadge.textContent = unreadCount;
                        notificationBadge.classList.toggle('hidden', unreadCount === 0);
                    })
                    .catch(error => console.error("Error fetching holiday request notifications:", error));
            }

            function fetchHolidayRequests() {
                fetch('/pending-vacations')
                    .then(response => response.json())
                    .then(data => {
                        const holidayRequestsContainer = document.getElementById('holidayRequests');
                        holidayRequestsContainer.innerHTML = "";

                        if (data.length === 0) {
                            holidayRequestsContainer.innerHTML = `
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    No holiday requests available.
                                </div>
                            `;
                            return;
                        }

                        data.forEach(request => {
                            const requestElement = document.createElement('div');
                            requestElement.className = "p-4 bg-gray-50 dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-between";
                            requestElement.innerHTML = `
                                <div>
                                    <div class="font-medium text-gray-800 dark:text-gray-200">${request.employee_name}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        ${request.start_date} to ${request.end_date} (${request.day_type || 'Full Day'})
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button 
                                        class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded"
                                        onclick="updateVacationStatus(${request.id}, 'approved', this.parentNode.parentNode)"
                                    >
                                        Approve
                                    </button>
                                    <button 
                                        class="px-3 py-1 text-red-600 border border-red-200 hover:bg-red-50 text-sm font-medium rounded"
                                        onclick="updateVacationStatus(${request.id}, 'rejected', this.parentNode.parentNode)"
                                    >
                                        Reject
                                    </button>
                                </div>
                            `;
                            holidayRequestsContainer.appendChild(requestElement);
                        });
                    })
                    .catch(error => console.error("Error fetching holiday requests:", error));
            }

            function fetchSickDayNotifications() {
                fetch('/manager/sick-day-notifications')
                    .then(response => response.json())
                    .then(data => {
                        sickLeaveNotificationDropdown.innerHTML = '';
                        let unreadCount = 0;

                        if (data.length === 0) {
                            sickLeaveNotificationDropdown.innerHTML = `
                                <div class="py-10 text-center">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full mx-auto flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400">No sick leave notifications available</p>
                                </div>
                            `;
                        } else {
                            const container = document.createElement('div');
                            container.className = "space-y-3 max-h-[60vh] overflow-y-auto pr-1";
                            
                            data.forEach(notification => {
                                const notificationElement = document.createElement('div');
                                notificationElement.className = "p-4 bg-gray-50 dark:bg-gray-700 rounded-lg shadow border border-gray-100 dark:border-gray-600 hover:shadow-md transition-shadow";
                                notificationElement.innerHTML = `
                                    <div class="flex items-start gap-3">
                                        <div class="bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 p-2 rounded-full">
                                            <span class="text-xl">🤒</span>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-gray-800 dark:text-gray-200">${notification.message}</p>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-2 mb-3 flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                ${moment(notification.created_at).fromNow()}
                                            </div>
                                            <button 
                                                class="w-full px-3 py-1 bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 text-white text-sm font-medium rounded"
                                                onclick="markSickLeaveAsRead(${notification.id}, this.parentNode.parentNode)"
                                            >
                                                Mark as Read
                                            </button>
                                        </div>
                                    </div>
                                `;
                                container.appendChild(notificationElement);
                                unreadCount++;
                            });
                            
                            sickLeaveNotificationDropdown.appendChild(container);
                        }

                        sickLeaveNotificationBadge.textContent = unreadCount;
                        sickLeaveNotificationBadge.classList.toggle('hidden', unreadCount === 0);
                    })
                    .catch(error => console.error("Error fetching sick day notifications:", error));
            }

            window.markSickLeaveAsRead = function(notificationId, notificationElement) {
                fetch(`/manager/sick-day-notifications/${notificationId}/read`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(() => {
                    notificationElement.remove();
                    let unreadCount = parseInt(sickLeaveNotificationBadge.textContent);
                    unreadCount = Math.max(0, unreadCount - 1);
                    sickLeaveNotificationBadge.textContent = unreadCount;
                    sickLeaveNotificationBadge.classList.toggle('hidden', unreadCount === 0);
                })
                .catch(error => console.error("Error marking sick leave notification as read:", error));
            };

            window.updateVacationStatus = function(vacationId, newStatus, notificationElement) {
                fetch(`/vacations/${vacationId}/update-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ status: newStatus })
                })
                .then(response => response.json())
                .then(() => {
                    notificationElement.remove();
                    let unreadCount = parseInt(notificationBadge.textContent);
                    unreadCount = Math.max(0, unreadCount - 1);
                    notificationBadge.textContent = unreadCount;
                    notificationBadge.classList.toggle('hidden', unreadCount === 0);
                    
                    // Update calendar after vacation status change
                    if (calendalInstance) {
                        fetchEvents();
                    }
                })
                .catch(error => console.error("Error updating vacation status:", error));
            };

            // Set current date
            const currentDateElement = document.getElementById('currentDate');
            const now = new Date();
            const formattedDate = now.toLocaleDateString('en-US', {
                weekday: 'long',
                month: 'long',
                day: 'numeric'
            });
            currentDateElement.textContent = formattedDate;

            // Sidebar employee lists
            const employeeList = document.getElementById('employeeList');
            const sickEmployeeList = document.getElementById('sickEmployeeList');
            const holidayEmployeeList = document.getElementById('holidayEmployeeList');
            const availableCount = document.getElementById('availableCount');
            const sickCount = document.getElementById('sickCount');
            const holidayCount = document.getElementById('holidayCount');

            function updateSidebar(date) {
                console.log("Clicked date:", date);
                
                // Update selected date display
                const selectedDate = new Date(date);
                currentDateElement.textContent = selectedDate.toLocaleDateString('en-US', {
                    weekday: 'long',
                    month: 'long',
                    day: 'numeric'
                });

                fetch('/approved-vacations')
                    .then(response => response.json())
                    .then(holidayData => {
                        let holidayEmployees = holidayData.filter(holiday =>
                            holiday.vacation_type !== 'Sick Leave' &&
                            date >= holiday.start_date && date <= holiday.end_date
                        );

                        displayHolidays(date, holidayEmployees);

                        fetch('/get-vacations')
                            .then(response => response.json())
                            .then(sickData => {
                                let sickEmployees = sickData.filter(vacation =>
                                    vacation.vacation_type === 'Sick Leave' &&
                                    date >= vacation.start_date && date <= vacation.end_date
                                );

                                displaySickLeaves(date, sickEmployees);
                                updateAvailableEmployees(holidayEmployees, sickEmployees);
                            })
                            .catch(error => console.error("Error fetching sick leaves:", error));
                    })
                    .catch(error => console.error("Error fetching approved vacations:", error));
            }

            function displayHolidays(date, holidayEmployees) {
                holidayEmployeeList.innerHTML = "";
                holidayCount.textContent = holidayEmployees.length;

                if (holidayEmployees.length > 0) {
                    holidayEmployees.forEach(holiday => {
                        const initials = holiday.name ? holiday.name.split(' ').map(n => n[0]).join('') : 'N/A';
                        
                        const div = document.createElement("div");
                        div.className = "px-3 py-2 bg-white/70 dark:bg-gray-800/70 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700/40 flex items-center";
                        div.innerHTML = `
                            <div class="w-7 h-7 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center mr-2 text-xs">
                                ${initials}
                            </div>
                            <span class="font-medium text-gray-800 dark:text-gray-200">${holiday.name} ${holiday.day_type ? `(${holiday.day_type})` : ''}</span>
                        `;
                        holidayEmployeeList.appendChild(div);
                    });
                } else {
                    const emptyState = document.createElement('div');
                    emptyState.className = "flex items-center justify-center h-12 text-gray-500 dark:text-gray-400 text-sm";
                    emptyState.innerHTML = `
                        <svg class="w-4 h-4 mr-1 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        No employees on holiday this day
                    `;
                    holidayEmployeeList.appendChild(emptyState);
                }
            }

            function displaySickLeaves(date, sickEmployees) {
                sickEmployeeList.innerHTML = "";
                sickCount.textContent = sickEmployees.length;

                if (sickEmployees.length > 0) {
                    sickEmployees.forEach(sick => {
                        const initials = sick.name ? sick.name.split(' ').map(n => n[0]).join('') : 'N/A';
                        
                        const div = document.createElement("div");
                        div.className = "px-3 py-2 bg-white/70 dark:bg-gray-800/70 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700/40 flex items-center";
                        div.innerHTML = `
                            <div class="w-7 h-7 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center mr-2 text-xs">
                                ${initials}
                            </div>
                            <span class="font-medium text-gray-800 dark:text-gray-200">${sick.name} ${sick.day_type ? `(${sick.day_type})` : ''}</span>
                        `;
                        sickEmployeeList.appendChild(div);
                    });
                } else {
                    const emptyState = document.createElement('div');
                    emptyState.className = "flex items-center justify-center h-12 text-gray-500 dark:text-gray-400 text-sm";
                    emptyState.innerHTML = `
                        <svg class="w-4 h-4 mr-1 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        No employees on sick leave this day
                    `;
                    sickEmployeeList.appendChild(emptyState);
                }
            }

            function updateAvailableEmployees(holidays, sickEmployees = []) {
                employeeList.innerHTML = "";
                const employees = @json($employees);
                
                const holidayNames = holidays.map(h => h.name);
                const sickNames = sickEmployees.map(s => s.name);
                
                const availableEmployees = employees.filter(emp => {
                    const fullName = `${emp.user.first_name} ${emp.user.last_name}`;
                    return !holidayNames.includes(fullName) && !sickNames.includes(fullName);
                });
                
                availableCount.textContent = availableEmployees.length;

                if (availableEmployees.length > 0) {
                    availableEmployees.forEach(emp => {
                        const fullName = `${emp.user.first_name} ${emp.user.last_name}`;
                        const initials = fullName.split(' ').map(n => n[0]).join('');
                        
                        const div = document.createElement("div");
                        div.className = "px-3 py-2 bg-white/70 dark:bg-gray-800/70 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700/40 flex items-center";
                        div.innerHTML = `
                            <div class="w-7 h-7 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center mr-2 text-xs">
                                ${initials}
                            </div>
                            <span class="font-medium text-gray-800 dark:text-gray-200">${fullName}</span>
                        `;
                        employeeList.appendChild(div);
                    });
                } else {
                    const emptyState = document.createElement('div');
                    emptyState.className = "flex items-center justify-center h-12 text-gray-500 dark:text-gray-400 text-sm";
                    emptyState.innerHTML = `
                        <svg class="w-4 h-4 mr-1 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        No available employees this day
                    `;
                    employeeList.appendChild(emptyState);
                }
            }

            // Chart setup
            const startDatePicker = document.getElementById('startDatePicker');
            const endDatePicker = document.getElementById('endDatePicker');
            const chartLoading = document.getElementById('chartLoading');
            let availabilityChart = null;

            // Date range buttons
            document.getElementById('7daysButton').addEventListener('click', function() {
                setDateRange('7d');
                updateButtonStyles('7daysButton');
            });
            
            document.getElementById('1monthButton').addEventListener('click', function() {
                setDateRange('1m');
                updateButtonStyles('1monthButton');
            });
            
            document.getElementById('3monthsButton').addEventListener('click', function() {
                setDateRange('3m');
                updateButtonStyles('3monthsButton');
            });
            
            function updateButtonStyles(activeButtonId) {
                // Reset all buttons
                ['7daysButton', '1monthButton', '3monthsButton'].forEach(id => {
                    const button = document.getElementById(id);
                    button.className = "text-xs px-2 py-1 rounded bg-gray-100 text-gray-600 font-medium hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600";
                });
                
                // Style active button
                document.getElementById(activeButtonId).className = "text-xs px-2 py-1 rounded bg-blue-100 text-blue-600 font-medium hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50";
            }
            
            function setDateRange(range) {
                const end = new Date();
                const start = new Date();
                
                switch(range) {
                    case '7d':
                        start.setDate(end.getDate() - 7);
                        break;
                    case '1m':
                        start.setMonth(end.getMonth() - 1);
                        break;
                    case '3m':
                        start.setMonth(end.getMonth() - 3);
                        break;
                }
                
                startDatePicker.value = start.toISOString().split('T')[0];
                endDatePicker.value = end.toISOString().split('T')[0];
                
                fetchAvailabilityData(startDatePicker.value, endDatePicker.value);
            }

            function fetchAvailabilityData(startDate, endDate) {
                if (!startDate || !endDate) {
                    console.warn("Please select both start and end dates.");
                    return;
                }

                chartLoading.style.display = 'flex';

                fetch(`/get-availability-data?start_date=${startDate}&end_date=${endDate}`)
                    .then(response => {
                        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                        return response.json();
                    })
                    .then(data => {
                        if (!Array.isArray(data)) throw new Error('Invalid data format');
                        updateChart(data);
                        
                        // Calculate and update stats
                        const totalDays = data.length;
                        document.getElementById('totalDays').textContent = totalDays;
                        
                        // Calculate percentages for employee availability
                        const totalAvailable = data.reduce((sum, day) => sum + day.available, 0);
                        const totalSick = data.reduce((sum, day) => sum + day.sick, 0);
                        const totalHoliday = data.reduce((sum, day) => sum + day.onHoliday, 0);
                        const total = totalAvailable + totalSick + totalHoliday;
                        
                        const availablePercentage = Math.round((totalAvailable / total) * 100) || 0;
                        const sickPercentage = Math.round((totalSick / total) * 100) || 0;
                        const holidayPercentage = Math.round((totalHoliday / total) * 100) || 0;
                        
                        document.getElementById('availablePercentage').textContent = `${availablePercentage}%`;
                        document.getElementById('sickPercentage').textContent = `${sickPercentage}%`;
                        document.getElementById('holidayPercentage').textContent = `${holidayPercentage}%`;
                        
                        chartLoading.style.display = 'none';
                    })
                    .catch(error => {
                        console.error('Error fetching availability data:', error);
                        chartLoading.style.display = 'none';
                    });
            }

            function updateChart(data) {
                const labels = data.map(day => {
                    // Format date to be more readable (e.g., "Apr 24")
                    const date = new Date(day.date);
                    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                });
                const availableData = data.map(day => day.available);
                const onHolidayData = data.map(day => day.onHoliday);
                const sickData = data.map(day => day.sick);

                if (availabilityChart) {
                    availabilityChart.destroy();
                }

                const ctx = document.getElementById('availabilityChart').getContext('2d');
                availabilityChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [
                            {
                                label: 'Available',
                                data: availableData,
                                backgroundColor: 'rgba(34, 197, 94, 0.7)',
                                borderColor: 'rgba(34, 197, 94, 1)',
                                borderRadius: 6,
                                borderWidth: 1,
                                stack: 'Stack 0'
                            },
                            {
                                label: 'On Holiday',
                                data: onHolidayData,
                                backgroundColor: 'rgba(234, 179, 8, 0.7)',
                                borderColor: 'rgba(234, 179, 8, 1)',
                                borderRadius: 6,
                                borderWidth: 1,
                                stack: 'Stack 0'
                            },
                            {
                                label: 'Sick Leave',
                                data: sickData,
                                backgroundColor: 'rgba(239, 68, 68, 0.7)',
                                borderColor: 'rgba(239, 68, 68, 1)',
                                borderRadius: 6,
                                borderWidth: 1,
                                stack: 'Stack 0'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                titleColor: '#374151',
                                bodyColor: '#374151',
                                borderColor: 'rgba(226, 232, 240, 1)',
                                borderWidth: 1,
                                cornerRadius: 10,
                                padding: 10,
                                boxPadding: 4,
                                usePointStyle: true,
                                callbacks: {
                                    label: function(context) {
                                        return `${context.dataset.label}: ${context.raw}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                stacked: true,
                                grid: {
                                    display: false
                                },
                                border: {
                                    display: false
                                },
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            },
                            y: {
                                stacked: true,
                                beginAtZero: true,
                                border: {
                                    display: false
                                },
                                grid: {
                                    color: 'rgba(226, 232, 240, 0.5)'
                                },
                                ticks: {
                                    precision: 0
                                }
                            }
                        },
                        onClick: function(event, elements) {
                            if (elements.length > 0) {
                                const index = elements[0].index;
                                const selectedDate = data[index].date;
                                updateSidebar(selectedDate);
                            }
                        }
                    }
                });
            }

            function onDateRangeChange() {
                const startDate = startDatePicker.value;
                const endDate = endDatePicker.value;
                fetchAvailabilityData(startDate, endDate);
            }

            startDatePicker.addEventListener('change', onDateRangeChange);
            endDatePicker.addEventListener('change', onDateRangeChange);

            // Calendar setup
            const calendarEl = document.getElementById('calendar');
            const calendarLoading = document.getElementById('calendarLoading');
            let calendalInstance = null;

            function fetchEvents() {
                // Show loading indicator
                calendarLoading.style.display = 'flex';

                // Fetch holiday and vacation data
                Promise.all([
                    fetch('/approved-vacations').then(res => res.json()).catch(() => []), // Default to empty array on error
                    fetch('/get-vacations').then(res => res.json()).catch(() => []) // Default to empty array on error
                ])
                .then(([holidayRequests, allVacations]) => {
                    // Ensure data is an array
                    holidayRequests = Array.isArray(holidayRequests) ? holidayRequests : [];
                    allVacations = Array.isArray(allVacations) ? allVacations : [];

                    // Format data for calendar
                    const calendarEvents = [
                        // Add holiday events
                        ...holidayRequests
                            .filter(vacation => vacation.vacation_type !== 'Sick Leave')
                            .map(vacation => ({
                                id: `holiday-${vacation.id}`,
                                title: `${vacation.employee_name} - Holiday`,
                                start: vacation.start_date,
                                end: vacation.end_date,
                                backgroundColor: 'rgba(234, 179, 8, 0.7)',
                                borderColor: 'rgb(234, 179, 8)',
                                textColor: 'rgb(161, 98, 7)',
                                extendedProps: {
                                    type: 'holiday',
                                    employeeName: vacation.employee_name
                                }
                            })),

                        // Add sick leave events
                        ...allVacations
                            .filter(vacation => vacation.vacation_type === 'Sick Leave')
                            .map(vacation => ({
                                id: `sick-${vacation.id}`,
                                title: `${vacation.employee_name} - Sick Leave`,
                                start: vacation.start_date,
                                end: vacation.end_date,
                                backgroundColor: 'rgba(239, 68, 68, 0.7)',
                                borderColor: 'rgb(239, 68, 68)',
                                textColor: 'rgb(185, 28, 28)',
                                extendedProps: {
                                    type: 'sick',
                                    employeeName: vacation.employee_name
                                }
                            }))
                    ];

                    // Initialize or update calendar
                    if (!calendalInstance) {
                        initializeCalendar(calendarEvents);
                    } else {
                        // Clear existing events and add new ones
                        calendalInstance.getEvents().forEach(event => event.remove());
                        calendarEvents.forEach(event => calendalInstance.addEvent(event));
                    }

                    // Hide loading indicator
                    calendarLoading.style.display = 'none';
                })
                .catch(error => {
                    console.error("Error fetching calendar data:", error);
                    calendarLoading.style.display = 'none';
                });
            }

            function initializeCalendar(events) {
                // Ensure events is an array
                events = Array.isArray(events) ? events : [];

                calendalInstance = new FullCalendar.Calendar(calendarEl, {
                    plugins: ['dayGrid', 'interaction'],
                    initialView: 'dayGridMonth',
                    events: events,
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,dayGridWeek'
                    },
                    height: 'auto',
                    dayMaxEvents: 3,
                    contentHeight: 400,
                    eventDisplay: 'block',
                    eventClick: function(info) {
                        const event = info.event;
                        const eventType = event.extendedProps.type;
                        const employeeName = event.extendedProps.employeeName;

                        // Show tooltip with event details
                        const tooltip = document.createElement('div');
                        tooltip.className = 'calendar-tooltip bg-white shadow-xl p-3 rounded-lg border border-gray-200 z-50 absolute';
                        tooltip.style.top = `${info.jsEvent.clientY + 10}px`;
                        tooltip.style.left = `${info.jsEvent.clientX + 10}px`;
                        tooltip.innerHTML = `
                            <div class="font-medium">${employeeName}</div>
                            <div class="text-sm ${eventType === 'holiday' ? 'text-yellow-600' : 'text-red-600'}">
                                ${eventType === 'holiday' ? 'On Holiday' : 'On Sick Leave'}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                ${new Date(event.start).toLocaleDateString()} to ${new Date(event.end).toLocaleDateString()}
                            </div>
                        `;

                        document.body.appendChild(tooltip);

                        // Remove tooltip after a delay
                        setTimeout(() => {
                            document.body.removeChild(tooltip);
                        }, 3000);
                    },
                    dateClick: function(info) {
                        updateSidebar(info.dateStr);
                    }
                });

                calendalInstance.render();
            }

            // Initialize the page
            fetchHolidayRequestNotifications();
            fetchHolidayRequests();
            fetchSickDayNotifications();
            
            // Set initial date range (7 days)
            const initialStartDate = moment().startOf('week').format('YYYY-MM-DD');
            const initialEndDate = moment().endOf('week').format('YYYY-MM-DD');
            startDatePicker.value = initialStartDate;
            endDatePicker.value = initialEndDate;
            fetchAvailabilityData(initialStartDate, initialEndDate);
            
            // Initialize calendar
            fetchEvents();
            
            // Initial sidebar update with current date
            updateSidebar(moment().format('YYYY-MM-DD'));
        });

        function filterHolidayRequests() {
            const searchInput = document.getElementById('searchHolidayRequests').value.toLowerCase();
            const requests = document.querySelectorAll('#holidayRequests > div');

            requests.forEach(request => {
                const employeeName = request.querySelector('.font-medium').textContent.toLowerCase();
                if (employeeName.includes(searchInput)) {
                    request.style.display = 'flex';
                } else {
                    request.style.display = 'none';
                }
            });
        } // comment
    </script>
</x-app-layout>