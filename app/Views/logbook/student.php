<div class="space-y-8 animate-fade-in-up" x-data="{ logTab: 'daily' }">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-poppins font-bold text-slate-800 dark:text-white">SIWES Logbook Log</h1>
            <p class="text-sm text-slate-500 dark:text-gray-400">Record daily tasks, upload drawings, and review supervisor feedback.</p>
        </div>
    </div>

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
</div>
