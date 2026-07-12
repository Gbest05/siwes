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
                <h1 class="text-3xl font-poppins font-bold text-slate-800 dark:text-white"><?= htmlspecialchars($department['name']) ?> Department Desk</h1>
                <p class="text-sm text-slate-500 dark:text-gray-400">Department Code: <span class="font-semibold text-slate-700 dark:text-gray-200"><?= htmlspecialchars($department['code']) ?></span> | Audit student records and export attachment sheets.</p>
            </div>
        </div>
        <a href="/siwes/coordinator/reports?type=students" class="bg-primary hover:bg-secondary text-white text-sm px-4 py-2.5 rounded-xl font-semibold shadow-md shadow-primary/20 transition flex items-center space-x-2 no-print">
            <i class="fa-solid fa-file-invoice"></i> <span>Export Department List</span>
        </a>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-darkCard rounded-3xl p-5 border border-slate-100 dark:border-slate-800 shadow-soft">
            <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Department Enrolment</span>
            <span class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1 block"><?= $stats['total_students'] ?></span>
        </div>
        <div class="bg-white dark:bg-darkCard rounded-3xl p-5 border border-slate-100 dark:border-slate-800 shadow-soft">
            <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Placed Attachments</span>
            <span class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1 block"><?= $stats['allocated_students'] ?></span>
        </div>
        <div class="bg-white dark:bg-darkCard rounded-3xl p-5 border border-slate-100 dark:border-slate-800 shadow-soft">
            <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Unplaced Students</span>
            <span class="text-3xl font-extrabold text-slate-800 dark:text-white mt-1 block"><?= $stats['unallocated_students'] ?></span>
        </div>
    </div>

    <!-- Department Students Table -->
    <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6">
        <h3 class="text-base font-bold text-slate-800 dark:text-white mb-4"><i class="fa-solid fa-graduation-cap mr-2 text-primary"></i> Department Student Audit</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-500 dark:text-gray-400">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-700 dark:text-gray-200">
                        <th class="py-3 font-semibold">Student Name</th>
                        <th class="py-3 font-semibold">Matric No</th>
                        <th class="py-3 font-semibold">Course</th>
                        <th class="py-3 font-semibold">Attachment Status</th>
                        <th class="py-3 font-semibold">Contact Email</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-400">No students registered in this department yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($students as $s): ?>
                            <tr>
                                <td class="py-3 font-medium text-slate-800 dark:text-white"><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?></td>
                                <td class="py-3 font-mono text-xs"><?= htmlspecialchars($s['matric_no']) ?></td>
                                <td class="py-3 text-xs"><?= htmlspecialchars($s['course_of_study']) ?></td>
                                <td class="py-3">
                                    <?php if ($s['siwes_status'] === 'unallocated'): ?>
                                        <span class="bg-warning/10 text-warning px-2.5 py-0.5 rounded-full text-xs font-semibold">Unplaced</span>
                                    <?php elseif ($s['siwes_status'] === 'allocated'): ?>
                                        <span class="bg-success/10 text-success px-2.5 py-0.5 rounded-full text-xs font-semibold">Placed</span>
                                    <?php else: ?>
                                        <span class="bg-primary/10 text-primary px-2.5 py-0.5 rounded-full text-xs font-semibold">Completed</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 text-xs"><?= htmlspecialchars($s['email']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
