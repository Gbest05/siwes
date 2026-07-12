<?php
use App\Core\Auth;
$user = Auth::user();
?>
<div class="space-y-8 animate-fade-in-up">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <a href="/siwes/settings" class="w-16 h-16 rounded-2xl bg-accent text-[#0A0E1A] font-bold flex items-center justify-center text-2xl uppercase shadow-md overflow-hidden shrink-0 hover:opacity-90 transition-opacity" title="Change Profile Picture">
                <?php if (!empty($user['profile_pic'])): ?>
                    <img src="<?= asset($user['profile_pic']) ?>" alt="Profile Photo" class="w-full h-full object-cover">
                <?php else: ?>
                    <?= substr($user['username'], 0, 2) ?>
                <?php endif; ?>
            </a>
            <div>
                <h1 class="text-3xl font-poppins font-bold text-slate-800 dark:text-white">Admin Dashboard</h1>
                <p class="text-sm text-slate-500 dark:text-gray-400">SIWES Placement Portal global system status.</p>
            </div>
        </div>
        <div class="flex space-x-2 no-print">
            <a href="/siwes/coordinator/allocations" class="bg-primary hover:bg-secondary text-white text-sm px-4 py-2.5 rounded-xl font-semibold shadow-md shadow-primary/20 transition flex items-center space-x-2">
                <i class="fa-solid fa-shuffle"></i> <span>Manage Allocations</span>
            </a>
            <a href="/siwes/coordinator/reports" class="bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-white border border-slate-200 dark:border-slate-700 text-sm px-4 py-2.5 rounded-xl font-semibold transition flex items-center space-x-2">
                <i class="fa-solid fa-file-invoice text-accent"></i> <span>View Reports</span>
            </a>
        </div>
    </div>

    <!-- Statistics Overview Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <!-- Card 1 -->
        <div class="bg-white dark:bg-darkCard rounded-3xl p-5 border border-slate-100 dark:border-slate-800 shadow-soft flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Total Students</span>
                <span class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1 block"><?= $metrics['total_students'] ?></span>
            </div>
            <div class="bg-primary/10 text-primary w-12 h-12 rounded-2xl flex items-center justify-center">
                <i class="fa-solid fa-graduation-cap text-xl"></i>
            </div>
        </div>
        <!-- Card 2 -->
        <div class="bg-white dark:bg-darkCard rounded-3xl p-5 border border-slate-100 dark:border-slate-800 shadow-soft flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Total Companies</span>
                <span class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1 block"><?= $metrics['total_companies'] ?></span>
            </div>
            <div class="bg-accent/10 text-accent w-12 h-12 rounded-2xl flex items-center justify-center">
                <i class="fa-solid fa-building text-xl"></i>
            </div>
        </div>
        <!-- Card 3 -->
        <div class="bg-white dark:bg-darkCard rounded-3xl p-5 border border-slate-100 dark:border-slate-800 shadow-soft flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Total Supervisors</span>
                <span class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1 block"><?= $metrics['total_supervisors'] ?></span>
            </div>
            <div class="bg-warning/10 text-warning w-12 h-12 rounded-2xl flex items-center justify-center">
                <i class="fa-solid fa-user-tie text-xl"></i>
            </div>
        </div>
        <!-- Card 4 -->
        <div class="bg-white dark:bg-darkCard rounded-3xl p-5 border border-slate-100 dark:border-slate-800 shadow-soft flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Active Placements</span>
                <span class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1 block"><?= $metrics['total_allocations'] ?></span>
            </div>
            <div class="bg-success/10 text-success w-12 h-12 rounded-2xl flex items-center justify-center">
                <i class="fa-solid fa-link text-xl"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <!-- Card 5 -->
        <div class="bg-white dark:bg-darkCard rounded-2xl p-4 border border-slate-100 dark:border-slate-800 shadow-soft flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-slate-400 uppercase block">Pending Companies</span>
                <span class="text-xl font-bold text-slate-800 dark:text-white mt-0.5 block"><?= $metrics['pending_applications'] ?></span>
            </div>
            <span class="text-warning text-lg"><i class="fa-solid fa-circle-exclamation"></i></span>
        </div>
        <!-- Card 6 -->
        <div class="bg-white dark:bg-darkCard rounded-2xl p-4 border border-slate-100 dark:border-slate-800 shadow-soft flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-slate-400 uppercase block">Verified Companies</span>
                <span class="text-xl font-bold text-slate-800 dark:text-white mt-0.5 block"><?= $metrics['approved_applications'] ?></span>
            </div>
            <span class="text-success text-lg"><i class="fa-solid fa-circle-check"></i></span>
        </div>
        <!-- Card 7 -->
        <div class="bg-white dark:bg-darkCard rounded-2xl p-4 border border-slate-100 dark:border-slate-800 shadow-soft flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-slate-400 uppercase block">Available Slots</span>
                <span class="text-xl font-bold text-slate-800 dark:text-white mt-0.5 block"><?= $metrics['available_slots'] ?? 0 ?></span>
            </div>
            <span class="text-primary text-lg"><i class="fa-solid fa-hashtag"></i></span>
        </div>
        <!-- Card 8 -->
        <div class="bg-white dark:bg-darkCard rounded-2xl p-4 border border-slate-100 dark:border-slate-800 shadow-soft flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-slate-400 uppercase block">Completed SIWES</span>
                <span class="text-xl font-bold text-slate-800 dark:text-white mt-0.5 block"><?= $metrics['completed_siwes'] ?></span>
            </div>
            <span class="text-accent text-lg"><i class="fa-solid fa-clipboard-check"></i></span>
        </div>
    </div>

    <!-- Analytical Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 no-print">
        <!-- Chart 1 -->
        <div class="bg-white dark:bg-darkCard p-5 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4">Department Placements</h3>
            <div class="relative h-64">
                <canvas id="deptChart"></canvas>
            </div>
        </div>
        <!-- Chart 2 -->
        <div class="bg-white dark:bg-darkCard p-5 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4">Top Companies Capacity</h3>
            <div class="relative h-64">
                <canvas id="companyChart"></canvas>
            </div>
        </div>
        <!-- Chart 3 -->
        <div class="bg-white dark:bg-darkCard p-5 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4">Allocation Methods</h3>
            <div class="relative h-64">
                <canvas id="allocationTypeChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activities Log Table -->
    <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6">
        <h3 class="text-base font-bold text-slate-800 dark:text-white mb-4"><i class="fa-solid fa-receipt mr-2 text-primary"></i> System Activity Logs</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-500 dark:text-gray-400">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-700 dark:text-gray-200">
                        <th class="py-3 font-semibold">User</th>
                        <th class="py-3 font-semibold">Activity Action</th>
                        <th class="py-3 font-semibold">IP Address</th>
                        <th class="py-3 font-semibold">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php if (empty($activities)): ?>
                        <tr>
                            <td colspan="4" class="py-4 text-center">No system activities recorded.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($activities as $act): ?>
                            <tr>
                                <td class="py-3 font-medium text-slate-800 dark:text-white"><?= htmlspecialchars($act['username'] ?? 'Anonymous') ?></td>
                                <td class="py-3"><?= htmlspecialchars($act['action']) ?></td>
                                <td class="py-3 text-xs"><?= htmlspecialchars($act['ip_address']) ?></td>
                                <td class="py-3 text-xs text-slate-400"><?= htmlspecialchars($act['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart JS Setup Script -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Department placements Chart
        const deptLabels = <?= json_encode(array_column($deptStats, 'code')) ?>;
        const deptCounts = <?= json_encode(array_column($deptStats, 'count')) ?>;
        new Chart(document.getElementById('deptChart'), {
            type: 'bar',
            data: {
                labels: deptLabels,
                datasets: [{
                    label: 'Students Count',
                    data: deptCounts,
                    backgroundColor: '#2563EB',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        // 2. Company Capacity Chart
        const compLabels = <?= json_encode(array_column($companyCapacity, 'name')) ?>;
        const compSlots = <?= json_encode(array_column($companyCapacity, 'available_slots')) ?>;
        new Chart(document.getElementById('companyChart'), {
            type: 'horizontalBar' || 'bar', // fall back to standard bar for vertical comparison
            data: {
                labels: compLabels.map(l => l.length > 15 ? l.substring(0, 15) + '...' : l),
                datasets: [{
                    data: compSlots,
                    backgroundColor: '#14B8A6',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // 3. Allocation Type Chart
        const allocTypes = <?= json_encode($allocationTypes) ?>;
        new Chart(document.getElementById('allocationTypeChart'), {
            type: 'doughnut',
            data: {
                labels: ['Automatic', 'Manual'],
                datasets: [{
                    data: [allocTypes.auto, allocTypes.manual],
                    backgroundColor: ['#2563EB', '#10B981'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    });
</script>
