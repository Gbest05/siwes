<div class="space-y-8 animate-fade-in-up" x-data="{ logTab: 'daily', showReviewModal: false, activeLog: {} }">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-poppins font-bold text-slate-800 dark:text-white">Verify Student Logbooks</h1>
            <p class="text-sm text-slate-500 dark:text-gray-400">Audit submitted logs, check graphic sketches, and leave review marks.</p>
        </div>
    </div>

    <!-- Mode toggles -->
    <div class="flex space-x-2 border-b border-slate-200 dark:border-slate-800 pb-px">
        <button @click="logTab = 'daily'" :class="logTab === 'daily' ? 'border-primary text-primary font-bold' : 'border-transparent text-slate-400 hover:text-slate-600'" class="border-b-2 px-4 py-2.5 text-sm transition-all focus:outline-none">Daily Logs</button>
        <button @click="logTab = 'weekly'" :class="logTab === 'weekly' ? 'border-primary text-primary font-bold' : 'border-transparent text-slate-400 hover:text-slate-600'" class="border-b-2 px-4 py-2.5 text-sm transition-all focus:outline-none">Weekly Summaries</button>
    </div>

    <!-- Daily Logs Tab -->
    <div x-show="logTab === 'daily'" class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6">
        <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4"><i class="fa-solid fa-list mr-2 text-primary"></i> Daily Student Attachments Logs</h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-500 dark:text-gray-400">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-700 dark:text-gray-200">
                        <th class="py-3 font-semibold">Student</th>
                        <th class="py-3 font-semibold">Date</th>
                        <th class="py-3 font-semibold">Week</th>
                        <th class="py-3 font-semibold">Description</th>
                        <th class="py-3 font-semibold">Sketch</th>
                        <th class="py-3 font-semibold">Status</th>
                        <th class="py-3 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-400">No daily logs submitted by assigned students.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td class="py-3 font-medium text-slate-800 dark:text-white"><?= htmlspecialchars($log['student_name']) ?></td>
                                <td class="py-3 text-xs"><?= htmlspecialchars($log['date']) ?> (<?= htmlspecialchars($log['day_of_week']) ?>)</td>
                                <td class="py-3 font-semibold text-xs">Week <?= $log['week_number'] ?></td>
                                <td class="py-3 text-xs max-w-xs truncate"><?= htmlspecialchars($log['activity_description']) ?></td>
                                <td class="py-3">
                                    <?php if (!empty($log['image_path'])): ?>
                                        <a href="<?= $log['image_path'] ?>" target="_blank" class="text-xs text-primary hover:underline"><i class="fa-solid fa-image"></i> View Image</a>
                                    <?php else: ?>
                                        <span class="text-xs text-slate-400">None</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3">
                                    <?php if ($log['supervisor_status'] === 'approved'): ?>
                                        <span class="bg-success/10 text-success px-2 py-0.5 rounded-full text-xs font-semibold">Approved</span>
                                    <?php elseif ($log['supervisor_status'] === 'queried'): ?>
                                        <span class="bg-danger/10 text-danger px-2 py-0.5 rounded-full text-xs font-semibold">Queried</span>
                                    <?php else: ?>
                                        <span class="bg-warning/10 text-warning px-2 py-0.5 rounded-full text-xs font-semibold">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 text-right">
                                    <button @click="activeLog = { id: '<?= $log['id'] ?>', type: 'daily', student: '<?= htmlspecialchars($log['student_name']) ?>', title: '<?= $log['date'] ?>', desc: '<?= htmlspecialchars($log['activity_description']) ?>', comments: '<?= htmlspecialchars($log['supervisor_comments'] ?? '') ?>' }; showReviewModal = true" class="bg-slate-100 hover:bg-primary hover:text-white dark:bg-slate-800 dark:hover:bg-primary px-3 py-1.5 rounded-lg text-xs font-semibold transition text-slate-600 dark:text-gray-300">Review</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Weekly Summaries Tab -->
    <div x-show="logTab === 'weekly'" class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6">
        <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4"><i class="fa-solid fa-calendar-check mr-2 text-primary"></i> Weekly summaries Reviews</h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-500 dark:text-gray-400">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-700 dark:text-gray-200">
                        <th class="py-3 font-semibold">Student</th>
                        <th class="py-3 font-semibold">Week</th>
                        <th class="py-3 font-semibold">Summary</th>
                        <th class="py-3 font-semibold">Grade</th>
                        <th class="py-3 font-semibold">Status</th>
                        <th class="py-3 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php if (empty($weeklyReports)): ?>
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-400">No weekly summaries submitted by assigned students.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($weeklyReports as $rep): ?>
                            <tr>
                                <td class="py-3 font-medium text-slate-800 dark:text-white"><?= htmlspecialchars($rep['student_name']) ?></td>
                                <td class="py-3 font-semibold text-xs">Week <?= $rep['week_number'] ?></td>
                                <td class="py-3 text-xs max-w-sm truncate"><?= htmlspecialchars($rep['summary']) ?></td>
                                <td class="py-3 font-bold text-slate-700 dark:text-gray-300"><?= $rep['supervisor_grade'] !== null ? $rep['supervisor_grade'] . '/10' : 'Unmarked' ?></td>
                                <td class="py-3">
                                    <?php if ($rep['supervisor_status'] === 'approved'): ?>
                                        <span class="bg-success/10 text-success px-2 py-0.5 rounded-full text-xs font-semibold">Approved</span>
                                    <?php elseif ($rep['supervisor_status'] === 'queried'): ?>
                                        <span class="bg-danger/10 text-danger px-2 py-0.5 rounded-full text-xs font-semibold">Queried</span>
                                    <?php else: ?>
                                        <span class="bg-warning/10 text-warning px-2 py-0.5 rounded-full text-xs font-semibold">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 text-right">
                                    <button @click="activeLog = { id: '<?= $rep['id'] ?>', type: 'weekly', student: '<?= htmlspecialchars($rep['student_name']) ?>', title: 'Week <?= $rep['week_number'] ?> Summary', desc: '<?= htmlspecialchars($rep['summary']) ?>', comments: '<?= htmlspecialchars($rep['supervisor_comments'] ?? '') ?>', grade: '<?= $rep['supervisor_grade'] ?? '' ?>' }; showReviewModal = true" class="bg-slate-100 hover:bg-primary hover:text-white dark:bg-slate-800 dark:hover:bg-primary px-3 py-1.5 rounded-lg text-xs font-semibold transition text-slate-600 dark:text-gray-300">Review</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Review Log Modal -->
    <div x-show="showReviewModal" class="fixed inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition>
        <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 max-w-md w-full p-6 shadow-2xl space-y-6" @click.away="showReviewModal = false">
            <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white" x-text="'Review student ' + (activeLog.type === 'daily' ? 'Daily Log' : 'Weekly Summary')"></h3>
                <button @click="showReviewModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            
            <form action="/siwes/supervisor/logbook/verify" method="POST" class="space-y-4">
                <input type="hidden" name="id" :value="activeLog.id">
                <input type="hidden" name="type" :value="activeLog.type">
                
                <div class="space-y-1 bg-slate-50 dark:bg-slate-800/50 p-3.5 rounded-xl border border-slate-100 dark:border-slate-700">
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block" x-text="activeLog.student + ' - ' + activeLog.title"></span>
                    <p class="text-xs text-slate-600 dark:text-gray-300 mt-1 italic leading-relaxed" x-text="'&ldquo;' + activeLog.desc + '&rdquo;'"></p>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Approval Review Decision</label>
                    <select name="status" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                        <option value="approved">Approve and Sign Log</option>
                        <option value="queried">Query and Send Back</option>
                    </select>
                </div>

                <!-- Show grade input only for weekly summaries -->
                <div x-show="activeLog.type === 'weekly'">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Weekly Grade Score (0 - 10)</label>
                    <input type="number" name="grade" min="0" max="10" :value="activeLog.grade" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="8">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Remarks Comments</label>
                    <textarea name="comments" rows="3" :value="activeLog.comments" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Write feedback details..."></textarea>
                </div>

                <button type="submit" class="w-full bg-primary hover:bg-secondary text-white py-3 rounded-xl font-semibold shadow-md shadow-primary/25 transition">Submit Log Review</button>
            </form>
        </div>
    </div>
</div>
