<x-app-layout>
    <x-sidebar>
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
    
    <!-- Animate.css for animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <!-- Swiper for carousels -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
    
    <!-- Three.js for 3D effects -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    
    <style>
        /* Base Variables */
        :root {
            --primary: #4f46e5;
            --primary-light: #818cf8;
            --primary-dark: #4338ca;
            --secondary: #06b6d4;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            
            /* Light theme colors */
            --bg-main: #f9fafb;
            --bg-card: #ffffff;
            --bg-card-secondary: #f3f4f6;
            --text-primary: #1f2937;
            --text-secondary: #4b5563;
            --text-muted: #9ca3af;
            --border-light: #e5e7eb;
            --border-medium: #d1d5db;
            
            /* Dark theme colors - will be applied with .dark class */
            --dark-bg-main: #111827;
            --dark-bg-card: #1f2937;
            --dark-bg-card-secondary: #374151;
            --dark-text-primary: #f9fafb;
            --dark-text-secondary: #e5e7eb;
            --dark-text-muted: #9ca3af;
            --dark-border-light: #374151;
            --dark-border-medium: #4b5563;
            
            /* Holiday colors */
            --holiday-whole: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%);
            --holiday-first: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%);
            --holiday-second: linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%);
            --sick-leave: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
            
            /* Status colors */
            --status-approved: #10b981;
            --status-pending: #f59e0b;
            --status-rejected: #ef4444;
            
            /* Animation durations */
            --animate-duration: 0.5s;
            
            /* 3D perspective */
            --perspective: 2000px;
        }
        
        /* Apply dark mode at the :root level with class .dark */
        .dark {
            --bg-main: var(--dark-bg-main);
            --bg-card: var(--dark-bg-card);
            --bg-card-secondary: var(--dark-bg-card-secondary);
            --text-primary: var(--dark-text-primary);
            --text-secondary: var(--dark-text-secondary);
            --text-muted: var(--dark-text-muted);
            --border-light: var(--dark-border-light);
            --border-medium: var(--dark-border-medium);
            color-scheme: dark;
        }
        
        /* Base styles */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            transition: background-color 0.5s, color 0.5s;
            background-color: var(--bg-main);
            color: var(--text-primary);
        }
        
        /* Custom Card Styles */
        .dashboard-card {
            background-color: var(--bg-card);
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-light);
            overflow: hidden;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            transform: perspective(var(--perspective)) translateZ(0);
        }
        
        .dashboard-card:hover {
            box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.2);
            transform: perspective(var(--perspective)) translateZ(10px);
        }
        
        .card-header {
            padding: 1.25rem;
            border-bottom: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: var(--bg-card-secondary);
            background-image: linear-gradient(to right, rgba(255,255,255,0.05), rgba(255,255,255,0.2));
        }
        
        /* Enhanced Glassmorphism effect */
        .glassmorphism {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 
                0 8px 32px rgba(31, 38, 135, 0.15),
                inset 0 0 0 1px rgba(255, 255, 255, 0.07);
            border-radius: 1rem;
        }
        
        .dark .glassmorphism {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, 0.3),
                inset 0 0 0 1px rgba(255, 255, 255, 0.03);
        }
        
        /* Advanced Gradient backgrounds */
        .gradient-bg {
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradient-shift 15s ease infinite;
        }
        
        .dark .gradient-bg {
            background: linear-gradient(-45deg, #4a1d1d, #5e1954, #0d3b4d, #0c4a3a);
            background-size: 400% 400%;
            animation: gradient-shift 15s ease infinite;
        }
        
        @keyframes gradient-shift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        /* Morphing background */
        .morphing-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.5;
        }
        
        /* Holiday/Sick day cards with enhanced gradients */
        .holiday-card {
            background: var(--holiday-whole);
            color: white;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
            box-shadow: 
                0 10px 15px -3px rgba(255, 154, 158, 0.3),
                0 4px 6px -4px rgba(255, 154, 158, 0.2);
        }
        
        .holiday-first-card {
            background: var(--holiday-first);
            color: white;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
            box-shadow: 
                0 10px 15px -3px rgba(161, 140, 209, 0.3),
                0 4px 6px -4px rgba(161, 140, 209, 0.2);
        }
        
        .holiday-second-card {
            background: var(--holiday-second);
            color: white;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
            box-shadow: 
                0 10px 15px -3px rgba(166, 193, 238, 0.3),
                0 4px 6px -4px rgba(166, 193, 238, 0.2);
        }
        
        .sick-leave-card {
            background: var(--sick-leave);
            color: white;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
            box-shadow: 
                0 10px 15px -3px rgba(132, 250, 176, 0.3),
                0 4px 6px -4px rgba(132, 250, 176, 0.2);
        }
        
        /* Status badge styling */
        .status-badge {
            @apply text-xs font-bold px-2.5 py-0.5 rounded-full inline-flex items-center justify-center;
            transition: all 0.3s ease;
        }
        
        .status-badge:hover {
            transform: scale(1.05);
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
        
        /* Enhanced Button styles */
        .btn {
            @apply px-4 py-2 rounded-xl font-medium relative overflow-hidden;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .btn:hover {
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.2);
        }
        
        .btn:active {
            transform: translateY(0) scale(0.98);
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.2),
                transparent
            );
            transition: 0.5s;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn-primary {
            @apply bg-indigo-600 hover:bg-indigo-700 text-white focus:ring-indigo-500;
            box-shadow: 
                0 4px 6px -1px rgba(79, 70, 229, 0.2),
                0 2px 4px -2px rgba(79, 70, 229, 0.1);
        }
        
        .btn-success {
            @apply bg-green-500 hover:bg-green-600 text-white focus:ring-green-500;
            box-shadow: 
                0 4px 6px -1px rgba(16, 185, 129, 0.2),
                0 2px 4px -2px rgba(16, 185, 129, 0.1);
        }
        
        .btn-danger {
            @apply bg-red-500 hover:bg-red-600 text-white focus:ring-red-500;
            box-shadow: 
                0 4px 6px -1px rgba(239, 68, 68, 0.2),
                0 2px 4px -2px rgba(239, 68, 68, 0.1);
        }
        
        .btn-warning {
            @apply bg-yellow-500 hover:bg-yellow-600 text-white focus:ring-yellow-500;
            box-shadow: 
                0 4px 6px -1px rgba(245, 158, 11, 0.2),
                0 2px 4px -2px rgba(245, 158, 11, 0.1);
        }
        
        .btn-info {
            @apply bg-blue-500 hover:bg-blue-600 text-white focus:ring-blue-500;
            box-shadow: 
                0 4px 6px -1px rgba(59, 130, 246, 0.2),
                0 2px 4px -2px rgba(59, 130, 246, 0.1);
        }
        
        .btn-ghost {
            @apply bg-gray-200 hover:bg-gray-300 text-gray-700 focus:ring-gray-500;
            box-shadow: 
                0 4px 6px -1px rgba(107, 114, 128, 0.1),
                0 2px 4px -2px rgba(107, 114, 128, 0.05);
        }
        
        /* Enhanced Calendar styling */
        .fc .fc-toolbar-title {
            color: var(--text-primary);
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .fc .fc-button-primary {
            @apply bg-indigo-600 border-indigo-600 shadow-md hover:bg-indigo-700 hover:border-indigo-700 focus:bg-indigo-700 focus:shadow-outline-indigo active:bg-indigo-800;
        }
        
        .fc .fc-daygrid-day.fc-day-today {
            background-color: rgba(79, 70, 229, 0.1);
        }
        
        .dark .fc .fc-daygrid-day.fc-day-today {
            background-color: rgba(79, 70, 229, 0.2);
        }
        
        .fc-day-today .fc-daygrid-day-number {
            @apply bg-indigo-600 text-white w-8 h-8 rounded-full flex items-center justify-center;
        }
        
        .fc .fc-daygrid-day-frame {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .fc .fc-daygrid-day-frame:hover {
            background-color: rgba(0, 0, 0, 0.03);
            transform: scale(1.02);
        }
        
        .dark .fc .fc-daygrid-day-frame:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        .fc th {
            font-weight: 600;
            color: var(--text-secondary);
            padding: 0.75rem;
        }
        
        .fc-day-sat, .fc-day-sun {
            background-color: rgba(0, 0, 0, 0.02);
        }
        
        .dark .fc-day-sat, .dark .fc-day-sun {
            background-color: rgba(255, 255, 255, 0.02);
        }
        
        .fc .fc-event {
            border-radius: 0.375rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: none;
            transition: all 0.3s ease;
        }
        
        .fc .fc-event:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        /* Fix for day names in dark mode */
        .dark .fc .fc-col-header-cell-cushion {
            color: var(--dark-text-primary); /* Use primary text color for better readability */
        }

        /* Enhanced Tooltip styling */
        .tooltip {
            @apply invisible absolute;
            width: max-content;
            max-width: 250px;
            z-index: 50;
            transform: translateY(10px);
            opacity: 0;
            transition: all 0.3s ease;
        }
        
        .has-tooltip:hover .tooltip {
            @apply visible;
            transform: translateY(0);
            opacity: 1;
        }
        
        /* Modal animation */
        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease forwards;
        }
        
        .modal-content {
            animation: slideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            transform-origin: center bottom;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideUp {
            from {
                transform: translateY(50px) scale(0.95);
                opacity: 0;
            }
            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }
        
        /* Enhanced Bell animation */
        .bell-pulse {
            animation: bell-ring 2s cubic-bezier(.36,.07,.19,.97) infinite;
            transform-origin: top;
        }
        
        @keyframes bell-ring {
            0%, 100% {
                transform: rotate(0);
            }
            2%, 8% {
                transform: rotate(15deg);
            }
            4%, 6%, 10% {
                transform: rotate(-15deg);
            }
        }
        
        /* Enhanced Confetti animation */
        .confetti {
            position: absolute;
            animation: fall 5s ease-in-out -2s infinite;
            opacity: 0;
        }
        
        @keyframes fall {
            0% {
                transform: translateY(0) rotateZ(0);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            35% {
                transform: translateY(-50vh) rotateZ(720deg);
                opacity: 0;
            }
            100% {
                opacity: 0;
            }
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }
        
        .dark ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }
        
        ::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }
        
        .dark ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.3);
        }
        
        .dark ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        /* Stat cards with 3D hover effects */
        .stat-card {
            border-radius: 1rem;
            overflow: hidden;
            position: relative;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 
                0 10px 25px -5px rgba(0, 0, 0, 0.1),
                0 10px 10px -5px rgba(0, 0, 0, 0.04);
            transform-style: preserve-3d;
            transform: perspective(1000px) translateZ(0) rotateX(0) rotateY(0);
        }
        
        .stat-card-content {
            transform-style: preserve-3d;
            transform: translateZ(20px);
        }
        
        .stat-card:hover {
            transform: perspective(1000px) translateZ(10px) rotateX(2deg) rotateY(2deg);
            box-shadow: 
                0 20px 30px -10px rgba(0, 0, 0, 0.2),
                0 10px 15px -5px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card-bg-1 {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        }
        
        .stat-card-bg-2 {
            background: linear-gradient(135deg, #f83600 0%, #f9d423 100%);
        }
        
        /* Floating elements */
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        /* Glow effect */
        .glow {
            position: relative;
        }
        
        .glow::after {
            content: "";
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            background: inherit;
            filter: blur(20px);
            opacity: 0.4;
            z-index: -1;
            border-radius: inherit;
            transition: all 0.3s ease;
        }
        
        .glow:hover::after {
            opacity: 0.7;
            filter: blur(30px);
        }
    </style>

    <!-- Animated background -->
    <div id="bg-canvas" class="morphing-bg"></div>

    <!-- Main content -->
    <div class="min-h-screen p-4 lg:p-8 relative overflow-hidden">
        <!-- Top Navigation Bar -->
        <div class="glassmorphism sticky top-4 z-40 mx-auto max-w-7xl mb-8">
            <div class="py-3 px-6">
                <div class="flex justify-between items-center">
                    <!-- Logo and Title -->
                    <div class="flex items-center">
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-2 rounded-lg shadow-lg mr-3 transform transition-transform hover:rotate-12">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                            Employee Time Off Portal
                        </h1>
                    </div>
                    
                    <!-- Controls -->
                    <div class="flex items-center space-x-4">
                        <!-- Theme toggle button with sun/moon animation -->
                        <button id="darkModeToggle" class="p-3 rounded-full glassmorphism hover:shadow-lg transition-all">
                            <div class="relative w-6 h-6 flex items-center justify-center">
                                <!-- Sun (light mode) -->
                                <svg id="sunIcon" class="w-6 h-6 text-yellow-500 absolute transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <!-- Moon (dark mode) -->
                                <svg id="moonIcon" class="w-6 h-6 text-blue-300 absolute transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                </svg>
                            </div>
                        </button>
                        
                        <!-- Notification bell -->
                        <div class="relative">
                            <button id="toggleNotifications" class="p-3 rounded-full glassmorphism hover:shadow-lg transition-all">
                                <span class="bell-pulse inline-block">
                                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </span>
                                <span id="notificationBadge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-5 h-5 flex items-center justify-center rounded-full shadow-lg hidden">0</span>
                            </button>

                            <div id="notificationDropdown" class="absolute right-0 mt-3 w-80 glassmorphism hidden z-50 animate__animated animate__fadeInDown">
                                <div class="p-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                                    <h3 class="font-semibold text-gray-800 dark:text-white">Notifications</h3>
                                    <button class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300" onclick="document.getElementById('notificationDropdown').classList.add('hidden')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="space-y-3">
                                    <!-- Notifications will be dynamically populated here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
        <!-- Main content area -->
        <div class="max-w-7xl mx-auto">
            <!-- Stats row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="dashboard-card p-6 overflow-visible">
                    <div class="card-header -mx-6 -mt-6 mb-6 bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
                        <h2 class="text-xl font-bold">
                            Time Off Overview
                        </h2>
                        <div>
                            <button id="saveRequests" class="btn btn-primary shadow-lg border border-white/20">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                Save Requests
                            </button>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="stat-card stat-card-bg-1 p-5 text-white glow">
                            <div class="stat-card-content">
                                <div class="text-sm opacity-75">Holidays Left</div>
                                <div class="flex items-baseline mt-2">
                                    <div class="text-4xl font-bold" id="remainingHolidays">0</div>
                                    <div class="text-sm ml-2 opacity-75">days</div>
                                </div>
                                <div class="absolute top-3 right-3 opacity-30 floating">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="w-full bg-white/20 h-1 rounded-full mt-4">
                                    <div class="bg-white h-1 rounded-full" style="width: 65%"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="stat-card stat-card-bg-2 p-5 text-white glow">
                            <div class="stat-card-content">
                                <div class="text-sm opacity-75">Sick Days Taken</div>
                                <div class="flex items-baseline mt-2">
                                    <div class="text-4xl font-bold" id="sickDaysTaken">0</div>
                                    <div class="text-sm ml-2 opacity-75">days</div>
                                </div>
                                <div class="absolute top-3 right-3 opacity-30 floating">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172L12 12m0 0l2.828-2.828M12 12l2.828 2.828M12 12L9.172 14.828" />
                                    </svg>
                                </div>
                                <div class="w-full bg-white/20 h-1 rounded-full mt-4">
                                    <div class="bg-white h-1 rounded-full" style="width: 25%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="dashboard-card p-6">
                    <div class="card-header -mx-6 -mt-6 mb-6 bg-gradient-to-r from-emerald-500 to-teal-500 text-white">
                        <h2 class="text-xl font-bold">
                            Quick Actions
                        </h2>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-4">
                        <a href="/manager-calendar" class="btn btn-success flex items-center justify-between group">
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                View Manager Dashboard
                            </span>
                            <svg class="w-5 h-5 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        
                        <button id="clearSelectionBtn" class="btn btn-danger flex items-center justify-between group">
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Clear Selected Dates
                            </span>
                            <svg class="w-5 h-5 transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        
                        <div class="flex items-center p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl border border-indigo-100 dark:border-indigo-800/20">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm text-indigo-700 dark:text-indigo-300">
                                Click on calendar dates to select time off or sick days
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Legend -->
            <div class="dashboard-card mb-8 p-6 overflow-visible">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Color Legend
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-7 gap-4">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full mb-2" style="background: var(--holiday-whole)"></div>
                        <span class="text-xs text-center">Whole Day</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full mb-2" style="background: var(--holiday-first)"></div>
                        <span class="text-xs text-center">First Half</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full mb-2" style="background: var(--holiday-second)"></div>
                        <span class="text-xs text-center">Second Half</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full mb-2" style="background: var(--sick-leave)"></div>
                        <span class="text-xs text-center">Sick Leave</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full mb-2 bg-green-500"></div>
                        <span class="text-xs text-center">Approved</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full mb-2 bg-yellow-500"></div>
                        <span class="text-xs text-center">Pending</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full mb-2 bg-red-500"></div>
                        <span class="text-xs text-center">Rejected</span>
                    </div>
                </div>
            </div>

            <!-- Calendar -->
            <div class="dashboard-card">
                <div class="card-header bg-gradient-to-r from-sky-500 to-blue-500 text-white">
                    <h2 class="text-xl font-bold flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Time Off Calendar
                    </h2>
                    <div>
                        <span class="text-sm text-white/80">
                            Today: {{ date('F j, Y') }}
                        </span>
                    </div>
                </div>
                <div class="p-2 md:p-4 lg:p-6">
                    <div id="calendar" class="min-h-[600px]"></div>
                </div>
            </div>

            
        </div>
    </div>

    <!-- Event Modal -->
    <div id="eventModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50 modal-overlay">
        <div class="glassmorphism rounded-2xl shadow-2xl max-w-md mx-4 w-full overflow-hidden modal-content">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 py-6 px-6 text-white">
                <h2 class="text-2xl font-bold">Request Time Off</h2>
                <p class="opacity-75 text-sm mt-1">Select the type of leave you want to request</p>
            </div>
            
            <div class="p-6 bg-white/80 dark:bg-gray-800/80">
                <!-- Event Type Selector -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <button id="holidayBtn" class="relative overflow-hidden rounded-xl p-4 flex flex-col items-center justify-center gap-2 border-2 border-transparent hover:border-indigo-500 transition-all bg-white dark:bg-gray-700 group">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-full flex items-center justify-center transform transition-transform group-hover:scale-110">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                            </svg>
                        </div>
                        <span class="font-medium text-gray-900 dark:text-white mt-1">Holiday</span>
                    </button>
                    
                    <button id="sickDayBtn" class="relative overflow-hidden rounded-xl p-4 flex flex-col items-center justify-center gap-2 border-2 border-transparent hover:border-indigo-500 transition-all bg-white dark:bg-gray-700 group">
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center transform transition-transform group-hover:scale-110">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="font-medium text-gray-900 dark:text-white mt-1">Sick Leave</span>
                    </button>
                </div>
                
                <!-- Holiday Options -->
                <div id="holidayOptions" class="hidden animate__animated animate__fadeInUp">
                    <h3 class="font-medium text-gray-800 dark:text-gray-200 mb-4">Select Day Type</h3>
                    
                    <div class="space-y-3">
                        <button id="wholeDayBtn" class="w-full p-4 rounded-xl text-white font-medium flex items-center holiday-card transition-all hover:shadow-lg hover:scale-[1.02] group">
                            <span class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">🌞</span>
                            <span>Whole Day</span>
                            <svg class="w-5 h-5 ml-auto transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <button id="firstHalfBtn" class="w-full p-4 rounded-xl text-white font-medium flex items-center holiday-first-card transition-all hover:shadow-lg hover:scale-[1.02] group">
                            <span class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">⛅</span>
                            <span>First Half</span>
                            <svg class="w-5 h-5 ml-auto transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <button id="secondHalfBtn" class="w-full p-4 rounded-xl text-white font-medium flex items-center holiday-second-card transition-all hover:shadow-lg hover:scale-[1.02] group">
                            <span class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">🌙</span>
                            <span>Second Half</span>
                            <svg class="w-5 h-5 ml-auto transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Sick Day Options -->
                <div id="sickDayRangePicker" class="hidden animate__animated animate__fadeInUp">
                    <h3 class="font-medium text-gray-800 dark:text-gray-200 mb-4">Select Sick Leave Dates</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                            <input type="date" id="sickStartDate" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                            <input type="date" id="sickEndDate" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        
                        <button id="applySickDaysBtn" class="w-full p-4 rounded-xl text-white font-medium flex items-center sick-leave-card transition-all hover:shadow-lg hover:scale-[1.02] group">
                            <span class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">🤒</span>
                            <span>Apply Sick Days</span>
                            <svg class="w-5 h-5 ml-auto transform transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="p-4 bg-white/90 dark:bg-gray-800/90 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                <button id="closeModalBtn" class="btn btn-ghost">Cancel</button>
            </div>
        </div>
    </div>
    
    <!-- Success Toast -->
    <div id="toast" class="fixed bottom-4 right-4 z-50 p-4 rounded-lg shadow-xl transform translate-y-20 opacity-0 transition-all duration-500 hidden max-w-xs">
        <div class="flex items-center">
            <div id="toastIcon" class="mr-3 flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div class="mr-2">
                <p id="toastMessage" class="font-medium"></p>
            </div>
            <div class="flex-shrink-0 ml-auto cursor-pointer" onclick="hideToast()">
                <svg class="w-4 h-4 opacity-70 hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
        </div>
    </div>
    
    <!-- Confetti Container -->
    <div id="confettiContainer" class="fixed inset-0 pointer-events-none hidden z-50"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Ensure required libraries are loaded
            if (typeof moment === 'undefined') {
                console.error("Moment.js is not loaded. Ensure it's included in your project.");
                return;
            }

            // DOM elements
            const notificationDropdown = document.getElementById('notificationDropdown');
            const notificationBadge = document.getElementById('notificationBadge');
            const saveRequestsBtn = document.getElementById('saveRequests');
            const clearSelectionBtn = document.getElementById('clearSelectionBtn');
            const calendarEl = document.getElementById('calendar');
            const sickDayRangePicker = document.getElementById('sickDayRangePicker');
            const sickStartDate = document.getElementById('sickStartDate');
            const sickEndDate = document.getElementById('sickEndDate');
            const applySickDaysBtn = document.getElementById('applySickDaysBtn');
            const closeModalBtn = document.getElementById('closeModalBtn');
            const eventModal = document.getElementById('eventModal');
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toastMessage');
            const toastIcon = document.getElementById('toastIcon');
            const confettiContainer = document.getElementById('confettiContainer');
            const darkModeToggle = document.getElementById('darkModeToggle');
            const sunIcon = document.getElementById('sunIcon');
            const moonIcon = document.getElementById('moonIcon');
            const bgCanvas = document.getElementById('bg-canvas');

            if (!calendarEl) {
                console.error("Calendar element not found!");
                return;
            }
            
            // Initialize background animation
            function initBackground() {
                // Check if we have Three.js loaded
                if (typeof THREE === 'undefined') {
                    console.warn('Three.js not loaded, skipping background animation');
                    return;
                }
                
                // Set up scene
                const scene = new THREE.Scene();
                const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
                
                const renderer = new THREE.WebGLRenderer({ alpha: true });
                renderer.setSize(window.innerWidth, window.innerHeight);
                bgCanvas.appendChild(renderer.domElement);
                
                // Create particles
                const particlesGeometry = new THREE.BufferGeometry();
                const particlesCount = 1000;
                
                const posArray = new Float32Array(particlesCount * 3);
                
                for (let i = 0; i < particlesCount * 3; i++) {
                    posArray[i] = (Math.random() - 0.5) * 10;
                }
                
                particlesGeometry.setAttribute('position', new THREE.BufferAttribute(posArray, 3));
                
                // Materials - different for light/dark mode
                const lightMaterial = new THREE.PointsMaterial({
                    size: 0.005,
                    color: 0x4f46e5, // Indigo
                    transparent: true,
                    opacity: 0.7,
                });
                
                const darkMaterial = new THREE.PointsMaterial({
                    size: 0.005,
                    color: 0x818cf8, // Light indigo
                    transparent: true,
                    opacity: 0.7,
                });
                
                const particlesMesh = new THREE.Points(particlesGeometry, document.documentElement.classList.contains('dark') ? darkMaterial : lightMaterial);
                scene.add(particlesMesh);
                
                camera.position.z = 2;
                
                // Animation
                const animate = () => {
                    requestAnimationFrame(animate);
                    particlesMesh.rotation.x += 0.0001;
                    particlesMesh.rotation.y += 0.0002;
                    renderer.render(scene, camera);
                };
                
                animate();
                
                // Resize handler
                window.addEventListener('resize', () => {
                    camera.aspect = window.innerWidth / window.innerHeight;
                    camera.updateProjectionMatrix();
                    renderer.setSize(window.innerWidth, window.innerHeight);
                });
                
                // Update material on theme change
                document.addEventListener('themeChanged', (e) => {
                    const isDark = e.detail.isDark;
                    particlesMesh.material = isDark ? darkMaterial : lightMaterial;
                });
            }
            
            // Dark mode toggle functionality with improved animation
            function initDarkMode() {
                const htmlElement = document.documentElement;
                const isDarkMode = localStorage.getItem('darkMode') === 'true';
                
                // Update the initial state
                if (isDarkMode) {
                    htmlElement.classList.add('dark');
                    updateDarkModeIcons(true);
                } else {
                    updateDarkModeIcons(false);
                }
                
                darkModeToggle.addEventListener('click', function() {
                    const isDark = htmlElement.classList.toggle('dark');
                    localStorage.setItem('darkMode', isDark);
                    updateDarkModeIcons(isDark);
                    
                    // Dispatch event for background to update
                    document.dispatchEvent(new CustomEvent('themeChanged', { 
                        detail: { isDark: isDark } 
                    }));
                });
            }
            
            // Animate between sun and moon icons
            function updateDarkModeIcons(isDark) {
                if (isDark) {
                    moonIcon.style.transform = 'scale(1) rotate(0)';
                    moonIcon.style.opacity = '1';
                    sunIcon.style.transform = 'scale(0) rotate(-90deg)';
                    sunIcon.style.opacity = '0';
                } else {
                    moonIcon.style.transform = 'scale(0) rotate(90deg)';
                    moonIcon.style.opacity = '0';
                    sunIcon.style.transform = 'scale(1) rotate(0)';
                    sunIcon.style.opacity = '1';
                }
            }

            // Fetch notifications function
            function fetchNotifications() {
                fetch('/workspace/notifications', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const notificationDropdown = document.getElementById('notificationDropdown');
                    const notificationBadge = document.getElementById('notificationBadge');
                    const notificationList = notificationDropdown.querySelector('.space-y-3');

                    notificationList.innerHTML = ''; // Clear existing notifications

                    if (data.length === 0) {
                        notificationList.innerHTML = '<div class="text-gray-500 text-sm">No unread notifications</div>';
                        notificationBadge.classList.add('hidden');
                        return;
                    }

                    notificationBadge.textContent = data.length;
                    notificationBadge.classList.remove('hidden');

                    data.forEach(notification => {
                        const notificationItem = document.createElement('div');
                        notificationItem.className = 'p-3 bg-gray-50 dark:bg-gray-700 rounded-lg shadow-sm border border-gray-100 dark:border-gray-600';
                        notificationItem.innerHTML = `
                            <p class="text-gray-800 dark:text-gray-200 font-medium">${notification.message}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">${notification.created_at}</p>
                            <button class="mt-2 px-3 py-1 bg-blue-500 text-white text-xs font-medium rounded-md hover:bg-blue-600 transition" onclick="markNotificationAsRead(${notification.id})">Mark as Read</button>
                        `;
                        notificationList.appendChild(notificationItem);
                    });
                })
                .catch(error => {
                    console.error('Error fetching notifications:', error);
                });
            }

            // Mark a notification as read
            window.markNotificationAsRead = function (notificationId) {
                console.log(`Marking notification ${notificationId} as read`); // Debugging log
                fetch(`/workspace/notifications/${notificationId}/mark-as-read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Notification marked as read:', data); // Debugging log
                    fetchNotifications(); // Refresh the dropdown
                })
                .catch(error => {
                    console.error('Error marking notification as read:', error);
                });
            };

            // Fetch notifications on page load
            fetchNotifications();

            // Toggle notification dropdown
            document.getElementById('toggleNotifications').addEventListener('click', function () {
                const dropdown = document.getElementById('notificationDropdown');
                dropdown.classList.toggle('hidden');
            });

            // Show toast message with improved animation
            function showToast(message, type = 'success') {
                toastMessage.textContent = message;
                
                // Set toast styles based on type
                if (type === 'success') {
                    toast.className = 'fixed bottom-4 right-4 z-50 p-4 rounded-lg shadow-xl transform translate-y-20 opacity-0 transition-all duration-500 bg-gradient-to-r from-green-500 to-emerald-600 text-white max-w-xs';
                    toastIcon.innerHTML = `
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    `;
                } else if (type === 'error') {
                    toast.className = 'fixed bottom-4 right-4 z-50 p-4 rounded-lg shadow-xl transform translate-y-20 opacity-0 transition-all duration-500 bg-gradient-to-r from-red-500 to-pink-600 text-white max-w-xs';
                    toastIcon.innerHTML = `
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    `;
                } else if (type === 'warning') {
                    toast.className = 'fixed bottom-4 right-4 z-50 p-4 rounded-lg shadow-xl transform translate-y-20 opacity-0 transition-all duration-500 bg-gradient-to-r from-yellow-500 to-amber-600 text-white max-w-xs';
                    toastIcon.innerHTML = `
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    `;
                }
                
                // Show toast with animation
                toast.classList.remove('hidden');
                
                // Animate in
                setTimeout(() => {
                    toast.classList.add('translate-y-0', 'opacity-100');
                }, 10);
                
                // Animate out after delay
                setTimeout(() => {
                    toast.classList.remove('translate-y-0', 'opacity-100');
                    toast.classList.add('translate-y-20', 'opacity-0');
                    
                    // Hide after animation completes
                    setTimeout(() => {
                        toast.classList.add('hidden');
                    }, 500);
                }, 3000);
            }
            
            // Hide toast manually
            window.hideToast = function() {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-20', 'opacity-0');
                setTimeout(() => {
                    toast.classList.add('hidden');
                }, 500);
            };

            let selectedHolidays = JSON.parse(localStorage.getItem('selectedHolidays')) || {};
            let selectedSickDays = new Set(JSON.parse(localStorage.getItem('selectedSickDays')) || []);
            let remainingHolidays = {{ auth()->user()->employee->leave_balance }};
            let sickDaysTaken = selectedSickDays.size;
            let currentDate = null;

            function updateCounters() {
                document.getElementById('remainingHolidays').textContent = remainingHolidays;
                document.getElementById('sickDaysTaken').textContent = sickDaysTaken;
            }

            function addEvent(date, type, period = "Full Day") {
                let title = type === "holiday" ? `Holiday (${period})` : 'Sick Day';
                let backgroundColor, textColor = 'white';
                
                switch(true) {
                    case (type === "holiday" && period === "Whole Day"):
                        backgroundColor = 'var(--holiday-whole)';
                        break;
                    case (type === "holiday" && period === "First Half"):
                        backgroundColor = 'var(--holiday-first)';
                        break;
                    case (type === "holiday" && period === "Second Half"):
                        backgroundColor = 'var(--holiday-second)';
                        break;
                    case (type === "sick"):
                        backgroundColor = 'var(--sick-leave)';
                        break;
                    default:
                        backgroundColor = '#ff7f7f';
                }
                
                calendar.addEvent({
                    id: date,
                    title: title,
                    start: date,
                    allDay: true,
                    backgroundColor: backgroundColor,
                    borderColor: 'transparent',
                    textColor: textColor,
                    extendedProps: {
                        type: type,
                        period: period
                    }
                });
            }

            function removeEvent(date) {
                let event = calendar.getEventById(date);
                if (event) event.remove();
            }

            function toggleEvent(date) {
                let dateObj = new Date(date);
                let today = new Date();
                today.setHours(0, 0, 0, 0);

                if (dateObj < today) {
                    showToast('You cannot select a past date', 'error');
                    return;
                }
                if (dateObj.getDay() === 0 || dateObj.getDay() === 6) {
                    showToast('You cannot select weekends', 'error');
                    return;
                }

                currentDate = date;

                // Reset modal state
                document.getElementById('holidayOptions').classList.add('hidden');
                document.getElementById('sickDayRangePicker').classList.add('hidden');
                document.getElementById('sickDayBtn').disabled = false;
                document.getElementById('holidayBtn').disabled = false;
                
                // Show the modal
                eventModal.classList.remove('hidden');
                eventModal.querySelector('.modal-content').classList.add('animate__animated', 'animate__fadeInUp');

                // Holiday button logic
                document.getElementById('holidayBtn').onclick = function () {
                    document.getElementById('holidayOptions').classList.remove('hidden');
                    document.getElementById('sickDayRangePicker').classList.add('hidden');
                    document.getElementById('sickDayBtn').disabled = true;
                };

                // Sick day button logic
                document.getElementById('sickDayBtn').onclick = function () {
                    document.getElementById('holidayOptions').classList.add('hidden');
                    document.getElementById('sickDayRangePicker').classList.remove('hidden');
                    document.getElementById('holidayBtn').disabled = true;
                };

                // Handle sick day range submission
                document.getElementById('applySickDaysBtn').onclick = function () {
                    const startDate = document.getElementById('sickStartDate').value;
                    const endDate = document.getElementById('sickEndDate').value;

                    if (!startDate || !endDate) {
                        showToast('Please select both start and end dates', 'warning');
                        return;
                    }

                    let current = new Date(startDate);
                    const end = new Date(endDate);
                    let addedDays = 0;

                    while (current <= end) {
                        // Skip weekends
                        if (current.getDay() !== 0 && current.getDay() !== 6) {
                            const formattedDate = current.toISOString().split('T')[0];
                            selectedSickDays.add(formattedDate);
                            addEvent(formattedDate, "sick");
                            addedDays++;
                        }
                        current.setDate(current.getDate() + 1);
                    }

                    sickDaysTaken = selectedSickDays.size;
                    closeModal();
                    updateCounters();
                    
                    showToast(`Added ${addedDays} sick day(s)`, 'success');
                };

                // Holiday option buttons
                document.getElementById('wholeDayBtn').onclick = function () {
                    selectedHolidays[date] = 'Whole Day';
                    addEvent(date, "holiday", "Whole Day");
                    closeModal();
                    updateCounters(); // Do not decrement remainingHolidays here
                    showToast('Whole day holiday added', 'success');
                };

                document.getElementById('firstHalfBtn').onclick = function () {
                    selectedHolidays[date] = 'First Half';
                    addEvent(date, "holiday", "First Half");
                    closeModal();
                    updateCounters(); // Do not decrement remainingHolidays here
                    showToast('First half holiday added', 'success');
                };

                document.getElementById('secondHalfBtn').onclick = function () {
                    selectedHolidays[date] = 'Second Half';
                    addEvent(date, "holiday", "Second Half");
                    closeModal();
                    updateCounters(); // Do not decrement remainingHolidays here
                    showToast('Second half holiday added', 'success');
                };

                // Add a "Remove Date" button to the modal
                const removeDateBtn = document.createElement('button');
                removeDateBtn.className = 'btn btn-danger mt-4 w-full';
                removeDateBtn.textContent = 'Remove Date';
                removeDateBtn.onclick = function () {
                    removeEvent(date);
                    delete selectedHolidays[date];
                    selectedSickDays.delete(date);
                    updateCounters();
                    closeModal();
                    showToast('Date removed from selection', 'success');
                };

                // Append the button to the modal
                const modalContent = eventModal.querySelector('.modal-content .p-6');
                if (!modalContent.querySelector('.btn-danger')) {
                    modalContent.appendChild(removeDateBtn);
                }

                localStorage.setItem('selectedHolidays', JSON.stringify(selectedHolidays));
                localStorage.setItem('selectedSickDays', JSON.stringify([...selectedSickDays]));
            }

            function closeModal() {
                eventModal.querySelector('.modal-content').classList.add('animate__animated', 'animate__fadeOutDown');
                
                setTimeout(() => {
                    eventModal.classList.add('hidden');
                    eventModal.querySelector('.modal-content').classList.remove('animate__animated', 'animate__fadeInUp', 'animate__fadeOutDown');
                    document.getElementById('holidayOptions').classList.add('hidden');
                    document.getElementById('sickDayRangePicker').classList.add('hidden');
                    document.getElementById('sickDayBtn').disabled = false;
                    document.getElementById('holidayBtn').disabled = false;
                    sickStartDate.value = '';
                    sickEndDate.value = '';
                }, 500);

                // Remove the "Remove Date" button when closing the modal
                const removeDateBtn = eventModal.querySelector('.btn-danger');
                if (removeDateBtn) {
                    removeDateBtn.remove();
                }
            }

            // Create enhanced confetti effect
            function createConfetti() {
                confettiContainer.classList.remove('hidden');
                confettiContainer.innerHTML = '';
                
                // Create a variety of shapes and colors
                const shapes = ['circle', 'square', 'triangle', 'line', 'star'];
                const colors = [
                    'var(--primary)', 'var(--primary-light)', 'var(--secondary)',
                    'var(--success)', 'var(--warning)', 'var(--danger)',
                    '#fc0388', '#03fc90', '#03dbfc', '#fcc203', '#a703fc'
                ];
                
                for (let i = 0; i < 150; i++) {
                    const confetti = document.createElement('div');
                    confetti.className = 'confetti';
                    
                    // Random size (3-8px)
                    const size = Math.random() * 5 + 3;
                    confetti.style.width = `${size}px`;
                    confetti.style.height = `${size}px`;
                    
                    // Random position
                    confetti.style.left = Math.random() * 100 + 'vw';
                    confetti.style.top = Math.random() * 100 + 'vh';
                    
                    // Random shape
                    const shapeType = shapes[Math.floor(Math.random() * shapes.length)];
                    if (shapeType === 'circle') {
                        confetti.style.borderRadius = '50%';
                    } else if (shapeType === 'square') {
                        // Already square by default
                    } else if (shapeType === 'triangle') {
                        confetti.style.width = '0';
                        confetti.style.height = '0';
                        confetti.style.borderLeft = `${size}px solid transparent`;
                        confetti.style.borderRight = `${size}px solid transparent`;
                        confetti.style.borderBottom = `${size * 2}px solid ${colors[Math.floor(Math.random() * colors.length)]}`;
                        confetti.style.background = 'transparent';
                    } else if (shapeType === 'line') {
                        confetti.style.width = `${size / 3}px`;
                        confetti.style.height = `${size * 3}px`;
                        confetti.style.borderRadius = '3px';
                    } else if (shapeType === 'star') {
                        // Creating a CSS star shape is complex, so we'll use a character
                        confetti.innerHTML = '★';
                        confetti.style.color = colors[Math.floor(Math.random() * colors.length)];
                        confetti.style.fontSize = `${size * 2}px`;
                        confetti.style.display = 'flex';
                        confetti.style.alignItems = 'center';
                        confetti.style.justifyContent = 'center';
                        confetti.style.background = 'transparent';
                    }
                    
                    // Random color (if not already set by shape)
                    if (shapeType !== 'triangle' && shapeType !== 'star') {
                        confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                    }
                    
                    // Random rotation
                    confetti.style.transform = `rotate(${Math.random() * 360}deg)`;
                    
                    // Random animation duration and delay
                    confetti.style.animationDuration = (Math.random() * 3 + 2) + 's';
                    confetti.style.animationDelay = (Math.random() * 2) + 's';
                    
                    confettiContainer.appendChild(confetti);
                }
                
                // Remove confetti after animation
                setTimeout(() => {
                    // Fade out instead of abrupt removal
                    confettiContainer.style.opacity = '0';
                    setTimeout(() => {
                        confettiContainer.innerHTML = '';
                        confettiContainer.classList.add('hidden');
                        confettiContainer.style.opacity = '1';
                    }, 1000);
                }, 5000);
            }

            // Fetch vacations and add them to the calendar
            function fetchVacations() {
                fetch('/workspace/get-vacations') // Ensure the correct endpoint is used
                    .then(response => response.json())
                    .then(data => {
                        // Clear existing events to avoid duplication
                        calendar.getEvents().forEach(event => event.remove());

                        data.forEach(vacation => {
                            let backgroundColor, textColor = 'white';

                            switch (vacation.approve_status.toLowerCase()) {
                                case 'approved':
                                    backgroundColor = 'var(--status-approved)';
                                    break;
                                case 'pending':
                                    backgroundColor = 'var(--status-pending)';
                                    textColor = '#663c00'; // Darker text for better contrast
                                    break;
                                case 'rejected':
                                    backgroundColor = 'var(--status-rejected)';
                                    break;
                                default:
                                    backgroundColor = '#6B7280'; // Default gray
                            }

                            // Include day_type in the event title
                            calendar.addEvent({
                                id: vacation.id,
                                title: `${vacation.vacation_type} (${vacation.day_type}) - ${vacation.approve_status}`,
                                start: vacation.start_date,
                                end: vacation.end_date ? vacation.end_date : vacation.start_date,
                                allDay: true,
                                backgroundColor: backgroundColor,
                                borderColor: 'transparent',
                                textColor: textColor,
                                extendedProps: {
                                    status: vacation.approve_status.toLowerCase(),
                                }
                            });
                        });
                    })
                    .catch(error => console.error("Error fetching vacations:", error));
            }

            let calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'en-gb',
                firstDay: 1,
                height: 'auto',
                themeSystem: 'standard',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek'
                },
                buttonText: {
                    today: 'Today',
                    month: 'Month',
                    week: 'Week'
                },
                dateClick: function (info) {
                    toggleEvent(info.dateStr);
                },
                eventDidMount: function(info) {
                    // Add enhanced tooltip with more information
                    const eventEl = info.el;
                    const event = info.event;
                    const type = event.extendedProps.type || '';
                    const status = event.extendedProps.status || '';
                    const period = event.extendedProps.period || '';
                    
                    // Create tooltip
                    const tooltip = document.createElement('div');
                    tooltip.className = 'tooltip glassmorphism p-3 rounded-lg shadow-lg text-gray-800 dark:text-gray-200 text-sm border border-gray-200 dark:border-gray-700';
                    tooltip.innerHTML = `
                        <div class="font-medium mb-1">${event.title}</div>
                        <div class="text-gray-500 dark:text-gray-400 text-xs">${new Date(event.start).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</div>
                        ${period ? `<div class="text-gray-600 dark:text-gray-300 mt-1 text-xs">Type: ${period}</div>` : ''}
                        ${status ? `<div class="text-${status === 'approved' ? 'green' : status === 'pending' ? 'yellow' : 'red'}-500 font-medium mt-1 text-xs">Status: ${status.charAt(0).toUpperCase() + status.slice(1)}</div>` : ''}
                    `;
                    
                    // Add to event element
                    eventEl.classList.add('has-tooltip');
                    eventEl.appendChild(tooltip);
                    
                    // Position tooltip on hover
                    eventEl.addEventListener('mouseenter', () => {
                        const rect = eventEl.getBoundingClientRect();
                        tooltip.style.top = `${rect.bottom + window.scrollY + 10}px`;
                        tooltip.style.left = `${rect.left + window.scrollX}px`;
                    });
                },
                eventClick: function(info) {
                    // Add a nice ripple effect on click
                    const eventEl = info.el;
                    const ripple = document.createElement('div');
                    ripple.className = 'absolute inset-0 bg-white/30 rounded-md';
                    ripple.style.animation = 'ripple 0.7s ease-out';
                    
                    // Add ripple animation keyframes
                    if (!document.querySelector('style#ripple-animation')) {
                        const style = document.createElement('style');
                        style.id = 'ripple-animation';
                        style.textContent = `
                            @keyframes ripple {
                                0% { transform: scale(0); opacity: 1; }
                                100% { transform: scale(3); opacity: 0; }
                            }
                        `;
                        document.head.appendChild(style);
                    }
                    
                    eventEl.style.position = 'relative';
                    eventEl.style.overflow = 'hidden';
                    eventEl.appendChild(ripple);
                    
                    // Remove ripple after animation
                    setTimeout(() => ripple.remove(), 700);
                }
            });

            // Add theme-reactive styles to the calendar
            function updateCalendarTheme() {
                // Get all calendar-related elements
                const calendarEl = document.querySelector('.fc');
                if (!calendarEl) return;
                
                // Check if dark mode is active
                const isDarkMode = document.documentElement.classList.contains('dark');
                
                // Update calendar header
                const calendarHeader = calendarEl.querySelector('.fc-header-toolbar');
                if (calendarHeader) {
                    calendarHeader.style.color = isDarkMode ? 'var(--dark-text-primary)' : 'var(--text-primary)';
                }
                
                // Update calendar table
                const calendarTable = calendarEl.querySelector('table');
                if (calendarTable) {
                    calendarTable.style.color = isDarkMode ? 'var(--dark-text-primary)' : 'var(--text-primary)';
                    calendarTable.style.backgroundColor = isDarkMode ? 'var(--dark-bg-card)' : 'var(--bg-card)';
                }
                
                // Update day cells
                const dayCells = calendarEl.querySelectorAll('.fc-daygrid-day');
                dayCells.forEach(cell => {
                    cell.style.borderColor = isDarkMode ? 'var(--dark-border-light)' : 'var(--border-light)';
                });
                
                // Update today highlight
                const todayCell = calendarEl.querySelector('.fc-day-today');
                if (todayCell) {
                    todayCell.style.backgroundColor = isDarkMode ? 'rgba(79, 70, 229, 0.2)' : 'rgba(79, 70, 229, 0.1)';
                }
            }

            // Initialize calendar and load data
            calendar.render();
            Object.keys(selectedHolidays).forEach(date => addEvent(date, "holiday", selectedHolidays[date]));
            selectedSickDays.forEach(date => addEvent(date, "sick"));
            fetchVacations();
            updateCounters();
            
            Object.keys(selectedHolidays).forEach(date => addEvent(date, "holiday", selectedHolidays[date]));
            selectedSickDays.forEach(date => addEvent(date, "sick"));
            fetchVacations();
            updateCounters();
            
            // Apply initial calendar theme
            setTimeout(updateCalendarTheme, 100);

            // Toggle notifications
            window.toggleNotifications = function () {
                const notificationDropdown = document.getElementById('notificationDropdown');
                notificationDropdown.classList.toggle('hidden');
                if (!notificationDropdown.classList.contains('hidden')) {
                    fetchNotifications();
                }
            };
            
            // Initial notifications load
            fetchNotifications();

            // Clear selection button handler with confirmation
            clearSelectionBtn.addEventListener('click', function() {
                // Create a more interesting confirmation
                eventModal.innerHTML = `
                    <div class="glassmorphism rounded-2xl shadow-2xl max-w-md mx-4 w-full overflow-hidden modal-content">
                        <div class="bg-gradient-to-r from-red-500 to-pink-600 py-6 px-6 text-white">
                            <h2 class="text-2xl font-bold">Clear All Selections</h2>
                            <p class="opacity-75 text-sm mt-1">Are you sure you want to clear all your selections?</p>
                        </div>
                        
                        <div class="p-6 bg-white/80 dark:bg-gray-800/80">
                            <p class="text-gray-800 dark:text-gray-200 mb-6">This will remove all holiday and sick day selections you've made. This action cannot be undone.</p>
                            
                            <div class="flex space-x-4">
                                <button id="confirmClearBtn" class="btn btn-danger flex-1">
                                    Yes, Clear All
                                </button>
                                <button id="cancelClearBtn" class="btn btn-ghost flex-1">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                
                // Show the modal
                eventModal.classList.remove('hidden');
                
                // Add event listeners
                document.getElementById('confirmClearBtn').addEventListener('click', function() {
                    // Remove all events from the calendar
                    Object.keys(selectedHolidays).forEach(date => {
                        removeEvent(date);
                    });
                    
                    selectedSickDays.forEach(date => {
                        removeEvent(date);
                    });
                    
                    // Reset variables
                    selectedHolidays = {};
                    selectedSickDays = new Set();
                    remainingHolidays = {{ auth()->user()->employee->leave_balance }};
                    sickDaysTaken = 0;
                    
                    // Update localStorage
                    localStorage.setItem('selectedHolidays', JSON.stringify(selectedHolidays));
                    localStorage.setItem('selectedSickDays', JSON.stringify([]));
                    
                    // Update UI
                    updateCounters();
                    
                    // Close the modal
                    eventModal.classList.add('hidden');
                    
                    // Show success toast
                    showToast('All selections cleared', 'success');
                });
                
                document.getElementById('cancelClearBtn').addEventListener('click', function() {
                    eventModal.classList.add('hidden');
                });
            });

            // Save vacation requests with enhanced animation
            saveRequestsBtn.addEventListener('click', function () {
                const sickDaysArray = [...selectedSickDays].map(date => ({
                    start_date: date,
                    end_date: date,
                }));

                fetch('/workspace/save-vacation', { // Updated URL to include /workspace prefix
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        holidays: selectedHolidays,
                        sickDays: sickDaysArray,
                    }),
                })
                    .then(response => {
                        if (!response.ok) {
                            if (response.status === 404) {
                                throw new Error('The requested endpoint was not found. Please check the URL.');
                            }
                            return response.text().then(text => {
                                try {
                                    const errorJson = JSON.parse(text);
                                    throw new Error(errorJson.error || 'Failed to save requests');
                                } catch {
                                    throw new Error('Unexpected response from the server. Please try again.');
                                }
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Deduct remaining holidays only after the request is successfully saved
                        Object.values(selectedHolidays).forEach(dayType => {
                            if (dayType === 'Whole Day') {
                                remainingHolidays -= 1;
                            } else {
                                remainingHolidays -= 0.5;
                            }
                        });

                        // Reset selected holidays and sick days
                        selectedHolidays = {};
                        selectedSickDays.clear();

                        // Update localStorage and UI
                        localStorage.setItem('selectedHolidays', JSON.stringify(selectedHolidays));
                        localStorage.setItem('selectedSickDays', JSON.stringify([...selectedSickDays]));
                        updateCounters();

                        showToast(data.message, 'success');
                        calendar.refetchEvents();
                    })
                    .catch(error => {
                        console.error('Error saving requests:', error);
                        showToast('Failed to save requests. Please try again.', 'error');
                    });
            });
            
            // Listen for theme changes to update calendar
            document.addEventListener('themeChanged', function() {
                updateCalendarTheme();
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                const dropdown = document.getElementById('notificationDropdown');
                const toggleButton = document.getElementById('toggleNotifications');
                if (!dropdown.contains(event.target) && !toggleButton.contains(event.target)) {
                    dropdown.classList.add('hidden');
                }
            });
            
            // Initialize the app
            initDarkMode();
            initBackground();
            
            // 3D tilt effect for stat cards
            document.querySelectorAll('.stat-card').forEach(card => {
                card.addEventListener('mousemove', function(e) {
                    const cardRect = card.getBoundingClientRect();
                    const x = e.clientX - cardRect.left;
                    const y = e.clientY - cardRect.top;
                    
                    const centerX = cardRect.width / 2;
                    const centerY = cardRect.height / 2;
                    
                    const rotateX = (y - centerY) / 10;
                    const rotateY = (centerX - x) / 10;
                    
                    card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateZ(10px)`;
                });
                
                card.addEventListener('mouseleave', function() {
                    card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateZ(0)';
                });
            });
        });
    </script>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    </x-sidebar>
</x-app-layout>