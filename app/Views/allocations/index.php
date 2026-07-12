<div class="space-y-8 animate-fade-in-up">
    <!-- Header -->
    <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-800 pb-4">
        <div>
            <h1 class="text-3xl font-poppins font-bold text-slate-800 dark:text-white">SIWES Placements & Allocations</h1>
            <p class="text-sm text-slate-500 dark:text-gray-400">Run automatic matching algorithm, lock manual assignments, and review placements.</p>
        </div>
        <div class="flex space-x-2 no-print">
            <form action="/siwes/coordinator/allocations/run" method="POST">
                <button type="submit" class="bg-primary hover:bg-secondary text-white text-sm px-5 py-2.5 rounded-xl font-semibold shadow-md shadow-primary/20 transition flex items-center space-x-2">
                    <i class="fa-solid fa-bolt animate-pulse"></i> <span>Execute Auto-Allocation</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Split view: Left manual assignment form, Right active allocations list -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Section: Manual Allocation Form -->
        <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6 h-fit no-print">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4"><i class="fa-solid fa-user-plus mr-2 text-primary"></i> Manual Placement Override</h3>
            
            <form action="/siwes/coordinator/allocations/assign" method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Select Student</label>
                    <select name="student_id" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" required>
                        <option value="">-- Choose Student --</option>
                        <?php foreach ($unallocated as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?> (<?= htmlspecialchars($s['matric_no']) ?>) - <?= htmlspecialchars($s['dept_code']) ?></option>
                        <?php endforeach; ?>
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

                <button type="submit" class="w-full bg-slate-100 hover:bg-primary hover:text-white text-slate-850 dark:bg-slate-850 dark:hover:bg-primary dark:text-white py-3 rounded-xl font-semibold shadow-sm transition">Assign Student</button>
            </form>
        </div>

        <!-- Section: Active Placements table -->
        <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6 lg:col-span-2">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4"><i class="fa-solid fa-list-check mr-2 text-accent"></i> Active Placement Allocations</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-500 dark:text-gray-400">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-700 dark:text-gray-200">
                            <th class="py-3 font-semibold">Student</th>
                            <th class="py-3 font-semibold">Matric / Dept</th>
                            <th class="py-3 font-semibold">Allocated Company</th>
                            <th class="py-3 font-semibold">Lock</th>
                            <th class="py-3 font-semibold text-right no-print">Override Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <?php if (empty($allocations)): ?>
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-400">No placements run yet. Click "Execute Auto-Allocation" in header.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($allocations as $a): ?>
                                <tr>
                                    <td class="py-3 font-medium text-slate-800 dark:text-white"><?= htmlspecialchars($a['student_name']) ?></td>
                                    <td class="py-3 text-xs"><span class="font-bold font-mono"><?= htmlspecialchars($a['matric_no']) ?></span><br><span class="text-slate-400"><?= htmlspecialchars($a['dept_code']) ?></span></td>
                                    <td class="py-3 text-xs"><?= htmlspecialchars($a['company_name']) ?></td>
                                    <td class="py-3">
                                        <?php if ($a['status'] === 'locked'): ?>
                                            <span class="text-primary font-bold text-xs"><i class="fa-solid fa-lock"></i> Locked</span>
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
