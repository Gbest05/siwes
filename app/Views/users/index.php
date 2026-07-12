<div class="space-y-8 animate-fade-in-up">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-poppins font-bold text-slate-800 dark:text-white">SIWES User Management</h1>
            <p class="text-sm text-slate-500 dark:text-gray-400">Monitor all accounts in the portal, toggle account status (suspend/activate), or permanently delete users.</p>
        </div>
    </div>

    <!-- Filters Panel -->
    <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 p-5 shadow-soft">
        <form action="/siwes/coordinator/users" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Search Username/Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 pl-9 pr-4 py-2 rounded-xl text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white" placeholder="Username or email...">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Account Role</label>
                <select name="role" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                    <option value="">All Roles</option>
                    <option value="admin" <?= ($filters['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="coordinator" <?= ($filters['role'] ?? '') === 'coordinator' ? 'selected' : '' ?>>Coordinator</option>
                    <option value="officer" <?= ($filters['role'] ?? '') === 'officer' ? 'selected' : '' ?>>Department Officer</option>
                    <option value="student" <?= ($filters['role'] ?? '') === 'student' ? 'selected' : '' ?>>Student</option>
                    <option value="supervisor" <?= ($filters['role'] ?? '') === 'supervisor' ? 'selected' : '' ?>>Company Supervisor</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Account Status</label>
                <select name="status" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none dark:text-white">
                    <option value="">All Statuses</option>
                    <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Suspended / Inactive</option>
                </select>
            </div>

            <div>
                <button type="submit" class="w-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-850 dark:hover:bg-slate-800 text-slate-700 dark:text-white py-2 rounded-xl text-xs font-bold transition">Apply Filters</button>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white dark:bg-darkCard rounded-3xl border border-slate-100 dark:border-slate-800 shadow-soft p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-500 dark:text-gray-400">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-700 dark:text-gray-200">
                        <th class="py-3 font-semibold">Username</th>
                        <th class="py-3 font-semibold">Email</th>
                        <th class="py-3 font-semibold">System Role</th>
                        <th class="py-3 font-semibold">Account Status</th>
                        <th class="py-3 font-semibold">Date Registered</th>
                        <th class="py-3 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-400">No user accounts found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td class="py-3 font-medium text-slate-800 dark:text-white"><?= htmlspecialchars($u['username']) ?></td>
                                <td class="py-3 text-xs"><?= htmlspecialchars($u['email']) ?></td>
                                <td class="py-3 text-xs font-semibold capitalize">
                                    <?php if ($u['role'] === 'admin'): ?>
                                        <span class="bg-red-500/10 text-red-500 px-2 py-0.5 rounded-full text-[10px]">Admin</span>
                                    <?php elseif ($u['role'] === 'coordinator'): ?>
                                        <span class="bg-primary/10 text-primary px-2 py-0.5 rounded-full text-[10px]">Coordinator</span>
                                    <?php elseif ($u['role'] === 'officer'): ?>
                                        <span class="bg-blue-500/10 text-blue-500 px-2 py-0.5 rounded-full text-[10px]">Officer</span>
                                    <?php elseif ($u['role'] === 'supervisor'): ?>
                                        <span class="bg-warning/10 text-warning px-2 py-0.5 rounded-full text-[10px]">Supervisor</span>
                                    <?php else: ?>
                                        <span class="bg-success/10 text-success px-2 py-0.5 rounded-full text-[10px]">Student</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 text-xs">
                                    <?php if ($u['status'] === 'active'): ?>
                                        <span class="text-success font-semibold"><i class="fa-solid fa-circle text-[8px] mr-1"></i> Active</span>
                                    <?php else: ?>
                                        <span class="text-danger font-semibold"><i class="fa-solid fa-circle text-[8px] mr-1"></i> Suspended</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 text-xs"><?= htmlspecialchars($u['created_at']) ?></td>
                                <td class="py-3 text-right">
                                    <div class="inline-flex space-x-1 justify-end">
                                        <!-- Suspend / Activate -->
                                        <form action="/siwes/coordinator/user/suspend" method="POST">
                                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                            <input type="hidden" name="status" value="<?= $u['status'] === 'active' ? 'inactive' : 'active' ?>">
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg text-xs font-bold transition <?= $u['status'] === 'active' ? 'bg-warning/10 text-warning hover:bg-warning hover:text-white' : 'bg-success/10 text-success hover:bg-success hover:text-white' ?>">
                                                <?= $u['status'] === 'active' ? 'Suspend' : 'Unsuspend' ?>
                                            </button>
                                        </form>

                                        <!-- Delete -->
                                        <form action="/siwes/coordinator/user/delete" method="POST" onsubmit="return confirm('WARNING: This will permanently delete this user and all their profile associations. Proceed?');">
                                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                            <button type="submit" class="px-2.5 py-1.5 bg-danger/10 text-danger hover:bg-danger hover:text-white rounded-lg text-xs font-bold transition">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <?php if ($pagination['total'] > 1): ?>
            <div class="flex justify-between items-center mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 no-print">
                <span class="text-xs text-slate-400">Showing page <?= $pagination['current'] ?> of <?= $pagination['total'] ?> (Total: <?= $pagination['total_items'] ?> user accounts)</span>
                <div class="inline-flex space-x-1">
                    <a href="/siwes/coordinator/users?page=<?= $pagination['current'] - 1 ?>&search=<?= urlencode($filters['search']) ?>&role=<?= urlencode($filters['role']) ?>&status=<?= urlencode($filters['status']) ?>" class="px-3 py-1.5 bg-slate-50 dark:bg-slate-800 rounded-lg text-xs font-semibold hover:bg-slate-100 transition <?= $pagination['current'] <= 1 ? 'pointer-events-none opacity-50' : '' ?>">&laquo; Prev</a>
                    <a href="/siwes/coordinator/users?page=<?= $pagination['current'] + 1 ?>&search=<?= urlencode($filters['search']) ?>&role=<?= urlencode($filters['role']) ?>&status=<?= urlencode($filters['status']) ?>" class="px-3 py-1.5 bg-slate-50 dark:bg-slate-800 rounded-lg text-xs font-semibold hover:bg-slate-100 transition <?= $pagination['current'] >= $pagination['total'] ? 'pointer-events-none opacity-50' : '' ?>">Next &raquo;</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
