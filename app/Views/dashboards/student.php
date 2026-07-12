<div class="space-y-8 animate-fade-in-up">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div class="flex items-center space-x-4">
            <a href="/siwes/settings" class="w-16 h-16 rounded-2xl bg-accent text-[#0A0E1A] font-bold flex items-center justify-center text-2xl uppercase shadow-md overflow-hidden shrink-0 hover:opacity-90 transition-opacity" title="Change Profile Picture">
                <?php if (!empty($user['profile_pic'])): ?>
                    <img src="<?= asset($user['profile_pic']) ?>" alt="Profile Photo" class="w-full h-full object-cover">
                <?php else: ?>
                    <?= substr($user['username'], 0, 2) ?>
                <?php endif; ?>
            </a>
            <div>
                <h1 class="text-3xl font-poppins font-bold text-slate-800 dark:text-white">Welcome, <?= htmlspecialchars($user['profile']['first_name'] ?? $user['username']) ?>!</h1>
                <p class="text-sm text-slate-500 dark:text-gray-400">Matric Number: <span class="font-semibold text-slate-700 dark:text-gray-200"><?= htmlspecialchars($user['profile']['matric_no'] ?? 'N/A') ?></span> | Course: <?= htmlspecialchars($user['profile']['course_of_study'] ?? 'N/A') ?></p>
            </div>
        </div>
        <div class="flex space-x-2 shrink-0">
            <?php if (!empty($allocation)): ?>
                <a href="/siwes/student/download-letter" class="bg-primary hover:bg-secondary text-white text-sm px-4 py-2.5 rounded-xl font-semibold shadow-md shadow-primary/20 transition flex items-center space-x-2">
                    <i class="fa-solid fa-download"></i> <span>Download Placement Letter</span>
                </a>
            <?php endif; ?>
            <a href="/siwes/student/logbook" class="bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-white border border-slate-200 dark:border-slate-700 text-sm px-4 py-2.5 rounded-xl font-semibold transition flex items-center space-x-2">
                <i class="fa-solid fa-book-open text-accent"></i> <span>Daily Logbook</span>
            </a>
        </div>
    </div>

    <!-- Status Tracker & Profile Progress -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Placement Status Card -->
        <div class="bg-white dark:bg-darkCard rounded-3xl p-6 border border-slate-100 dark:border-slate-800 shadow-soft lg:col-span-2">
            <h3 class="text-sm font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-4">SIWES Placement Status</h3>
            <div class="flex items-center space-x-4">
                <?php if ($user['profile']['siwes_status'] === 'unallocated'): ?>
                    <div class="bg-warning/10 text-warning p-4 rounded-2xl flex items-center justify-center">
                        <i class="fa-solid fa-circle-question text-3xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 dark:text-white text-lg">Awaiting Allocation</h4>
                        <p class="text-sm text-slate-500 dark:text-gray-400 mt-1">The system auto-allocation runner has not placed you yet. Ensure your preferences are set under profile configuration.</p>
                    </div>
                <?php elseif ($user['profile']['siwes_status'] === 'allocated'): ?>
                    <div class="bg-success/10 text-success p-4 rounded-2xl flex items-center justify-center">
                        <i class="fa-solid fa-circle-check text-3xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 dark:text-white text-lg">Placement Approved</h4>
                        <p class="text-sm text-slate-500 dark:text-gray-400 mt-1">You have been allocated to <span class="font-bold text-primary"><?= htmlspecialchars($allocation['company_name']) ?></span>. Click the button in the header to download your formal placement letter.</p>
                    </div>
                <?php elseif ($user['profile']['siwes_status'] === 'completed'): ?>
                    <div class="bg-primary/10 text-primary p-4 rounded-2xl flex items-center justify-center">
                        <i class="fa-solid fa-circle-check text-3xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 dark:text-white text-lg">SIWES Completed</h4>
                        <p class="text-sm text-slate-500 dark:text-gray-400 mt-1">Congratulations! You have completed your SIWES program attachment. Your evaluation reports have been compiled.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Visual Timeline Tracker -->
            <div class="mt-8 border-t border-slate-100 dark:border-slate-800 pt-6">
                <div class="flex items-center justify-between relative max-w-md mx-auto">
                    <!-- Progress Bar Line -->
                    <div class="absolute h-0.5 bg-slate-200 dark:bg-slate-700 left-4 right-4 top-1/2 -translate-y-1/2 -z-10"></div>
                    <div class="absolute h-0.5 bg-primary left-4 top-1/2 -translate-y-1/2 -z-10 transition-all duration-300" style="width: <?= $user['profile']['siwes_status'] === 'unallocated' ? '0%' : ($user['profile']['siwes_status'] === 'allocated' ? '50%' : '100%') ?>"></div>

                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center text-xs font-bold bg-white dark:bg-darkCard border-primary text-primary">1</div>
                        <span class="text-[10px] font-semibold mt-2 text-slate-500 dark:text-gray-400 uppercase">Enrolled</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center text-xs font-bold transition <?= ($user['profile']['siwes_status'] === 'allocated' || $user['profile']['siwes_status'] === 'completed') ? 'bg-primary border-primary text-white' : 'bg-white dark:bg-darkCard border-slate-300 text-slate-400' ?>">2</div>
                        <span class="text-[10px] font-semibold mt-2 text-slate-500 dark:text-gray-400 uppercase">Allocated</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center text-xs font-bold transition <?= $user['profile']['siwes_status'] === 'completed' ? 'bg-primary border-primary text-white' : 'bg-white dark:bg-darkCard border-slate-300 text-slate-400' ?>">3</div>
                        <span class="text-[10px] font-semibold mt-2 text-slate-500 dark:text-gray-400 uppercase">Completed</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logbook Progression Card -->
        <div class="bg-white dark:bg-darkCard rounded-3xl p-6 border border-slate-100 dark:border-slate-800 shadow-soft flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Daily Progress Tracker</h3>
                <span class="text-xs text-slate-500">Target: 120 completed workdays.</span>
            </div>
            
            <div class="my-4">
                <div class="flex justify-between items-center text-sm font-bold text-slate-700 dark:text-gray-200 mb-1">
                    <span>Approved Logs</span>
                    <span><?= $progress['approved'] ?> / 120 (<?= $progress['percentage'] ?>%)</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-800 h-2.5 rounded-full overflow-hidden">
                    <div class="bg-gradient-to-r from-primary to-accent h-full rounded-full transition-all duration-300" style="width: <?= min(100, $progress['percentage']) ?>%"></div>
                </div>
            </div>

            <div class="text-xs text-slate-400 dark:text-slate-500 border-t border-slate-50 dark:border-slate-800 pt-3">
                <i class="fa-solid fa-circle-info mr-1 text-primary"></i> Current total submitted: <span class="font-semibold text-slate-600 dark:text-gray-300"><?= $progress['total'] ?> days</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Allocated Company Details -->
        <div class="bg-white dark:bg-darkCard rounded-3xl p-6 border border-slate-100 dark:border-slate-800 shadow-soft lg:col-span-2">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4"><i class="fa-solid fa-industry mr-2 text-primary"></i> Company Attachment Placement</h3>
            
            <?php if (empty($allocation)): ?>
                <div class="py-8 text-center text-slate-400 dark:text-slate-500 space-y-2">
                    <i class="fa-solid fa-briefcase text-4xl mb-2"></i>
                    <p class="text-sm font-medium">No placement information available.</p>
                    <p class="text-xs">Once the unit executes auto-allocation, your corporate details will populate here.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <span class="text-xs text-slate-400 uppercase font-semibold">Company Name</span>
                            <p class="font-bold text-slate-800 dark:text-white"><?= htmlspecialchars($allocation['company_name']) ?></p>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 uppercase font-semibold">Office Address</span>
                            <p class="text-sm text-slate-600 dark:text-gray-300 mt-0.5"><?= htmlspecialchars($allocation['company_address']) ?></p>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 uppercase font-semibold">Contact Email</span>
                            <p class="text-sm text-primary hover:underline mt-0.5"><?= htmlspecialchars($allocation['company_email']) ?></p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <span class="text-xs text-slate-400 uppercase font-semibold">Corporate Supervisor</span>
                            <p class="font-bold text-slate-800 dark:text-white"><?= htmlspecialchars($allocation['supervisor_name'] ?? 'Pending Assignment') ?></p>
                        </div>
                        <?php if (!empty($allocation['supervisor_phone'])): ?>
                            <div>
                                <span class="text-xs text-slate-400 uppercase font-semibold">Supervisor Phone</span>
                                <p class="text-sm text-slate-600 dark:text-gray-300 mt-0.5"><?= htmlspecialchars($allocation['supervisor_phone']) ?></p>
                            </div>
                        <?php endif; ?>
                        <div>
                            <span class="text-xs text-slate-400 uppercase font-semibold">Placement Date</span>
                            <p class="text-xs text-slate-500 mt-0.5"><?= htmlspecialchars($allocation['allocation_date']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Grade Evaluation Status -->
        <div class="bg-white dark:bg-darkCard rounded-3xl p-6 border border-slate-100 dark:border-slate-800 shadow-soft">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4"><i class="fa-solid fa-graduation-cap mr-2 text-accent"></i> Final Performance Evaluation</h3>
            
            <?php if (empty($evaluation) || $evaluation['final_grade'] === 'Pending'): ?>
                <div class="py-8 text-center text-slate-400 dark:text-slate-500 space-y-2">
                    <i class="fa-solid fa-award text-4xl mb-2"></i>
                    <p class="text-sm font-medium">Evaluation pending supervisor grading.</p>
                    <p class="text-xs">Your company supervisor will grade your logbooks and submit scores at the end of the internship.</p>
                </div>
            <?php else: ?>
                <div class="text-center space-y-4">
                    <div class="inline-flex w-20 h-20 rounded-full bg-success/10 text-success items-center justify-center font-extrabold text-4xl border border-success/20">
                        <?= htmlspecialchars($evaluation['final_grade']) ?>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 uppercase font-semibold block">Supervisor Mark</span>
                        <p class="text-xl font-bold text-slate-800 dark:text-white mt-0.5"><?= $evaluation['supervisor_score'] ?> / 100</p>
                    </div>
                    <div class="text-left bg-slate-50 dark:bg-slate-800/50 p-3.5 rounded-xl border border-slate-100 dark:border-slate-700">
                        <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block">Supervisor Remarks</span>
                        <p class="text-xs text-slate-600 dark:text-gray-300 mt-1"><?= htmlspecialchars($evaluation['supervisor_remarks']) ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
