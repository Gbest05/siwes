<div class="space-y-8 animate-fade-in-up" x-data="{ showAddModal: false, showStatusModal: false, activeComplaintId: '', activeStatus: 'open' }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-poppins font-bold text-slate-800 dark:text-white">SIWES Complaints & Queries</h1>
            <p class="text-sm text-slate-500 dark:text-gray-400">Manage student queries, document complaints, and track resolutions.</p>
        </div>
        <button @click="showAddModal = true" class="bg-primary hover:bg-secondary text-white text-xs px-5 py-2.5 rounded-xl font-bold transition flex items-center space-x-2 shadow-md shadow-primary/20">
            <i class="fa-solid fa-plus text-xs"></i> <span>Create Complaint / Query</span>
        </button>
    </div>

    <!-- Filters Panel -->
    <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 p-5 shadow-soft">
        <form action="/siwes/coordinator/complaints" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Search Student or Title</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 pl-9 pr-4 py-2 rounded-xl text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Search by student name, matric no, or complaint title...">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Resolution Status</label>
                <select name="status" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-2 rounded-xl text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                    <option value="">All Statuses</option>
                    <option value="open" <?= ($filters['status'] ?? '') === 'open' ? 'selected' : '' ?>>Open</option>
                    <option value="in_progress" <?= ($filters['status'] ?? '') === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="resolved" <?= ($filters['status'] ?? '') === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                </select>
            </div>

            <div>
                <button type="submit" class="w-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-850 dark:hover:bg-slate-800 text-slate-700 dark:text-white py-2 rounded-xl text-xs font-bold transition">Filter Complaints</button>
            </div>
        </form>
    </div>

    <!-- Complaints Table -->
    <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-500 dark:text-gray-400">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-700 dark:text-gray-200">
                        <th class="py-3 font-semibold">Date</th>
                        <th class="py-3 font-semibold">Student Name & Matric</th>
                        <th class="py-3 font-semibold">Complaint Subject</th>
                        <th class="py-3 font-semibold">Details / Description</th>
                        <th class="py-3 font-semibold">Logged By</th>
                        <th class="py-3 font-semibold">Status</th>
                        <th class="py-3 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php if (empty($complaints)): ?>
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                <i class="fa-solid fa-circle-check text-4xl mb-2 text-success/70 block"></i>
                                No active complaints or queries recorded.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($complaints as $c): ?>
                            <tr>
                                <td class="py-3 text-xs text-slate-400 font-mono"><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
                                <td class="py-3">
                                    <p class="font-bold text-slate-800 dark:text-white"><?= htmlspecialchars($c['student_name']) ?></p>
                                    <p class="text-[11px] font-mono text-slate-400"><?= htmlspecialchars($c['matric_no']) ?></p>
                                </td>
                                <td class="py-3 font-medium text-slate-800 dark:text-white text-xs max-w-xs">
                                    <?= htmlspecialchars($c['title']) ?>
                                </td>
                                <td class="py-3 text-xs text-slate-600 dark:text-gray-300 max-w-sm leading-relaxed">
                                    <?= htmlspecialchars($c['description']) ?>
                                </td>
                                <td class="py-3 text-xs">
                                    <span class="font-semibold text-slate-700 dark:text-gray-200 block"><?= htmlspecialchars($c['creator_name']) ?></span>
                                    <span class="text-[10px] uppercase font-bold text-slate-400"><?= htmlspecialchars($c['creator_role']) ?></span>
                                </td>
                                <td class="py-3 text-xs">
                                    <?php if ($c['status'] === 'open'): ?>
                                        <span class="bg-danger/15 text-danger font-bold px-2.5 py-0.5 rounded-full text-[10px]">Open</span>
                                    <?php elseif ($c['status'] === 'in_progress'): ?>
                                        <span class="bg-warning/15 text-warning font-bold px-2.5 py-0.5 rounded-full text-[10px]">In Progress</span>
                                    <?php else: ?>
                                        <span class="bg-success/15 text-success font-bold px-2.5 py-0.5 rounded-full text-[10px]">Resolved</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 text-right">
                                    <button @click="activeComplaintId = '<?= $c['id'] ?>'; activeStatus = '<?= $c['status'] ?>'; showStatusModal = true" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-white rounded-lg text-xs font-bold transition">
                                        Update Status
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal: Create Complaint -->
    <div x-show="showAddModal" class="fixed inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition x-cloak>
        <div class="bg-white dark:bg-darkCard w-full max-w-md rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-xl space-y-6" @click.away="showAddModal = false">
            <div class="flex justify-between items-center pb-3 border-b border-slate-100 dark:border-slate-800">
                <h3 class="font-poppins font-bold text-slate-850 dark:text-white text-base">Create Student Complaint / Query</h3>
                <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-650 dark:hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form action="/siwes/coordinator/complaints/add" method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Target Student</label>
                    <select name="student_id" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                        <option value="">-- Select Student --</option>
                        <?php foreach ($students as $stu): ?>
                            <option value="<?= $stu['id'] ?>"><?= htmlspecialchars($stu['first_name'] . ' ' . $stu['last_name']) ?> (<?= htmlspecialchars($stu['matric_no']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Complaint / Query Title</label>
                    <input type="text" name="title" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="e.g. Unverified Acceptance Letter" required>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Detailed Description</label>
                    <textarea name="description" rows="4" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Explain the complaint or what the student needs to resolve..." required></textarea>
                </div>

                <button type="submit" class="w-full bg-primary hover:bg-secondary text-white py-3 rounded-xl font-semibold shadow-md shadow-primary/25 transition">Submit Complaint</button>
            </form>
        </div>
    </div>

    <!-- Modal: Update Complaint Status -->
    <div x-show="showStatusModal" class="fixed inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition x-cloak>
        <div class="bg-white dark:bg-darkCard w-full max-w-sm rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-xl space-y-6" @click.away="showStatusModal = false">
            <div class="flex justify-between items-center pb-3 border-b border-slate-100 dark:border-slate-800">
                <h3 class="font-poppins font-bold text-slate-850 dark:text-white text-base">Update Complaint Status</h3>
                <button @click="showStatusModal = false" class="text-slate-400 hover:text-slate-650 dark:hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form action="/siwes/coordinator/complaints/status" method="POST" class="space-y-4">
                <input type="hidden" name="complaint_id" :value="activeComplaintId">

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Select Status</label>
                    <select name="status" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                        <option value="open" :selected="activeStatus === 'open'">Open</option>
                        <option value="in_progress" :selected="activeStatus === 'in_progress'">In Progress</option>
                        <option value="resolved" :selected="activeStatus === 'resolved'">Resolved (Clear Query)</option>
                    </select>
                </div>

                <button type="submit" class="w-full bg-primary hover:bg-secondary text-white py-2.5 rounded-xl font-semibold shadow-md shadow-primary/25 transition">Update Status</button>
            </form>
        </div>
    </div>
</div>
