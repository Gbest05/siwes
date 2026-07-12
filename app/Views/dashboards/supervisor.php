<?php
use App\Core\Auth;
$user = Auth::user();
?>
<div class="space-y-8 animate-fade-in-up" x-data="{ showEvalModal: false, activeStudentId: null, activeStudentName: '' }">
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
                <h1 class="text-3xl font-poppins font-bold text-slate-800 dark:text-white">Supervisor Workspace</h1>
                <p class="text-sm text-slate-500 dark:text-gray-400">Review student work hours, sign daily logs, and submit evaluations.</p>
            </div>
        </div>
        <a href="/siwes/supervisor/logbooks" class="bg-primary hover:bg-secondary text-white text-sm px-4 py-2.5 rounded-xl font-semibold shadow-md shadow-primary/20 transition flex items-center space-x-2 no-print">
            <i class="fa-solid fa-list-check"></i> <span>Review Logbooks (<?= count($pendingLogs) ?>)</span>
        </a>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 gap-8">
        <!-- Assigned Students List -->
        <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6">
            <h3 class="text-base font-bold text-slate-800 dark:text-white mb-4"><i class="fa-solid fa-users mr-2 text-primary"></i> Assigned Students</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-500 dark:text-gray-400">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-700 dark:text-gray-200">
                            <th class="py-3 font-semibold">Student Name</th>
                            <th class="py-3 font-semibold">Matric No</th>
                            <th class="py-3 font-semibold">Department</th>
                            <th class="py-3 font-semibold">Phone</th>
                            <th class="py-3 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <?php if (empty($students)): ?>
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-400">No students currently allocated to your company slots.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($students as $s): ?>
                                <tr>
                                    <td class="py-3 font-medium text-slate-800 dark:text-white"><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?></td>
                                    <td class="py-3 font-mono text-xs"><?= htmlspecialchars($s['matric_no']) ?></td>
                                    <td class="py-3 text-xs font-semibold"><?= htmlspecialchars($s['dept_code']) ?></td>
                                    <td class="py-3 text-xs"><?= htmlspecialchars($s['phone']) ?></td>
                                    <td class="py-3 text-right">
                                        <button @click="activeStudentId = '<?= $s['id'] ?>'; activeStudentName = '<?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?>'; showEvalModal = true" class="bg-success/15 hover:bg-success text-success hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition">Evaluate Student</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pending Logs Review -->
        <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6">
            <h3 class="text-base font-bold text-slate-800 dark:text-white mb-4"><i class="fa-solid fa-list-check mr-2 text-warning"></i> Pending Logbook Reviews (<?= count($pendingLogs) ?>)</h3>
            <?php if (empty($pendingLogs)): ?>
                <div class="py-8 text-center text-slate-400 dark:text-slate-500 space-y-2">
                    <i class="fa-solid fa-circle-check text-4xl mb-2 text-success/75"></i>
                    <p class="text-sm font-medium">All student log entries verified!</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach (array_slice($pendingLogs, 0, 3) as $log): ?>
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-700 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                            <div>
                                <span class="text-xs bg-primary/10 text-primary px-2.5 py-0.5 rounded-full font-semibold uppercase tracking-wider">Week <?= $log['week_number'] ?></span>
                                <h4 class="font-bold text-slate-800 dark:text-white mt-2 text-sm"><?= htmlspecialchars($log['student_name']) ?> (<?= htmlspecialchars($log['matric_no']) ?>) - <?= $log['date'] ?></h4>
                                <p class="text-xs text-slate-500 dark:text-gray-400 mt-1 max-w-xl italic">"<?= htmlspecialchars($log['activity_description']) ?>"</p>
                            </div>
                            <div class="shrink-0">
                                <a href="/siwes/supervisor/logbooks" class="bg-primary hover:bg-secondary text-white text-xs px-4 py-2 rounded-lg font-bold transition">Review Logs</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (count($pendingLogs) > 3): ?>
                        <div class="text-center pt-2">
                            <a href="/siwes/supervisor/logbooks" class="text-xs font-semibold text-primary hover:underline">View all pending logbooks (<?= count($pendingLogs) ?>) &rarr;</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Evaluation Modal -->
    <div x-show="showEvalModal" class="fixed inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition>
        <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 max-w-md w-full p-6 shadow-2xl space-y-6" @click.away="showEvalModal = false">
            <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">SIWES Performance Evaluation</h3>
                <button @click="showEvalModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            
            <form action="/siwes/supervisor/evaluate" method="POST" class="space-y-4">
                <input type="hidden" name="student_id" :value="activeStudentId">
                <div>
                    <span class="text-xs text-slate-400 uppercase font-semibold">Student Name</span>
                    <p class="font-bold text-slate-800 dark:text-white text-sm" x-text="activeStudentName"></p>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Internship Performance Score (0 - 100)</label>
                    <input type="number" name="score" min="0" max="100" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="85" required>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Supervisor Remarks</label>
                    <textarea name="remarks" rows="3" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Highlight technical skills, punctuality, and productivity..." required></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Recommendations</label>
                    <textarea name="recommendations" rows="2" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Suggestions for the student or university unit..."></textarea>
                </div>

                <button type="submit" class="w-full bg-success hover:bg-success-dark text-white py-3 rounded-xl font-semibold shadow-md shadow-success/25 transition">Submit Evaluation Sheet</button>
            </form>
        </div>
    </div>
</div>
