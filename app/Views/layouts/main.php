<?php
use App\Core\Auth;
use App\Core\Session;
use App\Core\Database;

$user = Auth::user();
$role = Auth::getRole();
$username = $user ? $user['username'] : '';
$themeClass = Session::get('theme') === 'dark' ? 'dark' : '';
$uri = $_SERVER['REQUEST_URI'] ?? '';

// Fetch global settings
$db = Database::getInstance();
$settingsRaw = $db->fetchAll("SELECT * FROM settings");
$settings = [];
foreach ($settingsRaw as $s) {
    $settings[$s['key']] = $s['value'];
}

// Active state helper for sidebar links
$isActive = function($path) use ($uri) {
    return strpos($uri, $path) !== false;
};
$linkClass = function($path) use ($isActive) {
    if ($isActive($path)) {
        return 'bg-slate-800 text-white font-semibold shadow-sm border-l-4 border-accent pl-3';
    }
    return 'text-slate-350 hover:text-white hover:bg-slate-800/30';
};
?>
<!DOCTYPE html>
<html lang="en" class="<?= $themeClass ?>" x-data="{ darkMode: localStorage.getItem('theme') === 'dark', mobileMenuOpen: false }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIWES Allocation & Management Portal</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#0B1329',   /* Deep Navy */
                        secondary: '#1E293B', /* Slate */
                        accent: '#FFC000',    /* Gold Accent */
                        success: '#10B981',
                        warning: '#FFC000',
                        danger: '#EF4444',
                        darkBg: '#0A0E1A',
                        darkCard: '#111827',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        poppins: ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom CSS Stylesheet -->
    <link rel="stylesheet" href="/siwes/public/assets/css/custom.css">
</head>
<body class="bg-slate-50 text-[#1E293B] font-sans transition-colors duration-200 dark:bg-[#070B14] dark:text-gray-150 flex flex-col overflow-x-hidden <?= $user ? 'h-screen p-0 md:p-4 lg:p-6 overflow-hidden' : 'min-h-screen p-0' ?>">

    <?php if ($user): ?>
        <!-- ================= AUTHENTICATED INTEGRATED DASHBOARD SHELL ================= -->
        <div class="flex-1 flex w-full max-w-[1600px] mx-auto bg-primary dark:bg-slate-950 rounded-none md:rounded-[32px] overflow-hidden shadow-2xl border border-slate-800/10 dark:border-slate-850">
            
            <!-- Left Sidebar navigation -->
            <aside class="w-72 bg-primary flex flex-col p-6 text-white shrink-0 hidden md:flex select-none">
                <!-- Sidebar Header/Logo -->
                <div class="flex items-center space-x-3 pb-8 border-b border-slate-800/50">
                    <?php if (!empty($settings['logo_path'])): ?>
                        <div class="rounded-xl overflow-hidden w-10 h-10 shadow-md">
                            <img src="<?= asset($settings['logo_path']) ?>" alt="Logo" class="w-full h-full object-cover">
                        </div>
                    <?php else: ?>
                        <div class="bg-accent text-[#0A0E1A] p-2 rounded-xl flex items-center justify-center w-10 h-10 shadow-md shadow-accent/20">
                            <i class="fa-solid fa-graduation-cap text-lg"></i>
                        </div>
                    <?php endif; ?>
                    <div>
                        <span class="font-poppins font-bold text-lg text-white leading-none tracking-tight"><?= htmlspecialchars($settings['system_name'] ?? 'SIWES') ?></span>
                        <span class="text-xs text-accent font-semibold block uppercase tracking-wider">Placement Portal</span>
                    </div>
                </div>

                <!-- Navigation List -->
                <nav class="flex-1 py-8 space-y-2 select-none overflow-y-auto">
                    <div class="text-[10px] text-slate-500 font-bold uppercase tracking-wider px-3 mb-3">Main Navigation</div>
                    
                    <?php if ($role === 'admin'): ?>
                        <a href="/siwes/admin/dashboard" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm transition-all <?= $linkClass('/admin/dashboard') ?>">
                            <i class="fa-solid fa-chart-line text-lg w-5"></i> <span>Dashboard</span>
                        </a>
                        <a href="/siwes/coordinator/users" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm transition-all <?= $linkClass('/coordinator/users') ?>">
                            <i class="fa-solid fa-user-shield text-lg w-5"></i> <span>Manage Users</span>
                        </a>
                        <a href="/siwes/coordinator/departments" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm transition-all <?= $linkClass('/coordinator/departments') ?>">
                            <i class="fa-solid fa-graduation-cap text-lg w-5"></i> <span>Manage Departments</span>
                        </a>
                        <a href="/siwes/coordinator/allocations" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm transition-all <?= $linkClass('/coordinator/allocations') ?>">
                            <i class="fa-solid fa-shuffle text-lg w-5"></i> <span>SIWES Allocations</span>
                        </a>
                        <a href="/siwes/coordinator/students" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm transition-all <?= $linkClass('/coordinator/students') ?>">
                            <i class="fa-solid fa-users text-lg w-5"></i> <span>Manage Students</span>
                        </a>
                        <a href="/siwes/coordinator/companies" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm transition-all <?= $linkClass('/coordinator/companies') ?>">
                            <i class="fa-solid fa-building text-lg w-5"></i> <span>Manage Companies</span>
                        </a>
                        <a href="/siwes/coordinator/reports" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm transition-all <?= $linkClass('/coordinator/reports') ?>">
                            <i class="fa-solid fa-file-invoice text-lg w-5"></i> <span>Reports & Export</span>
                        </a>
                    <?php endif; ?>

                    <?php if ($role === 'coordinator'): ?>
                        <a href="/siwes/coordinator/dashboard" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm transition-all <?= $linkClass('/coordinator/dashboard') ?>">
                            <i class="fa-solid fa-chart-line text-lg w-5"></i> <span>Dashboard</span>
                        </a>
                        <a href="/siwes/coordinator/users" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm transition-all <?= $linkClass('/coordinator/users') ?>">
                            <i class="fa-solid fa-user-shield text-lg w-5"></i> <span>User Management</span>
                        </a>
                        <a href="/siwes/coordinator/departments" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm transition-all <?= $linkClass('/coordinator/departments') ?>">
                            <i class="fa-solid fa-graduation-cap text-lg w-5"></i> <span>Departments</span>
                        </a>
                        <a href="/siwes/coordinator/allocations" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm transition-all <?= $linkClass('/coordinator/allocations') ?>">
                            <i class="fa-solid fa-shuffle text-lg w-5"></i> <span>SIWES Allocations</span>
                        </a>
                        <a href="/siwes/coordinator/students" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm transition-all <?= $linkClass('/coordinator/students') ?>">
                            <i class="fa-solid fa-users text-lg w-5"></i> <span>Students Audit</span>
                        </a>
                        <a href="/siwes/coordinator/companies" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm transition-all <?= $linkClass('/coordinator/companies') ?>">
                            <i class="fa-solid fa-building text-lg w-5"></i> <span>Partner Directory</span>
                        </a>
                        <a href="/siwes/coordinator/reports" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm transition-all <?= $linkClass('/coordinator/reports') ?>">
                            <i class="fa-solid fa-file-invoice text-lg w-5"></i> <span>Print Reports</span>
                        </a>
                    <?php endif; ?>

                    <?php if ($role === 'officer'): ?>
                        <a href="/siwes/officer/dashboard" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm transition-all <?= $linkClass('/officer/dashboard') ?>">
                            <i class="fa-solid fa-chart-line text-lg w-5"></i> <span>Dashboard</span>
                        </a>
                        <a href="/siwes/coordinator/students" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm transition-all <?= $linkClass('/coordinator/students') ?>">
                            <i class="fa-solid fa-users text-lg w-5"></i> <span>Department Students</span>
                        </a>
                        <a href="/siwes/coordinator/reports" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm transition-all <?= $linkClass('/coordinator/reports') ?>">
                            <i class="fa-solid fa-file-invoice text-lg w-5"></i> <span>Department Reports</span>
                        </a>
                    <?php endif; ?>

                    <?php if ($role === 'student'): ?>
                        <a href="/siwes/student/dashboard" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm transition-all <?= $linkClass('/student/dashboard') ?>">
                            <i class="fa-solid fa-gauge-high text-lg w-5"></i> <span>Overview Dashboard</span>
                        </a>
                        <a href="/siwes/student/logbook" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm transition-all <?= $linkClass('/student/logbook') ?>">
                            <i class="fa-solid fa-book-open text-lg w-5"></i> <span>Daily Logbook</span>
                        </a>
                    <?php endif; ?>

                    <?php if ($role === 'supervisor'): ?>
                        <a href="/siwes/supervisor/dashboard" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm transition-all <?= $linkClass('/supervisor/dashboard') ?>">
                            <i class="fa-solid fa-gauge-high text-lg w-5"></i> <span>My Students</span>
                        </a>
                        <a href="/siwes/supervisor/logbooks" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm transition-all <?= $linkClass('/supervisor/logbooks') ?>">
                            <i class="fa-solid fa-list-check text-lg w-5"></i> <span>Verify Logbooks</span>
                        </a>
                    <?php endif; ?>

                    <a href="/siwes/settings" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm transition-all <?= $linkClass('/settings') ?> mt-auto">
                        <i class="fa-solid fa-gears text-lg w-5"></i> <span>Portal Settings</span>
                    </a>
                </nav>

                <!-- Sidebar Footer User Profile -->
                <div class="pt-6 border-t border-slate-800/50 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-accent text-[#0A0E1A] font-bold flex items-center justify-center uppercase shadow-sm overflow-hidden shrink-0">
                            <?php if (!empty($user['profile_pic'])): ?>
                                <img src="<?= asset($user['profile_pic']) ?>" alt="Avatar" class="w-full h-full object-cover">
                            <?php else: ?>
                                <?= substr($username, 0, 2) ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <p class="text-sm font-bold leading-tight text-white"><?= htmlspecialchars($username) ?></p>
                            <p class="text-[10px] text-slate-400 uppercase font-semibold mt-0.5"><?= htmlspecialchars($role) ?></p>
                        </div>
                    </div>
                    <a href="/siwes/auth/logout" class="text-slate-400 hover:text-danger p-2 bg-slate-800/40 hover:bg-slate-800 rounded-lg transition" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </a>
                </div>
            </aside>

            <!-- Inner Floating Content Board Panel -->
            <div class="flex-1 flex flex-col bg-white dark:bg-darkCard rounded-none md:rounded-[24px] m-0 md:m-3 overflow-hidden shadow-soft border border-slate-100 dark:border-slate-800">
                
                <!-- Content Top Bar controls -->
                <header class="bg-white dark:bg-darkCard border-b border-slate-100 dark:border-slate-800 px-6 py-4 flex items-center justify-between sticky top-0 z-30">
                    <div class="flex items-center space-x-4">
                        <!-- Mobile Hamburger menu button -->
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-slate-500 hover:text-slate-800 p-2 rounded-lg bg-slate-100 dark:bg-slate-800 dark:text-gray-300 transition-colors">
                            <i class="fa-solid fa-bars text-lg"></i>
                        </button>
                        <h2 class="text-lg font-bold font-poppins text-slate-800 dark:text-white capitalize"><?= htmlspecialchars($role) ?> Dashboard</h2>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Dark mode toggles -->
                        <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light');" class="text-slate-400 hover:text-accent p-2 rounded-lg bg-slate-100 dark:bg-slate-800 transition-colors" title="Toggle Light/Dark Mode">
                            <i x-show="!darkMode" class="fa-solid fa-moon text-lg"></i>
                            <i x-show="darkMode" class="fa-solid fa-sun text-lg"></i>
                        </button>

                        <!-- Notification trigger -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="text-slate-400 hover:text-accent p-2 rounded-lg bg-slate-100 dark:bg-slate-800 transition-colors relative">
                                <i class="fa-solid fa-bell text-lg"></i>
                                <span class="absolute top-1 right-1 w-2 h-2 bg-danger rounded-full ring-2 ring-white dark:ring-darkCard"></span>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-80 bg-white dark:bg-darkCard rounded-2xl shadow-xl border border-slate-150 dark:border-slate-800 py-2 z-50 text-slate-800 dark:text-white">
                                <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-800 font-semibold flex justify-between items-center">
                                    <span>Notifications</span>
                                    <span class="text-xs bg-accent/10 text-[#0A0E1A] px-2 py-0.5 rounded-full font-bold">New</span>
                                </div>
                                <div class="p-3 text-xs text-slate-500 dark:text-gray-400">SIWES system database seeded successfully. Ready for evaluation matching.</div>
                            </div>
                        </div>

                        <!-- User Profile Info -->
                        <div class="flex items-center space-x-2 pl-2 border-l border-slate-200 dark:border-slate-800">
                            <div class="w-8 h-8 rounded-lg bg-accent text-[#0A0E1A] font-bold flex items-center justify-center uppercase shadow-sm overflow-hidden shrink-0">
                                <?php if (!empty($user['profile_pic'])): ?>
                                    <img src="<?= asset($user['profile_pic']) ?>" alt="Avatar" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <?= substr($username, 0, 2) ?>
                                <?php endif; ?>
                            </div>
                            <span class="text-xs font-bold hidden sm:inline-block dark:text-white"><?= htmlspecialchars($username) ?></span>
                        </div>
                    </div>
                </header>

                <!-- Page content viewport -->
                <main class="flex-1 overflow-y-auto overflow-x-hidden p-6">
                    <!-- Toast notifications -->
                    <?php if (Session::hasFlash('success')): ?>
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mb-6 bg-success/15 border-l-4 border-success p-4 rounded-r-xl flex items-center justify-between text-success dark:bg-success/5">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-circle-check text-lg"></i>
                                <span class="text-sm font-semibold"><?= Session::getFlash('success') ?></span>
                            </div>
                            <button @click="show = false" class="text-success/75 hover:text-success"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                    <?php endif; ?>

                    <?php if (Session::hasFlash('error')): ?>
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mb-6 bg-danger/15 border-l-4 border-danger p-4 rounded-r-xl flex items-center justify-between text-danger dark:bg-danger/5">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                                <span class="text-sm font-semibold"><?= Session::getFlash('error') ?></span>
                            </div>
                            <button @click="show = false" class="text-danger/75 hover:text-danger"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                    <?php endif; ?>

                    <?= $content ?>
                </main>
            </div>
        </div>

    <?php else: ?>
        <!-- ================= GUEST LANDING PORTAL SHELL ================= -->
        <!-- Top Navigation Bar -->
        <header class="bg-[#0A0E1A] border-b border-slate-800 sticky top-0 z-40 w-full px-6 py-4 flex items-center justify-between text-white max-w-[1600px] mx-auto">
            <div class="flex items-center space-x-8">
                <a href="/siwes/" class="flex items-center space-x-2">
                    <?php if (!empty($settings['logo_path'])): ?>
                        <div class="rounded-xl overflow-hidden w-10 h-10 shadow-md">
                            <img src="<?= asset($settings['logo_path']) ?>" alt="Logo" class="w-full h-full object-cover">
                        </div>
                    <?php else: ?>
                        <div class="bg-accent text-[#0A0E1A] p-2 rounded-xl flex items-center justify-center w-10 h-10 shadow-md shadow-accent/20">
                            <i class="fa-solid fa-graduation-cap text-lg"></i>
                        </div>
                    <?php endif; ?>
                    <div>
                        <span class="font-poppins font-bold text-lg text-white leading-none tracking-tight"><?= htmlspecialchars($settings['system_name'] ?? 'SIWES') ?></span>
                        <span class="text-xs text-accent font-semibold block uppercase tracking-wider">Placement Portal</span>
                    </div>
                </a>
                
                <!-- Navigation Links -->
                <nav class="hidden md:flex space-x-6 text-sm font-medium text-slate-300">
                    <a href="/siwes/" class="hover:text-accent transition-colors border-b-2 border-accent pb-1 text-white">Home</a>
                    <a href="#specialized-fields" class="hover:text-accent transition-colors pb-1">Specializations</a>
                    <a href="#faq" class="hover:text-accent transition-colors pb-1">FAQ</a>
                    <a href="#contact" class="hover:text-accent transition-colors pb-1">Contact</a>
                </nav>
            </div>

            <div class="flex items-center space-x-4">
                <!-- Theme toggle -->
                <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light');" class="text-slate-400 hover:text-accent p-2 rounded-lg bg-slate-800 transition-colors" title="Toggle Light/Dark Mode">
                    <i x-show="!darkMode" class="fa-solid fa-moon text-lg"></i>
                    <i x-show="darkMode" class="fa-solid fa-sun text-lg"></i>
                </button>

                <!-- Hamburger menu button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-slate-400 hover:text-accent p-2 rounded-lg bg-slate-800 transition-colors">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>

                <div class="hidden sm:flex items-center space-x-3">
                    <a href="/siwes/auth/login" class="text-white hover:text-accent font-semibold text-sm px-4 py-2 border border-slate-700 hover:border-accent rounded-xl transition">Log In</a>
                    <a href="/siwes/auth/register" class="bg-accent hover:bg-accent/90 text-[#0A0E1A] font-bold text-sm px-5 py-2.5 rounded-xl transition shadow-md shadow-accent/20">Sign Up</a>
                </div>
            </div>
        </header>

        <!-- Main View content -->
        <main class="flex-1 bg-white dark:bg-darkBg max-w-[1600px] mx-auto w-full shadow-xl overflow-hidden">
            <!-- Alert toasts -->
            <?php if (Session::hasFlash('success')): ?>
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mx-6 mt-6 bg-success/15 border-l-4 border-success p-4 rounded-r-xl flex items-center justify-between text-success dark:bg-success/5 animate-pulse">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-circle-check text-lg"></i>
                        <span class="text-sm font-semibold"><?= Session::getFlash('success') ?></span>
                    </div>
                    <button @click="show = false" class="text-success/75 hover:text-success"><i class="fa-solid fa-xmark"></i></button>
                </div>
            <?php endif; ?>

            <?php if (Session::hasFlash('error')): ?>
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mx-6 mt-6 bg-danger/15 border-l-4 border-danger p-4 rounded-r-xl flex items-center justify-between text-danger dark:bg-danger/5">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                        <span class="text-sm font-semibold"><?= Session::getFlash('error') ?></span>
                    </div>
                    <button @click="show = false" class="text-danger/75 hover:text-danger"><i class="fa-solid fa-xmark"></i></button>
                </div>
            <?php endif; ?>

            <?= $content ?>
        </main>
    <?php endif; ?>

    <!-- Mobile-only floating bottom nav bar (Only for logged in users) -->
    <?php if ($user): ?>
        <nav class="md:hidden bg-white dark:bg-darkCard border-t border-slate-200 dark:border-slate-800 py-2 px-6 flex justify-between items-center select-none sticky bottom-0 z-40 text-slate-500">
            <?php if ($role === 'student'): ?>
                <a href="/siwes/student/dashboard" class="flex flex-col items-center hover:text-accent">
                    <i class="fa-solid fa-gauge-high text-lg"></i><span class="text-[10px] mt-0.5">Overview</span>
                </a>
                <a href="/siwes/student/logbook" class="flex flex-col items-center hover:text-accent">
                    <i class="fa-solid fa-book-open text-lg"></i><span class="text-[10px] mt-0.5">Logbook</span>
                </a>
            <?php elseif ($role === 'supervisor'): ?>
                <a href="/siwes/supervisor/dashboard" class="flex flex-col items-center hover:text-accent">
                    <i class="fa-solid fa-users text-lg"></i><span class="text-[10px] mt-0.5">Students</span>
                </a>
                <a href="/siwes/supervisor/logbooks" class="flex flex-col items-center hover:text-accent">
                    <i class="fa-solid fa-list-check text-lg"></i><span class="text-[10px] mt-0.5">Approve</span>
                </a>
            <?php else: ?>
                <a href="/siwes/<?= $role ?>/dashboard" class="flex flex-col items-center hover:text-accent">
                    <i class="fa-solid fa-chart-line text-lg"></i><span class="text-[10px] mt-0.5">Dashboard</span>
                </a>
                <a href="/siwes/coordinator/allocations" class="flex flex-col items-center hover:text-accent">
                    <i class="fa-solid fa-shuffle text-lg"></i><span class="text-[10px] mt-0.5">Assign</span>
                </a>
            <?php endif; ?>
            <a href="/siwes/settings" class="flex flex-col items-center hover:text-accent">
                <i class="fa-solid fa-user-gear text-lg"></i><span class="text-[10px] mt-0.5">Settings</span>
            </a>
        </nav>
    <?php endif; ?>

    <!-- Mobile Navigation Drawer Panel -->
    <div x-show="mobileMenuOpen" x-transition.opacity class="fixed inset-0 bg-black/60 z-50 md:hidden" @click="mobileMenuOpen = false" style="display: none;"></div>
    <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="fixed inset-y-0 left-0 w-72 bg-white dark:bg-darkCard shadow-2xl z-50 md:hidden flex flex-col p-6 border-r border-slate-200 dark:border-slate-800" style="display: none;">
        <!-- Drawer Header -->
        <div class="flex items-center justify-between pb-6 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center space-x-2">
                <?php if (!empty($settings['logo_path'])): ?>
                    <div class="rounded-xl overflow-hidden w-8 h-8">
                        <img src="<?= asset($settings['logo_path']) ?>" alt="Logo" class="w-full h-full object-cover">
                    </div>
                <?php else: ?>
                    <div class="bg-accent text-[#0A0E1A] p-2 rounded-xl flex items-center justify-center w-8 h-8">
                        <i class="fa-solid fa-graduation-cap text-sm"></i>
                    </div>
                <?php endif; ?>
                <span class="font-poppins font-bold text-base text-slate-800 dark:text-white"><?= htmlspecialchars($settings['system_name'] ?? 'SIWES') ?></span>
            </div>
            <button @click="mobileMenuOpen = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 dark:hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Navigation Lists -->
        <div class="flex-1 overflow-y-auto py-6 space-y-4">
            <?php if ($user): ?>
                <div class="text-xs text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider mb-2">Navigation Menu</div>
                
                <?php if ($role === 'admin'): ?>
                    <a href="/siwes/admin/dashboard" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-slate-105 dark:hover:bg-slate-800 transition text-slate-700 dark:text-gray-300">
                        <i class="fa-solid fa-chart-line text-lg w-5"></i> <span>Dashboard</span>
                    </a>
                    <a href="/siwes/coordinator/allocations" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-slate-105 dark:hover:bg-slate-800 transition text-slate-700 dark:text-gray-300">
                        <i class="fa-solid fa-shuffle text-lg w-5"></i> <span>SIWES Allocations</span>
                    </a>
                    <a href="/siwes/coordinator/students" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-slate-105 dark:hover:bg-slate-800 transition text-slate-700 dark:text-gray-300">
                        <i class="fa-solid fa-users text-lg w-5"></i> <span>Manage Students</span>
                    </a>
                    <a href="/siwes/coordinator/companies" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-slate-105 dark:hover:bg-slate-800 transition text-slate-700 dark:text-gray-300">
                        <i class="fa-solid fa-building text-lg w-5"></i> <span>Manage Companies</span>
                    </a>
                    <a href="/siwes/coordinator/reports" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-slate-105 dark:hover:bg-slate-800 transition text-slate-700 dark:text-gray-300">
                        <i class="fa-solid fa-file-invoice text-lg w-5"></i> <span>Reports & Export</span>
                    </a>
                <?php endif; ?>

                <?php if ($role === 'coordinator'): ?>
                    <a href="/siwes/coordinator/dashboard" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-slate-105 dark:hover:bg-slate-800 transition text-slate-700 dark:text-gray-300">
                        <i class="fa-solid fa-chart-line text-lg w-5"></i> <span>Dashboard</span>
                    </a>
                    <a href="/siwes/coordinator/allocations" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-slate-105 dark:hover:bg-slate-800 transition text-slate-700 dark:text-gray-300">
                        <i class="fa-solid fa-shuffle text-lg w-5"></i> <span>SIWES Allocations</span>
                    </a>
                    <a href="/siwes/coordinator/students" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-slate-105 dark:hover:bg-slate-800 transition text-slate-700 dark:text-gray-300">
                        <i class="fa-solid fa-users text-lg w-5"></i> <span>Students Audit</span>
                    </a>
                    <a href="/siwes/coordinator/companies" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-slate-105 dark:hover:bg-slate-800 transition text-slate-700 dark:text-gray-300">
                        <i class="fa-solid fa-building text-lg w-5"></i> <span>Partner Directory</span>
                    </a>
                    <a href="/siwes/coordinator/reports" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-slate-105 dark:hover:bg-slate-800 transition text-slate-700 dark:text-gray-300">
                        <i class="fa-solid fa-file-invoice text-lg w-5"></i> <span>Print Reports</span>
                    </a>
                <?php endif; ?>

                <?php if ($role === 'officer'): ?>
                    <a href="/siwes/officer/dashboard" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-slate-105 dark:hover:bg-slate-800 transition text-slate-700 dark:text-gray-300">
                        <i class="fa-solid fa-chart-line text-lg w-5"></i> <span>Dashboard</span>
                    </a>
                    <a href="/siwes/coordinator/students" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-slate-105 dark:hover:bg-slate-800 transition text-slate-700 dark:text-gray-300">
                        <i class="fa-solid fa-users text-lg w-5"></i> <span>Department Students</span>
                    </a>
                    <a href="/siwes/coordinator/reports" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-slate-105 dark:hover:bg-slate-800 transition text-slate-700 dark:text-gray-300">
                        <i class="fa-solid fa-file-invoice text-lg w-5"></i> <span>Department Reports</span>
                    </a>
                <?php endif; ?>

                <?php if ($role === 'student'): ?>
                    <a href="/siwes/student/dashboard" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-slate-105 dark:hover:bg-slate-800 transition text-slate-700 dark:text-gray-300">
                        <i class="fa-solid fa-gauge-high text-lg w-5"></i> <span>Overview Dashboard</span>
                    </a>
                    <a href="/siwes/student/logbook" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-slate-105 dark:hover:bg-slate-800 transition text-slate-700 dark:text-gray-300">
                        <i class="fa-solid fa-book-open text-lg w-5"></i> <span>Daily Logbook</span>
                    </a>
                <?php endif; ?>

                <?php if ($role === 'supervisor'): ?>
                    <a href="/siwes/supervisor/dashboard" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-slate-105 dark:hover:bg-slate-800 transition text-slate-700 dark:text-gray-300">
                        <i class="fa-solid fa-gauge-high text-lg w-5"></i> <span>My Students</span>
                    </a>
                    <a href="/siwes/supervisor/logbooks" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-slate-105 dark:hover:bg-slate-800 transition text-slate-700 dark:text-gray-300">
                        <i class="fa-solid fa-list-check text-lg w-5"></i> <span>Verify Logbooks</span>
                    </a>
                <?php endif; ?>
            <?php else: ?>
                <!-- Guest links -->
                <div class="text-xs text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider mb-2">Public Links</div>
                <a href="/siwes/" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-slate-105 dark:hover:bg-slate-800 transition text-slate-700 dark:text-gray-300">
                    <i class="fa-solid fa-house text-lg w-5"></i> <span>Home</span>
                </a>
                <a href="#specialized-fields" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-slate-105 dark:hover:bg-slate-800 transition text-slate-700 dark:text-gray-300">
                    <i class="fa-solid fa-briefcase text-lg w-5"></i> <span>Specializations</span>
                </a>
                <a href="#faq" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-slate-105 dark:hover:bg-slate-800 transition text-slate-700 dark:text-gray-300">
                    <i class="fa-solid fa-circle-question text-lg w-5"></i> <span>FAQ</span>
                </a>
                <a href="#contact" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-slate-105 dark:hover:bg-slate-800 transition text-slate-700 dark:text-gray-300">
                    <i class="fa-solid fa-envelope text-lg w-5"></i> <span>Contact</span>
                </a>
            <?php endif; ?>
        </div>

        <!-- Drawer Action Footer -->
        <div class="pt-6 border-t border-slate-100 dark:border-slate-800 space-y-3">
            <?php if ($user): ?>
                <a href="/siwes/settings" @click="mobileMenuOpen = false" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium hover:bg-slate-105 dark:hover:bg-slate-800 transition text-slate-700 dark:text-gray-300">
                    <i class="fa-solid fa-gears text-lg w-5"></i> <span>Portal Settings</span>
                </a>
                <a href="/siwes/auth/logout" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium bg-danger/10 hover:bg-danger/20 text-danger transition">
                    <i class="fa-solid fa-arrow-right-from-bracket text-lg w-5"></i> <span>Log Out</span>
                </a>
            <?php else: ?>
                <a href="/siwes/auth/login" @click="mobileMenuOpen = false" class="block text-center text-slate-700 dark:text-white hover:text-accent font-semibold text-sm px-4 py-2 border border-slate-200 dark:border-slate-700 hover:border-accent rounded-xl transition w-full">Log In</a>
                <a href="/siwes/auth/register" @click="mobileMenuOpen = false" class="block text-center bg-accent hover:bg-accent/90 text-[#0A0E1A] font-bold text-sm px-5 py-2.5 rounded-xl transition w-full">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
