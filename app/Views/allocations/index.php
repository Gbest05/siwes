<div class="space-y-8 animate-fade-in-up" x-data="{ tab: 'placement' }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-200 dark:border-slate-800 pb-4">
        <div>
            <h1 class="text-3xl font-poppins font-bold text-slate-800 dark:text-white">SIWES Placements & Supervisor Allocations</h1>
            <p class="text-sm text-slate-500 dark:text-gray-400">Run automatic matching algorithm, assign departmental & company supervisors, and customize placement letters.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 no-print">
            <a href="/siwes/coordinator/letters/placement" class="bg-primary/10 hover:bg-primary/20 text-primary dark:text-accent text-xs px-3.5 py-2.5 rounded-xl font-bold transition flex items-center space-x-1.5" title="Customize Placement Letter template">
                <i class="fa-solid fa-pen-nib"></i> <span>Edit Placement Letter</span>
            </a>
            <a href="/siwes/coordinator/complaints" class="bg-amber-500/15 hover:bg-amber-500 text-amber-700 dark:text-amber-400 hover:text-white text-xs px-3.5 py-2.5 rounded-xl font-bold transition flex items-center space-x-1.5">
                <i class="fa-solid fa-triangle-exclamation"></i> <span>Complaints & Queries</span>
            </a>
            <form action="/siwes/coordinator/allocations/run" method="POST">
                <button type="submit" class="bg-primary hover:bg-secondary text-white text-sm px-4 py-2.5 rounded-xl font-semibold shadow-md shadow-primary/20 transition flex items-center space-x-2">
                    <i class="fa-solid fa-bolt animate-pulse"></i> <span>Execute Auto-Allocation</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Split view: Left manual forms, Right active allocations list -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 items-start">
        
        <!-- Section: Forms (Placement, Company Supervisor & Departmental Supervisor) (4 cols) -->
        <div class="xl:col-span-4 bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6 h-fit no-print space-y-5">
            <!-- Tabs -->
            <div class="grid grid-cols-3 gap-1 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl text-[11px] font-bold text-center">
                <button type="button" @click="tab = 'placement'" :class="tab === 'placement' ? 'bg-white dark:bg-darkCard text-primary shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white'" class="py-2 rounded-lg transition truncate">
                    Placement
                </button>
                <button type="button" @click="tab = 'dept_sup'" :class="tab === 'dept_sup' ? 'bg-white dark:bg-darkCard text-primary shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white'" class="py-2 rounded-lg transition truncate">
                    Dept Super
                </button>
                <button type="button" @click="tab = 'company_sup'" :class="tab === 'company_sup' ? 'bg-white dark:bg-darkCard text-primary shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white'" class="py-2 rounded-lg transition truncate">
                    Company Super
                </button>
            </div>

            <!-- Form 1: Assign Placement & Company -->
            <div x-show="tab === 'placement'" class="space-y-4">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-building text-primary"></i> Assign Company Placement
                </h3>
                
                <form action="/siwes/coordinator/allocations/assign" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Select Student</label>
                        <select name="student_id" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                            <option value="">-- Choose Student --</option>
                            <?php if (!empty($unallocated)): ?>
                                <optgroup label="Unallocated Students">
                                    <?php foreach ($unallocated as $s): ?>
                                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?> (<?= htmlspecialchars($s['matric_no']) ?>)</option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endif; ?>
                            <?php if (!empty($allStudents)): ?>
                                <optgroup label="All Enrolled Students">
                                    <?php foreach ($allStudents as $s): ?>
                                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?> (<?= htmlspecialchars($s['matric_no']) ?>)</option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Select Corporate Partner</label>
                        <select name="company_id" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                            <option value="">-- Choose Company --</option>
                            <?php foreach ($companies as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?> (Slots: <?= $c['available_slots'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Company Supervisor (Optional)</label>
                        <select name="supervisor_id" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                            <option value="">-- Auto-detect company supervisor --</option>
                            <?php if (!empty($companySupervisors)): ?>
                                <?php foreach ($companySupervisors as $sup): ?>
                                    <option value="<?= $sup['id'] ?>"><?= htmlspecialchars($sup['first_name'] . ' ' . $sup['last_name']) ?> (<?= htmlspecialchars($sup['company_name'] ?? 'Company') ?>)</option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-primary hover:bg-secondary text-white py-3 rounded-xl font-semibold shadow-md shadow-primary/25 transition">Assign Placement</button>
                </form>
            </div>

            <!-- Form 2: Coordinator Assigns Departmental Supervisor -->
            <div x-show="tab === 'dept_sup'" class="space-y-4" style="display: none;">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-graduation-cap text-primary"></i> Assign Departmental Supervisor
                </h3>
                <p class="text-xs text-slate-400">Coordinator allocates a dedicated Computer Science departmental supervisor to inspect student credentials & issue queries.</p>

                <form action="/siwes/coordinator/allocations/assign-dept-supervisor" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Student</label>
                        <select name="student_id" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                            <option value="">-- Select Student --</option>
                            <?php foreach ($allStudents as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?> (<?= htmlspecialchars($s['matric_no']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Departmental Supervisor (CSC)</label>
                        <select name="dept_supervisor_id" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                            <option value="">-- Select Departmental Supervisor --</option>
                            <?php if (!empty($deptSupervisors)): ?>
                                <?php foreach ($deptSupervisors as $ds): ?>
                                    <option value="<?= $ds['id'] ?>"><?= htmlspecialchars($ds['first_name'] . ' ' . $ds['last_name']) ?> (Computer Science Dept)</option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="bg-primary/5 dark:bg-primary/10 p-3 rounded-xl text-xs text-slate-600 dark:text-gray-300">
                        <i class="fa-solid fa-circle-info text-primary mr-1"></i>
                        The departmental supervisor will review the student's uploaded acceptance letter and approve their logbook access.
                    </div>

                    <button type="submit" class="w-full bg-primary hover:bg-secondary text-white py-3 rounded-xl font-semibold shadow-md shadow-primary/25 transition">
                        Assign Departmental Supervisor
                    </button>
                </form>
            </div>

            <!-- Form 3: Direct Company Supervisor Assignment -->
            <div x-show="tab === 'company_sup'" class="space-y-4" style="display: none;">
                <h3 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-user-tie text-primary"></i> Assign Company Supervisor
                </h3>
                <p class="text-xs text-slate-400">Directly link the company supervisor to verify the student's daily logbooks.</p>

                <form action="/siwes/coordinator/allocations/assign-supervisor" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Student</label>
                        <select name="student_id" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                            <option value="">-- Select Student --</option>
                            <?php foreach ($allStudents as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?> (<?= htmlspecialchars($s['matric_no']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Company Supervisor</label>
                        <select name="supervisor_id" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                            <option value="">-- Select Company Supervisor --</option>
                            <?php if (!empty($companySupervisors)): ?>
                                <?php foreach ($companySupervisors as $sup): ?>
                                    <option value="<?= $sup['id'] ?>"><?= htmlspecialchars($sup['first_name'] . ' ' . $sup['last_name']) ?> (<?= htmlspecialchars($sup['company_name'] ?? 'Company') ?>)</option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Company Attachment (Optional)</label>
                        <select name="company_id" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                            <option value="">-- Use supervisor company / Existing --</option>
                            <?php foreach ($allCompanies as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-primary hover:bg-secondary text-white py-3 rounded-xl font-semibold shadow-md shadow-primary/25 transition">Assign Company Supervisor</button>
                </form>
            </div>
        </div>

        <!-- Section: Active Placements table (8 cols) -->
        <div class="xl:col-span-8 bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4 flex items-center justify-between">
                <span><i class="fa-solid fa-list-check mr-2 text-accent"></i> Active Placement Allocations</span>
                <span class="text-xs bg-slate-100 dark:bg-slate-800 text-slate-500 px-2.5 py-1 rounded-full font-normal">
                    Total: <?= count($allocations) ?>
                </span>
            </h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-500 dark:text-gray-400">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-700 dark:text-gray-200 text-xs uppercase tracking-wider">
                            <th class="py-3 font-semibold">Student</th>
                            <th class="py-3 font-semibold">Corporate Partner</th>
                            <th class="py-3 font-semibold">Dept Supervisor</th>
                            <th class="py-3 font-semibold">Company Mentor</th>
                            <th class="py-3 font-semibold">Lock</th>
                            <th class="py-3 font-semibold text-right no-print">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <?php if (empty($allocations)): ?>
                            <tr>
                                <td colspan="6" class="py-6 text-center text-slate-400">No placements run yet. Click "Execute Auto-Allocation" in header.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($allocations as $a): ?>
                                <tr>
                                    <td class="py-3 font-medium text-slate-800 dark:text-white">
                                        <p class="font-bold"><?= htmlspecialchars($a['student_name']) ?></p>
                                        <span class="text-xs font-mono text-slate-400"><?= htmlspecialchars($a['matric_no']) ?></span>
                                    </td>
                                    <td class="py-3 text-xs font-semibold text-primary dark:text-accent">
                                        <?= htmlspecialchars($a['company_name']) ?>
                                    </td>
                                    <!-- Departmental Supervisor Column -->
                                    <td class="py-3 text-xs">
                                        <?php if (!empty($a['dept_supervisor_name'])): ?>
                                            <span class="font-bold text-slate-800 dark:text-white block"><?= htmlspecialchars($a['dept_supervisor_name']) ?></span>
                                            <span class="text-[10px] text-primary dark:text-accent">Dept of Computer Science</span>
                                        <?php else: ?>
                                            <span class="text-amber-500 italic">Unassigned Dept Super</span>
                                        <?php endif; ?>
                                    </td>
                                    <!-- Company Supervisor Column -->
                                    <td class="py-3 text-xs">
                                        <?php if (!empty($a['supervisor_name'])): ?>
                                            <span class="font-bold text-slate-800 dark:text-white block"><?= htmlspecialchars($a['supervisor_name']) ?></span>
                                            <span class="text-[10px] text-success">Industry Supervisor</span>
                                        <?php else: ?>
                                            <span class="text-amber-500 italic">Pending Assignment</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3">
                                        <?php if ($a['status'] === 'locked'): ?>
                                            <span class="text-primary dark:text-accent font-bold text-xs"><i class="fa-solid fa-lock"></i> Locked</span>
                                        <?php else: ?>
                                            <span class="text-slate-400 text-xs"><i class="fa-solid fa-lock-open"></i> Active</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 text-right no-print">
                                        <form action="/siwes/coordinator/allocations/lock" method="POST">
                                            <input type="hidden" name="allocation_id" value="<?= $a['id'] ?>">
                                            <input type="hidden" name="lock" value="<?= $a['status'] === 'locked' ? '0' : '1' ?>">
                                            <button type="submit" class="bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 px-3 py-1.5 rounded-lg text-xs font-semibold transition text-slate-700 dark:text-gray-300">
                                                <?= $a['status'] === 'locked' ? 'Unlock' : 'Lock' ?>
                                            </button>
                                        </form>
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
