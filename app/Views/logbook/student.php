<div class="space-y-8 animate-fade-in-up" x-data="{ logTab: 'daily' }">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-poppins font-bold text-slate-800 dark:text-white">SIWES Logbook Log</h1>
            <p class="text-sm text-slate-500 dark:text-gray-400">Record daily tasks, upload drawings, and review supervisor feedback.</p>
        </div>
        <?php if (!empty($isLocked)): ?>
            <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-bold bg-amber-500/15 text-amber-600 dark:text-amber-400">
                <i class="fa-solid fa-lock mr-2"></i> Logbook Locked
            </span>
        <?php else: ?>
            <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-bold bg-success/15 text-success">
                <i class="fa-solid fa-lock-open mr-2"></i> Logbook Unlocked & Active
            </span>
        <?php endif; ?>
    </div>

    <?php if (!empty($isLocked)): ?>
        <!-- ================= LOCKED LOGBOOK VIEW ================= -->
        <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-8 md:p-12 max-w-3xl mx-auto text-center space-y-6">
            <div class="w-20 h-20 bg-amber-500/10 text-amber-500 rounded-3xl flex items-center justify-center text-3xl mx-auto shadow-inner">
                <i class="fa-solid fa-lock"></i>
            </div>

            <div class="space-y-2">
                <h2 class="text-2xl font-poppins font-bold text-slate-800 dark:text-white">
                    Daily Logbook is Currently Locked
                </h2>
                <p class="text-sm text-slate-500 dark:text-gray-400 max-w-xl mx-auto leading-relaxed">
                    Under academic SIWES regulations, students cannot view or log daily activities until their uploaded registration credentials (Acceptance Letter, Passport Photo, and CV) have been officially reviewed and approved by their <span class="font-bold text-slate-800 dark:text-white">Departmental Supervisor</span>.
                </p>
            </div>

            <!-- Verification Progress & Supervisor Info Card -->
            <div class="bg-slate-50 dark:bg-slate-800/50 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 text-left space-y-4 max-w-xl mx-auto">
                <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-700 pb-3">
                    <span class="text-xs uppercase font-bold text-slate-400 tracking-wider">SIWES Verification Status</span>
                    <?php if (($student['document_status'] ?? '') === 'queried'): ?>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-danger/15 text-danger flex items-center gap-1.5">
                            <i class="fa-solid fa-triangle-exclamation"></i> Queried / Defect Found
                        </span>
                    <?php else: ?>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/15 text-amber-600 flex items-center gap-1.5">
                            <i class="fa-solid fa-clock"></i> Pending Departmental Review
                        </span>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block font-semibold">Assigned Departmental Supervisor</span>
                        <p class="font-bold text-slate-800 dark:text-white text-sm mt-0.5">
                            <?= htmlspecialchars($student['dept_supervisor_name'] ?? 'Dr. Charles Koffi (Assigned)') ?>
                        </p>
                        <p class="text-slate-500 text-[11px]"><?= htmlspecialchars($student['dept_supervisor_email'] ?? 'koffi@university.edu.ng') ?></p>
                    </div>

                    <div>
                        <span class="text-slate-400 block font-semibold">Department</span>
                        <p class="font-bold text-primary dark:text-accent text-sm mt-0.5">
                            Computer Science (CSC)
                        </p>
                        <p class="text-slate-500 text-[11px]">Single Department Policy</p>
                    </div>
                </div>

                <?php if (!empty($student['supervisor_feedback'])): ?>
                    <div class="bg-amber-500/10 p-3 rounded-xl border border-amber-500/20 text-xs space-y-1">
                        <span class="font-bold text-amber-700 dark:text-amber-400 flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-exclamation"></i> Supervisor Feedback / Query Instructions:
                        </span>
                        <p class="text-slate-700 dark:text-slate-300"><?= htmlspecialchars($student['supervisor_feedback']) ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Resolution CTA -->
            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="/siwes/student/dashboard" class="w-full sm:w-auto bg-primary hover:bg-secondary text-white px-6 py-3 rounded-xl font-bold text-sm shadow-md shadow-primary/25 transition flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>Go to SIWES Document Center</span>
                </a>
                <a href="/siwes/settings" class="w-full sm:w-auto bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-gray-300 px-5 py-3 rounded-xl font-semibold text-sm transition text-center">
                    Check Notifications
                </a>
            </div>
        </div>

    <?php else: ?>
        <!-- ================= UNLOCKED ACTIVE LOGBOOK VIEW ================= -->
        <!-- Mode tabs toggles -->
        <div class="flex space-x-2 border-b border-slate-200 dark:border-slate-800 pb-px">
            <button @click="logTab = 'daily'" :class="logTab === 'daily' ? 'border-primary text-primary font-bold' : 'border-transparent text-slate-400 hover:text-slate-600'" class="border-b-2 px-4 py-2.5 text-sm transition-all focus:outline-none">Daily Logs</button>
            <button @click="logTab = 'weekly'" :class="logTab === 'weekly' ? 'border-primary text-primary font-bold' : 'border-transparent text-slate-400 hover:text-slate-600'" class="border-b-2 px-4 py-2.5 text-sm transition-all focus:outline-none">Weekly Summaries</button>
        </div>

    <!-- Daily Log Tab Contents -->
    <div x-show="logTab === 'daily'" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Add Daily Entry Form -->
        <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6 h-fit">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4"><i class="fa-solid fa-pen-to-square mr-2 text-primary"></i> Add Daily Entry</h3>
            
            <form action="/siwes/student/logbook/add" method="POST" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Activity Date</label>
                    <input type="date" name="date" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Week Number</label>
                        <select name="week_number" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                            <?php for ($i = 1; $i <= 24; $i++): ?>
                                <option value="<?= $i ?>">Week <?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Attachment Graphic</label>
                        <input type="file" name="image" accept="image/*" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-1 rounded-xl text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Activity Description</label>
                    <textarea name="activity_description" rows="4" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Outline activities carried out, tools used, and skills practiced..." required></textarea>
                </div>

                <button type="submit" class="w-full bg-primary hover:bg-secondary text-white py-3 rounded-xl font-semibold shadow-md shadow-primary/25 transition">Submit Log</button>
            </form>
        </div>

        <!-- Previous Logs Lists -->
        <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6 lg:col-span-2 space-y-4">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white"><i class="fa-solid fa-clock-rotate-left mr-2 text-accent"></i> Submitted Logs</h3>
            
            <?php if (empty($logs)): ?>
                <div class="py-12 text-center text-slate-400 dark:text-slate-500 space-y-2">
                    <i class="fa-solid fa-book text-4xl mb-2"></i>
                    <p class="text-sm font-medium">Your logbook is currently empty.</p>
                    <p class="text-xs">Use the left form to submit your daily activities logs.</p>
                </div>
            <?php else: ?>
                <div class="space-y-4 max-h-[500px] overflow-y-auto pr-2">
                    <?php foreach ($logs as $log): ?>
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-100 dark:border-slate-700 space-y-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="text-[10px] bg-primary/10 text-primary px-2.5 py-0.5 rounded-full font-semibold uppercase tracking-wider">Week <?= $log['week_number'] ?> | <?= $log['day_of_week'] ?></span>
                                    <h4 class="font-bold text-slate-800 dark:text-white mt-1 text-sm"><?= htmlspecialchars($log['date']) ?></h4>
                                </div>
                                
                                <!-- Status Badge -->
                                <?php if ($log['supervisor_status'] === 'approved'): ?>
                                    <span class="bg-success/10 text-success px-2.5 py-0.5 rounded-full text-xs font-semibold">Approved</span>
                                <?php elseif ($log['supervisor_status'] === 'queried'): ?>
                                    <span class="bg-danger/10 text-danger px-2.5 py-0.5 rounded-full text-xs font-semibold">Queried</span>
                                <?php else: ?>
                                    <span class="bg-warning/10 text-warning px-2.5 py-0.5 rounded-full text-xs font-semibold">Pending Approval</span>
                                <?php endif; ?>
                            </div>

                            <p class="text-xs text-slate-600 dark:text-gray-300 leading-relaxed"><?= htmlspecialchars($log['activity_description']) ?></p>

                            <!-- Image Attachment Preview if exists -->
                            <?php if (!empty($log['image_path'])): ?>
                                <div class="pt-2">
                                    <img src="<?= $log['image_path'] ?>" alt="Activity Drawing" class="max-h-32 rounded-xl object-contain border border-slate-200 dark:border-slate-700 bg-white">
                                </div>
                            <?php endif; ?>

                            <!-- Feedback comments -->
                            <?php if (!empty($log['supervisor_comments'])): ?>
                                <div class="border-t border-slate-200 dark:border-slate-700 pt-2 text-xs">
                                    <span class="font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[9px] block">Supervisor Remarks</span>
                                    <p class="text-slate-500 dark:text-gray-400 italic mt-0.5">"<?= htmlspecialchars($log['supervisor_comments']) ?>"</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Weekly Summary Tab Contents -->
    <div x-show="logTab === 'weekly'" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Add Weekly Entry Form -->
        <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6 h-fit">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4"><i class="fa-solid fa-pen-to-square mr-2 text-primary"></i> Submit Weekly Summary</h3>
            
            <form action="/siwes/student/logbook/weekly" method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Select Week</label>
                    <select name="week_number" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                        <?php for ($i = 1; $i <= 24; $i++): ?>
                            <option value="<?= $i ?>">Week <?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Weekly Summary</label>
                    <textarea name="summary" rows="5" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Provide a summary of accomplishments and notes for the week..." required></textarea>
                </div>

                <button type="submit" class="w-full bg-primary hover:bg-secondary text-white py-3 rounded-xl font-semibold shadow-md shadow-primary/25 transition">Submit Summary</button>
            </form>
        </div>

        <!-- Weekly Reports List -->
        <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6 lg:col-span-2 space-y-4">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white"><i class="fa-solid fa-calendar-check mr-2 text-accent"></i> Submitted Weekly Summaries</h3>
            
            <?php if (empty($weeklyReports)): ?>
                <div class="py-12 text-center text-slate-400 dark:text-slate-500 space-y-2">
                    <i class="fa-solid fa-list-check text-4xl mb-2"></i>
                    <p class="text-sm font-medium">No weekly summaries registered yet.</p>
                </div>
            <?php else: ?>
                <div class="space-y-4 max-h-[500px] overflow-y-auto pr-2">
                    <?php foreach ($weeklyReports as $rep): ?>
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-100 dark:border-slate-700 space-y-3">
                            <div class="flex justify-between items-center">
                                <h4 class="font-bold text-slate-800 dark:text-white text-sm">Week Summary: Week <?= $rep['week_number'] ?></h4>
                                <div class="flex items-center space-x-2">
                                    <?php if ($rep['supervisor_status'] === 'approved'): ?>
                                        <span class="bg-success/10 text-success px-2.5 py-0.5 rounded-full text-xs font-semibold">Approved</span>
                                        <?php if (!empty($rep['supervisor_grade'])): ?>
                                            <span class="bg-primary/10 text-primary px-2.5 py-0.5 rounded-full text-xs font-bold">Grade: <?= $rep['supervisor_grade'] ?>/10</span>
                                        <?php endif; ?>
                                    <?php elseif ($rep['supervisor_status'] === 'queried'): ?>
                                        <span class="bg-danger/10 text-danger px-2.5 py-0.5 rounded-full text-xs font-semibold">Queried</span>
                                    <?php else: ?>
                                        <span class="bg-warning/10 text-warning px-2.5 py-0.5 rounded-full text-xs font-semibold">Pending Approval</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <p class="text-xs text-slate-600 dark:text-gray-300 leading-relaxed"><?= htmlspecialchars($rep['summary']) ?></p>

                            <?php if (!empty($rep['supervisor_comments'])): ?>
                                <div class="border-t border-slate-200 dark:border-slate-700 pt-2 text-xs">
                                    <span class="font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[9px] block">Supervisor Feedbacks</span>
                                    <p class="text-slate-500 dark:text-gray-400 italic mt-0.5">"<?= htmlspecialchars($rep['supervisor_comments']) ?>"</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
