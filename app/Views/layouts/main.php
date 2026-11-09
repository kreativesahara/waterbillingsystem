<?php
use App\Core\Auth;
$currentUser = Auth::user();
$isAdmin = Auth::isAdmin();
$isTenant = Auth::isTenant();
$active = $activeNav ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Classic Heights Water Billing') ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- SweetAlert2 -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            body { background: white !important; color: black !important; }
        }
    </style>
</head>
<body class="h-full flex flex-col font-sans text-slate-800 antialiased">
    <div class="min-h-full flex flex-col">
        <!-- Top Navigation Bar -->
        <header class="bg-slate-900 border-b border-slate-800 sticky top-0 z-40 shadow-sm no-print">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Brand -->
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-sky-500 to-cyan-400 flex items-center justify-center text-white shadow-md shadow-sky-500/20">
                            <i data-lucide="droplet" class="w-6 h-6 fill-white"></i>
                        </div>
                        <div>
                            <span class="text-white font-bold text-lg tracking-tight">Classic Heights</span>
                            <span class="text-sky-400 text-xs block font-medium">Water Management System</span>
                        </div>
                    </div>

                    <!-- Role Badge & User Profile -->
                    <div class="flex items-center space-x-4">
                        <?php if ($currentUser): ?>
                            <div class="hidden sm:flex items-center space-x-2 bg-slate-800/80 px-3 py-1.5 rounded-full border border-slate-700">
                                <span class="w-2 h-2 rounded-full <?= $isAdmin ? 'bg-amber-400 animate-pulse' : 'bg-emerald-400' ?>"></span>
                                <span class="text-xs font-semibold uppercase tracking-wider text-slate-300"><?= $currentUser['role'] ?></span>
                                <span class="text-slate-500">|</span>
                                <span class="text-xs font-medium text-slate-200"><?= htmlspecialchars($currentUser['fullname']) ?></span>
                            </div>
                            <a href="/logout" class="inline-flex items-center space-x-1 text-slate-400 hover:text-rose-400 text-xs font-semibold py-1.5 px-3 rounded-lg border border-slate-700 hover:border-rose-900 hover:bg-rose-950/30 transition">
                                <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                                <span class="hidden sm:inline">Logout</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Secondary Subnav for Admin -->
            <?php if ($isAdmin): ?>
            <div class="bg-slate-800/95 border-t border-slate-700/60 px-4 sm:px-6 lg:px-8">
                <div class="max-w-7xl mx-auto flex space-x-1 sm:space-x-6 overflow-x-auto py-2">
                    <a href="/admin/dashboard" class="<?= $active === 'dashboard' ? 'text-sky-400 bg-slate-700/60 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-700/40' ?> px-3 py-1.5 rounded-lg text-sm flex items-center space-x-2 transition">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="/admin/meter-entry" class="<?= $active === 'meter_entry' ? 'text-sky-400 bg-slate-700/60 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-700/40' ?> px-3 py-1.5 rounded-lg text-sm flex items-center space-x-2 transition">
                        <i data-lucide="gauge" class="w-4 h-4"></i>
                        <span>Record Reading</span>
                    </a>
                    <a href="/admin/records" class="<?= $active === 'records' ? 'text-sky-400 bg-slate-700/60 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-700/40' ?> px-3 py-1.5 rounded-lg text-sm flex items-center space-x-2 transition">
                        <i data-lucide="receipt" class="w-4 h-4"></i>
                        <span>Billing Records</span>
                    </a>
                    <a href="/admin/tenants" class="<?= $active === 'tenants' ? 'text-sky-400 bg-slate-700/60 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-700/40' ?> px-3 py-1.5 rounded-lg text-sm flex items-center space-x-2 transition">
                        <i data-lucide="users" class="w-4 h-4"></i>
                        <span>Apartments & Tenants</span>
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Secondary Subnav for Tenant -->
            <?php if ($isTenant): ?>
            <div class="bg-slate-800/95 border-t border-slate-700/60 px-4 sm:px-6 lg:px-8">
                <div class="max-w-7xl mx-auto flex space-x-2 sm:space-x-6 overflow-x-auto py-2">
                    <a href="/tenant/dashboard" class="<?= $active === 'tenant_dashboard' ? 'text-sky-400 bg-slate-700/60 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-700/40' ?> px-3 py-1.5 rounded-lg text-sm flex items-center space-x-2 transition">
                        <i data-lucide="home" class="w-4 h-4"></i>
                        <span>My Water Bill</span>
                    </a>
                    <a href="/tenant/payments" class="<?= $active === 'tenant_payments' ? 'text-sky-400 bg-slate-700/60 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-700/40' ?> px-3 py-1.5 rounded-lg text-sm flex items-center space-x-2 transition">
                        <i data-lucide="activity" class="w-4 h-4"></i>
                        <span>Payments &amp; Activity Log</span>
                    </a>
                    <a href="/tenant/statement" class="<?= $active === 'tenant_statement' ? 'text-sky-400 bg-slate-700/60 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-700/40' ?> px-3 py-1.5 rounded-lg text-sm flex items-center space-x-2 transition">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        <span>Print Statement</span>
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </header>

        <!-- Main Content Canvas -->
        <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <?= $content ?>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-slate-200 mt-auto py-4 text-center text-xs text-slate-500 no-print">
            <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
                <div>
                    <span class="font-semibold text-slate-700">Classic Heights Apartments</span> &copy; <?= date('Y') ?> Lensmen Technologies. All rights reserved.
                </div>
                <div class="flex items-center space-x-4 text-slate-400">
                    <span>M-Pesa Paybill: <strong class="text-slate-600 font-mono">174379</strong></span>
                    <span>&bull;</span>
                    <span>Water Tariff: <strong class="text-slate-600">KSh 150 / m&sup3;</strong></span>
                </div>
            </div>
        </footer>
    </div>

    <!-- Initialize Lucide icons -->
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
