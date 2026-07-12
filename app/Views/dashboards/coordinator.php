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
                <h1 class="text-3xl font-poppins font-bold text-slate-800 dark:text-white">SIWES Coordinator Workspace</h1>
                <p class="text-sm text-slate-500 dark:text-gray-400">Review industry partners, approve attachments, and run matching algorithms.</p>
            </div>
        </div>
        <div class="flex space-x-2 no-print">
            <form action="/siwes/coordinator/allocations/run" method="POST">
                <button type="submit" class="bg-primary hover:bg-secondary text-white text-sm px-5 py-2.5 rounded-xl font-semibold shadow-md shadow-primary/20 transition flex items-center space-x-2">
                    <i class="fa-solid fa-bolt"></i> <span>Run Auto-Allocation</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <div class="bg-white dark:bg-darkCard rounded-3xl p-5 border border-slate-100 dark:border-slate-800 shadow-soft">
            <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Unallocated Students</span>
            <span class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1 block"><?= $metrics['unallocated_students'] ?></span>
        </div>
        <div class="bg-white dark:bg-darkCard rounded-3xl p-5 border border-slate-100 dark:border-slate-800 shadow-soft">
            <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Allocated Placements</span>
            <span class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1 block"><?= $metrics['allocated_students'] ?></span>
        </div>
        <div class="bg-white dark:bg-darkCard rounded-3xl p-5 border border-slate-100 dark:border-slate-800 shadow-soft">
            <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Pending Companies</span>
            <span class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1 block"><?= $metrics['pending_companies'] ?></span>
        </div>
        <div class="bg-white dark:bg-darkCard rounded-3xl p-5 border border-slate-100 dark:border-slate-800 shadow-soft">
            <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Pending Logbook Checks</span>
            <span class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1 block"><?= $metrics['pending_logbooks'] ?></span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Section 1: Pending Company Verifications -->
        <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6">
            <h3 class="text-base font-bold text-slate-800 dark:text-white mb-4"><i class="fa-solid fa-building-circle-exclamation mr-2 text-warning"></i> Pending Partner Applications</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-500 dark:text-gray-400">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-700 dark:text-gray-200">
                            <th class="py-3 font-semibold">Company Name</th>
                            <th class="py-3 font-semibold">Industry</th>
                            <th class="py-3 font-semibold">Slots</th>
                            <th class="py-3 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <?php if (empty($pendingCompanies)): ?>
                            <tr>
                                <td colspan="4" class="py-4 text-center text-slate-400">No pending company registrations.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pendingCompanies as $c): ?>
                                <tr>
                                    <td class="py-3 font-medium text-slate-800 dark:text-white"><?= htmlspecialchars($c['name']) ?></td>
                                    <td class="py-3 text-xs"><?= htmlspecialchars($c['industry_category']) ?></td>
                                    <td class="py-3 font-bold text-slate-700 dark:text-gray-300"><?= $c['available_slots'] ?></td>
                                    <td class="py-3 text-right">
                                        <div class="inline-flex space-x-1.5">
                                            <form action="/siwes/coordinator/company/verify" method="POST">
                                                <input type="hidden" name="company_id" value="<?= $c['id'] ?>">
                                                <input type="hidden" name="status" value="verified">
                                                <button type="submit" class="bg-success/10 hover:bg-success text-success hover:text-white px-2.5 py-1.5 rounded-lg text-xs font-bold transition"><i class="fa-solid fa-check"></i></button>
                                            </form>
                                            <form action="/siwes/coordinator/company/verify" method="POST">
                                                <input type="hidden" name="company_id" value="<?= $c['id'] ?>">
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="bg-danger/10 hover:bg-danger text-danger hover:text-white px-2.5 py-1.5 rounded-lg text-xs font-bold transition"><i class="fa-solid fa-xmark"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 2: Unallocated Students List -->
        <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6">
            <h3 class="text-base font-bold text-slate-800 dark:text-white mb-4"><i class="fa-solid fa-user-clock mr-2 text-primary"></i> Students Pending Allocation</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-500 dark:text-gray-400">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-700 dark:text-gray-200">
                            <th class="py-3 font-semibold">Student Name</th>
                            <th class="py-3 font-semibold">Dept</th>
                            <th class="py-3 font-semibold">Course</th>
                            <th class="py-3 font-semibold text-right">Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <?php if (empty($pendingStudents)): ?>
                            <tr>
                                <td colspan="4" class="py-4 text-center text-slate-400">All students have been allocated placements.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pendingStudents as $s): ?>
                                <tr>
                                    <td class="py-3 font-medium text-slate-800 dark:text-white"><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?></td>
                                    <td class="py-3 text-xs font-semibold"><?= htmlspecialchars($s['dept_code']) ?></td>
                                    <td class="py-3 text-xs"><?= htmlspecialchars($s['course_of_study']) ?></td>
                                    <td class="py-3 text-right">
                                        <a href="/siwes/coordinator/allocations" class="bg-slate-100 hover:bg-primary hover:text-white dark:bg-slate-800 dark:hover:bg-primary px-3 py-1.5 rounded-lg text-xs font-semibold transition text-slate-600 dark:text-gray-300">Assign</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
